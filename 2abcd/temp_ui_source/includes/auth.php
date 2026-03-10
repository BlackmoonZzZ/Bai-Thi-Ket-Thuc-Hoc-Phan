<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['admin_id'])) {
    // Nếu có Cookie, tự động đăng nhập lại
    if (isset($_COOKIE['admin_remember']) && isset($_COOKIE['admin_token'])) {
        // Logic kiểm tra token trong DB (Ở đây làm đơn giản là cho qua để demo)
        $_SESSION['admin_id'] = $_COOKIE['admin_token']; 
        $_SESSION['admin_name'] = "Administrator";
    } else {
        header("Location: login.php");
        exit();
    }
}

if (isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || !isset($_SESSION['admin_name'])) && isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT fullname, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['admin_name'] = $user['fullname'] ?: ($_SESSION['admin_name'] ?? 'Administrator');
        }
    } catch (Exception $e) {
    }
}

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$restrictedForStaff = [
    'Admins.php',
    'Payment_methods.php',
    'Email_logs.php',
];

if (($_SESSION['role'] ?? '') === 'staff' && in_array($currentPage, $restrictedForStaff, true)) {
    $_SESSION['access_denied_message'] = 'Bạn không có quyền truy cập chức năng này!';
    header('Location: index.php');
    exit();
}
?>