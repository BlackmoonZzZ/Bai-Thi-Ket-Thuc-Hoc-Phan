<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auto_increment_helper.php';

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
    header("Location: categories_platforms.php");
    exit();
}

// Xử lý thêm Platform
if (isset($_POST['add_platform'])) {
    $name = $_POST['plat_name'];
    $conn->prepare("INSERT INTO platforms (name) VALUES (?)")
         ->execute([$name]);
    header("Location: categories_platforms.php");
    exit();
}

include __DIR__ . '/includes/header.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
$platforms = $conn->query("SELECT * FROM platforms ORDER BY id DESC")->fetchAll();
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
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    height: 100%;
}
.card-header-styled {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #EEF2F7;
}
.card-title-styled {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.table th {
    color: var(--text-gray);
    font-weight: 700;
    border-bottom: 2px solid #EEF2F7;
    padding: 12px 15px;
}
.table td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #EEF2F7;
}
.item-name {
    font-weight: 700;
    color: var(--text-dark);
}
.btn-action {
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    transition: 0.2s;
}
.btn-edit { background: rgba(254, 197, 61, 0.15); color: #FEC53D; }
.btn-edit:hover { background: #FEC53D; color: white; }
.btn-delete { background: var(--danger-bg); color: var(--danger-text); }
.btn-delete:hover { background: #F93C65; color: white; }
.btn-add {
    background: var(--primary);
    color: white;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    transition: 0.2s;
}
.btn-add:hover {
    background: #3661CC;
    color: white;
}

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
    <h2 class="page-title"><i class="fas fa-layer-group me-2" style="color: var(--primary);"></i> Categories & Platforms</h2>
</div>

<div class="row">
    <!-- Danh mục -->
    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="card-header-styled">
                <h3 class="card-title-styled"><i class="fas fa-list me-2 text-primary"></i> Game Categories</h3>
                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus me-1"></i> Add New
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th class="text-center">Total Games</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): 
                            $count = $conn->query("SELECT COUNT(*) FROM products WHERE category_id={$cat['id']}")->fetchColumn();
                        ?>
                        <tr>
                            <td class="item-name"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?= $count ?></span>
                            </td>
                            <td class="text-end">
                                <button class="btn-action btn-edit me-1" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $cat['id'] ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_cat=<?= $cat['id'] ?>" onclick="return confirm('Are you sure you want to delete this category?')" class="btn-action btn-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal chỉnh sửa danh mục -->
                        <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="edit_cat_id" value="<?= $cat['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Category Name</label>
                                                <input type="text" name="edit_cat_name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="edit_cat_desc" class="form-control" rows="3"><?= htmlspecialchars($cat['description']) ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="edit_category" class="btn btn-primary fw-bold px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($categories)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray fw-bold">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Platform -->
    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="card-header-styled">
                <h3 class="card-title-styled"><i class="fas fa-gamepad me-2 text-primary"></i> Platforms</h3>
                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addPlatformModal">
                    <i class="fas fa-plus me-1"></i> Add New
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Platform Name</th>
                            <th class="text-center">Total Games</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($platforms as $plat): 
                            $count = $conn->query("SELECT COUNT(*) FROM products WHERE platform_id={$plat['id']}")->fetchColumn();
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-gray-100 rounded p-2 text-gray">
                                        <i class="fas fa-gamepad"></i>
                                    </div>
                                    <span class="item-name"><?= htmlspecialchars($plat['name']) ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?= $count ?></span>
                            </td>
                            <td class="text-end">
                                <button class="btn-action btn-edit me-1" data-bs-toggle="modal" data-bs-target="#editPlatformModal<?= $plat['id'] ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_plat=<?= $plat['id'] ?>" onclick="return confirm('Are you sure you want to delete this platform?')" class="btn-action btn-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal chỉnh sửa platform -->
                        <div class="modal fade" id="editPlatformModal<?= $plat['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Platform</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="edit_plat_id" value="<?= $plat['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Platform Name</label>
                                                <input type="text" name="edit_plat_name" class="form-control" value="<?= htmlspecialchars($plat['name']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="edit_platform" class="btn btn-primary fw-bold px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($platforms)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray fw-bold">No platforms found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm danh mục -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="cat_name" class="form-control" placeholder="e.g. Action" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="cat_desc" class="form-control" rows="3" placeholder="Category description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-primary fw-bold px-4">Add Category</button>
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
                <h5 class="modal-title">Add New Platform</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Platform Name</label>
                        <input type="text" name="plat_name" class="form-control" placeholder="e.g. PC, PS5" required>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_platform" class="btn btn-primary fw-bold px-4">Add Platform</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

