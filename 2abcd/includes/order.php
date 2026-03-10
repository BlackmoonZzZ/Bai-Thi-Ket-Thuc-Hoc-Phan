<?php
require_once __DIR__ . '/../config/database.php';

class Order
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create order from cart
    public function createOrder($userId, $cartItems, $paymentMethod = 'wallet', $couponId = null)
    {
        try {
            if (empty($cartItems)) {
                return ['success' => false, 'message' => 'Giỏ hàng trống'];
            }

            // Start transaction
            $this->conn->beginTransaction();

            $totalAmount = 0;
            $discountAmount = 0;
            $orderItems = [];

            // Calculate total and validate items
            foreach ($cartItems as $item) {
                $stmt = $this->conn->prepare("SELECT id, name, price, discount_percent, stock_quantity FROM products WHERE id = ? AND status = 'active'");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception("Sản phẩm không tồn tại");
                }

                if ($product['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Sản phẩm '{$product['name']}' không đủ số lượng");
                }

                // Calculate price with discount
                $price = $product['price'];
                if ($product['discount_percent'] > 0) {
                    $price = $price * (1 - $product['discount_percent'] / 100);
                }

                $totalAmount += $price * $item['quantity'];

                for ($i = 0; $i < $item['quantity']; $i++) {
                    $orderItems[] = [
                        'product_id' => $item['product_id'],
                        'price' => $price
                    ];
                }
            }

            // Apply coupon if provided
            if (!empty($couponId)) {
                $stmt = $this->conn->prepare(
                    "SELECT id, discount_type, discount_value FROM coupons WHERE id = ? AND is_active = TRUE AND expiry_date >= CURDATE() AND used_count < usage_limit"
                );
                $stmt->execute([$couponId]);
                $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($coupon) {
                    if ($coupon['discount_type'] === 'percentage') {
                        $discountAmount = $totalAmount * $coupon['discount_value'] / 100;
                    } else {
                        $discountAmount = $coupon['discount_value'];
                    }

                    // Increment coupon usage
                    $stmt = $this->conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
                    $stmt->execute([$couponId]);
                }
            }

            // Calculate final amount
            $finalAmount = max(0, $totalAmount - $discountAmount);

            // Check wallet balance for wallet payment
            if ($paymentMethod === 'wallet') {
                $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user['balance'] < $finalAmount) {
                    throw new Exception("Số dư ví không đủ");
                }
            }

            // Generate unique order number
            $orderNumber = 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -4));

            // Create order
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (order_number, user_id, total_amount, discount_amount, coupon_id, payment_method, status, payment_status) 
                 VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)"
            );

            $paymentStatus = ($paymentMethod === 'wallet') ? 'paid' : 'unpaid';

            $stmt->execute([$orderNumber, $userId, $totalAmount, $discountAmount, $couponId, $paymentMethod, $paymentStatus]);
            $orderId = $this->conn->lastInsertId();

            // Create order items and update stock
            foreach ($orderItems as $item) {
                // Get available product key
                $stmt = $this->conn->prepare(
                    "SELECT id FROM product_keys WHERE product_id = ? AND status = 'available' LIMIT 1 FOR UPDATE"
                );
                $stmt->execute([$item['product_id']]);
                $key = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$key) {
                    throw new Exception("Hết khoá khả dụng cho sản phẩm");
                }

                // Insert order item
                $stmt = $this->conn->prepare(
                    "INSERT INTO order_items (order_id, product_id, key_id, price) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$orderId, $item['product_id'], $key['id'], $item['price']]);

                // Mark key as sold
                $stmt = $this->conn->prepare("UPDATE product_keys SET status = 'sold', sold_to_user_id = ?, sold_at = NOW() WHERE id = ?");
                $stmt->execute([$userId, $key['id']]);

                // Update product stock
                $stmt = $this->conn->prepare("UPDATE products SET stock_quantity = stock_quantity - 1 WHERE id = ?");
                $stmt->execute([$item['product_id']]);
            }

            // Process payment
            if ($paymentMethod === 'wallet') {
                // Deduct from wallet
                $stmt = $this->conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$finalAmount, $userId]);

                // Log transaction
                $stmt = $this->conn->prepare(
                    "INSERT INTO wallet_transactions (user_id, amount, type, reference_id, description, balance_before, balance_after, status) 
                     VALUES (?, ?, 'purchase', ?, ?, ?, ?, 'completed')"
                );

                $stmt->execute([
                    $userId,
                    $finalAmount,
                    'ORD-' . $orderId,
                    'Mua sản phẩm',
                    $user['balance'],
                    $user['balance'] - $finalAmount
                ]);

                // Update order status to completed
                $stmt = $this->conn->prepare(
                    "UPDATE orders SET status = 'completed', payment_status = 'paid', completed_at = NOW() WHERE id = ?"
                );
                $stmt->execute([$orderId]);
            }

            // Commit transaction
            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Đơn hàng được tạo thành công',
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Create order error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Get user orders
    public function getUserOrders($userId, $limit = 20, $offset = 0)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT o.*, COUNT(oi.id) as item_count 
                 FROM orders o
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 WHERE o.user_id = ?
                 GROUP BY o.id
                 ORDER BY o.created_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get user orders error: " . $e->getMessage());
            return [];
        }
    }

    // Get order details
    public function getOrderDetails($orderId, $userId = null)
    {
        try {
            $where = "WHERE o.id = ?";
            $params = [$orderId];

            if ($userId) {
                $where .= " AND o.user_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->conn->prepare(
                "SELECT o.*, u.username, u.email, c.code as coupon_code 
                 FROM orders o
                 LEFT JOIN users u ON o.user_id = u.id
                 LEFT JOIN coupons c ON o.coupon_id = c.id
                 $where"
            );
            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return null;
            }

            // Get order items
            $stmt = $this->conn->prepare(
                "SELECT oi.*, p.name as product_name, p.image, pk.key_code 
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 LEFT JOIN product_keys pk ON oi.key_id = pk.id
                 WHERE oi.order_id = ?"
            );
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $order;

        } catch (Exception $e) {
            error_log("Get order details error: " . $e->getMessage());
            return null;
        }
    }

    // Get user library (purchased games)
    public function getUserLibrary($userId)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT DISTINCT p.*, pk.key_code, oi.created_at as purchase_date
                 FROM products p
                 JOIN order_items oi ON p.id = oi.product_id
                 JOIN product_keys pk ON oi.key_id = pk.id
                 JOIN orders o ON oi.order_id = o.id
                 WHERE o.user_id = ? AND o.status IN ('completed', 'refunded')
                 ORDER BY oi.created_at DESC"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get user library error: " . $e->getMessage());
            return [];
        }
    }

    // Cancel order
    public function cancelOrder($orderId, $userId = null)
    {
        try {
            $this->conn->beginTransaction();

            // Get order
            $where = "WHERE id = ?";
            $params = [$orderId];

            if ($userId) {
                $where .= " AND user_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->conn->prepare("SELECT * FROM orders $where");
            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || $order['status'] !== 'pending') {
                throw new Exception("Không thể hủy đơn hàng này");
            }

            // Get order items
            $stmt = $this->conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Restore keys to available
            foreach ($items as $item) {
                $stmt = $this->conn->prepare("UPDATE product_keys SET status = 'available', sold_to_user_id = NULL, sold_at = NULL WHERE id = ?");
                $stmt->execute([$item['key_id']]);

                // Restore stock
                $stmt = $this->conn->prepare("UPDATE products SET stock_quantity = stock_quantity + 1 WHERE id = ?");
                $stmt->execute([$item['product_id']]);
            }

            // Update order status
            $stmt = $this->conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$orderId]);

            $this->conn->commit();

            return ['success' => true, 'message' => 'Đơn hàng đã được hủy'];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Cancel order error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Refund order
    public function refundOrder($orderId, $userId = null)
    {
        try {
            $this->conn->beginTransaction();

            // Get order
            $where = "WHERE id = ?";
            $params = [$orderId];

            if ($userId) {
                $where .= " AND user_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->conn->prepare("SELECT * FROM orders $where");
            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || $order['status'] !== 'completed') {
                throw new Exception("Không thể hoàn tiền cho đơn hàng này");
            }

            $refundAmount = $order['total_amount'] - $order['discount_amount'];

            // Refund to wallet
            $stmt = $this->conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$refundAmount, $order['user_id']]);

            // Log transaction
            $stmt = $this->conn->prepare(
                "INSERT INTO wallet_transactions (user_id, amount, type, reference_id, description, status) 
                 VALUES (?, ?, 'refund', ?, 'Hoàn tiền đơn hàng', 'completed')"
            );
            $stmt->execute([$order['user_id'], $refundAmount, 'ORD-' . $orderId]);

            // Update order status
            $stmt = $this->conn->prepare("UPDATE orders SET status = 'refunded' WHERE id = ?");
            $stmt->execute([$orderId]);

            $this->conn->commit();

            return ['success' => true, 'message' => 'Đã hoàn tiền thành công', 'refund_amount' => $refundAmount];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Refund order error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
