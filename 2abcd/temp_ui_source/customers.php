<?php 
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_increment_helper.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-users"></i> Quản lý Khách hàng</h2>
</div>

<div class="card">
    <div class="card-body">
        <!-- Thanh tìm kiếm -->
        <div class="row mb-4">
            <div class="col-md-4">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email, phone, username..." value="<?= htmlspecialchars($search) ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    <input type="hidden" name="page" value="1">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">ID</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TÊN KHÁCH HÀNG</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">EMAIL</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">ĐIỆN THOẠI</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">ĐƠN HÀNG</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TỔNG CHI TIÊU</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">NGÀY THAM GIA</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TRẠNG THÁI</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($customers)): ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td colspan="9" style="padding: 30px; text-align: center; color: #94a3b8;">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: #475569; display: block;"></i>
                                <p style="margin: 0; font-size: 1.1rem;">Không tìm thấy khách hàng nào</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($customers as $c): ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px; color: #06b6d4; font-weight: 700;">#<?= $c['id'] ?></td>
                            <td style="padding: 15px; color: #e2e8f0;"><strong><?= $c['fullname'] ?? $c['username'] ?></strong></td>
                            <td style="padding: 15px; color: #e2e8f0;"><?= $c['email'] ?></td>
                            <td style="padding: 15px; color: #e2e8f0;"><?= $c['phone'] ?? 'N/A' ?></td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 6px 12px;">
                                    <?= $c['order_count'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px; color: #ec4899; font-weight: 700;"><?= number_format($c['total_spent']) ?>đ</td>
                            <td style="padding: 15px; color: #e2e8f0;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: <?= ($c['status'] ?? 'active') == 'active' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : '#ef4444' ?>; padding: 6px 12px;">
                                    <?= ($c['status'] ?? 'active') == 'active' ? 'Hoạt động' : 'Khóa' ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <div style="display: flex; gap: 6px;">
                                    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $c['id'] ?>" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id'] ?>" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; transition: all 0.3s ease;" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?= $c['id'] ?>&page=<?= $page ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" onclick="return confirm('Xóa user này?')" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết -->
                        <div class="modal fade" id="detailModal<?= $c['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
                                    <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                                        <h5 class="modal-title" style="color: #06b6d4;">Chi tiết khách hàng</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>
                                    <div class="modal-body" style="color: #e2e8f0;">
                                        <p><strong style="color: #06b6d4;">Username:</strong> <?= $c['username'] ?></p>
                                        <p><strong style="color: #06b6d4;">Tên:</strong> <?= $c['fullname'] ?? 'N/A' ?></p>
                                        <p><strong style="color: #06b6d4;">Email:</strong> <?= $c['email'] ?></p>
                                        <p><strong style="color: #06b6d4;">Điện thoại:</strong> <?= $c['phone'] ?? 'N/A' ?></p>
                                        <p><strong style="color: #06b6d4;">Số dư:</strong> <span style="color: #ec4899; font-weight: 700;"><?= number_format($c['balance'] ?? 0) ?>đ</span></p>
                                        <p><strong style="color: #06b6d4;">Ngày tham gia:</strong> <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></p>
                                        <p><strong style="color: #06b6d4;">Đơn hàng:</strong> <?= $c['order_count'] ?></p>
                                        <p><strong style="color: #06b6d4;">Tổng chi tiêu:</strong> <span style="color: #10b981; font-weight: 700;"><?= number_format($c['total_spent']) ?>đ</span></p>
                                        <p><strong style="color: #06b6d4;">Trạng thái:</strong> 
                                            <span class="badge" style="background: <?= ($c['status'] ?? 'active') == 'active' ? '#10b981' : '#ef4444' ?>;">
                                                <?= ($c['status'] ?? 'active') == 'active' ? 'Hoạt động' : 'Khóa' ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit User -->
                        <div class="modal fade" id="editModal<?= $c['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
                                    <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                                        <h5 class="modal-title" style="color: #06b6d4;">Sửa thông tin khách hàng</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body" style="color: #e2e8f0;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" value="<?= $c['username'] ?>" disabled style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #a78bfa;">
                                                <small style="color: #a78bfa;">Không thể chỉnh sửa</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Tên</label>
                                                <input type="text" name="fullname" class="form-control" value="<?= $c['fullname'] ?? '' ?>" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= $c['email'] ?>" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Điện thoại</label>
                                                <input type="text" name="phone" class="form-control" value="<?= $c['phone'] ?? '' ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Số dư</label>
                                                <input type="number" name="balance" class="form-control" value="<?= $c['balance'] ?? 0 ?>" step="0.01" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Trạng thái</label>
                                                <select name="status" class="form-select" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                                    <option value="active" style="color: #000;" <?= ($c['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                                    <option value="locked" style="color: #000;" <?= ($c['status'] ?? 'active') == 'locked' ? 'selected' : '' ?>>Khóa</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="border-top: 1px solid rgba(6, 182, 212, 0.2);">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
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
            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                <i class="fas fa-chevron-left"></i> Trước
            </a>
        </li>

        <?php
        // Hiển thị các số trang
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" style="<?= $i == $page ? 'background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: #06b6d4; color: white;' : 'background: #1e293b; border-color: #06b6d4; color: #06b6d4;' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Nút Next -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                Sau <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2" style="color: #94a3b8; font-size: 0.9rem;">
        Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> khách hàng)
    </div>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>