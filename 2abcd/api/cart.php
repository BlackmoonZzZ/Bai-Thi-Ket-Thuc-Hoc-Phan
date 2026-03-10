<?php
/**
 * Shopping Cart API
 * Handles add, update, remove, apply coupon, and checkout
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/coupon.php';
require_once __DIR__ . '/../includes/order.php';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    
    if (!$action) {
        throw new Exception('Hành động không được chỉ định');
    }
    
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            $result = Cart::add($productId, $quantity);
            echo json_encode($result);
            break;
        
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            
            $result = Cart::update($productId, $quantity);
            $result['cart'] = Cart::getCartDetails();
            echo json_encode($result);
            break;
        
        case 'remove':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $productId = (int)($_POST['product_id'] ?? 0);
            
            $result = Cart::remove($productId);
            $result['cart'] = Cart::getCartDetails();
            echo json_encode($result);
            break;
        
        case 'clear':
            $result = Cart::clear();
            echo json_encode($result);
            break;
        
        case 'get':
            $cart = Cart::getCartDetails();
            echo json_encode([
                'success' => true,
                'cart' => $cart
            ]);
            break;
        
        case 'coupon':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $couponCode = trim($_POST['coupon_code'] ?? '');
            
            if (empty($couponCode)) {
                throw new Exception('Vui lòng nhập mã giảm giá');
            }
            
            $result = Cart::applyCoupon($couponCode);
            if ($result['success']) {
                $result['cart'] = Cart::getCartDetails();
            }
            echo json_encode($result);
            break;
        
        case 'remove_coupon':
            $result = Cart::removeCoupon();
            $result['cart'] = Cart::getCartDetails();
            echo json_encode($result);
            break;
        
        case 'checkout':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            if (!Auth::isLoggedIn()) {
                throw new Exception('Bạn cần đăng nhập để thanh toán');
            }
            
            require_once __DIR__ . '/../includes/order.php';
            
            $paymentMethod = $_POST['payment_method'] ?? 'wallet';
            $cartItems = Cart::toOrderFormat();
            
            if (empty($cartItems)) {
                throw new Exception('Giỏ hàng trống');
            }
            
            $couponId = $_SESSION['cart_coupon']['id'] ?? null;
            
            $orderModel = new Order();
            $result = $orderModel->createOrder($_SESSION['user_id'], $cartItems, $paymentMethod, $couponId);
            
            if ($result['success']) {
                Cart::clear();
                $_SESSION['balance'] = $_SESSION['balance'] - $result['final_amount'];
            }
            
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
