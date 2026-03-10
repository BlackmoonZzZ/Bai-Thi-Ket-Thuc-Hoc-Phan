<?php 
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_increment_helper.php';
include __DIR__ . '/includes/header.php'; 

// Xử lý Xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    resetAutoIncrement($conn, 'products'); // Reset AUTO_INCREMENT
    echo "<script>window.location='products.php'</script>";
}

// Phân trang
$limit = 5; // Số game mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Đảm bảo page >= 1
$offset = ($page - 1) * $limit;

// Lấy tham số tìm kiếm và sắp xếp
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest'; // Mặc định sắp xếp theo mới nhất

// Xác định cột và thứ tự sắp xếp
$orderBy = "p.id DESC"; // Mặc định
switch($sort) {
    case 'name_asc':
        $orderBy = "p.name ASC";
        break;
    case 'name_desc':
        $orderBy = "p.name DESC";
        break;
    case 'newest':
        $orderBy = "p.id DESC";
        break;
    case 'oldest':
        $orderBy = "p.id ASC";
        break;
    case 'stock_high':
        $orderBy = "p.stock_quantity DESC";
        break;
    case 'stock_low':
        $orderBy = "p.stock_quantity ASC";
        break;
    case 'sold_high':
        $orderBy = "sold_quantity DESC";
        break;
    case 'sold_low':
        $orderBy = "sold_quantity ASC";
        break;
    case 'price_high':
        $orderBy = "p.price DESC";
        break;
    case 'price_low':
        $orderBy = "p.price ASC";
        break;
}


// Lấy danh sách game
$sql = "SELECT p.*, c.name as cat_name, COALESCE(s.sold_quantity, 0) as sold_quantity
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN (
            SELECT oi.product_id, COUNT(*) as sold_quantity
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE o.status = 'completed'
            GROUP BY oi.product_id
        ) s ON s.product_id = p.id";
$countSql = "SELECT COUNT(*) FROM products p";

if (!empty($search)) {
    $whereClause = " WHERE p.name LIKE :search_name OR p.developer LIKE :search_developer";
    $sql .= $whereClause;
    $countSql .= $whereClause;
    
    // Đếm tổng số bản ghi
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute([
        ':search_name' => "%$search%",
        ':search_developer' => "%$search%"
    ]);
    $totalRecords = $stmtCount->fetchColumn();
    
    // Lấy dữ liệu phân trang
    $stmt = $conn->prepare($sql . " ORDER BY $orderBy LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search_name', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search_developer', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();
} else {
    // Đếm tổng số bản ghi
    $totalRecords = $conn->query($countSql)->fetchColumn();
    
    // Lấy dữ liệu phân trang
    $stmt = $conn->prepare($sql . " ORDER BY $orderBy LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();
}

// Tính tổng số trang
$totalPages = ceil($totalRecords / $limit);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-box"></i> Quản lý Sản phẩm</h2>
    <a href="product_form.php" class="btn btn-primary" style="font-weight: 600; text-transform: uppercase;"><i class="fas fa-plus"></i> Thêm Game mới</a>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Tìm game..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="font-size: 0.9rem;">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="col-md-8">
        <div class="d-flex justify-content-end align-items-center gap-2">
            <label class="mb-0" style="color: #94a3b8; white-space: nowrap;"><i class="fas fa-sort"></i> Sắp xếp:</label>
            <select class="form-select" id="sortSelect" style="width: auto; font-size: 0.9rem;" onchange="handleSortChange(this.value)">
                <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                <option value="stock_high" <?= $sort == 'stock_high' ? 'selected' : '' ?>>Tồn kho: Nhiều → Ít</option>
                <option value="stock_low" <?= $sort == 'stock_low' ? 'selected' : '' ?>>Tồn kho: Ít → Nhiều</option>
                <option value="sold_high" <?= $sort == 'sold_high' ? 'selected' : '' ?>>Đã bán: Nhiều → Ít</option>
                <option value="sold_low" <?= $sort == 'sold_low' ? 'selected' : '' ?>>Đã bán: Ít → Nhiều</option>
                <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Giá: Cao → Thấp</option>
                <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Giá: Thấp → Cao</option>
            </select>
        </div>
    </div>
</div>

<script>
function handleSortChange(sortValue) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortValue);
    urlParams.set('page', '1'); // Reset về trang 1 khi thay đổi sắp xếp
    window.location.search = urlParams.toString();
}
</script>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">GAME</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">GIÁ BÁN</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">DANH MỤC</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TỒN KHO (KEY)</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">ĐÃ BÁN</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TRẠNG THÁI</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 30px; color: #94a3b8;">
                            <i class="fas fa-inbox fa-3x mb-3" style="color: #475569;"></i>
                            <p style="margin: 0; font-size: 1.1rem;">Không tìm thấy game nào</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($products as $p): ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="<?= $p['image'] ?>" width="50" height="50" style="border-radius: 6px; object-fit: cover; border: 1px solid rgba(6, 182, 212, 0.2);" alt="<?= $p['name'] ?>">
                                    <strong style="color: #e2e8f0;"><?= $p['name'] ?></strong>
                                </div>
                            </td>
                            <td style="padding: 15px; color: #ec4899; font-weight: 700; font-size: 1.1rem;"><?= number_format($p['price']) ?>đ</td>
                            <td style="padding: 15px;"><span class="badge" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 6px 12px;"><?= $p['cat_name'] ?? 'N/A' ?></span></td>
                            <td style="padding: 15px;">
                                <a href="product_keys.php?id=<?= $p['id'] ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border: 1px solid <?= $p['stock_quantity'] > 0 ? '#10b981' : '#ef4444' ?>; color: <?= $p['stock_quantity'] > 0 ? '#10b981' : '#ef4444' ?>; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;" onmouseover="this.style.background='<?= $p['stock_quantity'] > 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' ?>';" onmouseout="this.style.background='transparent';">
                                    <i class="fas fa-key"></i> <?= $p['stock_quantity'] ?> Key
                                </a>
                            </td>
                            <td style="padding: 15px;">
                                <span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: rgba(168, 85, 247, 0.1); color: #a855f7; border-radius: 6px; font-weight: 600; font-size: 0.9rem;">
                                    <i class="fas fa-chart-line"></i> <?= $p['sold_quantity'] ?? 0 ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: <?= $p['status']=='active' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : '#6b7280' ?>; padding: 6px 12px;">
                                    <?= $p['status']=='active' ? 'HIỆN' : 'ẨN' ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <div style="display: flex; gap: 6px;">
                                    <a href="product_form.php?id=<?= $p['id'] ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Sửa"><i class="fas fa-edit"></i></a>
                                    <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Xóa game này?')" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Xóa"><i class="fas fa-trash"></i></a>
                                </div>
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
            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                <i class="fas fa-chevron-left"></i> Trước
            </a>
        </li>

        <?php
        // Hiển thị các số trang
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>" style="<?= $i == $page ? 'background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: #06b6d4; color: white;' : 'background: #1e293b; border-color: #06b6d4; color: #06b6d4;' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link" style="background: #1e293b; border-color: #06b6d4; color: #64748b;">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Nút Next -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>" style="background: #1e293b; border-color: #06b6d4; color: #06b6d4;">
                Sau <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center mt-2" style="color: #94a3b8; font-size: 0.9rem;">
        Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> game)
    </div>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>