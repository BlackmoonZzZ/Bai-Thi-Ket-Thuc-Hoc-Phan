<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auto_increment_helper.php';
include __DIR__ . '/includes/header.php'; 

// Xử lý xóa user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    resetAutoIncrement($conn, 'users'); // Reset AUTO_INCREMENT
    echo "<script>window.location='customers.php'</script>";
}

// Xử lý cập nhật user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'] ?? 0;
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $balance = $_POST['balance'] ?? 0;
    $status = $_POST['status'] ?? 'active';

    $conn->prepare("UPDATE users SET fullname=?, email=?, phone=?, balance=?, status=? WHERE id=?")
         ->execute([$fullname, $email, $phone, $balance, $status, $id]);
    
    echo "<script>window.location='customers.php'</script>";
}

// Phân trang
$limit = 10; // Số khách hàng mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Tìm kiếm khách hàng
$search = $_GET['search'] ?? '';
$sql = "SELECT u.*, 
               COUNT(o.id) as order_count,
               COALESCE(SUM(o.total_amount), 0) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        WHERE u.role='customer'";

$countSql = "SELECT COUNT(*) FROM users u WHERE u.role='customer'";

if(!empty($search)) {
    $whereClause = " AND (u.fullname LIKE :search_fullname OR u.email LIKE :search_email OR u.phone LIKE :search_phone OR u.username LIKE :search_username)";
    $sql .= $whereClause;
    $countSql .= $whereClause;
}

$sql .= " GROUP BY u.id ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

// Đếm tổng số bản ghi
if(!empty($search)) {
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute([
        ':search_fullname' => "%$search%",
        ':search_email' => "%$search%",
        ':search_phone' => "%$search%",
        ':search_username' => "%$search%"
    ]);
    $totalRecords = $stmtCount->fetchColumn();
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search_fullname', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search_email', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search_phone', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search_username', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $customers = $stmt->fetchAll();
} else {
    $totalRecords = $conn->query($countSql)->fetchColumn();
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $customers = $stmt->fetchAll();
}

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
.customers-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.customer-id {
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
.order-count-badge {
    background: rgba(72, 128, 255, 0.1);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
}
.spent-tag {
    font-weight: 800;
    color: var(--text-dark);
}
.status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
}
.status-active { background: var(--success-bg); color: var(--success-text); }
.status-locked { background: var(--danger-bg); color: var(--danger-text); }
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
    border: none;
}
.btn-view { background: var(--primary); }
.btn-view:hover { background: #3A6DDF; color: white; }
.btn-edit { background: #FEC53D; }
.btn-edit:hover { background: #E5B137; color: white; }
.btn-delete { background: #F93C65; }
.btn-delete:hover { background: #E0365B; color: white; }

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
.modal-footer {
    border-top: 1px solid #EEF2F7;
    padding: 20px 25px;
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
    margin-bottom: 20px;
    font-size: 1.05rem;
}
.form-label {
    font-weight: 700;
    color: var(--text-dark);
}
.form-control, .form-select {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 8px;
    padding: 10px 15px;
}
.form-control:focus, .form-select:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
.form-control:disabled {
    background: #EEF2F7;
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title">Customers Management</h2>
    <div class="badge bg-primary text-white p-2 px-3 fw-bold rounded-3">
        <i class="fas fa-users me-1"></i> Total: <?= $totalRecords ?> Customers
    </div>
</div>

<div class="controls-card">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="form-control search-input" placeholder="Search name, email, phone, username..." value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="page" value="1">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search me-2"></i> Search</button>
            </form>
        </div>
    </div>
</div>

<div class="customers-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Customer Details</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($customers)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x mb-3 text-gray"></i>
                            <p class="text-gray fw-bold fs-5 mb-0">No customers found matching your criteria</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($customers as $c): ?>
                    <tr>
                        <td class="customer-id">#<?= $c['id'] ?></td>
                        <td>
                            <div class="customer-name"><?= htmlspecialchars($c['fullname'] ?? $c['username']) ?></div>
                            <div class="customer-email"><?= htmlspecialchars($c['email']) ?></div>
                        </td>
                        <td class="text-gray fw-semibold fs-7"><?= htmlspecialchars($c['phone'] ?? 'N/A') ?></td>
                        <td>
                            <span class="order-count-badge">
                                <?= $c['order_count'] ?> orders
                            </span>
                        </td>
                        <td class="spent-tag">$<?= number_format($c['total_spent']) ?></td>
                        <td class="text-gray fw-semibold fs-7"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                        <td>
                            <?php if (($c['status'] ?? 'active') == 'active'): ?>
                                <span class="status-badge status-active">Active</span>
                            <?php else: ?>
                                <span class="status-badge status-locked">Locked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="action-btn btn-view" data-bs-toggle="modal" data-bs-target="#detailModal<?= $c['id'] ?>" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id'] ?>" title="Edit Customer">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?= $c['id'] ?>&page=<?= $page ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" onclick="return confirm('Are you sure you want to delete this customer?')" class="action-btn btn-delete" title="Delete Customer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Chi tiết -->
                    <div class="modal fade" id="detailModal<?= $c['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="fas fa-user-circle text-primary me-2"></i> Customer Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 border-end">
                                            <div class="detail-label">Full Name</div>
                                            <div class="detail-value fw-bold text-primary"><?= htmlspecialchars($c['fullname'] ?? 'N/A') ?></div>
                                            
                                            <div class="detail-label">Username</div>
                                            <div class="detail-value"><?= htmlspecialchars($c['username']) ?></div>
                                            
                                            <div class="detail-label">Email Address</div>
                                            <div class="detail-value"><?= htmlspecialchars($c['email']) ?></div>
                                            
                                            <div class="detail-label">Phone Number</div>
                                            <div class="detail-value"><?= htmlspecialchars($c['phone'] ?? 'N/A') ?></div>
                                        </div>
                                        <div class="col-md-6 ps-4">
                                            <div class="detail-label">Account Balance</div>
                                            <div class="detail-value fw-bold text-success fs-4">$<?= number_format($c['balance'] ?? 0) ?></div>
                                            
                                            <div class="detail-label">Join Date</div>
                                            <div class="detail-value text-gray"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></div>
                                            
                                            <div class="detail-label">Account Statistics</div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-shopping-bag text-gray me-2 w-15px"></i>
                                                <span class="fw-semibold"><?= $c['order_count'] ?> Total Orders</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-chart-line text-gray me-2 w-15px"></i>
                                                <span class="fw-semibold">$<?= number_format($c['total_spent']) ?> Total Spent</span>
                                            </div>
                                            
                                            <div class="detail-label">Account Status</div>
                                            <div>
                                            <?php if (($c['status'] ?? 'active') == 'active'): ?>
                                                <span class="status-badge status-active">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge status-locked">Locked</span>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit User -->
                    <div class="modal fade" id="editModal<?= $c['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="fas fa-user-edit text-primary me-2"></i> Edit Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control fw-bold" value="<?= htmlspecialchars($c['username']) ?>" disabled>
                                            <div class="form-text mt-1 text-gray fs-7"><i class="fas fa-info-circle me-1"></i> Username cannot be changed.</div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($c['fullname'] ?? '') ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($c['phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($c['email']) ?>" required>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Account Balance</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-0 fw-bold">$</span>
                                                    <input type="number" name="balance" class="form-control border-start-0 ps-0" value="<?= $c['balance'] ?? 0 ?>" step="0.01">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Account Status</label>
                                                <select name="status" class="form-select fw-semibold">
                                                    <option value="active" <?= ($c['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="locked" <?= ($c['status'] ?? 'active') == 'locked' ? 'selected' : '' ?>>Locked</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-4">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if($totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
        <div class="pagination-info">
            Showing Page <?= $page ?> of <?= $totalPages ?> (<?= $totalRecords ?> total customers)
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
