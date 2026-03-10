<?php
require_once __DIR__ . '/../../includes/auth.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

$auth = new Auth();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $result = $auth->updateProfile($user['id'], $_POST['fullname'], $_POST['phone']);
        if ($result['success']) {
            $message = 'Cập nhật thông tin thành công!';
            $user = Auth::getUser();
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'change_password') {
        $result = $auth->changePassword($user['id'], $_POST['old_password'], $_POST['new_password']);
        if ($result['success']) {
            $message = 'Đổi mật khẩu thành công!';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Thông tin cá nhân - GAMEKEY.VN</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: { "primary": "#7f13ec", "background-dark": "#0f0a16", "surface-dark": "#1d1429" },
                    fontFamily: { "display": ["Space Grotesk", "sans-serif"] },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>
</head>

<body class="bg-background-dark text-white min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-surface-dark min-h-screen p-6 sticky top-0">
            <a href="index.php" class="flex items-center gap-3 text-primary mb-10">
                <div class="size-8">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 19.6865 45.8096 24L24 24L24 45.8096Z"
                            fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="text-white text-lg font-bold">GAMEKEY.VN</h2>
            </a>
            <nav class="space-y-2">
                <a href="index.php?page=dashboard"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">dashboard</span> Dashboard
                </a>
                <a href="index.php?page=library"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">sports_esports</span> Thư viện game
                </a>
                <a href="index.php?page=orders"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">receipt_long</span> Đơn hàng
                </a>
                <a href="index.php?page=wallet"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">account_balance_wallet</span> Ví tiền
                </a>
                <a href="index.php?page=profile"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/20 text-primary font-bold">
                    <span class="material-symbols-outlined">person</span> Thông tin cá nhân
                </a>
                <div class="pt-6 border-t border-white/10 mt-6">
                    <a href="index.php?page=logout"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors">
                        <span class="material-symbols-outlined">logout</span> Đăng xuất
                    </a>
                </div>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <h1 class="text-3xl font-black mb-8">Thông tin cá nhân</h1>

            <?php if ($message): ?>
                <div class="mb-6 p-4 bg-green-500/20 text-green-400 rounded-lg">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-500/20 text-red-400 rounded-lg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Profile Info -->
                <div class="bg-surface-dark rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6">Cập nhật thông tin</h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profile" />
                        <div>
                            <label class="block text-sm font-bold mb-2">Họ và tên</label>
                            <input type="text" name="fullname"
                                value="<?php echo htmlspecialchars($user['fullname']); ?>"
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                                class="w-full bg-black/40 border border-white/5 rounded-lg px-4 py-3 text-slate-500 cursor-not-allowed" />
                            <p class="text-slate-500 text-xs mt-1">Email không thể thay đổi</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Số điện thoại</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary" />
                        </div>
                        <button type="submit"
                            class="w-full bg-primary hover:bg-primary/80 text-white font-bold py-3 rounded-xl transition-colors">
                            Cập nhật thông tin
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-surface-dark rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6">Đổi mật khẩu</h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="change_password" />
                        <div>
                            <label class="block text-sm font-bold mb-2">Mật khẩu hiện tại</label>
                            <input type="password" name="old_password" required
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Mật khẩu mới</label>
                            <input type="password" name="new_password" required minlength="6"
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" required
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary" />
                        </div>
                        <button type="submit"
                            class="w-full bg-white/10 hover:bg-white/20 text-white font-bold py-3 rounded-xl transition-colors">
                            Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="bg-surface-dark rounded-xl p-6 mt-8">
                <h3 class="text-xl font-bold mb-6">Thông tin tài khoản</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-primary/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">person</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Tên đăng nhập</p>
                            <p class="font-bold">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-500">calendar_month</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Ngày tham gia</p>
                            <p class="font-bold">
                                <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-500">login</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Đăng nhập lần cuối</p>
                            <p class="font-bold">
                                <?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Chưa có'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-yellow-500">verified</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Trạng thái</p>
                            <p class="font-bold text-green-500">
                                <?php echo $user['status'] === 'active' ? 'Đang hoạt động' : 'Bị khóa'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>