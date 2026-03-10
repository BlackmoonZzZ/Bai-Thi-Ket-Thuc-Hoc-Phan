<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auto_increment_helper.php';
include __DIR__ . '/includes/header.php'; 

/** @var PDO $conn */

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
    $totalRecords = (int)$stmtCount->fetchColumn();
    
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
    $stmtCountAll = $conn->query($countSql);
    $totalRecords = $stmtCountAll ? (int)$stmtCountAll->fetchColumn() : 0;
    
    // Lấy dữ liệu phân trang
    $stmt = $conn->prepare($sql . " ORDER BY $orderBy LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();
}

// Tính tổng số trang
$totalPages = (int)ceil($totalRecords / max(1, $limit));
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
.btn-primary {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    padding: 10px 20px;
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
.filter-label {
    font-weight: 700;
    color: var(--text-dark);
    margin-right: 12px;
}
.filter-select {
    min-width: 200px;
}
.products-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.game-info {
    display: flex;
    align-items: center;
    gap: 15px;
}
.game-img {
    width: 60px;
    height: 45px;
    border-radius: 8px;
    object-fit: cover;
}
.game-name {
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
    font-size: 0.95rem;
}
.price-tag {
    font-weight: 800;
    color: var(--primary);
    font-size: 1rem;
}
.cat-badge {
    background: rgba(72, 128, 255, 0.1);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.8rem;
}
.stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
}
.stock-in { background: var(--success-bg); color: var(--success-text); }
.stock-out { background: var(--warning-bg); color: var(--warning-text); }
.sold-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(130, 128, 255, 0.1);
    color: #8280FF;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
}
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
}
.btn-edit { background: #FEC53D; }
.btn-edit:hover { background: #E5B137; color: white; }
.btn-delete { background: #F93C65; }
.btn-delete:hover { background: #E0365B; color: white; }

/* Pagination */
.pagination {
    margin-bottom: 0;
}
.page-link {
    border: none;
    color: var(--text-gray);
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
    margin: 0 4px;
    background: transparent;
}
.page-link:hover {
    background: #F5F6FA;
    color: var(--text-dark);
}
.page-item.active .page-link {
    background: var(--primary);
    color: white;
}
.page-item.disabled .page-link {
    color: #CBD5E1;
    background: transparent;
}
.pagination-info {
    color: var(--text-gray);
    font-weight: 600;
    font-size: 0.9rem;
}
</style>

<div class="page-header">
    <h2 class="page-title">Products List</h2>
    <a href="product_form.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<div class="controls-card">
    <div class="row align-items-center g-3">
        <div class="col-lg-5">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="form-control search-input" placeholder="Search product name or developer..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="col-lg-7 text-lg-end d-flex align-items-center justify-content-lg-end">
            <label class="filter-label"><i class="fas fa-sort amount-down"></i> Sort By:</label>
            <select class="form-select filter-select d-inline-block w-auto" id="sortSelect" onchange="handleSortChange(this.value)">
                <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                <option value="stock_high" <?= $sort == 'stock_high' ? 'selected' : '' ?>>Kho: Cao nhất</option>
                <option value="stock_low" <?= $sort == 'stock_low' ? 'selected' : '' ?>>Kho: Thấp nhất</option>
                <option value="sold_high" <?= $sort == 'sold_high' ? 'selected' : '' ?>>Bán chạy nhất</option>
                <option value="sold_low" <?= $sort == 'sold_low' ? 'selected' : '' ?>>Bán chậm nhất</option>
                <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Giá: Cao nhất</option>
                <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Giá: Thấp nhất</option>
            </select>
        </div>
    </div>
</div>

<script>
function handleSortChange(sortValue) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortValue);
    urlParams.set('page', '1');
    window.location.search = urlParams.toString();
}
</script>

<div class="products-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product Details</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock (Keys)</th>
                    <th>Total Sold</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($products)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-box-open fa-3x mb-3 text-gray"></i>
                        <p class="text-gray fw-bold fs-5 mb-0">No products found matching your criteria</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td>
                            <div class="game-info">
                                <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="game-img">
                                <div>
                                    <h4 class="game-name"><?= htmlspecialchars($p['name']) ?></h4>
                                    <div class="text-gray" style="font-size: 0.8rem; font-weight: 600;">ID: #<?= $p['id'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="price-tag">$<?= number_format($p['price']) ?></td>
                        <td>
                            <span class="cat-badge"><?= htmlspecialchars($p['cat_name'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <a href="product_keys.php?id=<?= $p['id'] ?>" class="stock-badge <?= $p['stock_quantity'] > 0 ? 'stock-in' : 'stock-out' ?>">
                                <i class="fas fa-key"></i> <?= $p['stock_quantity'] ?> Keys
                            </a>
                        </td>
                        <td>
                            <span class="sold-badge">
                                <i class="fas fa-chart-line"></i> <?= $p['sold_quantity'] ?? 0 ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $p['status'] == 'active' ? 'badge-completed' : 'bg-secondary' ?>">
                                <?= $p['status'] == 'active' ? 'Active' : 'Hidden' ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="product_form.php?id=<?= $p['id'] ?>" class="action-btn btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="action-btn btn-delete" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if($totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            Showing Page <?= $page ?> of <?= $totalPages ?> (<?= $totalRecords ?> total products)
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
