<?php
require_once __DIR__ . '/config/db.php';

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

<div class="container mt-4">
	<h2>Quản lý Key cho sản phẩm: <span style="color:#06b6d4; font-weight:bold;"> <?= htmlspecialchars($product['name']) ?> </span></h2>
	<form method="POST" class="mb-4">
		<label>Nhập key mới (mỗi dòng 1 key):</label>
		<textarea name="keys" class="form-control" rows="6" placeholder="KEY-1&#10;KEY-2&#10;KEY-3"></textarea>
		<button type="submit" class="btn btn-success mt-2">Thêm key</button>
	</form>

	<h4>Danh sách key hiện có (<?= count($keys) ?>)</h4>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th>Mã Key</th>
				<th>Trạng thái</th>
				<th>Ngày nhập</th>
				<th>Người mua</th>
				<th>Xóa</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($keys)): ?>
			<tr>
				<td colspan="6" class="text-center">Chưa có key nào</td>
			</tr>
			<?php else: ?>
				<?php foreach ($keys as $k): ?>
				<tr>
					<td><?= htmlspecialchars($k['id']) ?></td>
					<td><?= htmlspecialchars($k['key_code']) ?></td>
					<td><?= $k['status'] === 'available' ? '<span style="color:green">Sẵn sàng</span>' : '<span style="color:gray">Đã bán</span>' ?></td>
					<td><?= isset($k['created_at']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($k['created_at']))) : '' ?></td>
					<td><?= $k['sold_to_user_id'] ? 'User ID: ' . htmlspecialchars($k['sold_to_user_id']) : '-' ?></td>
					<td><a href="?id=<?= $product_id ?>&delete_key=<?= htmlspecialchars($k['id']) ?>" onclick="return confirm('Xóa key này?')" class="btn btn-danger btn-sm">Xóa</a></td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>