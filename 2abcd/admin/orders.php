<?php 
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/includes/header.php'; 

// Cập nhật trạng thái đơn hàng
if(isset($_POST['update_status'])) {
    try {
        $oid = (int)$_POST['order_id'];
        $st = trim($_POST['status']);
        
        // Validation
        $valid_statuses = ['pending', 'completed', 'cancelled', 'refunded'];
        if (!in_array($st, $valid_statuses)) {
            throw new Exception('Trạng thái không hợp lệ!');
        }
        
        $conn->beginTransaction();
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        if ($stmt->execute([$st, $oid])) {
            $conn->commit();
        } else {
            throw new Exception('Không thể cập nhật trạng thái!');
        }
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

// Phân trang
$limit = 5; // Số đơn hàng mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Đảm bảo page >= 1
$offset = ($page - 1) * $limit;

// Lấy tham số tìm kiếm và lọc
$search = $_GET['search'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';

// Xây dựng câu query
$sql = "
    SELECT o.*, 
           u.fullname, 
           u.email,
           COUNT(oi.id) as item_count
    FROM orders o 
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
";

$countSql = "SELECT COUNT(DISTINCT o.id) FROM orders o JOIN users u ON o.user_id = u.id";

$whereConditions = [];
$params = [];

// Điều kiện tìm kiếm
if (!empty($search)) {
    $whereConditions[] = "(o.order_number LIKE :search_order_number OR u.fullname LIKE :search_fullname OR u.email LIKE :search_email)";
    $params[':search_order_number'] = "%$search%";
    $params[':search_fullname'] = "%$search%";
    $params[':search_email'] = "%$search%";
}

// Điều kiện lọc trạng thái
if (!empty($filter_status)) {
    $whereConditions[] = "o.status = :status";
    $params[':status'] = $filter_status;
}

// Thêm WHERE clause nếu có điều kiện
if (!empty($whereConditions)) {
    $whereClause = " WHERE " . implode(" AND ", $whereConditions);
    $sql .= $whereClause;
    $countSql .= $whereClause;
}

$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

// Đếm tổng số bản ghi
$stmtCount = $conn->prepare($countSql);
foreach ($params as $key => $value) {
    $stmtCount->bindValue($key, $value);
}
$stmtCount->execute();
$totalRecords = $stmtCount->fetchColumn();

// Lấy dữ liệu phân trang
$stmt = $conn->prepare($sql . " LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

// Tính tổng số trang
$totalPages = ceil($totalRecords / $limit);
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
.controls-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 20px 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.search-form {
    display: flex;
    gap: 10px;
}
.search-input {
    min-width: 300px;
}
.filter-label {
    font-weight: 700;
    color: var(--text-dark);
    margin-right: 12px;
}
.filter-select {
    min-width: 200px;
}
.orders-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.order-number {
    font-weight: 800;
    color: var(--primary);
}
.customer-name {
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
}
.customer-email {
    color: var(--text-gray);
    font-size: 0.85rem;
}
.amount-tag {
    font-weight: 800;
    color: var(--text-dark);
}
.item-count-badge {
    background: rgba(72, 128, 255, 0.1);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
}
.status-select {
    font-weight: 600;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.85rem;
    cursor: pointer;
}
.status-pending { background: var(--warning-bg); color: var(--warning-text); border: 1px solid var(--warning-text); }
.status-completed { background: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-text); }
.status-cancelled { background: var(--danger-bg); color: var(--danger-text); border: 1px solid var(--danger-text); }
.status-refunded { background: rgba(130, 128, 255, 0.1); color: #8280FF; border: 1px solid #8280FF; }

.action-btn {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: white;
    text-decoration: none;
    transition: 0.2s;
    background: var(--primary);
    border: none;
}
.action-btn:hover { background: #3A6DDF; color: white; }

/* Pagination */
.pagination { margin-bottom: 0; }
.page-link {
    border: none;
    color: var(--text-gray);
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
    margin: 0 4px;
    background: transparent;
}
.page-link:hover { background: #F5F6FA; color: var(--text-dark); }
.page-item.active .page-link { background: var(--primary); color: white; }
.page-item.disabled .page-link { color: #CBD5E1; background: transparent; }
.pagination-info { color: var(--text-gray); font-weight: 600; font-size: 0.9rem; }

/* Modal */
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
}
.detail-label {
    font-weight: 700;
    color: var(--text-gray);
    margin-bottom: 5px;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.detail-value {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 15px;
}
.items-table th {
    background: #F8F9FB;
    color: var(--text-gray);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}
.total-row {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--primary);
}
</style>

<div class="page-header">
    <h2 class="page-title">Orders Management</h2>
    <div class="badge bg-primary text-white p-2 px-3 fw-bold rounded-3">
        <i class="fas fa-box me-1"></i> Total: <?= $totalRecords ?> Orders
    </div>
</div>

<div class="controls-card">
    <div class="row align-items-center g-3">
        <div class="col-lg-5">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="form-control search-input" placeholder="Search order ID, customer, email..." value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filter_status) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="col-lg-7 text-lg-end d-flex align-items-center justify-content-lg-end">
            <label class="filter-label"><i class="fas fa-filter"></i> Filter Status:</label>
            <select class="form-select filter-select d-inline-block w-auto" id="filterStatus" onchange="handleFilterChange(this.value)">
                <option value="">All Orders</option>
                <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                <option value="completed" <?= $filter_status == 'completed' ? 'selected' : '' ?>>✓ Completed</option>
                <option value="cancelled" <?= $filter_status == 'cancelled' ? 'selected' : '' ?>>✗ Cancelled</option>
                <option value="refunded" <?= $filter_status == 'refunded' ? 'selected' : '' ?>>↩ Refunded</option>
            </select>
        </div>
    </div>
</div>

<script>
function handleFilterChange(filterValue) {
    const urlParams = new URLSearchParams(window.location.search);
    if (filterValue) {
        urlParams.set('filter_status', filterValue);
    } else {
        urlParams.delete('filter_status');
    }
    urlParams.set('page', '1');
    window.location.search = urlParams.toString();
}
</script>

<div class="orders-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x mb-3 text-gray"></i>
                        <p class="text-gray fw-bold fs-5 mb-0">
                            <?= !empty($search) || !empty($filter_status) ? 'No orders found matching your criteria' : 'No orders available' ?>
                        </p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td class="order-number">#<?= htmlspecialchars($o['order_number']) ?></td>
                        <td>
                            <div class="customer-name"><?= htmlspecialchars($o['fullname'] ?? 'N/A') ?></div>
                            <div class="customer-email"><?= htmlspecialchars($o['email'] ?? 'N/A') ?></div>
                        </td>
                        <td>
                            <span class="item-count-badge">
                                <?= (int)$o['item_count'] ?> item(s)
                            </span>
                        </td>
                        <td class="amount-tag">$<?= number_format($o['total_amount']) ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <input type="hidden" name="update_status" value="1">
                                <?php
                                $statusClass = '';
                                if ($o['status'] == 'pending') $statusClass = 'status-pending';
                                elseif ($o['status'] == 'completed') $statusClass = 'status-completed';
                                elseif ($o['status'] == 'cancelled') $statusClass = 'status-cancelled';
                                elseif ($o['status'] == 'refunded') $statusClass = 'status-refunded';
                                ?>
                                <select name="status" class="form-select status-select <?= $statusClass ?>" onchange="this.form.submit()">
                                    <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>⏳ Pending</option>
                                    <option value="completed" <?= $o['status']=='completed'?'selected':'' ?>>✓ Completed</option>
                                    <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>✗ Cancelled</option>
                                    <option value="refunded" <?= $o['status']=='refunded'?'selected':'' ?>>↩ Refunded</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-gray fw-semibold fs-7"><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></td>
                        <td>
                            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#orderModal<?= $o['id'] ?>" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if($totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            Showing Page <?= $page ?> of <?= $totalPages ?> (<?= $totalRecords ?> total orders)
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modals Chi tiết Đơn hàng -->
<?php foreach($orders as $o): ?>
<div class="modal fade" id="orderModal<?= $o['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt text-primary me-2"></i> Order Details #<?= htmlspecialchars($o['order_number']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-label">Customer Info</div>
                        <div class="detail-value text-primary fs-5"><?= htmlspecialchars($o['fullname'] ?? 'N/A') ?></div>
                        <div class="text-gray mb-1"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($o['email'] ?? 'N/A') ?></div>
                        <div class="text-gray"><i class="fas fa-calendar-alt me-2"></i><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="detail-label">Payment Status</div>
                        <div class="detail-value mb-2">
                            <?php 
                            $ps = $o['payment_status'] ?? 'unpaid';
                            $psBadgeClass = $ps == 'paid' ? 'bg-success text-white' : ($ps == 'unpaid' ? 'bg-warning text-dark' : 'bg-danger text-white');
                            $psText = $ps == 'paid' ? 'Paid' : ($ps == 'unpaid' ? 'Unpaid' : 'Failed');
                            ?>
                            <span class="badge <?= $psBadgeClass ?> px-3 py-2 fs-6">
                                <?= htmlspecialchars($psText) ?>
                            </span>
                        </div>
                        <div class="text-gray fw-semibold">Method: <?= htmlspecialchars($o['payment_method'] ?? 'N/A') ?></div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-box-open text-primary me-2"></i> Order Items</h6>
                <div class="table-responsive">
                    <table class="table items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Key Code</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $order_items = $conn->prepare("
                                SELECT oi.*, p.name, pk.key_code 
                                FROM order_items oi 
                                JOIN products p ON oi.product_id = p.id 
                                LEFT JOIN product_keys pk ON oi.key_id = pk.id 
                                WHERE oi.order_id = ?
                            ");
                            $order_items->execute([$o['id']]);
                            $items = $order_items->fetchAll();
                            
                            if (count($items) === 0):
                            ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray">No items found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded text-primary fw-bold">
                                            <?= htmlspecialchars($item['key_code'] ?? '-') ?>
                                        </code>
                                    </td>
                                    <td class="text-end fw-bold text-dark">$<?= number_format($item['price']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <hr class="my-4" style="border-color: #EEF2F7;">
                
                <div class="text-end">
                    <?php if (isset($o['discount_amount']) && $o['discount_amount'] > 0): ?>
                    <div class="mb-2">
                        <span class="text-gray fw-bold me-3">Discount:</span> 
                        <span class="text-success fw-bold">-$<?= number_format($o['discount_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-gray fw-bold fs-5 me-3">Total Amount:</span> 
                        <span class="total-row">$<?= number_format($o['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>

