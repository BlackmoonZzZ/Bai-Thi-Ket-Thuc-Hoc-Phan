<?php
session_start();
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role IN ('admin', 'staff')");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        // Xử lý Cookie (Lưu 30 ngày)
        if ($remember) {
            setcookie('admin_remember', 'true', time() + (86400 * 30), "/");
            setcookie('admin_token', $user['id'], time() + (86400 * 30), "/");
        }

        header("Location: index.php");
        exit();
    } else {
        $error = "Sai email hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Admin Login - GameKey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 15px; padding: 40px; width: 400px; box-shadow: 0 15px 25px rgba(0,0,0,0.2); }
        .btn-custom { background: #764ba2; color: #fff; border: none; }
        .btn-custom:hover { background: #5a367e; color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4 fw-bold" style="color: #4a4a4a;">GameKey Admin</h3>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@gamekey.vn" required value="admin@gamekey.vn">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="******" required value="admin123">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2 fw-bold">ĐĂNG NHẬP</button>
        </form>
    </div>
</body>
</html>