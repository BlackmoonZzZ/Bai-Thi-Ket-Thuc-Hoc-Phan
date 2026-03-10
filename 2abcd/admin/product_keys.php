<?php
require_once __DIR__ . '/../config/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Xử lý xóa key (trước khi include header)
if (isset($_GET['delete_key'])) {
	if ($product_id <= 0) {
		header("Location: products.php");
		exit();
	}
	
	$key_id = intval($_GET['delete_key']);
	$stmt = $conn->prepare("DELETE FROM product_keys WHERE id=? AND product_id=?");
	$stmt->execute([$key_id, $product_id]);
	
	// Cập nhật tồn kho
	$countStmt = $conn->prepare("SELECT COUNT(*) FROM product_keys WHERE product_id=? AND status='available'");
	$countStmt->execute([$product_id]);
	$count = $countStmt->fetchColumn();
	
	$updateStmt = $conn->prepare("UPDATE products SET stock_quantity=? WHERE id=?");
	$updateStmt->execute([$count, $product_id]);
	
	header("Location: product_keys.php?id=$product_id");
	exit();
}

// Xử lý thêm key (trước khi include header)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['keys'])) {
	if ($product_id <= 0) {
		header("Location: products.php");
		exit();
	}
	
	$keys = explode("\n", $_POST['keys']);
	$inserted = 0;
	$duplicates = 0;
	
	foreach ($keys as $key) {
		$key = trim($key);
		if ($key !== '') {
			try {
				$stmt = $conn->prepare("INSERT INTO product_keys (product_id, key_code, status) VALUES (?, ?, 'available')");
				if ($stmt->execute([$product_id, $key])) {
					$inserted++;
				}
			} catch (PDOException $e) {
				if ($e->getCode() == 23000) {
					$duplicates++;
				}
			}
		}
	}
	
	// Cập nhật tồn kho
	$countStmt = $conn->prepare("SELECT COUNT(*) FROM product_keys WHERE product_id=? AND status='available'");
	$countStmt->execute([$product_id]);
	$count = $countStmt->fetchColumn();
	
	$updateStmt = $conn->prepare("UPDATE products SET stock_quantity=? WHERE id=?");
	$updateStmt->execute([$count, $product_id]);
	
	if ($duplicates > 0) {
		$message .= " ($duplicates key bị trùng)";
	}
	$_SESSION['success_message'] = $message;
	
	header("Location: product_keys.php?id=$product_id");
	exit();
}

// Bây giờ mới include header
include __DIR__ . '/includes/header.php';

// Kiểm tra product_id
if ($product_id <= 0) {
	echo '<div class="alert alert-danger">Vui lòng chọn sản phẩm hợp lệ!</div>';
	include __DIR__ . '/includes/footer.php';
	exit();
}

$product = $conn->prepare("SELECT name FROM products WHERE id=?");
$product->execute([$product_id]);
$product = $product->fetch();
if (!$product) {
	echo '<div class="alert alert-danger">Sản phẩm không tồn tại!</div>';
	include __DIR__ . '/includes/footer.php';
	exit();
}

// Lấy danh sách key
$keysStmt = $conn->prepare("SELECT * FROM product_keys WHERE product_id=? ORDER BY id DESC");
$keysStmt->execute([$product_id]);
$keys = $keysStmt->fetchAll();


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
.product-name-highlight {
    color: var(--primary);
}
.content-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
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
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    padding: 10px 20px;
}
.btn-back {
    background: #F8F9FB;
    color: var(--text-dark);
    border: 1px solid #EEF2F7;
    font-weight: 600;
}
.btn-back:hover {
    background: #EEF2F7;
    color: var(--text-dark);
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.key-code {
    background: #F8F9FB;
    padding: 6px 12px;
    border-radius: 6px;
    font-family: monospace;
    font-weight: 700;
    color: var(--primary);
    border: 1px solid #EEF2F7;
}
.status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
}
.status-available {
    background: var(--success-bg);
    color: var(--success-text);
}
.status-sold {
    background: var(--warning-bg);
    color: var(--warning-text);
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
    border: none;
}
.btn-delete { background: #F93C65; }
.btn-delete:hover { background: #E0365B; color: white; }
</style>

<div class="page-header mt-4">
    <div>
        <a href="products.php" class="btn btn-back btn-sm mb-2"><i class="fas fa-arrow-left"></i> Back to Products</a>
        <h2 class="page-title">Manage Product Keys</h2>
        <div class="text-gray mt-1 fw-bold fs-5">Product: <span class="product-name-highlight"><?= htmlspecialchars($product['name']) ?></span></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="content-card mb-4 mb-lg-0">
            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-plus-circle text-primary me-2"></i> Add New Keys</h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-gray">Enter keys (one per line):</label>
                    <textarea name="keys" class="form-control" rows="8" placeholder="XXXX-XXXX-XXXX-XXXX&#10;YYYY-YYYY-YYYY-YYYY&#10;ZZZZ-ZZZZ-ZZZZ-ZZZZ"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 justify-content-center">
                    <i class="fas fa-save"></i> Save Keys
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-key text-primary me-2"></i> Current Keys</h5>
                <span class="badge bg-primary text-white p-2 px-3 fw-bold rounded-3 h6 mb-0">Total: <?= count($keys) ?> Keys</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Key ID</th>
                            <th>Key Code</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Buyer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($keys)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x mb-3 text-gray"></i>
                                <p class="text-gray fw-bold fs-5 mb-0">No keys have been added yet</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($keys as $k): ?>
                            <tr>
                                <td class="fw-bold text-gray">#<?= htmlspecialchars($k['id']) ?></td>
                                <td><span class="key-code"><?= htmlspecialchars($k['key_code']) ?></span></td>
                                <td>
                                    <?php if ($k['status'] === 'available'): ?>
                                        <span class="status-badge status-available">Available</span>
                                    <?php else: ?>
                                        <span class="status-badge status-sold">Sold</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-gray fw-semibold fs-7"><?= isset($k['created_at']) ? htmlspecialchars(date('M d, Y H:i', strtotime($k['created_at']))) : '-' ?></td>
                                <td>
                                    <?php if ($k['sold_to_user_id']): ?>
                                        <span class="badge bg-secondary text-white">User ID: <?= htmlspecialchars($k['sold_to_user_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?id=<?= $product_id ?>&delete_key=<?= htmlspecialchars($k['id']) ?>" onclick="return confirm('Are you sure you want to delete this key?')" class="action-btn btn-delete" title="Delete Key">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
