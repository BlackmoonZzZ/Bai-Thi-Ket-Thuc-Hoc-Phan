<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auto_increment_helper.php';
include __DIR__ . '/includes/header.php'; 

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = createSlug($name); // Tạo slug tự động
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $cat_id = $_POST['category_id'];
    $plat_id = $_POST['platform_id'];
    $image = $_POST['image']; // Ở đây demo nhập URL ảnh cho nhanh
    $desc = $_POST['description'];
    $dev = $_POST['developer'];
    $release = $_POST['release_date'];

    $sql = "INSERT INTO products (name, slug, price, discount_percent, category_id, platform_id, image, description, developer, release_date, stock_quantity, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'active')";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$name, $slug, $price, $discount, $cat_id, $plat_id, $image, $desc, $dev, $release])) {
        echo "<script>alert('Thêm game thành công!'); window.location='products.php';</script>";
    } else {
        echo "<script>alert('Lỗi!');</script>";
    }
}

// Lấy danh mục và Platform để hiển thị select option
$cats = $conn->query("SELECT * FROM categories")->fetchAll();
$plats = $conn->query("SELECT * FROM platforms")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Thêm Game Mới</h4>
    <a href="products.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<div class="row">
    <div class="col-md-9 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Tên Game</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: Elden Ring" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá gốc (VND)</label>
                            <input type="number" name="price" class="form-control" placeholder="1000000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giảm giá (%)</label>
                            <input type="number" name="discount" class="form-control" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thể loại</label>
                            <select name="category_id" class="form-select">
                                <?php foreach($cats as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nền tảng</label>
                            <select name="platform_id" class="form-select">
                                <?php foreach($plats as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nhà phát triển</label>
                            <input type="text" name="developer" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày phát hành</label>
                            <input type="date" name="release_date" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Link Ảnh Bìa (URL)</label>
                            <input type="url" name="image" class="form-control" placeholder="https://..." required>
                            <div class="form-text">Bạn có thể copy link ảnh từ Steam để test nhanh.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Lưu Sản Phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
