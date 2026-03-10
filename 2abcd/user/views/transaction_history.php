<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Get all transactions
$transactions = [];
try {
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user['id']]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Transactions error: " . $e->getMessage());
}

function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . 'đ';
}

function getTypeText($type)
{
    switch ($type) {
        case 'deposit':
            return 'Nạp tiền';
        case 'purchase':
            return 'Mua hàng';
        case 'refund':
            return 'Hoàn tiền';
        case 'bonus':
            return 'Thưởng';
        default:
            return ucfirst($type);
    }
}

function getTypeIcon($type)
{
    switch ($type) {
        case 'deposit':
            return 'add_card';
        case 'purchase':
            return 'shopping_cart';
        case 'refund':
            return 'undo';
        case 'bonus':
            return 'redeem';
        default:
            return 'receipt';
    }
}

function getTypeColor($type)
{
    switch ($type) {
        case 'deposit':
        case 'refund':
        case 'bonus':
            return 'bg-green-500/20 text-green-500';
        case 'purchase':
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
    <title>Lịch sử giao dịch - GAMEKEY.VN</title>
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
                <a href="index.php?page=transaction_history"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/20 text-primary font-bold">
                    <span class="material-symbols-outlined">history</span> Lịch sử giao dịch
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
            <h1 class="text-3xl font-black mb-8">Lịch sử giao dịch</h1>

            <?php if (empty($transactions)): ?>
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-6xl text-slate-600 mb-4">history</span>
                    <h3 class="text-xl font-bold text-slate-400">Chưa có giao dịch nào</h3>
                    <p class="text-slate-500 mt-2">Các giao dịch của bạn sẽ hiển thị ở đây</p>
                </div>
            <?php else: ?>
                <div class="bg-surface-dark rounded-xl overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="text-left px-6 py-4 text-slate-400 font-bold text-sm">Loại</th>
                                <th class="text-left px-6 py-4 text-slate-400 font-bold text-sm">Mô tả</th>
                                <th class="text-left px-6 py-4 text-slate-400 font-bold text-sm">Thời gian</th>
                                <th class="text-right px-6 py-4 text-slate-400 font-bold text-sm">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-10 rounded-lg flex items-center justify-center <?php echo getTypeColor($tx['type']); ?>">
                                                <span class="material-symbols-outlined">
                                                    <?php echo getTypeIcon($tx['type']); ?>
                                                </span>
                                            </div>
                                            <span class="font-bold">
                                                <?php echo getTypeText($tx['type']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400">
                                        <?php echo htmlspecialchars($tx['description'] ?: '-'); ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400">
                                        <?php echo date('d/m/Y H:i', strtotime($tx['created_at'])); ?>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-bold <?php echo in_array($tx['type'], ['deposit', 'refund', 'bonus']) ? 'text-green-500' : 'text-red-500'; ?>">
                                        <?php echo (in_array($tx['type'], ['deposit', 'refund', 'bonus']) ? '+' : '-') . formatPrice($tx['amount']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>