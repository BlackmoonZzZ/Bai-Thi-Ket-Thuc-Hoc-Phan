<?php 
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php'; 

// Lấy ID product để edit
$product_id = $_GET['id'] ?? 0;
$product = null;

if ($product_id) {
    $product = $conn->query("SELECT * FROM products WHERE id=$product_id")->fetch();
}

// Xử lý update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $slug = createSlug($name);
        $price = (float)($_POST['price'] ?? 0);
        $discount = (int)($_POST['discount'] ?? 0);
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $plat_id = (int)($_POST['platform_id'] ?? 0);
        $desc = trim($_POST['description'] ?? '');
        $dev = trim($_POST['developer'] ?? '');
        $release = trim($_POST['release_date'] ?? '');
        $status = trim($_POST['status'] ?? 'active');

        // Validation
        if (empty($name)) throw new Exception('Tên game không được để trống!');
        if ($price <= 0) throw new Exception('Giá bán phải lớn hơn 0!');
        if ($cat_id <= 0) throw new Exception('Vui lòng chọn danh mục!');
        if ($plat_id <= 0) throw new Exception('Vui lòng chọn platform!');
        if (empty($desc)) throw new Exception('Mô tả không được để trống!');
        if (empty($dev)) throw new Exception('Nhà phát triển không được để trống!');
        if (empty($release)) throw new Exception('Ngày phát hành không được để trống!');
        
        // Xử lý upload ảnh
        $image = $product['image'] ?? ''; // Mặc định là ảnh cũ nếu có
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($filetype, $allowed)) {
                throw new Exception('Chỉ chấp nhận file JPG, JPEG, PNG, GIF!');
            }
            
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                throw new Exception('Kích thước file không được vượt quá 5MB!');
            }
            
            $newname = time() . '_' . uniqid() . '.' . $filetype;
            $upload_path = __DIR__ . '/uploads/' . $newname;
            
            // Tạo folder uploads nếu chưa có
            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0755, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = 'uploads/' . $newname;
            } else {
                throw new Exception('Không thể upload ảnh. Vui lòng kiểm tra quyền thư mục uploads!');
            }
        }

        // Bắt đầu transaction
        $conn->beginTransaction();

        if ($_POST['mode'] == 'edit' && $product_id) {
            // Update
            $sql = "UPDATE products SET name=?, slug=?, price=?, discount_percent=?, category_id=?, platform_id=?, 
                    image=?, description=?, developer=?, release_date=?, status=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$name, $slug, $price, $discount, $cat_id, $plat_id, $image, $desc, $dev, $release, $status, $product_id]);
            
            if ($result) {
                $conn->commit();
                echo "<script>alert('✓ Cập nhật game thành công!'); window.location='products.php';</script>";
            } else {
                throw new Exception('Không thể cập nhật game!');
            }
        } else {
            // Add new
            $sql = "INSERT INTO products (name, slug, price, discount_percent, category_id, platform_id, image, description, developer, release_date, stock_quantity, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$name, $slug, $price, $discount, $cat_id, $plat_id, $image, $desc, $dev, $release, $status]);
            
            if ($result) {
                $conn->commit();
                echo "<script>alert('✓ Thêm game thành công!'); window.location='products.php';</script>";
            } else {
                throw new Exception('Không thể thêm game mới!');
            }
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $error_msg = htmlspecialchars($e->getMessage());
        echo "<script>alert('❌ Lỗi: {$error_msg}'); history.back();</script>";
    }
}


// Lấy danh mục và Platform
$cats = $conn->query("SELECT * FROM categories")->fetchAll();
$plats = $conn->query("SELECT * FROM platforms")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold" style="color: #06b6d4;"><i class="fas fa-gamepad"></i> <?= $product ? 'Sửa Game' : 'Thêm Game Mới' ?></h4>
    <a href="products.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<div class="row">
    <div class="col-md-9 mx-auto">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="<?= $product ? 'edit' : 'add' ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Tên Game</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: Elden Ring" value="<?= $product['name'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                            <input type="number" name="price" class="form-control" placeholder="199000" value="<?= $product['price'] ?? '' ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giảm giá (%)</label>
                            <input type="number" name="discount" class="form-control" placeholder="0" value="<?= $product['discount_percent'] ?? 0 ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach($cats as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= $cat['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Platform</label>
                            <select name="platform_id" class="form-select" required>
                                <option value="">-- Chọn platform --</option>
                                <?php foreach($plats as $plat): ?>
                                <option value="<?= $plat['id'] ?>" <?= ($product && $product['platform_id'] == $plat['id']) ? 'selected' : '' ?>>
                                    <?= $plat['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ảnh bìa Game</label>
                            <div style="border: 2px dashed rgba(6, 182, 212, 0.4); border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background: rgba(6, 182, 212, 0.05);" id="upload-box">
                                <input type="file" name="image" id="image-input" class="form-control" accept="image/*" style="display: none;">
                                <div id="upload-text">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #06b6d4; margin-bottom: 10px; display: block;"></i>
                                    <p style="margin: 0; color: #e2e8f0;">Kéo ảnh vào đây hoặc <span style="color: #06b6d4; font-weight: 600;">chọn file</span></p>
                                    <small style="color: rgba(226, 232, 240, 0.6);">JPG, PNG, GIF (Max 5MB)</small>
                                </div>
                                <img id="upload-preview" src="" style="display: none; max-width: 100%; max-height: 200px; margin-top: 10px;" alt="Preview">
                            </div>
                            <?php if($product && $product['image']): ?>
                            <div style="margin-top: 10px;">
                                <small style="color: rgba(226, 232, 240, 0.6);">Ảnh hiện tại:</small>
                                <img src="<?= $product['image'] ?>" style="max-width: 100px; max-height: 100px; border-radius: 8px; margin-top: 8px;" alt="Current">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả game..." required><?= $product['description'] ?? '' ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nhà phát triển</label>
                            <input type="text" name="developer" class="form-control" placeholder="Ví dụ: FromSoftware" value="<?= $product['developer'] ?? '' ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày phát hành</label>
                            <input type="date" name="release_date" class="form-control" value="<?= $product['release_date'] ?? '' ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= ($product && $product['status'] == 'active') ? 'selected' : ($product ? '' : 'selected') ?>>Hiện</option>
                                <option value="inactive" <?= ($product && $product['status'] == 'inactive') ? 'selected' : '' ?>>Ẩn</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-grow-1">
                                    <i class="fas fa-check-circle"></i> <?= $product ? 'Cập nhật' : 'Thêm mới' ?>
                                </button>
                                <a href="products.php" class="btn btn-outline-secondary flex-grow-1">Hủy</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Upload ảnh drag and drop
const uploadBox = document.getElementById('upload-box');
const imageInput = document.getElementById('image-input');
const uploadText = document.getElementById('upload-text');
const uploadPreview = document.getElementById('upload-preview');

uploadBox.addEventListener('click', () => imageInput.click());

uploadBox.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadBox.style.borderColor = '#06b6d4';
    uploadBox.style.background = 'rgba(6, 182, 212, 0.1)';
});

uploadBox.addEventListener('dragleave', () => {
    uploadBox.style.borderColor = 'rgba(6, 182, 212, 0.4)';
    uploadBox.style.background = 'rgba(6, 182, 212, 0.05)';
});

uploadBox.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadBox.style.borderColor = 'rgba(6, 182, 212, 0.4)';
    uploadBox.style.background = 'rgba(6, 182, 212, 0.05)';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        previewImage();
    }
});

imageInput.addEventListener('change', previewImage);

function previewImage() {
    const file = imageInput.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            uploadText.style.display = 'none';
            uploadPreview.src = e.target.result;
            uploadPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
