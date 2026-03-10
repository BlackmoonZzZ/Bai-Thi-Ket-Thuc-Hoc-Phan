<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Get orders
$orders = [];
try {
    $stmt = $conn->prepare("
        SELECT o.*, COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Orders error: " . $e->getMessage());
}

function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . 'đ';
}

function getStatusText($status)
{
    switch ($status) {
        case 'pending':
            return 'Chờ xử lý';
        case 'processing':
            return 'Đang xử lý';
        case 'completed':
            return 'Hoàn thành';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return ucfirst($status);
    }
}

function getStatusColor($status)
{
    switch ($status) {
        case 'pending':
            return 'bg-yellow-500/20 text-yellow-500';
        case 'processing':
            return 'bg-blue-500/20 text-blue-500';
        case 'completed':
            return 'bg-green-500/20 text-green-500';
        case 'cancelled':
            return 'bg-red-500/20 text-red-500';
        default:
            return 'bg-slate-500/20 text-slate-500';
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đơn hàng - GAMEKEY.VN</title>
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
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/20 text-primary font-bold">
                    <span class="material-symbols-outlined">receipt_long</span> Đơn hàng
                </a>
                <a href="index.php?page=wallet"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">account_balance_wallet</span> Ví tiền
                </a>
                <a href="index.php?page=profile"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 transition-colors">
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
            <h1 class="text-3xl font-black mb-8">Lịch sử đơn hàng</h1>

            <?php if (empty($orders)): ?>
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-6xl text-slate-600 mb-4">receipt_long</span>
                    <h3 class="text-xl font-bold text-slate-400">Chưa có đơn hàng nào</h3>
                    <p class="text-slate-500 mt-2">Đặt hàng đầu tiên của bạn ngay!</p>
                    <a href="index.php"
                        class="inline-block mt-6 px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/80 transition-colors">
                        Mua game ngay
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($orders as $order): ?>
                        <div class="bg-surface-dark rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-lg font-bold"><?php echo htmlspecialchars($order['order_number']); ?></p>
                                    <p class="text-slate-400 text-sm">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-primary"><?php echo formatPrice($order['total_amount']); ?>
                                    </p>
                                    <span
                                        class="inline-block px-3 py-1 text-xs font-bold rounded-full <?php echo getStatusColor($order['status']); ?>">
                                        <?php echo getStatusText($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-white/10">
                                <p class="text-slate-400 text-sm">
                                    <?php echo $order['item_count']; ?> sản phẩm
                                    <?php if ($order['payment_method']): ?>
                                        • <?php echo htmlspecialchars($order['payment_method']); ?>
                                    <?php endif; ?>
                                </p>
                                <?php if ($order['status'] === 'completed'): ?>
                                    <a href="index.php?page=library" class="text-primary text-sm font-bold hover:underline">
                                        Xem key game →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>