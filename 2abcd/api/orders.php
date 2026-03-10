<?php
/**
 * Orders API
 * Handles order listing, details, cancellation, and refunds
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/order.php';
require_once __DIR__ . '/../includes/product.php';
require_once __DIR__ . '/../includes/cart.php';

try {
    if (!Auth::isLoggedIn()) {
        throw new Exception('Bạn cần đăng nhập');
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    $orderModel = new Order();

    if (!$action) {
        throw new Exception('Hành động không được chỉ định');
    }

    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $paymentMethod = $_POST['payment_method'] ?? 'balance';
            $couponCode = trim($_POST['coupon_code'] ?? '');

            // Get cart items
            $cartData = Cart::getCartDetails();
            $cartItems = $cartData['items'];

            if (empty($cartItems)) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
                break;
            }

            // Calculate total
            $cartTotal = $cartData['total'];

            // Validate balance for wallet payment
            if ($paymentMethod === 'balance') {
                $user = Auth::getUser();
                if ($user['balance'] < $cartTotal) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Thanh toán không hợp lệ - Số dư tài khoản không đủ. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.'
                    ]);
                    break;
                }
            }

            // Lookup coupon ID if code provided
            $couponId = null;
            if (!empty($couponCode)) {
                require_once __DIR__ . '/../config/database.php';
                $couponDb = new Database();
                $couponConn = $couponDb->getConnection();
                $stmt = $couponConn->prepare("SELECT id FROM coupons WHERE code = ? AND is_active = TRUE AND expiry_date >= CURDATE() AND used_count < usage_limit");
                $stmt->execute([$couponCode]);
                $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($coupon) {
                    $couponId = $coupon['id'];
                }
            }

            // Create the order
            $result = $orderModel->createOrder(
                $_SESSION['user_id'],
                $cartItems,
                $paymentMethod,
                $couponId
            );

            if ($result['success']) {
                // Clear cart after successful order
                Cart::clear();
            }

            echo json_encode($result);
            break;

        case 'list':
            $limit = (int) ($_GET['limit'] ?? 20);
            $offset = (int) ($_GET['offset'] ?? 0);

            if ($limit > 100)
                $limit = 100;
            if ($offset < 0)
                $offset = 0;

            $orders = $orderModel->getUserOrders($_SESSION['user_id'], $limit, $offset);

            echo json_encode([
                'success' => true,
                'orders' => $orders,
                'limit' => $limit,
                'offset' => $offset,
                'count' => count($orders)
            ]);
            break;

        case 'get':
            $orderId = (int) ($_GET['id'] ?? 0);

            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }

            $order = $orderModel->getOrderDetails($orderId, $_SESSION['user_id']);

            if (!$order) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            echo json_encode([
                'success' => true,
                'order' => $order
            ]);
            break;

        case 'details':
            $orderId = (int) ($_GET['id'] ?? 0);

            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }

            $order = $orderModel->getOrderDetails($orderId, $_SESSION['user_id']);

            if (!$order) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            echo json_encode([
                'success' => true,
                'order' => $order
            ]);
            break;

        case 'library':
            $library = $orderModel->getUserLibrary($_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'library' => $library,
                'count' => count($library)
            ]);
            break;

        case 'cancel':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $orderId = (int) ($_POST['order_id'] ?? 0);

            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }

            $result = $orderModel->cancelOrder($orderId, $_SESSION['user_id']);
            echo json_encode($result);
            break;

        case 'refund':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $orderId = (int) ($_POST['order_id'] ?? 0);

            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }

            $result = $orderModel->refundOrder($orderId, $_SESSION['user_id']);
            echo json_encode($result);
            break;

        default:
            throw new Exception('Hành động không hợp lệ');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
