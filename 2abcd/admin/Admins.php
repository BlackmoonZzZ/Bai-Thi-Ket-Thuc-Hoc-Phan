<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auto_increment_helper.php';
include __DIR__ . '/includes/header.php'; 

// Xử lý thêm admin/staff
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = trim($_POST['fullname']);
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role']; // admin hoặc staff
    
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, fullname, phone, role, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$username, $email, $password, $fullname, $phone, $role]);
        echo "<script>alert('Thêm thành công!'); window.location='Admins.php';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// Xử lý cập nhật admin/staff
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $email = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    // Nếu có mật khẩu mới
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET email=?, fullname=?, phone=?, role=?, status=?, password=? WHERE id=?");
        $stmt->execute([$email, $fullname, $phone, $role, $status, $password, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET email=?, fullname=?, phone=?, role=?, status=? WHERE id=?");
        $stmt->execute([$email, $fullname, $phone, $role, $status, $id]);
    }
    
    echo "<script>alert('Cập nhật thành công!'); window.location='Admins.php';</script>";
}

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Không cho xóa admin chính (ID = 1 hoặc admin đang đăng nhập)
    if ($id == 1 || $id == ($_SESSION['admin_id'] ?? null)) {
        echo "<script>alert('Không thể xóa tài khoản này!');</script>";
    } else {
        $conn->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        resetAutoIncrement($conn, 'users');
        echo "<script>window.location='Admins.php'</script>";
    }
}

// Lấy danh sách admin/staff
$admins = $conn->query("SELECT * FROM users WHERE role IN ('admin', 'staff') ORDER BY id ASC")->fetchAll();

// Danh sách quyền có thể gán
$availablePermissions = [
    'view_dashboard' => 'Xem Dashboard',
    'manage_products' => 'Quản lý Sản phẩm',
    'manage_categories' => 'Quản lý Danh mục',
    'manage_platforms' => 'Quản lý Nền tảng',
    'manage_keys' => 'Quản lý Key',
    'manage_orders' => 'Quản lý Đơn hàng',
    'manage_customers' => 'Quản lý Khách hàng',
    'manage_coupons' => 'Quản lý Mã giảm giá',
    'manage_admins' => 'Quản lý Admin/Staff',
    'view_reports' => 'Xem Báo cáo',
    'manage_settings' => 'Quản lý Cài đặt'
];
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
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
    margin-bottom: 30px;
}
.table th {
    color: var(--text-gray);
    font-weight: 700;
    border-bottom: 2px solid #EEF2F7;
    padding: 15px;
    text-transform: uppercase;
    font-size: 0.85rem;
    background: transparent;
}
.table td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #EEF2F7;
    color: var(--text-dark);
    font-weight: 500;
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.text-gray {
    color: var(--text-gray) !important;
}
.badge-role {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
}
.badge-admin { background: rgba(72, 128, 255, 0.15); color: var(--primary); }
.badge-staff { background: rgba(139, 92, 246, 0.15); color: #8B5CF6; }

.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}
.badge-active { background: rgba(0, 182, 155, 0.15); color: var(--success); }
.badge-locked { background: rgba(249, 60, 101, 0.15); color: var(--danger); }

.btn-action {
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    transition: 0.2s;
}
.btn-view { background: rgba(72, 128, 255, 0.1); color: var(--primary); }
.btn-view:hover { background: var(--primary); color: white; }
.btn-edit { background: rgba(254, 197, 61, 0.1); color: var(--warning); }
.btn-edit:hover { background: var(--warning); color: white; }
.btn-delete { background: rgba(249, 60, 101, 0.1); color: var(--danger); }
.btn-delete:hover { background: var(--danger); color: white; }

/* Modal Styling */
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
    color: var(--text-dark);
}
.modal-body strong {
    color: var(--text-gray);
    font-size: 0.9rem;
    text-transform: uppercase;
}
.modal-footer {
    border-top: 1px solid #EEF2F7;
    padding: 20px 25px;
}
.form-label {
    font-weight: 600;
    color: var(--text-dark);
}
.form-control, .form-select {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 10px 15px;
    color: var(--text-dark);
}
.form-control:focus, .form-select:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
.form-control:disabled {
    background: #E9ECEF;
    color: var(--text-gray);
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title"><i class="fas fa-user-shield me-2 text-primary"></i> Admin/Staff Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-1"></i> Add Admin/Staff
    </button>
</div>

<div class="content-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>USERNAME</th>
                        <th>FULL NAME</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>STATUS</th>
                        <th>CREATED AT</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($admins)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-gray fw-bold">
                                <div class="mb-3"><i class="fas fa-users-slash fa-3x"></i></div>
                                No Admin/Staff accounts found.
                            </td>
                        </tr>
                    <?php else: ?>
                    <?php foreach($admins as $admin): 
                        $permissions = [];
                    ?>
                    <tr>
                        <td class="ps-4 text-dark fw-bold font-monospace">#<?= $admin['id'] ?></td>
                        <td><strong class="text-dark"><?= $admin['username'] ?></strong></td>
                        <td><?= $admin['fullname'] ?></td>
                        <td class="text-gray"><?= $admin['email'] ?></td>
                        <td>
                            <span class="badge-role d-inline-block <?= $admin['role'] == 'admin' ? 'badge-admin' : 'badge-staff' ?>">
                                <?= $admin['role'] == 'admin' ? 'Admin' : 'Staff' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-status d-inline-block <?= $admin['status'] == 'active' ? 'badge-active' : 'badge-locked' ?>">
                                <?= $admin['status'] == 'active' ? 'Active' : 'Locked' ?>
                            </span>
                        </td>
                        <td class="text-gray"><?= date('d/m/Y', strtotime($admin['created_at'])) ?></td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn-action btn-view" data-bs-toggle="modal" data-bs-target="#viewModal<?= $admin['id'] ?>" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editModal<?= $admin['id'] ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($admin['id'] != 1 && $admin['id'] != ($_SESSION['admin_id'] ?? null)): ?>
                                <a href="?delete=<?= $admin['id'] ?>" onclick="return confirm('Are you sure you want to delete this account?')" class="btn-action btn-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Xem Chi tiết -->
                    <div class="modal fade" id="viewModal<?= $admin['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Account Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <p><strong>Username:</strong><br><span class="text-dark fw-bold"><?= $admin['username'] ?></span></p>
                                            <p><strong>Full Name:</strong><br><span class="text-dark"><?= $admin['fullname'] ?></span></p>
                                            <p><strong>Email:</strong><br><span class="text-dark"><?= $admin['email'] ?></span></p>
                                            <p><strong>Phone:</strong><br><span class="text-dark"><?= $admin['phone'] ?? 'N/A' ?></span></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p><strong>Role:</strong><br>
                                                <span class="badge-role d-inline-block mt-1 <?= $admin['role'] == 'admin' ? 'badge-admin' : 'badge-staff' ?>">
                                                    <?= $admin['role'] == 'admin' ? 'Admin' : 'Staff' ?>
                                                </span>
                                            </p>
                                            <p><strong>Status:</strong><br>
                                                <span class="badge-status d-inline-block mt-1 <?= $admin['status'] == 'active' ? 'badge-active' : 'badge-locked' ?>">
                                                    <?= $admin['status'] == 'active' ? 'Active' : 'Locked' ?>
                                                </span>
                                            </p>
                                            <p><strong>Created At:</strong><br><span class="text-dark"><?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></span></p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded-3 mt-2">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-key text-primary me-2"></i> Permissions:</h6>
                                        <?php if($admin['role'] == 'admin'): ?>
                                            <div class="alert alert-primary mb-0 d-flex align-items-center">
                                                <i class="fas fa-crown me-2 fs-5"></i>
                                                <div>Admin has full access to all features.</div>
                                            </div>
                                        <?php else: ?>
                                            <?php if(empty($permissions)): ?>
                                                <p class="text-gray mb-0"><i class="fas fa-info-circle me-1"></i> No permissions assigned</p>
                                            <?php else: ?>
                                                <div class="row g-2">
                                                    <?php foreach($permissions as $perm): ?>
                                                        <div class="col-md-6">
                                                            <div class="p-2 bg-white rounded border d-flex align-items-center text-success">
                                                                <i class="fas fa-check-circle me-2"></i> <?= $availablePermissions[$perm] ?? $perm ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light rounded-bottom-4">
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Sửa -->
                    <div class="modal fade" id="editModal<?= $admin['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" id="adminEditForm<?= $admin['id'] ?>" data-draft-key="admins_edit_<?= $admin['id'] ?>">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" value="<?= $admin['username'] ?>" disabled>
                                                <small class="text-gray">Cannot be changed</small>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="fullname" class="form-control" value="<?= $admin['fullname'] ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="<?= $admin['email'] ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="<?= $admin['phone'] ?? '' ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">New Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep unchanged">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                                <select name="status" class="form-select" required>
                                                    <option value="active" <?= $admin['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="locked" <?= $admin['status'] == 'locked' ? 'selected' : '' ?>>Locked</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Role <span class="text-danger">*</span></label>
                                            <select name="role" class="form-select" required onchange="togglePermissions(this, <?= $admin['id'] ?>)">
                                                <option value="admin" <?= $admin['role'] == 'admin' ? 'selected' : '' ?>>Admin (Full Access)</option>
                                                <option value="staff" <?= $admin['role'] == 'staff' ? 'selected' : '' ?>>Staff (Limited Access)</option>
                                            </select>
                                        </div>
                                        
                                        <div id="permissionsDiv<?= $admin['id'] ?>" class="bg-light p-3 rounded-3" style="<?= $admin['role'] == 'admin' ? 'display:none;' : '' ?>">
                                            <label class="form-label mb-2">Permissions <span class="text-danger">*</span></label>
                                            <div class="row">
                                                <?php foreach($availablePermissions as $key => $label): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check p-2 bg-white rounded border">
                                                        <input class="form-check-input ms-1 me-2" type="checkbox" name="permissions[]" value="<?= $key ?>" id="perm_edit_<?= $admin['id'] ?>_<?= $key ?>" <?= in_array($key, $permissions) ? 'checked' : '' ?>>
                                                        <label class="form-check-label d-block rounded" for="perm_edit_<?= $admin['id'] ?>_<?= $key ?>" style="cursor: pointer;">
                                                            <?= $label ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Update Account</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm Admin/Staff -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Admin/Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="adminAddForm" data-draft-key="admins_add">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required onchange="togglePermissions(this, 'add')">
                                <option value="admin">Admin (Full Access)</option>
                                <option value="staff">Staff (Limited Access)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="permissionsDivadd" class="bg-light p-3 rounded-3" style="display:none;">
                        <label class="form-label mb-2">Permissions <span class="text-danger">*</span></label>
                        <div class="row">
                            <?php foreach($availablePermissions as $key => $label): ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-1 me-2" type="checkbox" name="permissions[]" value="<?= $key ?>" id="perm_add_<?= $key ?>">
                                    <label class="form-check-label d-block rounded" for="perm_add_<?= $key ?>" style="cursor: pointer;">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Add Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePermissions(select, id) {
    const permDiv = document.getElementById('permissionsDiv' + id);
    if (select.value === 'staff') {
        permDiv.style.display = 'block';
    } else {
        permDiv.style.display = 'none';
    }
}

(function () {
    function getDraftKey(form) {
        return form ? form.getAttribute('data-draft-key') : null;
    }

    function serializeForm(form) {
        const data = {};
        const elements = form.querySelectorAll('input[name], select[name], textarea[name]');
        elements.forEach(function (el) {
            const name = el.getAttribute('name');
            if (!name) return;

            if (el.type === 'checkbox') {
                if (!data[name]) data[name] = [];
                if (el.checked) data[name].push(el.value);
                return;
            }

            if (el.type === 'radio') {
                if (el.checked) data[name] = el.value;
                return;
            }

            data[name] = el.value;
        });
        return data;
    }

    function applyDraft(form, draft) {
        if (!draft || typeof draft !== 'object') return;

        const elements = form.querySelectorAll('input[name], select[name], textarea[name]');
        elements.forEach(function (el) {
            const name = el.getAttribute('name');
            if (!name) return;

            if (el.type === 'checkbox') {
                const values = Array.isArray(draft[name]) ? draft[name] : [];
                el.checked = values.includes(el.value);
                return;
            }

            if (el.type === 'radio') {
                if (draft[name] !== undefined) {
                    el.checked = String(el.value) === String(draft[name]);
                }
                return;
            }

            if (draft[name] !== undefined) {
                el.value = draft[name];
            }
        });

        const roleSelect = form.querySelector('select[name="role"]');
        if (roleSelect) {
            const idInput = form.querySelector('input[name="id"]');
            const id = idInput ? idInput.value : 'add';
            if (typeof togglePermissions === 'function') {
                togglePermissions(roleSelect, id);
            }
        }
    }

    function saveDraft(form) {
        const key = getDraftKey(form);
        if (!key) return;
        try {
            localStorage.setItem(key, JSON.stringify(serializeForm(form)));
        } catch (e) {}
    }

    function loadDraft(form) {
        const key = getDraftKey(form);
        if (!key) return;
        try {
            const raw = localStorage.getItem(key);
            if (!raw) return;
            applyDraft(form, JSON.parse(raw));
        } catch (e) {}
    }

    function clearDraft(form) {
        const key = getDraftKey(form);
        if (!key) return;
        try {
            localStorage.removeItem(key);
        } catch (e) {}
    }

    function setupForm(form) {
        if (!form) return;

        loadDraft(form);

        form.addEventListener('input', function () {
            saveDraft(form);
        });
        form.addEventListener('change', function () {
            saveDraft(form);
        });
        form.addEventListener('submit', function () {
            clearDraft(form);
        });

        const modal = form.closest('.modal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                loadDraft(form);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupForm(document.getElementById('adminAddForm'));
        document.querySelectorAll('form[id^="adminEditForm"]').forEach(function (f) {
            setupForm(f);
        });
    });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
