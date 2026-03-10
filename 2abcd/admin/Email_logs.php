<?php 
require_once __DIR__ . '/../config/db.php';
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
.compact-stat-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
    height: 100%;
}
.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
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

.badge-type { background: rgba(72, 128, 255, 0.15); color: var(--primary); padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
.badge-status { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
.badge-sent { background: rgba(0, 182, 155, 0.15); color: var(--success); }
.badge-failed { background: rgba(249, 60, 101, 0.15); color: var(--danger); }
.badge-pending { background: rgba(254, 197, 61, 0.15); color: var(--warning); }

.btn-action {
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    transition: 0.2s;
}
.btn-view { background: rgba(72, 128, 255, 0.1); color: var(--primary); }
.btn-view:hover { background: var(--primary); color: white; }

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
    <h2 class="page-title"><i class="fas fa-envelope text-primary me-2"></i> Email Logs</h2>
</div>

<!-- Thống kê -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="compact-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-gray fw-bold mb-1" style="font-size: 0.85rem; text-transform: uppercase;">Total Emails</p>
                    <h3 class="text-dark fw-bold mb-0"><?= number_format($stats['total']) ?></h3>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(72, 128, 255, 0.15); color: var(--primary);">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="compact-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-gray fw-bold mb-1" style="font-size: 0.85rem; text-transform: uppercase;">Sent</p>
                    <h3 class="text-dark fw-bold mb-0"><?= number_format($stats['sent']) ?></h3>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(0, 182, 155, 0.15); color: var(--success);">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="compact-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-gray fw-bold mb-1" style="font-size: 0.85rem; text-transform: uppercase;">Failed</p>
                    <h3 class="text-dark fw-bold mb-0"><?= number_format($stats['failed']) ?></h3>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(249, 60, 101, 0.15); color: var(--danger);">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="compact-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-gray fw-bold mb-1" style="font-size: 0.85rem; text-transform: uppercase;">Pending</p>
                    <h3 class="text-dark fw-bold mb-0"><?= number_format($stats['pending']) ?></h3>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(254, 197, 61, 0.15); color: var(--warning);">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-body p-0">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by Email or Subject..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="email_type" class="form-select">
                        <option value="">All Email Types</option>
                        <option value="order_confirmation" <?= $email_type == 'order_confirmation' ? 'selected' : '' ?>>Order Confirmation</option>
                        <option value="key_delivery" <?= $email_type == 'key_delivery' ? 'selected' : '' ?>>Key Delivery</option>
                        <option value="admin_notification" <?= $email_type == 'admin_notification' ? 'selected' : '' ?>>Admin Notification</option>
                        <option value="other" <?= $email_type == 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="sent" <?= $status == 'sent' ? 'selected' : '' ?>>Sent</option>
                        <option value="failed" <?= $status == 'failed' ? 'selected' : '' ?>>Failed</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 h-100 fw-bold">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">RECIPIENT EMAIL</th>
                        <th>SUBJECT</th>
                        <th>TYPE</th>
                        <th>STATUS</th>
                        <th>TIMESTAMP</th>
                        <th class="text-end pe-4">DETAILS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($emails)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-gray fw-bold">
                                <div class="mb-3"><i class="fas fa-inbox fa-3x"></i></div>
                                No email logs found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($emails as $email): 
                            $type_labels = [
                                'order_confirmation' => 'Order Confirmation',
                                'key_delivery' => 'Key Delivery',
                                'admin_notification' => 'Admin Notification',
                                'other' => 'Other'
                            ];
                            $type_icons = [
                                'order_confirmation' => 'fa-shopping-cart',
                                'key_delivery' => 'fa-key',
                                'admin_notification' => 'fa-bell',
                                'other' => 'fa-envelope'
                            ];
                            $status_class = [
                                'sent' => 'badge-sent',
                                'failed' => 'badge-failed',
                                'pending' => 'badge-pending'
                            ];
                            $status_text = [
                                'sent' => 'Sent',
                                'failed' => 'Failed',
                                'pending' => 'Pending'
                            ];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stat-icon-wrapper" style="width: 36px; height: 36px; font-size: 1rem; background: rgba(72, 128, 255, 0.1); color: var(--primary);">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span class="text-dark fw-bold"><?= htmlspecialchars($email['to_email']) ?></span>
                                </div>
                            </td>
                            <td class="text-dark">
                                <?= htmlspecialchars($email['subject']) ?>
                            </td>
                            <td>
                                <span class="badge-type d-inline-block">
                                    <i class="fas <?= $type_icons[$email['email_type']] ?? 'fa-envelope' ?> me-1"></i>
                                    <?= $type_labels[$email['email_type']] ?? $email['email_type'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-status d-inline-block <?= $status_class[$email['status']] ?? 'badge-pending' ?>">
                                    <?= $status_text[$email['status']] ?? $email['status'] ?>
                                </span>
                            </td>
                            <td class="text-gray fw-bold">
                                <?php if($email['sent_at']): ?>
                                    <?= date('d/m/Y H:i', strtotime($email['sent_at'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not Sent</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn-action btn-view" data-bs-toggle="modal" data-bs-target="#detailModal<?= $email['id'] ?>" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết -->
                        <div class="modal fade" id="detailModal<?= $email['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Email Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <p class="mb-1 text-gray fw-bold">Recipient Email:</p>
                                                <p class="text-dark fw-bold fs-6"><?= htmlspecialchars($email['to_email']) ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <p class="mb-1 text-gray fw-bold">Status:</p>
                                                <span class="badge-status d-inline-block <?= $status_class[$email['status']] ?? 'badge-pending' ?>">
                                                    <?= $status_text[$email['status']] ?? $email['status'] ?>
                                                </span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <p class="mb-1 text-gray fw-bold">Email Type:</p>
                                                <span class="badge-type d-inline-block">
                                                    <?= $type_labels[$email['email_type']] ?? $email['email_type'] ?>
                                                </span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <?php if($email['order_id']): ?>
                                                    <p class="mb-1 text-gray fw-bold">Related Order:</p>
                                                    <a href="orders.php?id=<?= $email['order_id'] ?>" class="text-primary fw-bold text-decoration-none border-bottom border-primary">
                                                        #<?= $email['order_id'] ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <hr class="border-light my-3">
                                        
                                        <div class="mb-3">
                                            <p class="mb-1 text-gray fw-bold">Subject:</p>
                                            <p class="text-dark fw-bold"><?= htmlspecialchars($email['subject']) ?></p>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <p class="mb-1 text-gray fw-bold">Created At:</p>
                                                <p class="text-dark"><?= date('d/m/Y H:i:s', strtotime($email['created_at'])) ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <?php if($email['sent_at']): ?>
                                                    <p class="mb-1 text-gray fw-bold">Sent At:</p>
                                                    <p class="text-dark"><?= date('d/m/Y H:i:s', strtotime($email['sent_at'])) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if($email['error_message']): ?>
                                            <hr class="border-light my-3">
                                            <div class="alert alert-danger mb-0 rounded-3 border-0 bg-opacity-10" style="background-color: rgba(249, 60, 101, 0.1);">
                                                <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Error Message:</strong>
                                                <span class="text-danger"><?= nl2br(htmlspecialchars($email['error_message'])) ?></span>
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
        <!-- Nút Previous -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link shadow-sm" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>">
                <i class="fas fa-chevron-left me-1"></i> Prev
            </a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link shadow-sm" href="?page=1&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link shadow-sm">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link shadow-sm" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link shadow-sm">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link shadow-sm" href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Nút Next -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link shadow-sm" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&email_type=<?= urlencode($email_type) ?>&status=<?= urlencode($status) ?>">
                Next <i class="fas fa-chevron-right ms-1"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-3 text-muted fw-bold fs-7">
        Showing Page <?= $page ?> of <?= $totalPages ?> (Total <?= $totalRecords ?> emails)
    </div>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
