<?php 
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_increment_helper.php';

// Xử lý xóa Category/Platform
if (isset($_GET['delete_cat'])) {
    $id = $_GET['delete_cat'];
    $conn->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    resetAutoIncrement($conn, 'categories'); // Reset AUTO_INCREMENT
    header("Location: categories_platforms.php");
    exit();
}

if (isset($_GET['delete_plat'])) {
    $id = $_GET['delete_plat'];
    $conn->prepare("DELETE FROM platforms WHERE id=?")->execute([$id]);
    resetAutoIncrement($conn, 'platforms'); // Reset AUTO_INCREMENT
    header("Location: categories_platforms.php");
    exit();
}

// Xử lý thêm Category
if (isset($_POST['add_category'])) {
    $name = $_POST['cat_name'];
    $slug = createSlug($name);
    $desc = $_POST['cat_desc'];
    $conn->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)")
         ->execute([$name, $slug, $desc]);
    header("Location: ../adminbtl/categories_platforms.php");
    exit();
}

// Xử lý thêm Platform
if (isset($_POST['add_platform'])) {
    $name = $_POST['plat_name'];
    $conn->prepare("INSERT INTO platforms (name) VALUES (?)")
         ->execute([$name]);
    header("Location: ../adminbtl/categories_platforms.php");
    exit();
}

include __DIR__ . '/../adminbtl/includes/header.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
$platforms = $conn->query("SELECT * FROM platforms ORDER BY id DESC")->fetchAll();
?>

<h1 style="color: #06b6d4; font-weight: 700; margin-bottom: 30px;"><i class="fas fa-layer-group"></i> Danh mục & Platform</h1>

<div class="row">
    <!-- Danh mục -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header py-3">
                <h6 style="margin: 0; color: #06b6d4; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                    <i class="fas fa-list"></i> Danh mục Game
                    <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="padding: 4px 8px; font-size: 0.85rem;">
                        <i class="fas fa-plus"></i> Thêm
                    </button>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: transparent;">
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px;">Tên danh mục</th>
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px; text-align: center;">Số game</th>
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categories as $cat): 
                                $count = $conn->query("SELECT COUNT(*) FROM products WHERE category_id={$cat['id']}")->fetchColumn();
                            ?>
                            <tr>
                                <td class="fw-bold"><?= $cat['name'] ?></td>
                                <td><span class="badge bg-info"><?= $count ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $cat['id'] ?>" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete_cat=<?= $cat['id'] ?>" onclick="return confirm('Xóa danh mục này?')" class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal chỉnh sửa danh mục -->
                            <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="edit_cat_id" value="<?= $cat['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Tên danh mục</label>
                                                    <input type="text" name="edit_cat_name" class="form-control" value="<?= $cat['name'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Mô tả</label>
                                                    <textarea name="edit_cat_desc" class="form-control" rows="3"><?= $cat['description'] ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" name="edit_category" class="btn btn-primary">Cập nhật</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header py-3">
                <h6 style="margin: 0; color: #06b6d4; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                    <i class="fas fa-gamepad"></i> Platform
                    <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addPlatformModal" style="padding: 4px 8px; font-size: 0.85rem;">
                        <i class="fas fa-plus"></i> Thêm
                    </button>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: transparent;">
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px;">Platform</th>
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px; text-align: center;">Icon</th>
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px; text-align: center;">Số game</th>
                                <th style="color: #06b6d4; font-weight: 700; padding: 12px; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($platforms as $plat): 
                                $count = $conn->query("SELECT COUNT(*) FROM products WHERE platform_id={$plat['id']}")->fetchColumn();
                            ?>
                            <tr>
                                <td class="fw-bold"><?= $plat['name'] ?></td>
                                <td><i class="fas fa-gamepad"></i></td>
                                <td><span class="badge bg-info"><?= $count ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPlatformModal<?= $plat['id'] ?>" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete_plat=<?= $plat['id'] ?>" onclick="return confirm('Xóa platform này?')" class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal chỉnh sửa platform -->
                            <div class="modal fade" id="editPlatformModal<?= $plat['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Chỉnh sửa platform</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="edit_plat_id" value="<?= $plat['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Tên platform</label>
                                                    <input type="text" name="edit_plat_name" class="form-control" value="<?= $plat['name'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" name="edit_platform" class="btn btn-primary">Cập nhật</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm danh mục -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" name="cat_name" class="form-control" placeholder="Ví dụ: Action" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="cat_desc" class="form-control" rows="3" placeholder="Mô tả danh mục..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="add_category" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thêm platform -->
<div class="modal fade" id="addPlatformModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm platform mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên platform</label>
                        <input type="text" name="plat_name" class="form-control" placeholder="Ví dụ: PC, PS5" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="add_platform" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
