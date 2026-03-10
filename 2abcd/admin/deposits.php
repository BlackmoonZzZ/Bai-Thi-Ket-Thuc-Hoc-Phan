<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/includes/header.php';

require_once __DIR__ . '/../includes/wallet.php';

$wallet = new Wallet();
$message = '';
$messageType = '';

// Handle approve / reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = (int)($_POST['transaction_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($transactionId && $action === 'approve') {
        $result = $wallet->approveDeposit($transactionId, $_SESSION['admin_id'] ?? null);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    } elseif ($transactionId && $action === 'reject') {
        $reason = $_POST['reason'] ?? 'Admin từ chối';
        $result = $wallet->rejectDeposit($transactionId, $reason);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Pagination
$limit = 20;
$pageNum = max(1, (int)($_GET['p'] ?? 1));
$offset = ($pageNum - 1) * $limit;
$statusFilter = $_GET['status'] ?? 'pending';

// Fetch wallet deposit transactions
$validStatuses = ['pending', 'completed', 'rejected', ''];
if (!in_array($statusFilter, $validStatuses)) $statusFilter = 'pending';

$whereSql = "WHERE wt.type = 'deposit'";
$params = [];
if ($statusFilter !== '') {
    $whereSql .= " AND wt.status = ?";
    $params[] = $statusFilter;
}

$countStmt = $conn->prepare("SELECT COUNT(*) FROM wallet_transactions wt $whereSql");
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

$params[] = $limit;
$params[] = $offset;
$stmt = $conn->prepare(
    "SELECT wt.*, u.username, u.email, u.fullname, u.balance as current_balance
     FROM wallet_transactions wt
     LEFT JOIN users u ON wt.user_id = u.id
     $whereSql
     ORDER BY wt.created_at DESC
     LIMIT ? OFFSET ?"
);
$stmt->execute($params);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$stats = $conn->query("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status='completed' THEN amount ELSE 0 END) as approved_total,
    SUM(CASE WHEN status='pending' THEN amount ELSE 0 END) as pending_total
    FROM wallet_transactions WHERE type='deposit'")->fetch(PDO::FETCH_ASSOC);
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
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    height: 100%;
}
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.stat-info {
    flex-grow: 1;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 4px;
}
.stat-label {
    color: var(--text-gray);
    font-size: 0.9rem;
    font-weight: 600;
}
.content-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.deposit-id {
    font-weight: 800;
    color: var(--text-gray);
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
.reference-code {
    background: #F8F9FB;
    padding: 6px 10px;
    border-radius: 6px;
    font-family: monospace;
    font-weight: 700;
    color: var(--primary);
    border: 1px solid #EEF2F7;
}
.amount-text {
    font-weight: 800;
    font-size: 1.1rem;
}
.balance-text {
    font-weight: 700;
    color: var(--text-dark);
}
.status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
}
.status-pending { background: rgba(254, 197, 61, 0.15); color: #FEC53D; }
.status-completed { background: var(--success-bg); color: var(--success-text); }
.status-rejected { background: var(--danger-bg); color: var(--danger-text); }
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    transition: 0.2s;
}
.btn-approve { background: var(--success-bg); color: var(--success-text); }
.btn-approve:hover { background: #E1F9F0; color: #00A65A; }
.btn-reject { background: var(--danger-bg); color: var(--danger-text); }
.btn-reject:hover { background: #FFEDF0; color: #F93C65; }

/* Filter Tabs */
.nav-tabs {
    border-bottom: 2px solid #EEF2F7;
    margin-bottom: 20px;
}
.nav-tabs .nav-link {
    border: none;
    color: var(--text-gray);
    font-weight: 700;
    padding: 12px 20px;
    margin-bottom: -2px;
    background: transparent;
}
.nav-tabs .nav-link:hover {
    color: var(--primary);
}
.nav-tabs .nav-link.active {
    color: var(--primary);
    border-bottom: 2px solid var(--primary);
    background: transparent;
}

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
.form-label {
    font-weight: 700;
    color: var(--text-dark);
}
.form-control {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 12px 15px;
}
.form-control:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title">Deposits Management</h2>
    <?php if (($stats['pending_count'] ?? 0) > 0): ?>
        <div class="badge bg-warning text-dark p-2 px-3 fw-bold rounded-3">
            <i class="fas fa-exclamation-circle me-1"></i> <?= $stats['pending_count'] ?> Pending Requests
        </div>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show fw-bold" role="alert">
        <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(254, 197, 61, 0.15); color: #FEC53D;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($stats['pending_count']) ?> Requests</div>
                <div class="stat-label">Pending Approval (<?= number_format($stats['pending_total']) ?>đ)</div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(0, 182, 155, 0.15); color: #00B69B;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($stats['approved_total']) ?>đ</div>
                <div class="stat-label">Total Approved</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(72, 128, 255, 0.15); color: #4880FF;">
                <i class="fas fa-list-alt"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($stats['total']) ?></div>
                <div class="stat-label">Total Deposit Requests</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs & Table -->
<div class="content-card">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?= $statusFilter === 'pending' ? 'active' : '' ?>" href="?status=pending">
                Pending <span class="badge bg-warning text-dark ms-1 rounded-pill"><?= $stats['pending_count'] ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $statusFilter === 'completed' ? 'active' : '' ?>" href="?status=completed">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $statusFilter === 'rejected' ? 'active' : '' ?>" href="?status=rejected">Rejected</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $statusFilter === '' ? 'active' : '' ?>" href="?status=">All Requests</a>
        </li>
    </ul>

    <div class="table-responsive mt-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Current Balance</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deposits)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x mb-3 text-gray"></i>
                            <p class="text-gray fw-bold fs-5 mb-0">No deposit requests found</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($deposits as $dep): ?>
                        <tr>
                            <td class="deposit-id">#<?= $dep['id'] ?></td>
                            <td>
                                <div class="customer-name"><?= htmlspecialchars($dep['fullname'] ?: $dep['username']) ?></div>
                                <div class="customer-email"><?= htmlspecialchars($dep['email']) ?></div>
                            </td>
                            <td>
                                <span class="reference-code"><?= htmlspecialchars($dep['reference_id'] ?? '—') ?></span>
                            </td>
                            <td class="amount-text text-success">
                                +<?= number_format($dep['amount']) ?>đ
                            </td>
                            <td class="balance-text">
                                <?= number_format($dep['current_balance']) ?>đ
                            </td>
                            <td>
                                <?php if ($dep['status'] === 'pending'): ?>
                                    <span class="status-badge status-pending">Pending</span>
                                <?php elseif ($dep['status'] === 'completed'): ?>
                                    <span class="status-badge status-completed">Approved</span>
                                <?php elseif ($dep['status'] === 'rejected'): ?>
                                    <span class="status-badge status-rejected">Rejected</span>
                                <?php else: ?>
                                    <span class="status-badge bg-secondary text-white"><?= htmlspecialchars($dep['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-gray fw-semibold fs-7">
                                <?= date('M d, Y H:i', strtotime($dep['created_at'])) ?>
                            </td>
                            <td>
                                <?php if ($dep['status'] === 'pending'): ?>
                                    <div class="d-flex gap-2">
                                        <form method="POST" onsubmit="return confirm('Confirm APPROVAL of <?= number_format($dep['amount']) ?>đ for <?= htmlspecialchars($dep['fullname'] ?: $dep['username']) ?>?')">
                                            <input type="hidden" name="transaction_id" value="<?= $dep['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="action-btn btn-approve" title="Approve Request">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <button class="action-btn btn-reject" title="Reject Request"
                                            onclick="showRejectModal(<?= $dep['id'] ?>, '<?= htmlspecialchars(addslashes($dep['fullname'] ?: $dep['username'])) ?>')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray fw-bold fs-7">
                                        <i class="fas <?= $dep['status'] === 'completed' ? 'fa-check text-success' : 'fa-times text-danger' ?> me-1"></i>
                                        <?= $dep['status'] === 'completed' ? 'Processed' : 'Declined' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
            <div class="pagination-info">
                Showing Page <?= $pageNum ?> of <?= $totalPages ?> (<?= $totalRecords ?> total requests)
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?= $pageNum <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?status=<?= urlencode($statusFilter) ?>&p=<?= $pageNum - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = max(1, $pageNum-2); $i <= min($totalPages, $pageNum+2); $i++): ?>
                        <li class="page-item <?= $i === $pageNum ? 'active' : '' ?>">
                            <a class="page-link" href="?status=<?= urlencode($statusFilter) ?>&p=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $pageNum >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?status=<?= urlencode($statusFilter) ?>&p=<?= $pageNum + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-times-circle text-danger me-2"></i> Reject Deposit Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4 text-gray text-center fs-5">Are you sure you want to reject the deposit request for <strong id="rejectUserName" class="text-dark"></strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection (Optional):</label>
                        <textarea name="reason" class="form-control" rows="4" placeholder="Enter reason here..."></textarea>
                    </div>
                    <input type="hidden" name="transaction_id" id="rejectTransactionId">
                    <input type="hidden" name="action" value="reject">
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(id, username) {
    document.getElementById('rejectTransactionId').value = id;
    document.getElementById('rejectUserName').textContent = username;
    var rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    rejectModal.show();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
