<?php
// Session should be started in the main entry point

require_once __DIR__ . '/../config/database.php';

class Cart
{
    private static $database;

    // Initialize cart session
    public static function init()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (!isset($_SESSION['cart_coupon'])) {
            $_SESSION['cart_coupon'] = null;
        }
    }

    // Add item to cart with validation
    public static function add($productId, $quantity = 1)
    {
        try {
            self::init();

            $productId = (int)$productId;
            $quantity = (int)$quantity;

            if ($productId <= 0 || $quantity <= 0) {
                return ['success' => false, 'message' => 'Sản phẩm hoặc số lượng không hợp lệ'];
            }

            // Verify product exists
            $db = self::getConnection();
            $stmt = $db->prepare("SELECT id, stock_quantity FROM products WHERE id = ? AND status = 'active'");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
            }

            // Check stock
            if ($product['stock_quantity'] < $quantity) {
                return ['success' => false, 'message' => 'Số lượng không đủ trong kho'];
            }

            // Add to cart
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $quantity;
            }
            else {
                $_SESSION['cart'][$productId] = $quantity;
            }

            return ['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'cart_count' => self::getCount()];

        }
        catch (Exception $e) {
            error_log("Cart add error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Update item quantity
    public static function update($productId, $quantity)
    {
        try {
            self::init();

            $productId = (int)$productId;
            $quantity = (int)$quantity;

            if ($quantity <= 0) {
                return self::remove($productId);
            }

            if (!isset($_SESSION['cart'][$productId])) {
                return ['success' => false, 'message' => 'Sản phẩm không trong giỏ hàng'];
            }

            // Verify stock
            $db = self::getConnection();
            $stmt = $db->prepare("SELECT stock_quantity FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stock_quantity'] < $quantity) {
                return ['success' => false, 'message' => 'Số lượng không đủ trong kho'];
            }

            $_SESSION['cart'][$productId] = $quantity;

            return ['success' => true, 'message' => 'Đã cập nhật giỏ hàng', 'cart_count' => self::getCount()];

        }
        catch (Exception $e) {
            error_log("Cart update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Remove item from cart
    public static function remove($productId)
    {
        try {
            self::init();

            $productId = (int)$productId;

            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }

            return ['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng', 'cart_count' => self::getCount()];

        }
        catch (Exception $e) {
            error_log("Cart remove error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Clear cart
    public static function clear()
    {
        $_SESSION['cart'] = [];
        $_SESSION['cart_coupon'] = null;
        return ['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng'];
    }

    // Get cart items
    public static function getItems()
    {
        self::init();
        return $_SESSION['cart'];
    }

    // Get cart count (total items)
    public static function getCount()
    {
        self::init();
        return array_sum($_SESSION['cart']);
    }

    // Get cart with product details and totals
    public static function getCartDetails()
    {
        require_once __DIR__ . '/product.php';

        self::init();

        $productModel = new Product();
        $items = [];
        $subtotal = 0;

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $productModel->getProduct($productId);

            if ($product) {
                // Calculate price with discount
                $price = $product['price'];
                if ($product['discount_percent'] > 0) {
                    $price = $price * (1 - $product['discount_percent'] / 100);
                }

                $itemSubtotal = $price * $quantity;

                $items[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'image' => $product['image'],
                    'price' => $product['price'],
                    'discount_percent' => $product['discount_percent'],
                    'final_price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal
                ];

                $subtotal += $itemSubtotal;
            }
        }

        // Apply coupon if exists
        $discount = 0;
        $coupon = null;
        if (!empty($_SESSION['cart_coupon'])) {
            $coupon = $_SESSION['cart_coupon'];
            if ($coupon['discount_type'] === 'percentage') {
                $discount = $subtotal * $coupon['discount_value'] / 100;
            }
            else {
                $discount = $coupon['discount_value'];
            }
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'coupon' => $coupon,
            'count' => count($items)
        ];
    }

    // Apply coupon to cart
    public static function applyCoupon($couponCode)
    {
        try {
            self::init();

            if (empty(self::getItems())) {
                return ['success' => false, 'message' => 'Giỏ hàng trống'];
            }

            $db = self::getConnection();

            // Get coupon
            $stmt = $db->prepare(
                "SELECT * FROM coupons WHERE code = ? AND is_active = TRUE AND expiry_date >= CURDATE()"
            );
            $stmt->execute([$couponCode]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$coupon) {
                return ['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn'];
            }

            // Check usage limit
            if ($coupon['used_count'] >= $coupon['usage_limit']) {
                return ['success' => false, 'message' => 'Mã giảm giá đã được sử dụng hết'];
            }

            // Check minimum order amount
            $cartDetails = self::getCartDetails();
            if ($cartDetails['subtotal'] < $coupon['min_order_amount']) {
                return ['success' => false, 'message' => 'Đơn hàng phải tối thiểu ' . number_format($coupon['min_order_amount']) . ' VND'];
            }

            // Apply coupon
            $_SESSION['cart_coupon'] = [
                'id' => $coupon['id'],
                'code' => $coupon['code'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
                'description' => $coupon['description']
            ];

            return ['success' => true, 'message' => 'Áp dụng mã giảm giá thành công'];

        }
        catch (Exception $e) {
            error_log("Apply coupon error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Remove coupon from cart
    public static function removeCoupon()
    {
        self::init();
        $_SESSION['cart_coupon'] = null;
        return ['success' => true, 'message' => 'Đã xóa mã giảm giá'];
    }

    // Convert cart to order format
    public static function toOrderFormat()
    {
        self::init();

        $items = [];
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];
        }

        return $items;
    }

    // Get database connection
    private static function getConnection()
    {
        $database = new Database();
        return $database->getConnection();
    }
}
