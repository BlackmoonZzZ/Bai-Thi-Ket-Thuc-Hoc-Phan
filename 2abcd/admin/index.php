<?php
require_once __DIR__ . '/../config/db.php';

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
/* Dashboard Specific CSS */
.dashboard-card {
    background: #FFFFFF;
    border: none;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    height: 100%;
}
.dash-info {
    display: flex;
    flex-direction: column;
}
.dash-title {
    font-size: 0.9rem;
    color: var(--text-dark);
    font-weight: 700;
    margin-bottom: 12px;
}
.dash-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 12px;
}
.dash-subtitle {
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dash-subtitle.up { color: #00B69B; }
.dash-subtitle.down { color: #F93C65; }

.dash-icon-wrapper {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.icon-purple { background: rgba(130, 128, 255, 0.15); color: #8280FF; }
.icon-yellow { background: rgba(254, 197, 61, 0.15); color: #FEC53D; }
.icon-green { background: rgba(74, 217, 145, 0.15); color: #4AD991; }
.icon-red { background: rgba(255, 144, 102, 0.15); color: #FF9066; }

/* Filter Section */
.page-header {
    margin-bottom: 25px;
}
.page-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
}

/* Charts Card */
.chart-card {
    background: #FFFFFF;
    border-radius: 14px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.chart-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
}
.chart-filter {
    background: #F5F6FA;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 6px 15px;
    font-weight: 700;
    color: var(--text-dark);
    font-size: 0.85rem;
    cursor: pointer;
}
.chart-container {
    height: 350px;
}

/* Deals Details Table */
.deals-table-container {
    background: #FFFFFF;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.deals-table-container .table {
    margin-bottom: 0;
}
.deals-table-container .table th {
    background: #F5F6FA;
    padding: 15px 20px;
    font-weight: 700;
    color: var(--text-dark);
    font-size: 0.85rem;
    border: none;
    text-transform: capitalize;
}
.deals-table-container .table tr {
    border-bottom: 1px solid var(--border-color);
}
.deals-table-container .table tr:last-child {
    border-bottom: none;
}
.deals-table-container .table td {
    padding: 15px 20px;
    border: none;
    vertical-align: middle;
    font-weight: 600;
    font-size: 0.9rem;
    color: #404040;
}
.badge {
    border: none;
}
</style>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2 class="page-title">Dashboard</h2>
    <div class="btn-group">
        <a href="?filter=week" class="btn <?= $filter == 'week' ? 'btn-primary' : 'btn-light' ?> border" style="font-weight:700;">7 Ngày</a>
        <a href="?filter=month" class="btn <?= $filter == 'month' ? 'btn-primary' : 'btn-light' ?> border" style="font-weight:700;">30 Ngày</a>
        <a href="?filter=year" class="btn <?= $filter == 'year' ? 'btn-primary' : 'btn-light' ?> border" style="font-weight:700;">1 Năm</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Card 1 -->
    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-card">
            <div class="dash-info">
                <div class="dash-title">Total User</div>
                <div class="dash-value"><?= number_format($new_customers) ?></div>
                <div class="dash-subtitle up"><i class="fas fa-chart-line"></i> 8.5% Up from yesterday</div>
            </div>
            <div class="dash-icon-wrapper icon-purple">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <!-- Card 2 -->
    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-card">
            <div class="dash-info">
                <div class="dash-title">Total Order</div>
                <div class="dash-value"><?= number_format($total_orders) ?></div>
                <div class="dash-subtitle up"><i class="fas fa-chart-line"></i> 1.3% Up from past week</div>
            </div>
            <div class="dash-icon-wrapper icon-yellow">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>
    
    <!-- Card 3 -->
    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-card">
            <div class="dash-info">
                <div class="dash-title">Total Sales</div>
                <div class="dash-value">$<?= number_format($total_revenue) ?></div>
                <div class="dash-subtitle down"><i class="fas fa-chart-line" style="transform: scaleY(-1);"></i> 4.3% Down from yesterday</div>
            </div>
            <div class="dash-icon-wrapper icon-green">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
    </div>
    
    <!-- Card 4 -->
    <div class="col-xl-3 col-sm-6">
        <div class="dashboard-card">
            <div class="dash-info">
                <div class="dash-title">Total Pending</div>
                <div class="dash-value"><?= number_format($orders_pending) ?></div>
                <div class="dash-subtitle up"><i class="fas fa-chart-line"></i> 1.8% Up from yesterday</div>
            </div>
            <div class="dash-icon-wrapper icon-red">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<!-- Sales Details Chart -->
<div class="chart-card">
    <div class="chart-header">
        <h3 class="chart-title">Sales Details</h3>
        <select class="chart-filter">
            <option>October</option>
            <option>November</option>
            <option>December</option>
        </select>
    </div>
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<!-- Pie charts row config -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="chart-card h-100 mb-0">
            <div class="chart-header">
                <h3 class="chart-title">Doanh thu theo danh mục</h3>
            </div>
            <div class="chart-container" style="height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card h-100 mb-0">
            <div class="chart-header">
                <h3 class="chart-title">Top Game Bán Chạy</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <tbody>
                        <?php foreach($top_games as $game): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="width: 60px;">
                                <img src="<?= htmlspecialchars($game['image']) ?>" alt="" style="width: 45px; height: 35px; border-radius: 8px; object-fit: cover;">
                            </td>
                            <td>
                                <div style="font-weight: 800; color: var(--text-dark);"><?= htmlspecialchars($game['name']) ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-gray);">Đã bán: <?= $game['sold_count'] ?></div>
                            </td>
                            <td class="text-end" style="font-weight: 800; color: var(--primary);">
                                $<?= number_format($game['total_revenue']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($top_games)): ?>
                        <tr><td colspan="3" class="text-center text-gray py-4">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Deals Details Table -->
<div class="deals-table-container">
    <div class="chart-header mb-3">
        <h3 class="chart-title">Deals Details</h3>
        <select class="chart-filter">
            <option>October</option>
            <option>November</option>
        </select>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Customer Name</th>
                    <th>Date - Time</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_orders as $order): ?>
                <tr>
                    <td style="font-weight: 800; color: var(--text-dark);">Order #<?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= htmlspecialchars($order['fullname'] ?: $order['username']) ?></td>
                    <td><?= date('d.m.Y - h:i A', strtotime($order['created_at'])) ?></td>
                    <td style="font-weight: 800; color: var(--text-dark);">$<?= number_format($order['total_amount']) ?></td>
                    <td>
                        <?php if ($order['status'] == 'completed'): ?>
                            <span class="badge badge-completed">Delivered</span>
                        <?php elseif ($order['status'] == 'pending'): ?>
                            <span class="badge badge-processing">Processing</span>
                        <?php else: ?>
                            <span class="badge badge-rejected">Rejected</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recent_orders)): ?>
                <tr>
                    <td colspan="5" class="text-center text-gray py-4">No recent deals.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> <!-- Closure for .main-content from header -->

<script>
Chart.defaults.font.family = "'Nunito Sans', sans-serif";
Chart.defaults.color = '#718EBF';
Chart.defaults.scale.grid.color = '#E6E9F4';

// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');

let gradientBlue = salesCtx.createLinearGradient(0, 0, 0, 400);
gradientBlue.addColorStop(0, 'rgba(72, 128, 255, 0.2)');
gradientBlue.addColorStop(1, 'rgba(72, 128, 255, 0.0)');

new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_chart) ?>,
        datasets: [{
            label: 'Sales',
            data: <?= json_encode($revenue_data) ?>,
            borderColor: '#4880FF',
            backgroundColor: gradientBlue,
            borderWidth: 2.5,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#4880FF',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#202224',
                bodyColor: '#4880FF',
                borderColor: '#E6E9F4',
                borderWidth: 1,
                padding: 12,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) { label += ': '; }
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: true,
                    drawBorder: false,
                },
                border: { dash: [5, 5], display: false },
                ticks: {
                    padding: 15,
                    callback: function(value) {
                        return value >= 1000 ? (value/1000) + 'k' : value;
                    }
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false,
                },
                ticks: {
                    padding: 15
                }
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
                '#4880FF',
                '#00B69B',
                '#FF9066',
                '#FEC53D',
                '#8280FF',
                '#F93C65'
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: { size: 13, weight: '600' },
                    color: '#202224'
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
