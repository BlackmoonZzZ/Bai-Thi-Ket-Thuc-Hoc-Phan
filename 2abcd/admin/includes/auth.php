<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Nếu user_id được set từ frontend login, ánh xạ qua các biến session của admin
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff')) {
    $_SESSION['admin_id'] = $_SESSION['user_id'];
    $_SESSION['admin_name'] = $_SESSION['fullname'] ?? $_SESSION['username'];
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php?page=login");
    exit();
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

