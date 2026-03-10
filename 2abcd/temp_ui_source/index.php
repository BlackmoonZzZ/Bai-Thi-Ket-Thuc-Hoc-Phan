<?php
require_once __DIR__ . '/config/db.php';

// ===== NOTIFICATION DATA =====
$orders_pending = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$low_stock = $conn->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 10 AND stock_quantity > 0")->fetchColumn();
$out_of_stock = $conn->query("SELECT COUNT(*) FROM products WHERE stock_quantity = 0")->fetchColumn();
$total_notifications = $orders_pending + $low_stock + $out_of_stock;

include __DIR__ . '/includes/header.php'; 

// Lấy bộ lọc thời gian
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'month';
$date_start = '';
$dates_chart = [];
$labels_chart = [];

switch($filter) {
    case 'week':
        $date_start = date('Y-m-d', strtotime('-7 days'));
        for($i=6; $i>=0; $i--){
            $d = date('Y-m-d', strtotime("-$i days"));
            $dates_chart[] = $d;
            $labels_chart[] = date('d/m', strtotime($d));
        }
        break;
    case 'year':
        $date_start = date('Y-m-d', strtotime('-12 months'));
        for($i=11; $i>=0; $i--){
            $d = date('Y-m-01', strtotime("-$i months"));
            $dates_chart[] = $d;
            $labels_chart[] = date('m/Y', strtotime($d));
        }
        break;
    case 'since2024':
        $date_start = '2024-01-01';
        $start = new DateTime('2024-01-01');
        $end = new DateTime();
        $interval = new DateInterval('P1M');
        $period = new DatePeriod($start, $interval, $end);
        
        foreach ($period as $dt) {
            $d = $dt->format('Y-m-01');
            $dates_chart[] = $d;
            $labels_chart[] = $dt->format('m/Y');
        }
        $current_month = date('Y-m-01');
        if(!in_array($current_month, $dates_chart)) {
            $dates_chart[] = $current_month;
            $labels_chart[] = date('m/Y');
        }
        break;
    case 'month':
    default:
        $date_start = date('Y-m-d', strtotime('-30 days'));
        for($i=29; $i>=0; $i--){
            $d = date('Y-m-d', strtotime("-$i days"));
            $dates_chart[] = $d;
            $labels_chart[] = date('d/m', strtotime($d));
        }
        break;
}

// ===== THỐNG KÊ NHANH =====
$today = date('Y-m-d');

$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = ? AND status = 'completed'");
$stmt->execute([$today]);
$revenue_today = $stmt->fetchColumn();

$new_customers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();

$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) >= ? AND status = 'completed'");
$stmt->execute([$date_start]);
$total_revenue = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) >= ?");
$stmt->execute([$date_start]);
$total_orders = $stmt->fetchColumn();

// ===== DỮ LIỆU BIỂU ĐỒ =====
$revenue_data = [];
if($filter == 'year' || $filter == 'since2024') {
    foreach($dates_chart as $date) {
        $month_start = date('Y-m-01', strtotime($date));
        $month_end = date('Y-m-t', strtotime($date));
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'");
        $stmt->execute([$month_start, $month_end]);
        $revenue_data[] = $stmt->fetchColumn();
    }
} else {
    foreach($dates_chart as $date) {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = ? AND status = 'completed'");
        $stmt->execute([$date]);
        $revenue_data[] = $stmt->fetchColumn();
    }
}

// ===== TOP 5 GAME =====
$stmt = $conn->prepare("SELECT p.name, p.image, COUNT(oi.id) as sold_count, SUM(oi.price) as total_revenue
                  FROM products p
                  JOIN order_items oi ON p.id = oi.product_id
                  JOIN orders o ON oi.order_id = o.id
                  WHERE DATE(o.created_at) >= ? AND o.status = 'completed'
                  GROUP BY p.id, p.name, p.image
                  ORDER BY sold_count DESC
                  LIMIT 5");
$stmt->execute([$date_start]);
$top_games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== TOP 5 KHÁCH HÀNG =====
$stmt = $conn->prepare("SELECT u.username, u.fullname, u.email, COUNT(o.id) as order_count, SUM(o.total_amount) as total_spent
                      FROM users u
                      JOIN orders o ON u.id = o.user_id
                      WHERE DATE(o.created_at) >= ? AND o.status = 'completed'
                      GROUP BY u.id, u.username, u.fullname, u.email
                      ORDER BY total_spent DESC
                      LIMIT 5");
$stmt->execute([$date_start]);
$top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== HOẠT ĐỘNG GẦN ĐÂY =====
$recent_orders = $conn->query("SELECT o.order_number, o.total_amount, o.status, o.created_at, u.username, u.fullname
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      ORDER BY o.created_at DESC
                      LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// ===== BIỂU ĐỒ TRÒN =====
$stmt = $conn->prepare("SELECT c.name, COALESCE(SUM(oi.price), 0) as revenue
                         FROM categories c
                         LEFT JOIN products p ON c.id = p.category_id
                         LEFT JOIN order_items oi ON p.id = oi.product_id
                         LEFT JOIN orders o ON oi.order_id = o.id
                         WHERE (o.id IS NULL OR (DATE(o.created_at) >= ? AND o.status = 'completed'))
                         GROUP BY c.id, c.name
                         HAVING revenue > 0
                         ORDER BY revenue DESC
                         LIMIT 6");
$stmt->execute([$date_start]);
$category_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

$category_labels = array_column($category_revenue, 'name');
$category_data = array_column($category_revenue, 'revenue');

// ===== BIỂU ĐỒ CỘT =====
$stmt = $conn->prepare("SELECT 
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                        SUM(CASE WHEN status = 'refunded' THEN 1 ELSE 0 END) as refunded
                     FROM orders
                     WHERE DATE(created_at) >= ?");
$stmt->execute([$date_start]);
$order_status = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<style>
/* Compact Design Overrides */
.compact-stat-card {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.6) 100%);
    border: 1px solid rgba(6, 182, 212, 0.15);
    border-radius: 10px;
    padding: 16px;
    transition: all 0.3s ease;
}

.compact-stat-card:hover {
    border-color: #06b6d4;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(6, 182, 212, 0.15);
}

.compact-stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin-bottom: 10px;
}

.compact-stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 4px;
    background: linear-gradient(135deg, #06b6d4 0%, #ec4899 100%);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.compact-stat-label {
    font-size: 0.75rem;
    color: rgba(226, 232, 240, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.compact-card {
    border: 1px solid rgba(6, 182, 212, 0.15);
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.8) 100%);
    margin-bottom: 20px;
}

.compact-card-header {
    background: rgba(6, 182, 212, 0.05);
    border-bottom: 1px solid rgba(6, 182, 212, 0.15);
    padding: 12px 16px;
    font-weight: 600;
    color: #06b6d4;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.compact-card-body {
    padding: 16px;
}

.filter-compact {
    background: rgba(15, 23, 42, 0.5);
    border:  1px solid rgba(6, 182, 212, 0.15);
    border-radius: 8px;
    padding: 10px 16px;
    margin-bottom: 20px;
}

.btn-filter-compact {
    padding: 6px 14px;
    font-size: 0.8rem;
    border-radius: 6px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.top-summary {
    background: rgba(6, 182, 212, 0.08);
    border: 1px solid rgba(6, 182, 212, 0.2);
    border-radius: 8px;
    padding: 14px;
    text-align: center;
}

.top-summary-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #10b981;
}

.top-summary-label {
    font-size: 0.75rem;
    color: #06b6d4;
    margin-bottom: 6px;
    font-weight: 600;
    text-transform: uppercase;
}

.list-item-compact {
    padding: 10px 0;
    border-bottom: 1px solid rgba(6, 182, 212, 0.08);
}

.list-item-compact:last-child {
    border-bottom: none;
}

.rank-badge {
    font-size: 1rem;
    font-weight: 700;
    min-width: 24px;
    text-align: center;
}

.game-img-compact {
    width: 50px;
    height: 35px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.table-compact {
    font-size: 0.85rem;
}

.table-compact th {
    padding: 10px;
    font-size: 0.75rem;
}

.table-compact td {
    padding: 10px;
}

.chart-container-compact {
    height: 250px;
    position: relative;
}

.notification-modal-compact .alert {
    padding: 10px 12px;
    margin-bottom: 10px;
    border-radius: 6px;
}
</style>

<!-- Bộ lọc -->
<div class="filter-compact d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div style="color: #06b6d4; font-size: 0.85rem; font-weight: 600;">
        <i class="fas fa-filter"></i> Thống kê
    </div>
    <div class="btn-group btn-group-sm">
        <a href="?filter=week" class="btn btn-filter-compact <?= $filter == 'week' ? 'btn-info' : 'btn-outline-info' ?>">
            7 Ngày
        </a>
        <a href="?filter=month" class="btn btn-filter-compact <?= $filter == 'month' ? 'btn-info' : 'btn-outline-info' ?>">
            30 Ngày
        </a>
        <a href="?filter=year" class="btn btn-filter-compact <?= $filter == 'year' ? 'btn-info' : 'btn-outline-info' ?>">
            12 Tháng
        </a>
        <a href="?filter=since2024" class="btn btn-filter-compact <?= $filter == 'since2024' ? 'btn-info' : 'btn-outline-info' ?>">
            Tất cả
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="compact-stat-card">
            <div class="compact-stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="compact-stat-value"><?= number_format($revenue_today) ?>đ</div>
            <div class="compact-stat-label">Doanh thu hôm nay</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#notificationModal">
            <div class="compact-stat-card" style="cursor: pointer;">
                <div class="compact-stat-icon" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="compact-stat-value"><?= $orders_pending ?></div>
                <div class="compact-stat-label">Đơn chờ xử lý</div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#notificationModal">
            <div class="compact-stat-card" style="cursor: pointer;">
                <div class="compact-stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="compact-stat-value"><?= $out_of_stock ?></div>
                <div class="compact-stat-label">Sản phẩm hết Key</div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#notificationModal">
            <div class="compact-stat-card" style="cursor: pointer;">
                <div class="compact-stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="compact-stat-value"><?= $low_stock ?></div>
                <div class="compact-stat-label">Sản phẩm sắp hết</div>
            </div>
        </a>
    </div>
</div>

<!-- Summary Row -->
<div class="row mb-3">
    <div class="col-md-6 mb-3">
        <div class="top-summary">
            <div class="top-summary-label">
                <i class="fas fa-chart-line"></i> Tổng doanh thu 
                <?php 
                if($filter == 'week') echo '(7 ngày)'; 
                elseif($filter == 'month') echo '(30 ngày)';
                elseif($filter == 'since2024') echo '(Từ 2024)';
                else echo '(12 tháng)'; 
                ?>
            </div>
            <div class="top-summary-value"><?= number_format($total_revenue) ?>đ</div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="top-summary">
            <div class="top-summary-label" style="color: #06b6d4;">
                <i class="fas fa-receipt"></i> Tổng đơn hàng
                <?php 
                if($filter == 'week') echo '(7 ngày)'; 
                elseif($filter == 'month') echo '(30 ngày)';
                elseif($filter == 'since2024') echo '(Từ 2024)';
                else echo '(12 tháng)'; 
                ?>
            </div>
            <div class="top-summary-value" style="color: #06b6d4;"><?= number_format($total_orders) ?></div>
        </div>
    </div>
</div>

<!-- Biểu đồ doanh thu -->
<div class="compact-card">
    <div class="compact-card-header">
        <i class="fas fa-chart-area"></i> Biểu đồ doanh thu
    </div>
    <div class="compact-card-body">
        <div class="chart-container-compact">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Row Charts -->
<div class="row">
    <div class="col-lg-6 mb-3">
        <div class="compact-card">
            <div class="compact-card-header">
                <i class="fas fa-chart-pie"></i> Doanh thu theo danh mục
            </div>
            <div class="compact-card-body">
                <div class="chart-container-compact">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-3">
        <div class="compact-card">
            <div class="compact-card-header">
                <i class="fas fa-chart-bar"></i> Đơn hàng theo trạng thái
            </div>
            <div class="compact-card-body">
                <div class="chart-container-compact">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Stats Row -->
<div class="row">
    <div class="col-lg-6 mb-3">
        <div class="compact-card">
            <div class="compact-card-header">
                <i class="fas fa-trophy"></i> Top 5 Game bán chạy
            </div>
            <div class="compact-card-body" style="max-height: 320px; overflow-y: auto;">
                <?php if(empty($top_games)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p style="font-size: 0.85rem;">Chưa có dữ liệu</p>
                    </div>
                <?php else: ?>
                    <?php foreach($top_games as $index => $game): ?>
                        <div class="list-item-compact d-flex align-items-center">
                            <div class="rank-badge me-2" style="color: #06b6d4;">
                                #<?= $index + 1 ?>
                            </div>
                            <?php if($game['image']): ?>
                                <img src="<?= htmlspecialchars($game['image']) ?>" alt="" class="game-img-compact">
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <div style="color: #e2e8f0; font-weight: 600; font-size: 0.85rem; margin-bottom: 2px;">
                                    <?= htmlspecialchars($game['name']) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: rgba(226, 232, 240, 0.6);">
                                    <i class="fas fa-shopping-bag"></i> <?= $game['sold_count'] ?> • 
                                    <i class="fas fa-dollar-sign"></i> <?= number_format($game['total_revenue']) ?>đ
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-3">
        <div class="compact-card">
            <div class="compact-card-header">
                <i class="fas fa-crown"></i> Top 5 Khách hàng VIP
            </div>
            <div class="compact-card-body" style="max-height: 320px; overflow-y: auto;">
                <?php if(empty($top_customers)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p style="font-size: 0.85rem;">Chưa có dữ liệu</p>
                    </div>
                <?php else: ?>
                    <?php foreach($top_customers as $index => $customer): ?>
                        <div class="list-item-compact d-flex align-items-center">
                            <div class="rank-badge me-2" style="color: #ec4899;">
                                #<?= $index + 1 ?>
                            </div>
                            <div class="flex-grow-1">
                                <div style="color: #e2e8f0; font-weight: 600; font-size: 0.85rem; margin-bottom: 2px;">
                                    <?= htmlspecialchars($customer['fullname'] ?: $customer['username']) ?>
                                </div>
                                <div style="font-size: 0.72rem; color: rgba(226, 232, 240, 0.6);">
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($customer['email']) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: rgba(226, 232, 240, 0.6); margin-top: 2px;">
                                    <i class="fas fa-receipt"></i> <?= $customer['order_count'] ?> đơn • 
                                    <i class="fas fa-dollar-sign"></i> <?= number_format($customer['total_spent']) ?>đ
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="compact-card">
    <div class="compact-card-header">
        <i class="fas fa-history"></i> Hoạt động gần đây
    </div>
    <div class="compact-card-body">
        <div class="table-responsive">
            <table class="table table-hover table-compact">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($recent_orders)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p style="font-size: 0.85rem;">Chưa có hoạt động</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($recent_orders as $order): ?>
                            <tr>
                                <td>
                                    <span style="color: #06b6d4; font-weight: 600;">
                                        <?= htmlspecialchars($order['order_number']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($order['fullname'] ?: $order['username']) ?></td>
                                <td style="color: #10b981; font-weight: 600;">
                                    <?= number_format($order['total_amount']) ?>đ
                                </td>
                                <td>
                                    <?php
                                    $status_colors = [
                                        'pending' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'info'
                                    ];
                                    $status_text = [
                                        'pending' => 'Chờ xử lý',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                        'refunded' => 'Hoàn tiền'
                                    ];
                                    $color = $status_colors[$order['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= $status_text[$order['status']] ?? $order['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <small style="color: rgba(226, 232, 240, 0.6);">
                                        <?= date('d/m H:i', strtotime($order['created_at'])) ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#e2e8f0';
Chart.defaults.borderColor = 'rgba(6, 182, 212, 0.1)';

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_chart) ?>,
        datasets: [{
            label: 'Doanh thu',
            data: <?= json_encode($revenue_data) ?>,
            borderColor: '#06b6d4',
            backgroundColor: 'rgba(6, 182, 212, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#06b6d4',
            pointHoverRadius: 5
        }]
    },
    options: { 
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 10,
                displayColors: false,
                callbacks: {
                    label: (context) => new Intl.NumberFormat('vi-VN').format(context.parsed.y) + 'đ'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    color: '#e2e8f0',
                    font: { size: 10 },
                    callback: (value) => new Intl.NumberFormat('vi-VN', { 
                        notation: 'compact'
                    }).format(value) + 'đ'
                },
                grid: { color: 'rgba(6, 182, 212, 0.1)' }
            },
            x: {
                ticks: { color: '#e2e8f0', font: { size: 10 } },
                grid: { display: false }
            }
        }
    }
});

// Category Chart
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($category_labels) ?>,
        datasets: [{
            data: <?= json_encode($category_data) ?>,
            backgroundColor: [
                'rgba(6, 182, 212, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
                labels: { 
                    color: '#e2e8f0',
                    padding: 10,
                    font: { size: 11 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 10,
                callbacks: {
                    label: (context) => {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.parsed) + 'đ (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Order Status Chart
new Chart(document.getElementById('orderStatusChart'), {
    type: 'bar',
    data: {
        labels: ['Chờ xử lý', 'Hoàn thành', 'Đã hủy', 'Hoàn tiền'],
        datasets: [{
            data: [
                <?= $order_status['pending'] ?>,
                <?= $order_status['completed'] ?>,
                <?= $order_status['cancelled'] ?>,
                <?= $order_status['refunded'] ?>
            ],
            backgroundColor: [
                'rgba(245, 158, 11, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(6, 182, 212, 0.8)'
            ],
            borderRadius: 6
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 10,
                callbacks: {
                    label: (context) => 'Số đơn: ' + context.parsed.y
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    color: '#e2e8f0',
                    font: { size: 10 },
                    stepSize: 1
                },
                grid: { color: 'rgba(6, 182, 212, 0.1)' }
            },
            x: {
                ticks: { color: '#e2e8f0', font: { size: 10 } },
                grid: { display: false }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
