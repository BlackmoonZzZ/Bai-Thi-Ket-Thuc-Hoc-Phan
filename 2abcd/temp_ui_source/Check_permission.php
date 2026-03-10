<?php
/**
 * File kiểm tra quyền hạn
 * Sử dụng để kiểm tra xem user có quyền truy cập chức năng hay không
 */

function checkPermission($permission) {
    // Nếu là admin thì có toàn quyền
    if ($_SESSION['role'] == 'admin') {
        return true;
    }
    
    // Nếu là staff thì kiểm tra quyền
    if ($_SESSION['role'] == 'staff') {
        $permissions = isset($_SESSION['permissions']) ? json_decode($_SESSION['permissions'], true) : [];
        return in_array($permission, $permissions);
    }
    
    // Customer không có quyền gì
    return false;
}

function requirePermission($permission, $redirectUrl = 'index.php') {
    if (!checkPermission($permission)) {
        echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location='$redirectUrl';</script>";
        exit;
    }
}

// Hàm kiểm tra nhiều quyền (chỉ cần 1 trong số các quyền)
function checkAnyPermission($permissions) {
    foreach ($permissions as $perm) {
        if (checkPermission($perm)) {
            return true;
        }
    }
    return false;
}

// Hàm kiểm tra tất cả quyền (cần có đủ tất cả quyền)
function checkAllPermissions($permissions) {
    foreach ($permissions as $perm) {
        if (!checkPermission($perm)) {
            return false;
        }
    }
    return true;
}