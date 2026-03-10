<?php 
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php'; 

// Phân trang
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Lọc
$email_type = $_GET['email_type'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM email_logs WHERE 1=1";
$params = [];

if (!empty($email_type)) {
    $sql .= " AND email_type = :email_type";
    $params[':email_type'] = $email_type;
}

if (!empty($status)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

if (!empty($search)) {
    $sql .= " AND (to_email LIKE :search_to_email OR subject LIKE :search_subject)";
    $params[':search_to_email'] = "%$search%";
    $params[':search_subject'] = "%$search%";
}

// Đếm tổng
$countSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();

// Lấy dữ liệu
$sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$emails = $stmt->fetchAll();

$totalPages = ceil($totalRecords / $limit);

// Thống kê
$stats = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='sent' THEN 1 ELSE 0 END) as sent,
    SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending
    FROM email_logs")->fetch();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-envelope"></i> Lịch sử Email</h2>
</div>

<!-- Thống kê -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border: none;">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.9rem;">Tổng email</p>
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['total']) ?></h3>
                    </div>
                    <i class="fas fa-envelope fa-2x" style="color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.9rem;">Đã gửi</p>
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['sent']) ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x" style="color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.9rem;">Thất bại</p>
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['failed']) ?></h3>
                    </div>
                    <i class="fas fa-times-circle fa-2x" style="color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.9rem;">Đang chờ</p>
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['pending']) ?></h3>
                    </div>
                    <i class="fas fa-clock fa-2x" style="color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Email, Subject..." value="<?= htmlspecialchars($search) ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                </div>
                <div class="col-md-3">
                    <select name="email_type" class="form-select" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        <option value="" style="color: #000;">Tất cả loại email</option>
                        <option value="order_confirmation" style="color: #000;" <?= $email_type == 'order_confirmation' ? 'selected' : '' ?>>Xác nhận đơn hàng</option>
                        <option value="key_delivery" style="color: #000;" <?= $email_type == 'key_delivery' ? 'selected' : '' ?>>Gửi Keys</option>
                        <option value="admin_notification" style="color: #000;" <?= $email_type == 'admin_notification' ? 'selected' : '' ?>>Thông báo Admin</option>
                        <option value="other" style="color: #000;" <?= $email_type == 'other' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        <option value="" style="color: #000;">Trạng thái</option>
                        <option value="sent" style="color: #000;" <?= $status == 'sent' ? 'selected' : '' ?>>Đã gửi</option>
                        <option value="failed" style="color: #000;" <?= $status == 'failed' ? 'selected' : '' ?>>Thất bại</option>
                        <option value="pending" style="color: #000;" <?= $status == 'pending' ? 'selected' : '' ?>>Đang chờ</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">EMAIL NHẬN</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">TIÊU ĐỀ</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">LOẠI</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">TRẠNG THÁI</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">THỜI GIAN</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">CHI TIẾT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($emails)): ?>
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8;">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: #475569; display: block;"></i>
                                <p style="margin: 0; font-size: 1.1rem;">Chưa có email nào</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($emails as $email): 
                            $type_labels = [
                                'order_confirmation' => 'Xác nhận ĐH',
                                'key_delivery' => 'Gửi Keys',
                                'admin_notification' => 'Thông báo Admin',
                                'other' => 'Khác'
                            ];
                            $type_icons = [
                                'order_confirmation' => 'fa-shopping-cart',
                                'key_delivery' => 'fa-key',
                                'admin_notification' => 'fa-bell',
                                'other' => 'fa-envelope'
                            ];
                        ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px; color: #e2e8f0;">
                                <i class="fas fa-envelope" style="color: #06b6d4;"></i>
                                <?= htmlspecialchars($email['to_email']) ?>
                            </td>
                            <td style="padding: 15px; color: #e2e8f0;">
                                <?= htmlspecialchars($email['subject']) ?>
                            </td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: rgba(6, 182, 212, 0.2); color: #06b6d4; padding: 6px 12px;">
                                    <i class="fas <?= $type_icons[$email['email_type']] ?? 'fa-envelope' ?>"></i>
                                    <?= $type_labels[$email['email_type']] ?? $email['email_type'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <?php
                                $status_colors = [
                                    'sent' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                                    'failed' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                                    'pending' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'
                                ];
                                $status_text = [
                                    'sent' => 'Đã gửi',
                                    'failed' => 'Thất bại',
                                    'pending' => 'Đang chờ'
                                ];
                                ?>
                                <span class="badge" style="background: <?= $status_colors[$email['status']] ?? '#6b7280' ?>; padding: 6px 12px;">
                                    <?= $status_text[$email['status']] ?? $email['status'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px; color: #e2e8f0;">
                                <?php if($email['sent_at']): ?>
                                    <?= date('d/m/Y H:i', strtotime($email['sent_at'])) ?>
                                <?php else: ?>
                                    <span style="color: #64748b;">Chưa gửi</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px;">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $email['id'] ?>" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết -->
                        <div class="modal fade" id="detailModal<?= $email['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="color: #06b6d4;">Chi tiết Email</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" style="color: #e2e8f0;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong style="color: #06b6d4;">Email nhận:</strong><br><?= htmlspecialchars($email['to_email']) ?></p>
                                                <p><strong style="color: #06b6d4;">Loại email:</strong><br>
                                                    <span class="badge" style="background: rgba(6, 182, 212, 0.2); color: #06b6d4;">
                                                        <?= $type_labels[$email['email_type']] ?? $email['email_type'] ?>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong style="color: #06b6d4;">Trạng thái:</strong><br>
                                                    <span class="badge" style="background: <?= $status_colors[$email['status']] ?? '#6b7280' ?>;">
                                                        <?= $status_text[$email['status']] ?? $email['status'] ?>
                                                    </span>
                                                </p>
                                                <?php if($email['order_id']): ?>
                                                    <p><strong style="color: #06b6d4;">Đơn hàng:</strong><br>
                                                        <a href="orders.php?id=<?= $email['order_id'] ?>" style="color: #06b6d4;">
                                                            #<?= $email['order_id'] ?>
                                                        </a>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <hr style="border-color: rgba(6, 182, 212, 0.2);">
                                        <p><strong style="color: #06b6d4;">Tiêu đề:</strong><br><?= htmlspecialchars($email['subject']) ?></p>
                                        <p><strong style="color: #06b6d4;">Thời gian tạo:</strong> <?= date('d/m/Y H:i:s', strtotime($email['created_at'])) ?></p>
                                        <?php if($email['sent_at']): ?>
                                            <p><strong style="color: #06b6d4;">Thời gian gửi:</strong> <?= date('d/m/Y H:i:s', strtotime($email['sent_at'])) ?></p>
                                        <?php endif; ?>
                                        <?php if($email['error_message']): ?>
                                            <hr style="border-color: rgba(6, 182, 212, 0.2);">
                                            <div class="alert alert-danger">
                                                <strong><i class="fas fa-exclamation-triangle"></i> Lỗi:</strong><br>
                                                <?= nl2br(htmlspecialchars($email['error_message'])) ?>
                                            </div>
                                        <?php endif; ?>
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
            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                <i class="fas fa-chevron-left"></i> Trước
            </a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>" style="<?= $i == $page ? 'background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: #06b6d4; color: white;' : 'background: #1e293b; border-color: #06b6d4; color: #06b6d4;' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                Sau <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2" style="color: #94a3b8; font-size: 0.9rem;">
        Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> email)
    </div>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>