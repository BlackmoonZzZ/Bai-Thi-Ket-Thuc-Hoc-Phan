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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { 
            --primary: #0f172a; 
            --secondary: #1e293b; 
            --accent: #06b6d4;
            --accent-pink: #ec4899;
            --text-light: #e2e8f0;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
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
            background: rgba(15, 23, 42, 0.65);
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
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff; 
            z-index: 1000; 
            transition: 0.3s;
            border-right: 1px solid rgba(6, 182, 212, 0.2);
            overflow-y: auto;
        }
        
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(6, 182, 212, 0.5); border-radius: 3px; }
        
        .sidebar-brand { 
            height: 80px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.4rem; 
            font-weight: 900;
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            letter-spacing: 2px;
            color: #06b6d4;
            text-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
        }
        
        .sidebar .nav-flex-column { margin-top: 20px; }
        
        .sidebar .nav-link { 
            color: rgba(226, 232, 240, 0.7);
            padding: 14px 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
            margin: 5px 10px;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        
        .sidebar .nav-link i { 
            width: 24px;
            text-align: center;
            margin-right: 12px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover { 
            color: #06b6d4;
            background: rgba(6, 182, 212, 0.1);
            padding-left: 24px;
        }
        
        .sidebar .nav-link.active { 
            color: #06b6d4;
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.2) 0%, rgba(6, 182, 212, 0.05) 100%);
            border-left: 3px solid #06b6d4;
            padding-left: 17px;
        }
        
        .sidebar .nav-label { 
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: rgba(6, 182, 212, 0.6);
            padding: 15px 20px 10px;
            margin-top: 10px;
            font-weight: 700;
        }
        
        .sidebar .nav-link.logout { 
            color: #f59e0b;
            margin-top: auto;
            margin-bottom: 20px;
        }
        
        /* Main Content */
        .main-content { 
            margin-left: 260px; 
            padding: 30px;
            transition: 0.3s;
            min-height: 100vh;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);
            padding: 20px 30px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }
        
        .top-bar h2 { 
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #06b6d4 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        
        .top-bar-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 1.4rem;
            color: #06b6d4;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .notification-bell:hover { 
            color: #ec4899;
            background: rgba(6, 182, 212, 0.1);
            transform: scale(1.05);
        }
        
        .notification-bell i {
            display: block;
        }
        
        /* Red dot - chỉ hiện khi có thông báo */
        .notification-dot {
            position: absolute;
            top: 4px;
            right: 8px;
            width: 12px;
            height: 12px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #0f172a;
            animation: pulse 2s infinite;
            display: none; /* Ẩn mặc định */
        }
        
        .notification-dot.active {
            display: block; /* Chỉ hiện khi có class active */
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }
        
        /* Badge số thông báo */
        .badge-notification {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 50%;
            min-width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            border: 2px solid #0f172a;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            display: none; /* Ẩn mặc định */
        }
        
        .badge-notification.active {
            display: flex; /* Chỉ hiện khi có class active */
        }

        #notificationModal .modal-content {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
            border: 1px solid rgba(6, 182, 212, 0.25);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
        }

        #notificationModal .modal-header {
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
            padding: 14px 16px;
        }

        #notificationModal .modal-title {
            color: #06b6d4;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        #notificationModal .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        #notificationModal .btn-close:hover {
            opacity: 1;
        }

        #notificationModal .modal-body {
            padding: 16px;
        }

        #notificationModal .alert {
            padding: 12px 14px;
            margin-bottom: 12px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
        }

        #notificationModal .alert-warning {
            border-color: rgba(245, 158, 11, 0.35);
            background: rgba(245, 158, 11, 0.12);
        }

        #notificationModal .alert-danger {
            border-color: rgba(239, 68, 68, 0.35);
            background: rgba(239, 68, 68, 0.12);
        }

        #notificationModal .alert i {
            width: 18px;
            text-align: center;
        }

        #notificationModal .btn.btn-sm {
            border-radius: 8px;
            font-weight: 700;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .admin-profile:hover {
            background: rgba(6, 182, 212, 0.1);
        }

        .admin-dropdown .dropdown-menu {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
            border: 1px solid rgba(6, 182, 212, 0.25);
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
            padding: 8px;
            min-width: 220px;
        }

        .admin-dropdown .dropdown-item {
            color: #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            font-weight: 600;
        }

        .admin-dropdown .dropdown-item:hover {
            background: rgba(6, 182, 212, 0.12);
            color: #06b6d4;
        }

        .admin-dropdown .dropdown-divider {
            border-top: 1px solid rgba(6, 182, 212, 0.15);
            margin: 8px 0;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #06b6d4;
            object-fit: cover;
        }
        
        .admin-info h6 {
            margin: 0;
            font-size: 0.9rem;
            color: #e2e8f0;
            font-weight: 600;
        }
        
        .admin-info span {
            font-size: 0.8rem;
            color: #06b6d4;
        }
        
        /* Cards */
        .stat-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.5) 0%, rgba(30, 41, 59, 0.5) 100%);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            padding: 25px;
            color: #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .stat-card:hover {
            border-color: #06b6d4;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.05) 0%, rgba(236, 72, 153, 0.05) 100%);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.2);
        }
        
        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2) 0%, rgba(236, 72, 153, 0.2) 100%);
        }
        
        .stat-card-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #06b6d4 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-card-label {
            font-size: 0.9rem;
            color: rgba(226, 232, 240, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .stat-card-change {
            display: inline-block;
            margin-top: 10px;
            font-size: 0.85rem;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .stat-card-change.up {
            color: #fbbf24;
            background: rgba(251, 191, 36, 0.1);
        }
        
        .stat-card-change.down {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
        }
        
        /* Card */
        .card {
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(30, 41, 59, 0.7) 100%);
            color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .card-header {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
            padding: 20px;
            font-weight: 700;
            color: #06b6d4;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .card-body {
            padding: 20px;
            background: transparent !important;
            color: #e2e8f0 !important;
        }
        
        /* Table */
        .table {
            color: #e2e8f0;
            border-color: rgba(6, 182, 212, 0.1);
            background: transparent;
        }
        
        .table thead th {
            border-bottom: 2px solid rgba(6, 182, 212, 0.2);
            color: #06b6d4;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            background: transparent;
            padding: 15px;
        }
        
        .table tbody tr {
            border-bottom: 1px solid rgba(6, 182, 212, 0.1);
            transition: all 0.3s ease;
            background: transparent;
        }
        
        .table tbody tr:hover {
            background: rgba(6, 182, 212, 0.1);
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            background: transparent;
            color: #e2e8f0;
        }
        
        .table-light {
            background: transparent !important;
        }
        
        .table-light th {
            background: transparent !important;
        }
        
        .table-bordered {
            border-color: rgba(6, 182, 212, 0.1) !important;
        }
        
        .table-bordered td, .table-bordered th {
            border-color: rgba(6, 182, 212, 0.1) !important;
            background: transparent !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(6, 182, 212, 0.1) !important;
        }
        
        /* Alert */
        .alert {
            background: transparent !important;
            border: 1px solid rgba(6, 182, 212, 0.2) !important;
            color: #e2e8f0 !important;
        }
        
        .alert-info {
            background: rgba(6, 182, 212, 0.1) !important;
            border-color: rgba(6, 182, 212, 0.3) !important;
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.3) !important;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(6, 182, 212, 0.4);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            font-weight: 600;
            color: white;
        }
        
        .btn-success:hover {
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            font-weight: 600;
            color: white;
        }
        
        .btn-danger:hover {
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            font-weight: 600;
            color: white;
        }
        
        .btn-warning:hover {
            color: white;
        }
        
        .btn-secondary {
            background: rgba(107, 114, 128, 0.5);
            border: 1px solid rgba(6, 182, 212, 0.2);
            color: #e2e8f0;
            font-weight: 600;
        }
        
        .btn-secondary:hover {
            background: rgba(107, 114, 128, 0.7);
            color: #06b6d4;
        }
        
        .btn-outline-secondary {
            border: 1px solid rgba(107, 114, 128, 0.5);
            color: #e2e8f0;
        }
        
        .btn-outline-secondary:hover {
            background: rgba(107, 114, 128, 0.2);
            color: #06b6d4;
        }
        
        .btn-outline-info {
            border: 1px solid #06b6d4;
            color: #06b6d4;
        }
        
        .btn-outline-info:hover {
            background: #06b6d4;
            color: white;
        }
        
        .btn-info {
            background: #06b6d4;
            color: white;
            border: none;
        }
        
        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        .badge.bg-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
            color: white !important;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: white !important;
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
        }
        
        .badge.bg-secondary {
            background: rgba(107, 114, 128, 0.6) !important;
            color: #e2e8f0 !important;
        }
        
        .badge.bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
        }
        
        /* Modal */
        .modal-content {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
            border: 1px solid rgba(6, 182, 212, 0.2);
            color: #e2e8f0;
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);
        }
        
        .modal-title {
            font-weight: 700;
            color: #06b6d4;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        /* Form */
        .form-control, .form-select {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(6, 182, 212, 0.2);
            color: #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .form-control::placeholder {
            color: rgba(226, 232, 240, 0.4);
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(30, 41, 59, 0.8);
            border-color: #06b6d4;
            box-shadow: 0 0 0 0.2rem rgba(6, 182, 212, 0.25);
            color: #e2e8f0;
        }
        
        .form-label {
            color: #e2e8f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .dropdown-menu {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
            border: 1px solid rgba(6, 182, 212, 0.2);
        }
        
        .dropdown-item {
            color: #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
        }
        
        .dropdown-divider {
            border-color: rgba(6, 182, 212, 0.1);
        }
        
        /* Text Utils */
        .text-gray-800 {
            color: #e2e8f0 !important;
        }
        
        .text-danger {
            color: #ec4899 !important;
        }
        
        .text-white-50 {
            color: rgba(226, 232, 240, 0.5) !important;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(6, 182, 212, 0.5); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(6, 182, 212, 0.8); }
        
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; }
            .main-content { margin-left: 0; padding: 15px; }
            .top-bar { flex-direction: column; gap: 15px; }
        }
    </style>
</head>
<body>

<?php $isStaff = (($_SESSION['role'] ?? '') === 'staff'); ?>

<div id="pageLoadingOverlay" aria-hidden="true">
    <div class="text-center">
        <div class="spinner-border" role="status" style="color: #06b6d4; width: 3rem; height: 3rem;"></div>
        <div style="margin-top: 12px; color: #e2e8f0; font-weight: 600; letter-spacing: 0.5px;">Đang tải...</div>
    </div>
</div>

<nav class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-gamepad"></i> GAMEKEY
    </div>
    <div class="nav flex-column">
        <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <div class="nav-label">Sản phẩm</div>
        <a href="products.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='products.php'?'active':'' ?>">
            <i class="fas fa-box"></i> Tất cả Game
        </a>
        <a href="categories_platforms.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='categories_platforms.php'?'active':'' ?>">
            <i class="fas fa-tags"></i> Danh mục & Platform
        </a>
        <a href="key_inventory.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='key_inventory.php'?'active':'' ?>">
            <i class="fas fa-warehouse"></i> Kho Key
        </a>

        <div class="nav-label">Kinh doanh</div>
        <a href="orders.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='orders.php'?'active':'' ?>">
            <i class="fas fa-shopping-cart"></i> Đơn hàng
        </a>
        <a href="customers.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='customers.php'?'active':'' ?>">
            <i class="fas fa-users"></i> Khách hàng
        </a>
        <a href="coupons.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='coupons.php'?'active':'' ?>">
            <i class="fas fa-ticket-alt"></i> Mã giảm giá
        </a>
      <div class="nav-label">Thanh toán</div>
        <a href="transactions.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='transactions.php'?'active':'' ?>">
            <i class="fas fa-receipt"></i> Lịch sử GD
        </a>
        <?php if (!$isStaff): ?>
            <a href="Payment_methods.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='Payment_methods.php'?'active':'' ?>">
                <i class="fas fa-credit-card"></i> Phương thức TT
            </a>

            <div class="nav-label">Hệ thống</div>
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
        <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
        <div class="top-bar-icons">
            <div class="notification-bell" data-bs-toggle="modal" data-bs-target="#notificationModal">
                <i class="fas fa-bell"></i>
                <?php if($total_notifications_header > 0): ?>
                    <span class="notification-dot active"></span>
                    <span class="badge-notification active"><?= $total_notifications_header ?></span>
                <?php endif; ?>
            </div>
            <div class="dropdown admin-dropdown">
                <?php $adminDisplayName = $_SESSION['admin_name'] ?? 'Administrator'; ?>
                <a class="admin-profile dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminDisplayName) ?>&background=06b6d4&color=fff&bold=true" class="admin-avatar" alt="Admin">
                    <div class="admin-info">
                        <h6><?= $adminDisplayName ?></h6>
                        <span><?= (($_SESSION['role'] ?? '') === 'staff') ? 'Staff' : 'Admin' ?></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (!empty($_SESSION['access_denied_message'])): ?>
        <div class="alert alert-danger" style="margin: 0 0 15px 0;">
            <?= htmlspecialchars($_SESSION['access_denied_message']) ?>
        </div>
        <?php unset($_SESSION['access_denied_message']); ?>
    <?php endif; ?>

    <div class="modal fade" id="notificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="fas fa-bell"></i> Thông báo
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if($orders_pending_header > 0): ?>
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div class="flex-grow-1">
                                <strong><?= $orders_pending_header ?></strong> đơn hàng chờ xử lý
                            </div>
                            <a href="orders.php?status=pending" class="btn btn-sm btn-warning ms-2">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($out_of_stock_header > 0): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-times-circle me-2"></i>
                            <div class="flex-grow-1">
                                <strong><?= $out_of_stock_header ?></strong> sản phẩm đã hết Key
                            </div>
                            <a href="products.php?out_of_stock=1" class="btn btn-sm btn-danger ms-2">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($low_stock_header > 0): ?>
                        <div class="alert alert-warning d-flex align-items-center" style="background: rgba(245, 158, 11, 0.15) !important;">
                            <i class="fas fa-battery-empty me-2"></i>
                            <div class="flex-grow-1">
                                <strong><?= $low_stock_header ?></strong> sản phẩm sắp hết Key
                            </div>
                            <a href="products.php?low_stock=1" class="btn btn-sm btn-warning ms-2">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($orders_pending_header == 0 && $low_stock_header == 0 && $out_of_stock_header == 0): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x mb-2" style="color: #10b981;"></i>
                            <p style="color: rgba(226, 232, 240, 0.8); font-size: 0.9rem; margin: 0;">Hệ thống hoạt động bình thường</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>