<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/db.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Get user stats
$orderCount = 0;
$gameCount = 0;
$totalSpent = 0;
$recentOrders = [];

try {
    // Count orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $orderCount = $stmt->fetchColumn();

    // Count games purchased
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT oi.product_id) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ? AND o.status = 'completed'");
    $stmt->execute([$user['id']]);
    $gameCount = $stmt->fetchColumn();

    // Total spent
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = ? AND status = 'completed'");
    $stmt->execute([$user['id']]);
    $totalSpent = $stmt->fetchColumn();

    // Recent orders
    $stmt = $conn->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([$user['id']]);
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}

function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . 'đ';
}
?>
<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard - GAMEKEY.VN</title>
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
                    colors: {
                        "primary": "#7f13ec",
                        "background-dark": "#0f0a16",
                        "surface-dark": "#1d1429",
                    },
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
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/20 text-primary font-bold">
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
                <a href="index.php?page=transaction_history"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">history</span> Lịch sử giao dịch
                </a>
                <a href="index.php?page=profile"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">person</span> Thông tin cá nhân
                </a>
                <a href="index.php?page=cart"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">shopping_cart</span> Giỏ hàng
                </a>
                <div class="pt-6 border-t border-white/10 mt-6">
                    <a href="index.php?page=logout"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors">
                        <span class="material-symbols-outlined">logout</span> Đăng xuất
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Welcome -->
            <div class="mb-8">
                <h1 class="text-3xl font-black">Xin chào,
                    <?php echo htmlspecialchars($user['fullname'] ?: $user['username']); ?>! 👋
                </h1>
                <p class="text-slate-400 mt-2">Chào mừng bạn quay trở lại GAMEKEY.VN</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-surface-dark rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-primary/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-2xl">account_balance_wallet</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Số dư ví</p>
                            <p class="text-2xl font-bold text-primary">
                                <?php echo formatPrice($user['balance']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-dark rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-500 text-2xl">sports_esports</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Game đã mua</p>
                            <p class="text-2xl font-bold">
                                <?php echo $gameCount; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-dark rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-500 text-2xl">receipt_long</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Đơn hàng</p>
                            <p class="text-2xl font-bold">
                                <?php echo $orderCount; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-dark rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="size-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-yellow-500 text-2xl">payments</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Tổng chi tiêu</p>
                            <p class="text-2xl font-bold">
                                <?php echo formatPrice($totalSpent); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="index.php?page=wallet"
                    class="bg-gradient-to-r from-primary to-purple-600 rounded-xl p-6 hover:scale-[1.02] transition-transform">
                    <span class="material-symbols-outlined text-4xl mb-4">add_card</span>
                    <h3 class="text-xl font-bold">Nạp tiền</h3>
                    <p class="text-white/70 text-sm mt-2">Nạp tiền vào ví để mua game</p>
                </a>
                <a href="index.php"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-colors">
                    <span class="material-symbols-outlined text-4xl mb-4 text-primary">storefront</span>
                    <h3 class="text-xl font-bold">Mua game</h3>
                    <p class="text-slate-400 text-sm mt-2">Khám phá kho game khổng lồ</p>
                </a>
                <a href="index.php?page=library"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-colors">
                    <span class="material-symbols-outlined text-4xl mb-4 text-green-500">vpn_key</span>
                    <h3 class="text-xl font-bold">Xem key</h3>
                    <p class="text-slate-400 text-sm mt-2">Xem key các game đã mua</p>
                </a>
            </div>

            <!-- Recent Orders -->
            <div class="bg-surface-dark rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold">Đơn hàng gần đây</h2>
                    <a href="index.php?page=orders" class="text-primary text-sm font-bold hover:underline">Xem tất
                        cả</a>
                </div>
                <?php if (empty($recentOrders)): ?>
                    <div class="text-center py-12">
                        <span class="material-symbols-outlined text-5xl text-slate-600 mb-4">inbox</span>
                        <p class="text-slate-400">Chưa có đơn hàng nào</p>
                        <a href="index.php" class="inline-block mt-4 text-primary font-bold hover:underline">Mua game
                            ngay</a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentOrders as $order): ?>
                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                                <div>
                                    <p class="font-bold">
                                        <?php echo htmlspecialchars($order['order_number']); ?>
                                    </p>
                                    <p class="text-slate-400 text-sm">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-primary">
                                        <?php echo formatPrice($order['total_amount']); ?>
                                    </p>
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-bold rounded <?php echo $order['status'] === 'completed' ? 'bg-green-500/20 text-green-500' : 'bg-yellow-500/20 text-yellow-500'; ?>">
                                        <?php echo $order['status'] === 'completed' ? 'Hoàn thành' : 'Đang xử lý'; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>