<?php 
require_once __DIR__ . '/config/db.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</h2>
    <div class="d-flex gap-2">
        <span class="badge" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 8px 16px; font-size: 0.9rem;">
            <i class="fas fa-box"></i> Tổng: <?= $totalRecords ?> đơn
        </span>
    </div>
</div>

<!-- Thanh tìm kiếm và lọc -->
<div class="row mb-3">
    <div class="col-md-4">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Tìm mã đơn, khách hàng, email..." 
                   value="<?= htmlspecialchars($search) ?>" style="font-size: 0.9rem;">
            <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filter_status) ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="col-md-8">
        <div class="d-flex justify-content-end align-items-center gap-2">
            <label class="mb-0" style="color: #94a3b8; white-space: nowrap;"><i class="fas fa-filter"></i> Lọc trạng thái:</label>
            <select class="form-select" id="filterStatus" style="width: auto; font-size: 0.9rem;" onchange="handleFilterChange(this.value)">
                <option value="">Tất cả</option>
                <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>⏳ Chờ xử lý</option>
                <option value="completed" <?= $filter_status == 'completed' ? 'selected' : '' ?>>✓ Hoàn thành</option>
                <option value="cancelled" <?= $filter_status == 'cancelled' ? 'selected' : '' ?>>✗ Đã hủy</option>
                <option value="refunded" <?= $filter_status == 'refunded' ? 'selected' : '' ?>>↩ Hoàn tiền</option>
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
    urlParams.set('page', '1'); // Reset về trang 1 khi thay đổi lọc
    window.location.search = urlParams.toString();
}
</script>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Mã Đơn</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Khách hàng</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Email</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Số SP</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Tổng tiền</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Trạng thái</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Ngày đặt</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 30px; color: #94a3b8;">
                            <i class="fas fa-inbox fa-3x mb-3" style="color: #475569;"></i>
                            <p style="margin: 0; font-size: 1.1rem;">
                                <?= !empty($search) || !empty($filter_status) ? 'Không tìm thấy đơn hàng nào' : 'Không có đơn hàng nào' ?>
                            </p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($orders as $o): ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px; color: #06b6d4; font-weight: 700;">#<?= htmlspecialchars($o['order_number']) ?></td>
                            <td style="padding: 15px; color: #e2e8f0;"><strong><?= htmlspecialchars($o['fullname'] ?? 'N/A') ?></strong></td>
                            <td style="padding: 15px; color: #e2e8f0; font-size: 0.9rem;"><?= htmlspecialchars($o['email'] ?? 'N/A') ?></td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 6px 12px;">
                                    <?= (int)$o['item_count'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px; color: #ec4899; font-weight: 700;"><?= number_format($o['total_amount'], 0, ',', '.') ?>đ</td>
                            <td style="padding: 15px;">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" 
                                        style="width: auto; border-color: <?= $o['status']=='completed'?'#10b981':($o['status']=='pending'?'#f59e0b':'#ef4444') ?>; background: rgba(6, 182, 212, 0.05); color: #e2e8f0;">
                                        <option value="pending" style="color: #000;" <?= $o['status']=='pending'?'selected':'' ?>>⏳ Chờ xử lý</option>
                                        <option value="completed" style="color: #000;" <?= $o['status']=='completed'?'selected':'' ?>>✓ Hoàn thành</option>
                                        <option value="cancelled" style="color: #000;" <?= $o['status']=='cancelled'?'selected':'' ?>>✗ Đã hủy</option>
                                        <option value="refunded" style="color: #000;" <?= $o['status']=='refunded'?'selected':'' ?>>↩ Hoàn tiền</option>
                                    </select>
                                </form>
                            </td>
                            <td style="padding: 15px; color: #e2e8f0; font-size: 0.9rem;"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                            <td style="padding: 15px;">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal<?= $o['id'] ?>" 
                                    style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
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
            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>" 
               style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                <i class="fas fa-chevron-left"></i> Trước
            </a>
        </li>

        <?php
        // Hiển thị các số trang
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>" 
                   style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>" 
                   style="<?= $i == $page ? 'background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: #06b6d4; color: white;' : 'background: #1e293b; border-color: #06b6d4; color: #06b6d4;' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>" 
                   style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Nút Next -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($filter_status) ? '&filter_status=' . urlencode($filter_status) : '' ?>" 
               style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                Sau <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2" style="color: #94a3b8; font-size: 0.9rem;">
        Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> đơn hàng)
    </div>
</nav>
<?php endif; ?>

<!-- Modals Chi tiết Đơn hàng -->
<?php foreach($orders as $o): ?>
<div class="modal fade" id="orderModal<?= $o['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(6, 182, 212, 0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                <h5 class="modal-title" style="color: #06b6d4;"><i class="fas fa-receipt"></i> Chi tiết Đơn hàng #<?= htmlspecialchars($o['order_number']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body" style="color: #e2e8f0;">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong style="color: #06b6d4;">Khách hàng:</strong> <?= htmlspecialchars($o['fullname'] ?? 'N/A') ?></p>
                        <p><strong style="color: #06b6d4;">Email:</strong> <?= htmlspecialchars($o['email'] ?? 'N/A') ?></p>
                        <p><strong style="color: #06b6d4;">Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong style="color: #06b6d4;">Trạng thái thanh toán:</strong>
                            <?php 
                            $payment_status_colors = [
                                'paid' => '#10b981',
                                'unpaid' => '#f59e0b',
                                'failed' => '#ef4444'
                            ];
                            $payment_status_text = [
                                'paid' => 'Đã thanh toán',
                                'unpaid' => 'Chưa thanh toán',
                                'failed' => 'Thanh toán thất bại'
                            ];
                            $ps = $o['payment_status'] ?? 'unpaid';
                            ?>
                            <span class="badge" style="background: <?= $payment_status_colors[$ps] ?? '#6b7280' ?>; padding: 6px 12px;">
                                <?= htmlspecialchars($payment_status_text[$ps] ?? 'Không xác định') ?>
                            </span>
                        </p>
                        <p><strong style="color: #06b6d4;">Phương thức:</strong> <?= htmlspecialchars($o['payment_method'] ?? 'N/A') ?></p>
                    </div>
                </div>
                <hr style="border-color: rgba(6, 182, 212, 0.2);">
                <h6 style="color: #06b6d4; font-weight: 700; margin-bottom: 15px;">📦 Sản phẩm trong đơn hàng:</h6>
                <div class="table-responsive">
                    <table class="table table-sm" style="color: #e2e8f0;">
                        <thead>
                            <tr style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                <th>Sản phẩm</th>
                                <th>Mã Key</th>
                                <th>Giá</th>
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
                                <td colspan="3" style="text-align: center; padding: 20px; color: #64748b;">Không có sản phẩm</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($items as $item): ?>
                                <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><code style="background: rgba(6, 182, 212, 0.1); padding: 4px 8px; border-radius: 4px; color: #06b6d4; font-size: 0.85rem;"><?= htmlspecialchars($item['key_code'] ?? '-') ?></code></td>
                                    <td style="color: #ec4899; font-weight: 700;"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <hr style="border-color: rgba(6, 182, 212, 0.2);">
                <div style="text-align: right;">
                    <p><strong style="color: #06b6d4;">Tổng tiền:</strong> <span style="color: #ec4899; font-weight: 700; font-size: 1.2rem;"><?= number_format($o['total_amount'], 0, ',', '.') ?>đ</span></p>
                    <?php if (isset($o['discount_amount']) && $o['discount_amount'] > 0): ?>
                    <p><strong style="color: #06b6d4;">Giảm giá:</strong> <span style="color: #10b981; font-weight: 700;">-<?= number_format($o['discount_amount'], 0, ',', '.') ?>đ</span></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
