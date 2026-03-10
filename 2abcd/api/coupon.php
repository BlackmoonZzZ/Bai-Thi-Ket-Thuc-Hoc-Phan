<?php
/**
 * Coupon API
 * Handles coupon validation and discount calculation
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../includes/coupon.php';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? null;

    if (!$action) {
        throw new Exception('Hành động không được chỉ định');
    }

    $couponModel = new Coupon();

    switch ($action) {
        case 'validate':
            $code = trim($_GET['code'] ?? $_POST['code'] ?? '');
            $total = (float)($_GET['total'] ?? $_POST['total'] ?? 0);

            if (empty($code)) {
                throw new Exception('Vui lòng nhập mã giảm giá');
            }

            if ($total <= 0) {
                throw new Exception('Hóa đơn không hợp lệ');
            }

            $result = $couponModel->validateCoupon($code, $total);
            echo json_encode($result);
            break;

        case 'apply':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Bạn cần đăng nhập');
            }

            $code = trim($_POST['code'] ?? '');
            $total = (float)($_POST['total'] ?? 0);

            if (empty($code)) {
                throw new Exception('Vui lòng nhập mã giảm giá');
            }

            $result = $couponModel->validateCoupon($code, $total);
            if ($result['success']) {
                $_SESSION['cart_coupon'] = $result['coupon'];
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
