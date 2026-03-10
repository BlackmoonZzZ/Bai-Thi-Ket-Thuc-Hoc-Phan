<?php 
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_increment_helper.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-ticket-alt"></i> Quản lý Mã giảm giá</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal" style="font-weight: 600; text-transform: uppercase;">
        <i class="fas fa-plus"></i> Thêm mã mới
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">MÃ CODE</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">GIÁ TRỊ</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">LOẠI</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">SỬ DỤNG</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HẾT HẠN</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TRẠNG THÁI</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($coupons)): ?>
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 30px; color: #94a3b8;">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: #475569; display: block;"></i>
                                <p style="margin: 0; font-size: 1.1rem;">Chưa có mã giảm giá nào</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($coupons as $c): ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px;"><strong style="color: #06b6d4;"><?= $c['code'] ?? 'N/A' ?></strong></td>
                            <td style="padding: 15px; color: #ec4899; font-weight: 700;"><?= $c['discount_value'] ?? 0 ?><?= ($c['discount_type'] ?? '') == 'percentage' ? '%' : 'đ' ?></td>
                            <td style="padding: 15px;"><span class="badge" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 6px 12px;"><?= ($c['discount_type'] ?? '') == 'percentage' ? 'Phần trăm' : 'Cố định' ?></span></td>
                            <td style="padding: 15px; color: #e2e8f0;"><?= ($c['used_count'] ?? 0) . '/' . ($c['usage_limit'] ?? 0) ?></td>
                            <td style="padding: 15px; color: #e2e8f0;"><?= $c['expiry_date'] ? date('d/m/Y', strtotime($c['expiry_date'])) : 'Không giới hạn' ?></td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: <?= ($c['is_active'] ?? 0) ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : '#6b7280' ?>; padding: 6px 12px;">
                                    <?= ($c['is_active'] ?? 0) ? 'Kích hoạt' : 'Vô hiệu' ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <div style="display: flex; gap: 6px;">
                                    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editCouponModal<?= $c['id'] ?>" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?= $c['id'] ?>&page=<?= $page ?>" onclick="return confirm('Xóa mã này?')" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal chỉnh sửa -->
                        <div class="modal fade" id="editCouponModal<?= $c['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
                                    <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                                        <h5 class="modal-title" style="color: #06b6d4;">Chỉnh sửa mã giảm giá</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body" style="color: #e2e8f0;">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <input type="hidden" name="current_page" value="<?= $page ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Mã code</label>
                                                <input type="text" name="code" class="form-control" value="<?= $c['code'] ?>" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Giá trị giảm</label>
                                                <input type="number" name="discount_value" class="form-control" value="<?= $c['discount_value'] ?? 0 ?>" step="0.01" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Loại</label>
                                                <select name="discount_type" class="form-select" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                                    <option value="percentage" style="color: #000;" <?= ($c['discount_type'] ?? '') == 'percentage' ? 'selected' : '' ?>>Phần trăm (%)</option>
                                                    <option value="fixed" style="color: #000;" <?= ($c['discount_type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Cố định (đ)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Lượt dùng tối đa</label>
                                                <input type="number" name="usage_limit" class="form-control" value="<?= $c['usage_limit'] ?? 0 ?>" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Hết hạn</label>
                                                <input type="date" name="expiry_date" class="form-control" value="<?= $c['expiry_date'] ?? '' ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" <?= ($c['is_active'] ?? 0) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="is_active">Kích hoạt</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="border-top: 1px solid rgba(6, 182, 212, 0.2);">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-primary">Cập nhật</button>
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
            <a class="page-link" href="?page=<?= $page - 1 ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                <i class="fas fa-chevron-left"></i> Trước
            </a>
        </li>

        <?php
        // Hiển thị các số trang
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>" style="<?= $i == $page ? 'background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: #06b6d4; color: white;' : 'background: #1e293b; border-color: #06b6d4; color: #06b6d4;' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $totalPages ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Nút Next -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                Sau <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2" style="color: #94a3b8; font-size: 0.9rem;">
        Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> mã giảm giá)
    </div>
</nav>
<?php endif; ?>

<!-- Modal Thêm mới -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                <h5 class="modal-title" style="color: #06b6d4;">Thêm mã giảm giá mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="color: #e2e8f0;">
                    <input type="hidden" name="current_page" value="1">
                    <div class="mb-3">
                        <label class="form-label">Mã code</label>
                        <input type="text" name="code" class="form-control" placeholder="VD: SUMMER2024" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá trị giảm</label>
                        <input type="number" name="discount_value" class="form-control" placeholder="10" step="0.01" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại</label>
                        <select name="discount_type" class="form-select" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                            <option value="percentage" style="color: #000;">Phần trăm (%)</option>
                            <option value="fixed" style="color: #000;">Cố định (đ)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lượt dùng tối đa</label>
                        <input type="number" name="usage_limit" class="form-control" placeholder="100" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hết hạn</label>
                        <input type="date" name="expiry_date" class="form-control" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            <label class="form-check-label">Kích hoạt</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(6, 182, 212, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>