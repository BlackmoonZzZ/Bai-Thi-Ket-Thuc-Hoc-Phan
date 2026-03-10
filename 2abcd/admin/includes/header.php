<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

// Lấy dữ liệu notification để hiển thị badge
if(isset($conn)) {
    $orders_pending_header = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $low_stock_header = $conn->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 10 AND stock_quantity > 0")->fetchColumn();
    $out_of_stock_header = $conn->query("SELECT COUNT(*) FROM products WHERE stock_quantity = 0")->fetchColumn();
    $total_notifications_header = $orders_pending_header + $low_stock_header + $out_of_stock_header;
} else {
    $total_notifications_header = 0;
    $orders_pending_header = 0;
    $low_stock_header = 0;
    $out_of_stock_header = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameKey Admin Portal</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-body: #F5F6FA;
            --bg-surface: #FFFFFF;
            --primary: #4880FF;
            --text-dark: #202224;
            --text-gray: #718EBF;
            --border-color: #E6E9F4;
            
            --success-bg: #CCF0EB;
            --success-text: #00B69B;
            --warning-bg: #FFE2E5;
            --warning-text: #F93C65;
            --pending-bg: #E2E5FF;
            --pending-text: #6226EF;
            --info-bg: #FFF4DE;
            --info-text: #FF947A;
        }

        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
        }

        #pageLoadingOverlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 120ms ease;
        }

        #pageLoadingOverlay.show {
            opacity: 1;
            pointer-events: all;
        }

        /* Sidebar Design */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            background: var(--bg-surface);
            z-index: 1000;
            transition: 0.3s;
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .sidebar-brand {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 900;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            letter-spacing: 0.5px;
        }
        
        .sidebar-brand span {
            color: var(--primary);
        }

        .sidebar .nav-flex-column { margin-top: 15px; }

        .sidebar .nav-link {
            color: var(--text-gray);
            padding: 14px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            position: relative;
            font-size: 0.95rem;
            border-left: 4px solid transparent;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.15rem;
            text-align: center;
            margin-right: 12px;
            color: var(--text-gray);
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            background: rgba(72, 128, 255, 0.05);
        }

        .sidebar .nav-link:hover i {
            color: var(--primary);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: var(--primary);
            border-left: 0;
            margin: 5px 15px;
            border-radius: 8px;
            padding: 14px 15px;
        }

        .sidebar .nav-link.active i {
            color: #fff;
        }

        .sidebar .nav-label {
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 1px;
            color: #94a3b8;
            padding: 15px 20px 5px;
            font-weight: 700;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: 0.3s;
            min-height: 100vh;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: var(--bg-surface);
            height: 70px;
            padding: 0 30px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.01);
        }
        
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .menu-toggle {
            font-size: 1.2rem;
            color: var(--text-dark);
            cursor: pointer;
        }

        .search-bar {
            background: #F5F6FA;
            border-radius: 20px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            width: 350px;
        }

        .search-bar input {
            border: none;
            background: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .search-bar input::placeholder {
            color: #A6A6A6;
            font-weight: 400;
        }

        .top-bar-icons {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 1.3rem;
            color: #FFB648;
            transition: all 0.3s ease;
        }
        
        .notification-bell.messages-btn {
            color: #4880FF;
        }

        .notification-dot {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #FF4747;
            border-radius: 50%;
            display: none;
        }
        
        .notification-dot.active { display: block; }
        
        .badge-notification {
            position: absolute;
            top: -6px; right: -8px;
            background: #FF4747;
            color: white;
            border-radius: 50%;
            width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem;
            font-weight: 800;
            border: 2px solid #fff;
            display: none;
        }
        .badge-notification.active { display: flex; }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            text-decoration: none !important;
        }

        .admin-dropdown .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 10px;
            min-width: 200px;
        }

        .admin-dropdown .dropdown-item {
            border-radius: 8px;
            padding: 10px 15px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .admin-dropdown .dropdown-item:hover {
            background: var(--bg-body);
            color: var(--primary);
        }

        .admin-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
        }

        .admin-info { display: flex; flex-direction: column; }
        .admin-info h6 { margin: 0; font-size: 0.95rem; color: #404040; font-weight: 800; }
        .admin-info span { font-size: 0.8rem; color: #565656; font-weight: 600; }

        /* Cards Layout */
        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 25px;
        }
        
        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            color: var(--text-dark);
            margin-bottom: 30px;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body { padding: 25px; }

        /* Table Design */
        .table { margin-bottom: 0; color: var(--text-dark); }
        .table thead th {
            background: #F1F4F9;
            color: var(--text-dark);
            font-weight: 800;
            border-bottom: none;
            padding: 15px 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .table thead th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        .table thead th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        
        .table tbody tr { transition: all 0.2s; border-bottom: 1px solid var(--border-color); border-top: none; }
        .table tbody tr:last-child { border-bottom: none; }
        .table tbody tr:hover { background-color: #FAFAFA; }
        .table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
        }
        .table-responsive { overflow-x: auto; }

        /* Status Badges */
        .badge {
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
        }
        .badge-completed, .badge.bg-success { background: var(--success-bg) !important; color: var(--success-text) !important; border: none; }
        .badge-processing, .badge.bg-warning { background: var(--pending-bg) !important; color: var(--pending-text) !important; border: none; }
        .badge-rejected, .badge.bg-danger { background: var(--warning-bg) !important; color: var(--warning-text) !important; border: none; }
        .badge.bg-info { background: var(--info-bg) !important; color: var(--info-text) !important; border: none; }

        /* General Buttons */
        .btn { font-weight: 700; border-radius: 8px; padding: 8px 20px; transition: 0.3s; }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: #3566D4; box-shadow: 0 4px 10px rgba(72, 128, 255, 0.3); }

        /* Forms */
        .form-control, .form-select {
            background: #F5F6FA;
            border: 1px solid var(--border-color);
            padding: 12px 15px;
            border-radius: 8px;
            color: var(--text-dark);
            font-weight: 600;
        }
        .form-control:focus, .form-select:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.15);
        }
        .form-label { font-weight: 800; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 8px; }

        /* Modal */
        .modal-content { border-radius: 14px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 20px 25px; }
        .modal-title { font-weight: 800; font-size: 1.2rem; color: var(--text-dark); }

        /* Utils */
        .text-gray { color: var(--text-gray) !important; font-weight: 600; }
        .text-primary { color: var(--primary) !important; }
        .text-dark { color: var(--text-dark) !important; }

        .form-control::placeholder { color: #A6A6A6; font-weight: 400; }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); width: 250px; }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
            .top-bar-left .menu-toggle { display: block; }
        }
        @media (min-width: 992px) {
            .top-bar-left .menu-toggle { display: none; }
        }
    </style>
</head>
<body>

<?php $isStaff = (($_SESSION['role'] ?? '') === 'staff'); ?>

<div id="pageLoadingOverlay" aria-hidden="true">
    <div class="text-center">
        <div class="spinner-border" role="status" style="color: var(--primary); width: 3rem; height: 3rem;"></div>
        <div style="margin-top: 12px; color: var(--text-dark); font-weight: 800;">Đang tải...</div>
    </div>
</div>

<nav class="sidebar">
    <div class="sidebar-brand">
        <span style="color:var(--primary); font-size:1.8rem; margin-right:8px;"><i class="fas fa-cube"></i></span> DashStack
    </div>
    <div class="nav flex-column mt-3">
        <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">
            <i class="fas fa-layer-group"></i> Dashboard
        </a>
        
        <div class="nav-label">Products</div>
        <a href="products.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='products.php'?'active':'' ?>">
            <i class="fas fa-box"></i> Tất cả Game
        </a>
        <a href="categories_platforms.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='categories_platforms.php'?'active':'' ?>">
            <i class="fas fa-tags"></i> Danh mục & Platform
        </a>
        <a href="key_inventory.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='key_inventory.php'?'active':'' ?>">
            <i class="fas fa-key"></i> Kho Key
        </a>

        <div class="nav-label">Sales</div>
        <a href="orders.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='orders.php'?'active':'' ?>">
            <i class="fas fa-shopping-cart"></i> Đơn hàng
        </a>
        <a href="customers.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='customers.php'?'active':'' ?>">
            <i class="fas fa-users"></i> Khách hàng
        </a>
        <a href="coupons.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='coupons.php'?'active':'' ?>">
            <i class="fas fa-ticket-alt"></i> Mã giảm giá
        </a>
        
        <div class="nav-label">Payments</div>
        <a href="transactions.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='transactions.php'?'active':'' ?>">
            <i class="fas fa-receipt"></i> Lịch sử GD
        </a>
        <a href="deposits.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='deposits.php'?'active':'' ?>">
            <i class="fas fa-wallet"></i> Nạp tiền
            <?php
            if(isset($conn)) {
                try {
                    $pendingDep = $conn->query("SELECT COUNT(*) FROM wallet_transactions WHERE type='deposit' AND status='pending'")->fetchColumn();
                    if ($pendingDep > 0) echo '<span class="badge bg-danger ms-2 px-2 py-1" style="border-radius:4px;">' . $pendingDep . '</span>';
                } catch(Exception $e) {}
            }
            ?>
        </a>
        
        <?php if (!$isStaff): ?>
            <div class="nav-label">System</div>
            <a href="Payment_methods.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='Payment_methods.php'?'active':'' ?>">
                <i class="fas fa-credit-card"></i> Phương thức TT
            </a>
            <a href="Admins.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='Admins.php'?'active':'' ?>">
                <i class="fas fa-user-shield"></i> Admin/Staff
            </a>
            <a href="Email_logs.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='Email_logs.php'?'active':'' ?>">
                <i class="fas fa-envelope"></i> Lịch sử Email
            </a>
        <?php endif; ?>
    </div>
</nav>

<div class="main-content">
    <div class="top-bar">
        <div class="top-bar-left">
            <i class="fas fa-bars menu-toggle"></i>
            <div class="search-bar">
                <i class="fas fa-search" style="color:#A6A6A6;"></i>
                <input type="text" placeholder="Search">
            </div>
        </div>
        <div class="top-bar-icons">
            <div class="notification-bell" data-bs-toggle="modal" data-bs-target="#notificationModal">
                <i class="far fa-bell"></i>
                <?php if($total_notifications_header > 0): ?>
                    <span class="notification-dot active"></span>
                <?php endif; ?>
            </div>
            
            <div class="dropdown admin-dropdown">
                <?php $adminDisplayName = $_SESSION['admin_name'] ?? 'Administrator'; ?>
                <a class="admin-profile dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminDisplayName) ?>&background=E2E5FF&color=4880FF&bold=true" class="admin-avatar" alt="Admin">
                    <div class="admin-info">
                        <h6><?= $adminDisplayName ?></h6>
                        <span><?= $isStaff ? 'Staff' : 'Admin' ?></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2 text-gray"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (!empty($_SESSION['access_denied_message'])): ?>
        <div class="alert alert-danger" style="margin: 0 0 25px 0; border-radius: 12px; font-weight: 700; background: var(--warning-bg); color: var(--warning-text); border: none;">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_SESSION['access_denied_message']) ?>
        </div>
        <?php unset($_SESSION['access_denied_message']); ?>
    <?php endif; ?>

    <div class="modal fade" id="notificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="fas fa-bell text-primary"></i> Notifications
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if($orders_pending_header > 0): ?>
                        <div class="alert alert-warning d-flex align-items-center mb-3 border-0" style="background:#FFF4DE; color:#FF947A;">
                            <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong><?= $orders_pending_header ?></strong> pending orders need review
                            </div>
                            <a href="orders.php?status=pending" class="btn btn-sm btn-light" style="color:#FF947A;"><i class="fas fa-arrow-right"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if($out_of_stock_header > 0): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-3 border-0" style="background:#FFE2E5; color:#F93C65;">
                            <i class="fas fa-times-circle me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong><?= $out_of_stock_header ?></strong> products are out of stock
                            </div>
                            <a href="products.php?out_of_stock=1" class="btn btn-sm btn-light" style="color:#F93C65;"><i class="fas fa-arrow-right"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if($low_stock_header > 0): ?>
                        <div class="alert alert-info d-flex align-items-center mb-0 border-0" style="background:#E2E5FF; color:#6226EF;">
                            <i class="fas fa-battery-empty me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong><?= $low_stock_header ?></strong> products are low on stock
                            </div>
                            <a href="products.php?low_stock=1" class="btn btn-sm btn-light" style="color:#6226EF;"><i class="fas fa-arrow-right"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if($orders_pending_header == 0 && $low_stock_header == 0 && $out_of_stock_header == 0): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x mb-3" style="color: #00B69B;"></i>
                            <h6 style="color: var(--text-dark); font-weight:800;">You're all caught up!</h6>
                            <p class="text-gray mb-0">No new notifications at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>