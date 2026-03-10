<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Get wallet transactions
$transactions = [];
try {
    $stmt = $conn->prepare("SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmt->execute([$user['id']]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Wallet error: " . $e->getMessage());
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
    <title>Ví tiền - GAMEKEY.VN</title>
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
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/20 text-primary font-bold">
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
            <h1 class="text-3xl font-black mb-8">Ví tiền</h1>

            <!-- Balance Card -->
            <div class="bg-gradient-to-r from-primary to-purple-600 rounded-2xl p-8 mb-8">
                <p class="text-white/70 mb-2">Số dư khả dụng</p>
                <p class="text-5xl font-black mb-6">
                    <?php echo formatPrice($user['balance']); ?>
                </p>
                <button onclick="showDepositModal()"
                    class="bg-white text-primary font-bold px-8 py-3 rounded-xl hover:bg-white/90 transition-colors">
                    <span class="material-symbols-outlined align-middle mr-2">add</span>Nạp tiền
                </button>
            </div>

            <!-- Deposit Options -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <button onclick="deposit(50000)"
                    class="bg-surface-dark rounded-xl p-6 text-center hover:border-primary border border-transparent transition-colors">
                    <p class="text-2xl font-bold text-primary mb-2">50.000đ</p>
                    <p class="text-slate-400 text-sm">Nạp nhanh</p>
                </button>
                <button onclick="deposit(100000)"
                    class="bg-surface-dark rounded-xl p-6 text-center hover:border-primary border border-transparent transition-colors">
                    <p class="text-2xl font-bold text-primary mb-2">100.000đ</p>
                    <p class="text-slate-400 text-sm">Phổ biến</p>
                </button>
                <button onclick="deposit(200000)"
                    class="bg-surface-dark rounded-xl p-6 text-center hover:border-primary border border-transparent transition-colors">
                    <p class="text-2xl font-bold text-primary mb-2">200.000đ</p>
                    <p class="text-slate-400 text-sm">Tiết kiệm</p>
                </button>
            </div>

            <!-- Transaction History -->
            <div class="bg-surface-dark rounded-xl p-6">
                <h3 class="text-xl font-bold mb-6">Lịch sử giao dịch ví</h3>

                <?php if (empty($transactions)): ?>
                    <div class="text-center py-12">
                        <span class="material-symbols-outlined text-5xl text-slate-600 mb-4">receipt_long</span>
                        <p class="text-slate-400">Chưa có giao dịch nào</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($transactions as $tx): ?>
                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="size-10 rounded-lg flex items-center justify-center <?php echo $tx['type'] === 'deposit' || $tx['type'] === 'bonus' ? 'bg-green-500/20' : 'bg-red-500/20'; ?>">
                                        <span
                                            class="material-symbols-outlined <?php echo $tx['type'] === 'deposit' || $tx['type'] === 'bonus' ? 'text-green-500' : 'text-red-500'; ?>">
                                            <?php echo $tx['type'] === 'deposit' || $tx['type'] === 'bonus' ? 'arrow_downward' : 'arrow_upward'; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-bold">
                                            <?php
                                            switch ($tx['type']) {
                                                case 'deposit':
                                                    echo 'Nạp tiền';
                                                    break;
                                                case 'purchase':
                                                    echo 'Thanh toán đơn hàng';
                                                    break;
                                                case 'refund':
                                                    echo 'Hoàn tiền';
                                                    break;
                                                case 'bonus':
                                                    echo 'Thưởng';
                                                    break;
                                                default:
                                                    echo ucfirst($tx['type']);
                                            }
                                            ?>
                                        </p>
                                        <p class="text-slate-400 text-sm">
                                            <?php echo date('d/m/Y H:i', strtotime($tx['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                                <p
                                    class="font-bold <?php echo $tx['type'] === 'deposit' || $tx['type'] === 'bonus' || $tx['type'] === 'refund' ? 'text-green-500' : 'text-red-500'; ?>">
                                    <?php echo ($tx['type'] === 'deposit' || $tx['type'] === 'bonus' || $tx['type'] === 'refund' ? '+' : '-') . formatPrice($tx['amount']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function getApiBase() {
            const path = window.location.pathname;
            const match = path.match(/^\/[^\/]+/);
            return match ? match[0] : '';
        }

        async function deposit(amount) {
            try {
                const base = getApiBase();
                // 1. Request deposit from API
                const response = await fetch(`${base}/api/wallet.php?action=request_deposit`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `amount=${amount}`
                });
                const data = await response.json();
                
                if (data.success) {
                    // 2. Show Modal with QR
                    showQRModal(data.amount, data.reference_id);
                } else {
                    alert(data.message || 'Lỗi nạp tiền');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi kết nối');
            }
        }

        function showQRModal(amount, reference) {
            const modal = document.getElementById('qrModal');
            const qrImg = document.getElementById('qrImage');
            const amountText = document.getElementById('modalAmount');
            const referenceText = document.getElementById('modalReference');
            
            // VietQR API: https://img.vietqr.io/image/<BANK_ID>-<ACCOUNT_NO>-<TEMPLATE>.png?amount=<AMOUNT>&addInfo=<DESCRIPTION>&accountName=<NAME>
            const bankId = '970436'; // Vietcombank
            const accountNo = '1234567890';
            const accountName = encodeURIComponent('CONG TY GAMEKEY VIET NAM');
            const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact2.png?amount=${amount}&addInfo=${reference}&accountName=${accountName}`;
            
            qrImg.src = qrUrl;
            amountText.innerText = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
            referenceText.innerText = reference;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeQRModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            location.reload(); // Refresh to show pending transaction
        }

        function showDepositModal() {
            const amount = prompt('Nhập số tiền muốn nạp (VNĐ):');
            if (amount && !isNaN(amount) && parseInt(amount) >= 10000) {
                deposit(parseInt(amount));
            } else if (amount) {
                alert('Số tiền tối thiểu là 10.000đ');
            }
        }
    </script>

    <!-- QR Payment Modal -->
    <div id="qrModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-surface-dark w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-white/10 animate-in fade-in zoom-in duration-300">
            <div class="p-6 text-center border-b border-white/5">
                <h3 class="text-xl font-bold text-white">Thanh toán nạp tiền</h3>
                <p class="text-slate-400 text-sm mt-1">Quét mã QR dưới đây để hoàn tất</p>
            </div>
            
            <div class="p-8 flex flex-col items-center">
                <div class="bg-white p-4 rounded-xl mb-6 shadow-inner">
                    <img id="qrImage" src="" alt="VietQR" class="size-64 object-contain">
                </div>
                
                <div class="w-full space-y-4">
                    <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                        <span class="text-slate-400 text-sm">Số tiền:</span>
                        <span id="modalAmount" class="text-primary font-bold text-lg">0đ</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                        <span class="text-slate-400 text-sm">Nội dung:</span>
                        <div class="flex items-center gap-2">
                            <span id="modalReference" class="text-white font-mono font-bold tracking-wider">DEP_XXXX</span>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('modalReference').innerText); alert('Đã sao chép nội dung!')" class="text-primary hover:text-primary/80 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 w-full">
                    <button onclick="closeQRModal()" class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                        Xác nhận đã chuyển khoản
                    </button>
                    <p class="text-slate-500 text-[11px] text-center mt-4">
                        * Giao dịch sẽ được xử lý tự động hoặc duyệt trong vòng 5-15 phút.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>