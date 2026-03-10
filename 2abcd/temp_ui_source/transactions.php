<?php 
require_once __DIR__ . '/config/db.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-receipt"></i> Lịch sử Giao dịch</h2>
</div>

<!-- Thống kê -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border: none;">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.9rem;">Tổng giao dịch</p>
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['total_trans']) ?></h3>
                    </div>
                    <i class="fas fa-exchange-alt fa-2x" style="color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.9rem;">Thành công</p>
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['total_completed']) ?>đ</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x" style="color: rgba(255,255,255,0.3);"></i>
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
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['total_pending']) ?>đ</h3>
                    </div>
                    <i class="fas fa-clock fa-2x" style="color: rgba(255,255,255,0.3);"></i>
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
                        <h3 style="color: white; margin: 5px 0; font-weight: 700;"><?= number_format($stats['total_failed']) ?>đ</h3>
                    </div>
                    <i class="fas fa-times-circle fa-2x" style="color: rgba(255,255,255,0.3);"></i>
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
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Mã GD, Username, Email..." value="<?= htmlspecialchars($search) ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        <option value="" style="color: #000;">Tất cả trạng thái</option>
                        <option value="pending" style="color: #000;" <?= $status == 'pending' ? 'selected' : '' ?>>Đang chờ</option>
                        <option value="completed" style="color: #000;" <?= $status == 'completed' ? 'selected' : '' ?>>Thành công</option>
                        <option value="failed" style="color: #000;" <?= $status == 'failed' ? 'selected' : '' ?>>Thất bại</option>
                        <option value="refunded" style="color: #000;" <?= $status == 'refunded' ? 'selected' : '' ?>>Hoàn tiền</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="method" class="form-select" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        <option value="" style="color: #000;">Phương thức</option>
                        <option value="bank_transfer" style="color: #000;" <?= $method == 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
                        <option value="momo" style="color: #000;" <?= $method == 'momo' ? 'selected' : '' ?>>MoMo</option>
                        <option value="zalopay" style="color: #000;" <?= $method == 'zalopay' ? 'selected' : '' ?>>ZaloPay</option>
                        <option value="vnpay" style="color: #000;" <?= $method == 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                        <option value="balance" style="color: #000;" <?= $method == 'balance' ? 'selected' : '' ?>>Số dư</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">MÃ GIAO DỊCH</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">KHÁCH HÀNG</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">ĐƠN HÀNG</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">PHƯƠNG THỨC</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">SỐ TIỀN</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">TRẠNG THÁI</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">THỜI GIAN</th>
                        <th style="color: #06b6d4; font-weight: 700; padding: 15px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($transactions)): ?>
                        <tr>
                            <td colspan="8" style="padding: 30px; text-align: center; color: #94a3b8;">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: #475569; display: block;"></i>
                                <p style="margin: 0; font-size: 1.1rem;">Không tìm thấy giao dịch nào</p>
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
                                'bank_transfer' => 'Chuyển khoản',
                                'momo' => 'MoMo',
                                'zalopay' => 'ZaloPay',
                                'vnpay' => 'VNPay',
                                'balance' => 'Số dư'
                            ];
                            $status_colors = [
                                'pending' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'info'
                            ];
                            $status_text = [
                                'pending' => 'Đang chờ',
                                'completed' => 'Thành công',
                                'failed' => 'Thất bại',
                                'refunded' => 'Hoàn tiền'
                            ];
                        ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px; color: #06b6d4; font-weight: 700; font-family: monospace;">
                                <?= htmlspecialchars($t['transaction_code']) ?>
                            </td>
                            <td style="padding: 15px; color: #e2e8f0;">
                                <div><strong><?= htmlspecialchars($t['fullname'] ?: $t['username']) ?></strong></div>
                                <div style="font-size: 0.85rem; color: #94a3b8;"><?= htmlspecialchars($t['email']) ?></div>
                            </td>
                            <td style="padding: 15px; color: #e2e8f0;">
                                <?php if($t['order_number']): ?>
                                    <a href="orders.php?id=<?= $t['order_id'] ?>" style="color: #06b6d4; text-decoration: none;">
                                        #<?= htmlspecialchars($t['order_number']) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #64748b;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px;">
                                <span style="color: #e2e8f0;">
                                    <i class="fas <?= $method_icons[$t['payment_method']] ?? 'fa-money-bill' ?>"></i>
                                    <?= $method_names[$t['payment_method']] ?? $t['payment_method'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px; color: #10b981; font-weight: 700; font-size: 1.05rem;">
                                <?= number_format($t['amount']) ?>đ
                            </td>
                            <td style="padding: 15px;">
                                <?php
                                $color = $status_colors[$t['status']] ?? 'secondary';
                                $badge_gradient = [
                                    'warning' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                                    'success' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                                    'danger' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                                    'info' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'
                                ];
                                ?>
                                <span class="badge" style="background: <?= $badge_gradient[$color] ?? '#6b7280' ?>; padding: 6px 12px;">
                                    <?= $status_text[$t['status']] ?? $t['status'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px; color: #e2e8f0;">
                                <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                            </td>
                            <td style="padding: 15px;">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $t['id'] ?>" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px;" title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết giao dịch -->
                        <div class="modal fade" id="detailModal<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="color: #06b6d4;">Chi tiết giao dịch</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" style="color: #e2e8f0;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong style="color: #06b6d4;">Mã giao dịch:</strong><br><?= htmlspecialchars($t['transaction_code']) ?></p>
                                                <p><strong style="color: #06b6d4;">Khách hàng:</strong><br><?= htmlspecialchars($t['fullname'] ?: $t['username']) ?></p>
                                                <p><strong style="color: #06b6d4;">Email:</strong><br><?= htmlspecialchars($t['email']) ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong style="color: #06b6d4;">Đơn hàng:</strong><br><?= $t['order_number'] ? '#' . htmlspecialchars($t['order_number']) : 'N/A' ?></p>
                                                <p><strong style="color: #06b6d4;">Phương thức:</strong><br><?= $method_names[$t['payment_method']] ?? $t['payment_method'] ?></p>
                                                <p><strong style="color: #06b6d4;">Số tiền:</strong><br><span style="color: #10b981; font-weight: 700; font-size: 1.2rem;"><?= number_format($t['amount']) ?>đ</span></p>
                                            </div>
                                        </div>
                                        <hr style="border-color: rgba(6, 182, 212, 0.2);">
                                        <p><strong style="color: #06b6d4;">Trạng thái:</strong> 
                                            <span class="badge bg-<?= $color ?>">
                                                <?= $status_text[$t['status']] ?? $t['status'] ?>
                                            </span>
                                        </p>
                                        <p><strong style="color: #06b6d4;">Ngày tạo:</strong> <?= date('d/m/Y H:i:s', strtotime($t['created_at'])) ?></p>
                                        <?php if($t['updated_at'] != $t['created_at']): ?>
                                            <p><strong style="color: #06b6d4;">Cập nhật:</strong> <?= date('d/m/Y H:i:s', strtotime($t['updated_at'])) ?></p>
                                        <?php endif; ?>
                                        <?php if($t['note']): ?>
                                            <hr style="border-color: rgba(6, 182, 212, 0.2);">
                                            <p><strong style="color: #06b6d4;">Ghi chú:</strong><br><?= nl2br(htmlspecialchars($t['note'])) ?></p>
                                        <?php endif; ?>
                                        <?php if($t['transaction_details']): ?>
                                            <hr style="border-color: rgba(6, 182, 212, 0.2);">
                                            <p><strong style="color: #06b6d4;">Chi tiết kỹ thuật:</strong></p>
                                            <pre style="background: rgba(6, 182, 212, 0.05); padding: 10px; border-radius: 6px; color: #94a3b8; font-size: 0.85rem;"><?= htmlspecialchars($t['transaction_details']) ?></pre>
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
            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?><?= !empty($method) ? '&method=' . urlencode($method) : '' ?><?= !empty($date_from) ? '&date_from=' . urlencode($date_from) : '' ?><?= !empty($date_to) ? '&date_to=' . urlencode($date_to) : '' ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                <i class="fas fa-chevron-left"></i> Trước
            </a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?><?= !empty($method) ? '&method=' . urlencode($method) : '' ?><?= !empty($date_from) ? '&date_from=' . urlencode($date_from) : '' ?><?= !empty($date_to) ? '&date_to=' . urlencode($date_to) : '' ?>" style="<?= $i == $page ? 'background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: #06b6d4; color: white;' : 'background: #1e293b; border-color: #06b6d4; color: #06b6d4;' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?><?= !empty($method) ? '&method=' . urlencode($method) : '' ?><?= !empty($date_from) ? '&date_from=' . urlencode($date_from) : '' ?><?= !empty($date_to) ? '&date_to=' . urlencode($date_to) : '' ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                Sau <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2" style="color: #94a3b8; font-size: 0.9rem;">
        Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> giao dịch)
    </div>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>