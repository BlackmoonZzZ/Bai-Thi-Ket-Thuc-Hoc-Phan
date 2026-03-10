<?php
/**
 * Authentication API
 * Handles login, register, logout, profile updates
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../includes/auth.php';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? null;

    if (!$action) {
        throw new Exception('Hành động không được chỉ định');
    }

    $auth = new Auth();

    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $email = trim($_POST['email'] ?? $_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                throw new Exception('Email/tên đăng nhập và mật khẩu không được để trống');
            }

            $result = $auth->login($email, $password);
            echo json_encode($result);
            break;

        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $fullname = trim($_POST['fullname'] ?? '');

            if (empty($username) || empty($email) || empty($password) || empty($fullname)) {
                throw new Exception('Vui lòng nhập đầy đủ thông tin');
            }

            if ($password !== $confirmPassword) {
                throw new Exception('Mật khẩu xác nhận không khớp');
            }

            $result = $auth->register($username, $email, $password, $fullname);
            echo json_encode($result);
            break;

        case 'logout':
            $result = Auth::logout();
            echo json_encode($result);
            break;

        case 'check':
            if (Auth::isLoggedIn()) {
                echo json_encode([
                    'success' => true,
                    'authenticated' => true,
                    'user' => Auth::getUser()
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'authenticated' => false
                ]);
            }
            break;

        case 'update_profile':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            if (!Auth::isLoggedIn()) {
                throw new Exception('Bạn cần đăng nhập');
            }

            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? null);

            $result = $auth->updateProfile($_SESSION['user_id'], $fullname, $phone);
            echo json_encode($result);
            break;

        case 'change_password':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            if (!Auth::isLoggedIn()) {
                throw new Exception('Bạn cần đăng nhập');
            }

            $oldPassword = $_POST['old_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('Vui lòng nhập đầy đủ thông tin');
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception('Mật khẩu xác nhận không khớp');
            }

            $result = $auth->changePassword($_SESSION['user_id'], $oldPassword, $newPassword);
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
