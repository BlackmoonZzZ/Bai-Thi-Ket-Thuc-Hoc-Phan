<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Get purchased games
$games = [];
try {
    $stmt = $conn->prepare("
        SELECT p.*, pk.key_code, o.created_at as purchase_date, o.order_number
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_keys pk ON oi.key_id = pk.id
        WHERE o.user_id = ? AND o.status = 'completed'
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Library error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Thư viện game - GAMEKEY.VN</title>
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
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/20 text-primary font-bold">
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
            <h1 class="text-3xl font-black mb-8">Thư viện game của bạn</h1>

            <?php if (empty($games)): ?>
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-6xl text-slate-600 mb-4">sports_esports</span>
                    <h3 class="text-xl font-bold text-slate-400">Thư viện trống</h3>
                    <p class="text-slate-500 mt-2">Mua game để thêm vào thư viện của bạn</p>
                    <a href="index.php"
                        class="inline-block mt-6 px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/80 transition-colors">
                        Mua game ngay
                    </a>
                </div>
            <?php else: ?>
                <p class="text-slate-400 mb-6">
                    <?php echo count($games); ?> game trong thư viện
                </p>
                <div class="grid gap-6">
                    <?php foreach ($games as $game): ?>
                        <div class="bg-surface-dark rounded-xl p-6 flex gap-6">
                            <div class="size-32 rounded-lg bg-cover bg-center shrink-0"
                                style="background-image: url('<?php echo htmlspecialchars($game['image'] ?: 'https://placehold.co/200x200/7f13ec/white?text=Game'); ?>');">
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold mb-2">
                                    <?php echo htmlspecialchars($game['name']); ?>
                                </h3>
                                <p class="text-slate-400 text-sm mb-4">
                                    Mua ngày
                                    <?php echo date('d/m/Y', strtotime($game['purchase_date'])); ?>
                                    • Đơn hàng #
                                    <?php echo htmlspecialchars($game['order_number']); ?>
                                </p>

                                <div class="bg-black/30 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-slate-400 text-xs uppercase font-bold mb-1">Game Key</p>
                                            <p class="font-mono text-lg tracking-wider" id="key-<?php echo $game['id']; ?>">
                                                <?php echo !empty($game['key_code']) ? htmlspecialchars($game['key_code']) : '••••-••••-••••-••••'; ?>
                                            </p>
                                        </div>
                                        <?php if (!empty($game['key_code'])): ?>
                                            <button onclick="copyKey('<?php echo htmlspecialchars($game['key_code']); ?>')"
                                                class="flex items-center gap-2 px-4 py-2 bg-primary/20 text-primary font-bold rounded-lg hover:bg-primary/30 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                                Sao chép
                                            </button>
                                        <?php else: ?>
                                            <span class="text-yellow-500 text-sm">Đang xử lý...</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function copyKey(key) {
            navigator.clipboard.writeText(key).then(() => {
                alert('Đã sao chép key vào clipboard!');
            }).catch(() => {
                prompt('Sao chép key:', key);
            });
        }
    </script>
</body>

</html>