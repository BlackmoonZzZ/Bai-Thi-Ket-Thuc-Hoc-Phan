<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auto_increment_helper.php';
include __DIR__ . '/includes/header.php'; 

// Xử lý xóa mã giảm giá
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->prepare("DELETE FROM coupons WHERE id=?")->execute([$id]);
    resetAutoIncrement($conn, 'coupons'); // Reset AUTO_INCREMENT
    $page = $_GET['page'] ?? 1;
    echo "<script>window.location='coupons.php?page=$page'</script>";
}

// Xử lý thêm/cập nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'] ?? '';
    $discount_value = $_POST['discount_value'] ?? 0;
    $discount_type = $_POST['discount_type'] ?? 'percentage'; // 'percentage' hoặc 'fixed'
    $usage_limit = $_POST['usage_limit'] ?? 0;
    $expiry_date = $_POST['expiry_date'] ?? null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Xử lý expiry_date: nếu rỗng thì set NULL
    if (empty($expiry_date)) {
        $expiry_date = null;
    }

    if (isset($_POST['id']) && $_POST['id']) {
        // Cập nhật
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE coupons SET code=?, discount_value=?, discount_type=?, usage_limit=?, expiry_date=?, is_active=? WHERE id=?");
        $stmt->execute([$code, $discount_value, $discount_type, $usage_limit, $expiry_date, $is_active, $id]);
    } else {
        // Thêm mới
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_value, discount_type, usage_limit, expiry_date, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$code, $discount_value, $discount_type, $usage_limit, $expiry_date, $is_active]);
    }
    $page = $_POST['current_page'] ?? 1;
    echo "<script>window.location='coupons.php?page=$page'</script>";
}

// Phân trang
$limit = 10; // Số mã giảm giá mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Đếm tổng số bản ghi
$totalRecords = $conn->query("SELECT COUNT(*) FROM coupons")->fetchColumn();

// Lấy danh sách với phân trang
$stmt = $conn->prepare("SELECT * FROM coupons ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$coupons = $stmt->fetchAll();

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
.content-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
    margin-bottom: 30px;
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
    font-weight: 500;
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.badge-type {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    background: rgba(72, 128, 255, 0.15);
    color: var(--primary);
}
.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}
.badge-active { background: rgba(0, 182, 155, 0.15); color: var(--success); }
.badge-inactive { background: rgba(249, 60, 101, 0.15); color: var(--danger); }

.btn-action {
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    transition: 0.2s;
}
.btn-edit { background: rgba(254, 197, 61, 0.1); color: var(--warning); }
.btn-edit:hover { background: var(--warning); color: white; }
.btn-delete { background: rgba(249, 60, 101, 0.1); color: var(--danger); }
.btn-delete:hover { background: var(--danger); color: white; }

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
.modal-footer {
    border-top: 1px solid #EEF2F7;
    padding: 20px 25px;
}
.form-label {
    font-weight: 600;
    color: var(--text-dark);
}
.form-control, .form-select {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 10px 15px;
    color: var(--text-dark);
}
.form-control:focus, .form-select:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
/* Pagination */
.pagination .page-link {
    border: none;
    padding: 8px 16px;
    color: var(--text-gray);
    font-weight: 600;
    border-radius: 8px;
    margin: 0 4px;
    background: var(--bg-surface);
}
.pagination .page-item.active .page-link {
    background: var(--primary);
    color: white;
}
.pagination .page-link:hover:not(.active) {
    background: #F8F9FB;
    color: var(--primary);
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title"><i class="fas fa-ticket-alt me-2 text-primary"></i> Coupon Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
        <i class="fas fa-plus me-1"></i> Add Coupon
    </button>
</div>

<div class="content-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">COUPON CODE</th>
                        <th>DISCOUNT</th>
                        <th>TYPE</th>
                        <th>USAGE</th>
                        <th>EXPIRY DATE</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($coupons)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-gray fw-bold">
                                <div class="mb-3"><i class="fas fa-ticket-alt fa-3x"></i></div>
                                No coupons found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($coupons as $c): ?>
                        <tr>
                            <td class="ps-4"><strong class="text-dark fs-6"><?= htmlspecialchars($c['code'] ?? 'N/A') ?></strong></td>
                            <td class="text-primary fw-bold fs-6">
                                <?php 
                                    if (($c['discount_type'] ?? '') == 'percentage') {
                                        echo floatval($c['discount_value']) . '%';
                                    } else {
                                        echo number_format($c['discount_value'] ?? 0, 0, ',', '.') . ' ₫';
                                    }
                                ?>
                            </td>
                            <td><span class="badge-type"><?= ($c['discount_type'] ?? '') == 'percentage' ? 'Percentage' : 'Fixed Amount' ?></span></td>
                            <td class="text-gray fw-bold"><?= ($c['used_count'] ?? 0) ?> / <?= ($c['usage_limit'] ?? 0) ?></td>
                            <td class="text-gray"><?= $c['expiry_date'] ? date('d/m/Y', strtotime($c['expiry_date'])) : '<span class="text-muted">No Expiry</span>' ?></td>
                            <td>
                                <span class="badge-status d-inline-block <?= ($c['is_active'] ?? 0) ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= ($c['is_active'] ?? 0) ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editCouponModal<?= $c['id'] ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?= $c['id'] ?>&page=<?= $page ?>" onclick="return confirm('Are you sure you want to delete this coupon?')" class="btn-action btn-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal chỉnh sửa -->
                        <div class="modal fade" id="editCouponModal<?= $c['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Coupon</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <input type="hidden" name="current_page" value="<?= $page ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($c['code']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                                <input type="number" name="discount_value" class="form-control" value="<?= $c['discount_value'] ?? 0 ?>" step="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                                <select name="discount_type" class="form-select" required>
                                                    <option value="percentage" <?= ($c['discount_type'] ?? '') == 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                                                    <option value="fixed" <?= ($c['discount_type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Fixed Amount (VND)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Usage Limit <span class="text-danger">*</span></label>
                                                <input type="number" name="usage_limit" class="form-control" value="<?= $c['usage_limit'] ?? 0 ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Expiry Date</label>
                                                <input type="date" name="expiry_date" class="form-control" value="<?= $c['expiry_date'] ? date('Y-m-d', strtotime($c['expiry_date'])) : '' ?>">
                                                <small class="text-gray">Leave empty for no expiry</small>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check form-switch mt-2">
                                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActiveEdit<?= $c['id'] ?>" value="1" <?= ($c['is_active'] ?? 0) ? 'checked' : '' ?>>
                                                    <label class="form-check-label ms-1 fw-bold" for="isActiveEdit<?= $c['id'] ?>">Active Coupon</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary fw-bold">Update</button>
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
    </div>
</div>

<!-- Phân trang -->
<?php if($totalPages > 1): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <!-- Nút Previous -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link shadow-sm" href="?page=<?= $page - 1 ?>">
                <i class="fas fa-chevron-left me-1"></i> Prev
            </a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link shadow-sm" href="?page=1">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link shadow-sm">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link shadow-sm" href="?page=<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link shadow-sm">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link shadow-sm" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Nút Next -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link shadow-sm" href="?page=<?= $page + 1 ?>">
                Next <i class="fas fa-chevron-right ms-1"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-3 text-muted fw-bold fs-7">
        Showing Page <?= $page ?> of <?= $totalPages ?> (Total <?= $totalRecords ?> coupons)
    </div>
</nav>
<?php endif; ?>

<!-- Modal Thêm mới -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="current_page" value="1">
                    <div class="mb-3">
                        <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. SUMMER2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" name="discount_value" class="form-control" placeholder="10" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                        <select name="discount_type" class="form-select" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (VND)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usage Limit <span class="text-danger">*</span></label>
                        <input type="number" name="usage_limit" class="form-control" placeholder="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control">
                        <small class="text-gray">Leave empty for no expiry</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActiveAdd" value="1" checked>
                            <label class="form-check-label ms-1 fw-bold" for="isActiveAdd">Active Coupon</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Add Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
