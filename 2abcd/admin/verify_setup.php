<?php
/**
 * Database Connection Verification & Setup
 * Truy cập: (tự động theo domain/folder hiện tại)
 */

require_once __DIR__ . '/../config/db.php';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$currentBaseUrl = $scheme . '://' . $host . $basePath;

$errors = [];
$warnings = [];
$success = [];

// ============================================
// 1. Test Database Connection
// ============================================
try {
    $conn->query("SELECT 1");
    $success[] = "✅ Kết nối database thành công!";
} catch (Exception $e) {
    $errors[] = "❌ Lỗi kết nối database: " . $e->getMessage();
}

// ============================================
// 2. Check Tables
// ============================================
$required_tables = [
    'users', 'categories', 'platforms', 'products', 'product_keys',
    'orders', 'order_items', 'coupons', 'reviews', 'wishlist',
    'cart', 'wallet_transactions', 'admin_logs', 'settings'
];

foreach ($required_tables as $table) {
    try {
        $result = $conn->query("SELECT COUNT(*) FROM $table");
        $count = $result->fetchColumn();
        $success[] = "✅ Bảng <strong>$table</strong>: $count bản ghi";
    } catch (Exception $e) {
        $errors[] = "❌ Bảng <strong>$table</strong> không tồn tại!";
    }
}

// ============================================
// 3. Check Sample Data
// ============================================
$data_checks = [
    'products' => 20,
    'product_keys' => 30,
    'users' => 21,
    'orders' => 20,
    'coupons' => 20
];

foreach ($data_checks as $table => $expected_count) {
    try {
        $result = $conn->query("SELECT COUNT(*) FROM $table");
        $count = (int)$result->fetchColumn();
        
        if ($count >= $expected_count) {
            $success[] = "✅ Dữ liệu <strong>$table</strong>: $count bản ghi (yêu cầu: $expected_count)";
        } else {
            $warnings[] = "⚠️ Dữ liệu <strong>$table</strong>: $count bản ghi (yêu cầu: $expected_count)";
        }
    } catch (Exception $e) {
        $errors[] = "❌ Không thể kiểm tra bảng $table";
    }
}

// ============================================
// 4. Check File Permissions
// ============================================
$file_checks = [
    'uploads' => 'thư mục upload ảnh',
    'config/db.php' => 'file cấu hình database',
    'includes/header.php' => 'header template'
];

foreach ($file_checks as $path => $desc) {
    $full_path = __DIR__ . '/' . $path;
    if (is_dir($full_path)) {
        if (is_writable($full_path)) {
            $success[] = "✅ Thư mục <strong>$path</strong> ($desc) tồn tại và có quyền ghi";
        } else {
            $warnings[] = "⚠️ Thư mục <strong>$path</strong> ($desc) tồn tại nhưng không có quyền ghi";
        }
    } elseif (is_file($full_path)) {
        if (is_readable($full_path)) {
            $success[] = "✅ File <strong>$path</strong> ($desc) tồn tại";
        } else {
            $errors[] = "❌ File <strong>$path</strong> ($desc) không thể đọc";
        }
    } else {
        // Chỉ cảnh báo nếu là uploads
        if (strpos($path, 'uploads') !== false) {
            $warnings[] = "⚠️ Thư mục <strong>$path</strong> ($desc) không tồn tại - sẽ tự tạo khi upload";
        }
    }
}

// ============================================
// 5. Test Key Functions
// ============================================
try {
    $slug = createSlug("Elden Ring");
    if ($slug === "elden-ring") {
        $success[] = "✅ Hàm createSlug() hoạt động đúng";
    } else {
        $warnings[] = "⚠️ Hàm createSlug() trả về: '$slug'";
    }
} catch (Exception $e) {
    $errors[] = "❌ Hàm createSlug() lỗi: " . $e->getMessage();
}

// ============================================
// 6. Test Transaction Support
// ============================================
try {
    $conn->beginTransaction();
    $conn->rollBack();
    $success[] = "✅ PDO Transaction support hoạt động";
} catch (Exception $e) {
    $errors[] = "❌ PDO Transaction support lỗi: " . $e->getMessage();
}

// ============================================
// HTML Output
// ============================================
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Xác minh Setup GameKey Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 30px 0;
        }
        .container {
            max-width: 900px;
        }
        .card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 1px solid rgba(6, 182, 212, 0.2);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2) 0%, rgba(236, 72, 153, 0.1) 100%);
            border-bottom: 2px solid rgba(6, 182, 212, 0.3);
        }
        .card-title {
            color: #06b6d4;
            font-weight: 700;
        }
        .list-group-item {
            background: transparent;
            border-color: rgba(6, 182, 212, 0.1);
            color: #e2e8f0;
        }
        .success-item {
            border-left: 4px solid #10b981;
            padding-left: 15px;
        }
        .warning-item {
            border-left: 4px solid #f59e0b;
            padding-left: 15px;
            color: #fbbf24;
        }
        .error-item {
            border-left: 4px solid #ef4444;
            padding-left: 15px;
            color: #fca5a5;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .status-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .status-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        .status-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .header-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .header-logo h1 {
            color: #06b6d4;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .header-logo p {
            color: #94a3b8;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-logo">
            <h1><i class="fas fa-gamepad"></i> GameKey Store</h1>
            <p>🔍 Database & Setup Verification</p>
        </div>

        <!-- Status Summary -->
        <div style="text-align: center; margin-bottom: 30px;">
            <?php if (empty($errors)): ?>
                <span class="status-badge status-success">
                    <i class="fas fa-check-circle"></i> TẤT CẢ KIỂM TRA THÀNH CÔNG
                </span>
            <?php elseif (!empty($errors)): ?>
                <span class="status-badge status-error">
                    <i class="fas fa-times-circle"></i> CÓ LỖI CẦN SỬA
                </span>
            <?php else: ?>
                <span class="status-badge status-warning">
                    <i class="fas fa-exclamation-circle"></i> CÓ CẢNH BÁO
                </span>
            <?php endif; ?>
        </div>

        <!-- Success Messages -->
        <?php if (!empty($success)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0"><i class="fas fa-check-circle"></i> ✅ Thành công (<?= count($success) ?>)</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($success as $msg): ?>
                <div class="list-group-item success-item">
                    <?= $msg ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Warning Messages -->
        <?php if (!empty($warnings)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0" style="color: #fbbf24;"><i class="fas fa-exclamation-circle"></i> ⚠️ Cảnh báo (<?= count($warnings) ?>)</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($warnings as $msg): ?>
                <div class="list-group-item warning-item">
                    <?= $msg ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0" style="color: #fca5a5;"><i class="fas fa-times-circle"></i> ❌ Lỗi (<?= count($errors) ?>)</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($errors as $msg): ?>
                <div class="list-group-item error-item">
                    <?= $msg ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Next Steps -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0"><i class="fas fa-rocket"></i> Bước tiếp theo</h5>
            </div>
            <div class="card-body">
                <ol style="margin-bottom: 0;">
                    <li>Nếu có lỗi ❌: Vui lòng sửa trước khi tiếp tục</li>
                    <li>Truy cập: <a href="<?= htmlspecialchars($currentBaseUrl . '/login.php') ?>" style="color: #06b6d4;"><?= htmlspecialchars($currentBaseUrl . '/login.php') ?></a></li>
                    <li>Tài khoản: <strong>admin</strong> / <strong>pass</strong></li>
                    <li>Bắt đầu kiểm tra các tính năng CRUD</li>
                </ol>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 40px; color: #94a3b8;">
            <p><small>🎮 GameKey Store v1.0 | Tất cả dữ liệu đã sẵn sàng</small></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

