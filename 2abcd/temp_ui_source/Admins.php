<?php 
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_increment_helper.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-user-shield"></i> Quản lý Admin/Staff</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus"></i> Thêm Admin/Staff
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">ID</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">USERNAME</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HỌ TÊN</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">EMAIL</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">VAI TRÒ</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TRẠNG THÁI</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">NGÀY TẠO</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($admins as $admin): 
                        $permissions = [];
                    ?>
                    <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                        <td style="padding: 15px; color: #06b6d4; font-weight: 700;">#<?= $admin['id'] ?></td>
                        <td style="padding: 15px; color: #e2e8f0;"><strong><?= $admin['username'] ?></strong></td>
                        <td style="padding: 15px; color: #e2e8f0;"><?= $admin['fullname'] ?></td>
                        <td style="padding: 15px; color: #e2e8f0;"><?= $admin['email'] ?></td>
                        <td style="padding: 15px;">
                            <span class="badge" style="background: <?= $admin['role'] == 'admin' ? 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)' : 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)' ?>; padding: 6px 12px; text-transform: uppercase;">
                                <?= $admin['role'] == 'admin' ? 'Admin' : 'Staff' ?>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <span class="badge" style="background: <?= $admin['status'] == 'active' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : '#ef4444' ?>; padding: 6px 12px;">
                                <?= $admin['status'] == 'active' ? 'Hoạt động' : 'Khóa' ?>
                            </span>
                        </td>
                        <td style="padding: 15px; color: #e2e8f0;"><?= date('d/m/Y', strtotime($admin['created_at'])) ?></td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 6px;">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $admin['id'] ?>" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px;" title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $admin['id'] ?>" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px;" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($admin['id'] != 1 && $admin['id'] != ($_SESSION['admin_id'] ?? null)): ?>
                                <a href="?delete=<?= $admin['id'] ?>" onclick="return confirm('Xóa tài khoản này?')" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none;" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Xem Chi tiết -->
                    <div class="modal fade" id="viewModal<?= $admin['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
                                <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                                    <h5 class="modal-title" style="color: #06b6d4;">Chi tiết tài khoản</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                </div>
                                <div class="modal-body" style="color: #e2e8f0;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong style="color: #06b6d4;">Username:</strong> <?= $admin['username'] ?></p>
                                            <p><strong style="color: #06b6d4;">Họ tên:</strong> <?= $admin['fullname'] ?></p>
                                            <p><strong style="color: #06b6d4;">Email:</strong> <?= $admin['email'] ?></p>
                                            <p><strong style="color: #06b6d4;">Điện thoại:</strong> <?= $admin['phone'] ?? 'N/A' ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong style="color: #06b6d4;">Vai trò:</strong> 
                                                <span class="badge" style="background: <?= $admin['role'] == 'admin' ? '#ec4899' : '#8b5cf6' ?>;">
                                                    <?= $admin['role'] == 'admin' ? 'Admin' : 'Staff' ?>
                                                </span>
                                            </p>
                                            <p><strong style="color: #06b6d4;">Trạng thái:</strong> 
                                                <span class="badge" style="background: <?= $admin['status'] == 'active' ? '#10b981' : '#ef4444' ?>;">
                                                    <?= $admin['status'] == 'active' ? 'Hoạt động' : 'Khóa' ?>
                                                </span>
                                            </p>
                                            <p><strong style="color: #06b6d4;">Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></p>
                                        </div>
                                    </div>
                                    
                                    <hr style="border-color: rgba(6, 182, 212, 0.2);">
                                    
                                    <h6 style="color: #06b6d4; margin-bottom: 15px;"><i class="fas fa-key"></i> Quyền hạn:</h6>
                                    <?php if($admin['role'] == 'admin'): ?>
                                        <div class="alert alert-info" style="background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); color: #06b6d4;">
                                            <i class="fas fa-crown"></i> Admin có toàn quyền truy cập
                                        </div>
                                    <?php else: ?>
                                        <?php if(empty($permissions)): ?>
                                            <p style="color: #94a3b8;"><i class="fas fa-info-circle"></i> Chưa có quyền nào được gán</p>
                                        <?php else: ?>
                                            <div class="row">
                                                <?php foreach($permissions as $perm): ?>
                                                    <div class="col-md-6 mb-2">
                                                        <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 8px 12px; font-size: 0.85rem;">
                                                            <i class="fas fa-check"></i> <?= $availablePermissions[$perm] ?? $perm ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Sửa -->
                    <div class="modal fade" id="editModal<?= $admin['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
                                <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                                    <h5 class="modal-title" style="color: #06b6d4;">Sửa tài khoản</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                </div>
                                <form method="POST" id="adminEditForm<?= $admin['id'] ?>" data-draft-key="admins_edit_<?= $admin['id'] ?>">
                                    <div class="modal-body" style="color: #e2e8f0;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" value="<?= $admin['username'] ?>" disabled style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #a78bfa;">
                                                <small style="color: #a78bfa;">Không thể chỉnh sửa</small>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Họ tên <span style="color: #ef4444;">*</span></label>
                                                <input type="text" name="fullname" class="form-control" value="<?= $admin['fullname'] ?>" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email <span style="color: #ef4444;">*</span></label>
                                                <input type="email" name="email" class="form-control" value="<?= $admin['email'] ?>" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Điện thoại</label>
                                                <input type="text" name="phone" class="form-control" value="<?= $admin['phone'] ?? '' ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Mật khẩu mới</label>
                                                <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Trạng thái <span style="color: #ef4444;">*</span></label>
                                                <select name="status" class="form-select" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                                                    <option value="active" style="color: #000;" <?= $admin['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                                    <option value="locked" style="color: #000;" <?= $admin['status'] == 'locked' ? 'selected' : '' ?>>Khóa</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Vai trò <span style="color: #ef4444;">*</span></label>
                                            <select name="role" class="form-select" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;" onchange="togglePermissions(this, <?= $admin['id'] ?>)">
                                                <option value="admin" style="color: #000;" <?= $admin['role'] == 'admin' ? 'selected' : '' ?>>Admin (Toàn quyền)</option>
                                                <option value="staff" style="color: #000;" <?= $admin['role'] == 'staff' ? 'selected' : '' ?>>Staff (Giới hạn)</option>
                                            </select>
                                        </div>
                                        
                                        <div id="permissionsDiv<?= $admin['id'] ?>" style="<?= $admin['role'] == 'admin' ? 'display:none;' : '' ?>">
                                            <label class="form-label">Quyền hạn <span style="color: #ef4444;">*</span></label>
                                            <div class="row">
                                                <?php foreach($availablePermissions as $key => $label): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $key ?>" id="perm_edit_<?= $admin['id'] ?>_<?= $key ?>" <?= in_array($key, $permissions) ? 'checked' : '' ?> style="background-color: rgba(6, 182, 212, 0.1); border-color: #06b6d4;">
                                                        <label class="form-check-label" for="perm_edit_<?= $admin['id'] ?>_<?= $key ?>" style="color: #e2e8f0;">
                                                            <?= $label ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top: 1px solid rgba(6, 182, 212, 0.2);">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
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

<!-- Modal Thêm Admin/Staff -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                <h5 class="modal-title" style="color: #06b6d4;">Thêm Admin/Staff mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST" id="adminAddForm" data-draft-key="admins_add">
                <div class="modal-body" style="color: #e2e8f0;">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="username" class="form-control" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ tên <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="fullname" class="form-control" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span style="color: #ef4444;">*</span></label>
                            <input type="email" name="email" class="form-control" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Điện thoại</label>
                            <input type="text" name="phone" class="form-control" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu <span style="color: #ef4444;">*</span></label>
                            <input type="password" name="password" class="form-control" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vai trò <span style="color: #ef4444;">*</span></label>
                            <select name="role" class="form-select" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;" onchange="togglePermissions(this, 'add')">
                                <option value="admin" style="color: #000;">Admin (Toàn quyền)</option>
                                <option value="staff" style="color: #000;">Staff (Giới hạn)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="permissionsDivadd" style="display:none;">
                        <label class="form-label">Quyền hạn <span style="color: #ef4444;">*</span></label>
                        <div class="row">
                            <?php foreach($availablePermissions as $key => $label): ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $key ?>" id="perm_add_<?= $key ?>" style="background-color: rgba(6, 182, 212, 0.1); border-color: #06b6d4;">
                                    <label class="form-check-label" for="perm_add_<?= $key ?>" style="color: #e2e8f0;">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(6, 182, 212, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
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