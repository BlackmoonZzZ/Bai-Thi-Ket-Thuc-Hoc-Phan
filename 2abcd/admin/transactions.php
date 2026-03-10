<?php 
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/includes/header.php'; 

// Phân trang
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Lọc theo trạng thái và phương thức
$status = $_GET['status'] ?? '';
$method = $_GET['method'] ?? '';
$search = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$sql = "SELECT t.*, u.username, u.fullname, u.email, o.order_number
        FROM transactions t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN orders o ON t.order_id = o.id
        WHERE 1=1";

$params = [];

if (!empty($status)) {
    $sql .= " AND t.status = :status";
    $params[':status'] = $status;
}

if (!empty($method)) {
    $sql .= " AND t.payment_method = :method";
    $params[':method'] = $method;
}

if (!empty($search)) {
    $sql .= " AND (t.transaction_code LIKE :search_transaction_code OR u.username LIKE :search_username OR u.email LIKE :search_email OR o.order_number LIKE :search_order_number)";
    $params[':search_transaction_code'] = "%$search%";
    $params[':search_username'] = "%$search%";
    $params[':search_email'] = "%$search%";
    $params[':search_order_number'] = "%$search%";
}

if (!empty($date_from)) {
    $sql .= " AND DATE(t.created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $sql .= " AND DATE(t.created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

// Đếm tổng bản ghi
$countSql = str_replace("SELECT t.*, u.username, u.fullname, u.email, o.order_number", "SELECT COUNT(*)", $sql);
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();

// Lấy dữ liệu
$sql .= " ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll();

$totalPages = ceil($totalRecords / $limit);

// Thống kê
$stats = $conn->query("SELECT 
    COUNT(*) as total_trans,
    SUM(CASE WHEN status='completed' THEN amount ELSE 0 END) as total_completed,
    SUM(CASE WHEN status='pending' THEN amount ELSE 0 END) as total_pending,
    SUM(CASE WHEN status='failed' THEN amount ELSE 0 END) as total_failed
    FROM transactions")->fetch();

// Danh sách phương thức thanh toán
$payment_methods = $conn->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY display_order")->fetchAll();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.page-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
}
.stat-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
    border: none;
}
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}
.stat-icon.total { background: rgba(72, 128, 255, 0.1); color: var(--primary); }
.stat-icon.completed { background: rgba(0, 182, 155, 0.1); color: var(--success); }
.stat-icon.pending { background: rgba(254, 197, 61, 0.1); color: var(--warning); }
.stat-icon.failed { background: rgba(249, 60, 101, 0.1); color: var(--danger); }
.stat-info p {
    color: var(--text-gray);
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 5px;
}
.stat-info h3 {
    color: var(--text-dark);
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0;
}
.content-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
    margin-bottom: 30px;
}
.filter-form .form-control, .filter-form .form-select {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 10px 15px;
    color: var(--text-dark);
}
.filter-form .form-control:focus, .filter-form .form-select:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
.filter-form .btn-primary {
    border-radius: 10px;
    padding: 10px;
}
.table th {
    color: var(--text-gray);
    font-weight: 700;
    border-bottom: 2px solid #EEF2F7;
    padding: 15px;
    text-transform: uppercase;
    font-size: 0.85rem;
    background: transparent;
}
.table td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #EEF2F7;
    color: var(--text-dark);
    font-weight: 600;
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.text-gray {
    color: var(--text-gray) !important;
}
.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}
.badge-pending { background: rgba(254, 197, 61, 0.15); color: #FEC53D; }
.badge-completed { background: rgba(0, 182, 155, 0.15); color: #00B69B; }
.badge-failed { background: rgba(249, 60, 101, 0.15); color: #F93C65; }
.badge-refunded { background: rgba(72, 128, 255, 0.15); color: var(--primary); }

.btn-action {
    padding: 6px 10px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    transition: 0.2s;
    background: #F8F9FB;
    color: var(--text-dark);
}
.btn-action:hover {
    background: rgba(72, 128, 255, 0.1);
    color: var(--primary);
}

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.modal-header {
    background: #F8F9FB;
    border-bottom: 1px solid #EEF2F7;
    border-radius: 16px 16px 0 0;
    padding: 20px 25px;
}
.modal-title {
    font-weight: 800;
    color: var(--text-dark);
}
.modal-body {
    padding: 25px;
    color: var(--text-dark);
}
.modal-body strong {
    color: var(--text-gray);
    font-size: 0.9rem;
    text-transform: uppercase;
}
.modal-footer {
    border-top: 1px solid #EEF2F7;
    padding: 20px 25px;
}
.pagination .page-link {
    border: none;
    border-radius: 8px;
    margin: 0 4px;
    color: var(--text-gray);
    font-weight: 600;
}
.pagination .page-item.active .page-link {
    background-color: var(--primary);
    color: white;
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title"><i class="fas fa-receipt me-2 text-primary"></i> Transaction History</h2>
</div>

<!-- Thống kê -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Transactions</p>
                <h3><?= number_format($stats['total_trans']) ?></h3>
            </div>
            <div class="stat-icon total">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <p>Completed</p>
                <h3><?= number_format($stats['total_completed']) ?>đ</h3>
            </div>
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <p>Pending</p>
                <h3><?= number_format($stats['total_pending']) ?>đ</h3>
            </div>
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-info">
                <p>Failed</p>
                <h3><?= number_format($stats['total_failed']) ?>đ</h3>
            </div>
            <div class="stat-icon failed">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-body p-0">
        <!-- Bộ lọc -->
        <form method="GET" class="filter-form mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Transaction ID, Username, Email..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="failed" <?= $status == 'failed' ? 'selected' : '' ?>>Failed</option>
                        <option value="refunded" <?= $status == 'refunded' ? 'selected' : '' ?>>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="bank_transfer" <?= $method == 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                        <option value="momo" <?= $method == 'momo' ? 'selected' : '' ?>>MoMo</option>
                        <option value="zalopay" <?= $method == 'zalopay' ? 'selected' : '' ?>>ZaloPay</option>
                        <option value="vnpay" <?= $method == 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                        <option value="balance" <?= $method == 'balance' ? 'selected' : '' ?>>Wallet Balance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">TRANSACTION ID</th>
                        <th>CUSTOMER</th>
                        <th>ORDER</th>
                        <th>METHOD</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($transactions)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-gray fw-bold">
                                <div class="mb-3"><i class="fas fa-inbox fa-3x"></i></div>
                                No transactions found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($transactions as $t): 
                            $method_icons = [
                                'bank_transfer' => 'fa-university',
                                'momo' => 'fa-wallet',
                                'zalopay' => 'fa-wallet',
                                'vnpay' => 'fa-credit-card',
                                'balance' => 'fa-coins'
                            ];
                            $method_names = [
                                'bank_transfer' => 'Bank Transfer',
                                'momo' => 'MoMo',
                                'zalopay' => 'ZaloPay',
                                'vnpay' => 'VNPay',
                                'balance' => 'Wallet Balance'
                            ];
                            $status_classes = [
                                'pending' => 'badge-pending',
                                'completed' => 'badge-completed',
                                'failed' => 'badge-failed',
                                'refunded' => 'badge-refunded'
                            ];
                            $status_text = [
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded'
                            ];
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark font-monospace">
                                <?= htmlspecialchars($t['transaction_code']) ?>
                            </td>
                            <td>
                                <div><strong class="text-dark"><?= htmlspecialchars($t['fullname'] ?: $t['username']) ?></strong></div>
                                <div class="text-gray" style="font-size: 0.85rem;"><?= htmlspecialchars($t['email']) ?></div>
                            </td>
                            <td>
                                <?php if($t['order_number']): ?>
                                    <a href="orders.php?id=<?= $t['order_id'] ?>" class="text-primary text-decoration-none fw-bold">
                                        #<?= htmlspecialchars($t['order_number']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fas <?= $method_icons[$t['payment_method']] ?? 'fa-money-bill' ?> text-gray"></i>
                                    <?= $method_names[$t['payment_method']] ?? $t['payment_method'] ?>
                                </span>
                            </td>
                            <td class="text-success fw-bold">
                                <?= number_format($t['amount']) ?>đ
                            </td>
                            <td>
                                <?php $badge_class = $status_classes[$t['status']] ?? 'bg-secondary text-white'; ?>
                                <span class="badge-status inline-block <?= $badge_class ?>">
                                    <?= $status_text[$t['status']] ?? ucfirst($t['status']) ?>
                                </span>
                            </td>
                            <td class="text-gray">
                                <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#detailModal<?= $t['id'] ?>" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết giao dịch -->
                        <div class="modal fade" id="detailModal<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Transaction Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Transaction ID:</strong><br><span class="text-dark fw-bold font-monospace"><?= htmlspecialchars($t['transaction_code']) ?></span></p>
                                                <p><strong>Customer:</strong><br><span class="text-dark"><?= htmlspecialchars($t['fullname'] ?: $t['username']) ?></span></p>
                                                <p><strong>Email:</strong><br><span class="text-dark"><?= htmlspecialchars($t['email']) ?></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Order:</strong><br><?= $t['order_number'] ? '<a href="orders.php?id='.$t['order_id'].'" class="fw-bold text-primary">#' . htmlspecialchars($t['order_number']).'</a>' : '<span class="text-gray">N/A</span>' ?></p>
                                                <p><strong>Method:</strong><br><span class="text-dark"><?= $method_names[$t['payment_method']] ?? $t['payment_method'] ?></span></p>
                                                <p><strong>Amount:</strong><br><span class="text-success fw-bold fs-5"><?= number_format($t['amount']) ?>đ</span></p>
                                            </div>
                                        </div>
                                        <div class="bg-light p-3 rounded-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>Status:</strong>
                                                <span class="badge-status <?= $badge_class ?>"><?= $status_text[$t['status']] ?? ucfirst($t['status']) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <strong>Created At:</strong>
                                                <span class="text-dark"><?= date('d/m/Y H:i:s', strtotime($t['created_at'])) ?></span>
                                            </div>
                                            <?php if($t['updated_at'] != $t['created_at']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <strong>Last Updated:</strong>
                                                    <span class="text-dark"><?= date('d/m/Y H:i:s', strtotime($t['updated_at'])) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if($t['note']): ?>
                                            <div class="mb-3">
                                                <strong>Note:</strong>
                                                <p class="text-dark bg-light p-2 rounded mt-1"><?= nl2br(htmlspecialchars($t['note'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($t['transaction_details']): ?>
                                            <div>
                                                <strong>Technical Details:</strong>
                                                <pre class="bg-light p-2 rounded mt-1 text-gray" style="font-size: 0.85rem; max-height: 150px; overflow-y: auto;"><?= htmlspecialchars($t['transaction_details']) ?></pre>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Phân trang -->
<?php if($totalPages > 1): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?><?= !empty($method) ? '&method=' . urlencode($method) : '' ?><?= !empty($date_from) ? '&date_from=' . urlencode($date_from) : '' ?><?= !empty($date_to) ? '&date_to=' . urlencode($date_to) : '' ?>">
                <i class="fas fa-chevron-left me-1"></i> Previous
            </a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?><?= !empty($method) ? '&method=' . urlencode($method) : '' ?><?= !empty($date_from) ? '&date_from=' . urlencode($date_from) : '' ?><?= !empty($date_to) ? '&date_to=' . urlencode($date_to) : '' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?><?= !empty($method) ? '&method=' . urlencode($method) : '' ?><?= !empty($date_from) ? '&date_from=' . urlencode($date_from) : '' ?><?= !empty($date_to) ? '&date_to=' . urlencode($date_to) : '' ?>">
                Next <i class="fas fa-chevron-right ms-1"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2 text-gray" style="font-size: 0.9rem;">
        Page <?= $page ?> of <?= $totalPages ?> (<?= $totalRecords ?> total transactions)
    </div>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
