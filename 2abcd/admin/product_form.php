<?php 
require_once __DIR__ . '/../config/db.php';
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
.content-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
}
.form-label {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 8px;
}
.form-control, .form-select {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 12px 15px;
    color: var(--text-dark);
    transition: all 0.2s ease;
}
.form-control:focus, .form-select:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
.upload-box {
    border: 2px dashed #EEF2F7;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    background: #F8F9FB;
    transition: all 0.2s ease;
}
.upload-box:hover, .upload-box.dragover {
    border-color: var(--primary);
    background: rgba(72, 128, 255, 0.05);
}
.upload-icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 12px;
}
.btn-primary {
    padding: 12px 25px;
    font-weight: 700;
    border-radius: 10px;
}
.btn-secondary {
    background: #F8F9FB;
    color: var(--text-gray);
    border: none;
    font-weight: 600;
}
.btn-secondary:hover {
    background: #EEF2F7;
    color: var(--text-dark);
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title"><i class="fas fa-gamepad text-primary me-2"></i> <?= $product ? 'Edit Game' : 'Add New Game' ?></h2>
    <a href="products.php" class="btn btn-secondary px-4 py-2"><i class="fas fa-arrow-left me-2"></i> Back to Products</a>
</div>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="<?= $product ? 'edit' : 'add' ?>">
                
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">Game Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Elden Ring" value="<?= $product['name'] ?? '' ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Price (VNĐ)</label>
                        <input type="number" name="price" class="form-control" placeholder="199000" value="<?= $product['price'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Discount (%)</label>
                        <input type="number" name="discount" class="form-control" placeholder="0" value="<?= $product['discount_percent'] ?? 0 ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach($cats as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= $cat['name'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Platform</label>
                        <select name="platform_id" class="form-select" required>
                            <option value="">-- Select Platform --</option>
                            <?php foreach($plats as $plat): ?>
                            <option value="<?= $plat['id'] ?>" <?= ($product && $product['platform_id'] == $plat['id']) ? 'selected' : '' ?>>
                                <?= $plat['name'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Game Cover Image</label>
                        <div class="upload-box" id="upload-box">
                            <input type="file" name="image" id="image-input" class="form-control" accept="image/*" style="display: none;">
                            <div id="upload-text">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <p class="text-dark fw-bold mb-1">Drag and drop your image here or <span class="text-primary">browse</span></p>
                                <small class="text-muted">Supported formats: JPG, PNG, GIF (Max 5MB)</small>
                            </div>
                            <img id="upload-preview" src="" style="display: none; max-width: 100%; max-height: 250px; margin-top: 15px; border-radius: 8px;" alt="Preview">
                        </div>
                        <?php if($product && $product['image']): ?>
                        <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <div>
                                <small class="text-muted d-block fw-bold mb-1">Current Image:</small>
                                <img src="<?= $product['image'] ?>" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" alt="Current">
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Enter game description..." required><?= $product['description'] ?? '' ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Developer/Publisher</label>
                        <input type="text" name="developer" class="form-control" placeholder="e.g. FromSoftware" value="<?= $product['developer'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Release Date</label>
                        <input type="date" name="release_date" class="form-control" value="<?= $product['release_date'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active" <?= ($product && $product['status'] == 'active') ? 'selected' : ($product ? '' : 'selected') ?>>Active (Visible)</option>
                            <option value="inactive" <?= ($product && $product['status'] == 'inactive') ? 'selected' : '' ?>>Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-4 pt-3 border-top">
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="products.php" class="btn btn-secondary px-4 py-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-save me-2"></i> <?= $product ? 'Save Changes' : 'Add Game' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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

