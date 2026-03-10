<?php
/**
 * Shared Login Page
 * Works for both Admin and Customer
 */
session_start();
require_once 'config/db.php';
require_once 'includes/auth.php';

// Already logged in - redirect based on role
if (Auth::isLoggedIn()) {
    $user = Auth::getUser();
    if ($user['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: user/index.php?page=home");
    }
    exit();
}

$error = '';
$success = '';
$isRegister = isset($_GET['action']) && $_GET['action'] === 'register';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? 'login';
    $auth = new Auth();

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $result = $auth->login($email, $password);

        if ($result['success']) {
            // Remember me
            if ($remember && isset($result['user'])) {
                setcookie('user_remember', 'true', time() + (86400 * 30), "/");
                setcookie('user_token', $result['user']['id'], time() + (86400 * 30), "/");
            }

            // Redirect based on role
            if (isset($result['user']) && $result['user']['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php?page=home");
            }
            exit();
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'register') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullname = trim($_POST['fullname'] ?? '');

        if ($password !== $confirmPassword) {
            $error = 'Mật khẩu xác nhận không khớp';
        } else {
            $result = $auth->register($username, $email, $password, $fullname);
            if ($result['success']) {
                $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
                $isRegister = false;
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isRegister ? 'Đăng ký' : 'Đăng nhập'; ?> - GameKey Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract Background Elements */
        body::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--primary);
            filter: blur(120px);
            border-radius: 50%;
            top: -100px;
            right: -50px;
            opacity: 0.3;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: var(--secondary);
            filter: blur(120px);
            border-radius: 50%;
            bottom: -100px;
            left: -50px;
            opacity: 0.2;
            z-index: 0;
        }

        .auth-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .logo-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
            color: white;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .brand-name {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, #fff, #94a3b8);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .brand-tagline {
            color: var(--text-muted);
            font-size: 14px;
        }

        .tab-nav {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            padding: 5px;
            border-radius: 14px;
            margin-bottom: 30px;
        }

        .tab-nav a {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            transition: all 0.3s;
            font-size: 15px;
        }

        .tab-nav a.active {
            background: var(--glass);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            color: var(--text-main);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
            z-index: 5;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px 12px 48px;
            color: white;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            color: white;
        }

        .form-control::placeholder {
            color: #475569;
        }

        .btn-auth {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: white;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        .form-check-label {
            color: var(--text-muted);
            font-size: 14px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: white;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-wrapper">
                <div class="logo-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h1 class="brand-name">GameKey Store</h1>
                <p class="brand-tagline">Nền tảng mua sắm key game bản quyền</p>
            </div>

            <div class="tab-nav">
                <a href="?action=login" class="<?php echo !$isRegister ? 'active' : ''; ?>">Đăng nhập</a>
                <a href="?action=register" class="<?php echo $isRegister ? 'active' : ''; ?>">Đăng ký</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?= htmlspecialchars($success) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($isRegister): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <div class="input-group">
                            <i class="fas fa-user-tag"></i>
                            <input type="text" name="fullname" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" class="form-control" placeholder="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="••••••" required minlength="6">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <div class="input-group">
                            <i class="fas fa-shield-alt"></i>
                            <input type="password" name="confirm_password" class="form-control" placeholder="••••••" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-auth">
                        <span>ĐĂNG KÝ NGAY</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="••••••" required>
                        </div>
                    </div>
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" style="background-color: transparent; border-color: var(--glass-border);">
                            <label class="form-check-label" for="remember">Ghi nhớ tôi</label>
                        </div>
                        <a href="#" class="text-decoration-none" style="font-size: 13px; color: var(--primary);">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btn btn-auth">
                        <span>ĐĂNG NHẬP</span>
                        <i class="fas fa-sign-in-alt"></i>
                    </button>
                </form>
            <?php endif; ?>

            <div class="back-link">
                <a href="user/index.php">
                    <i class="fas fa-long-arrow-alt-left me-1"></i>
                    Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>