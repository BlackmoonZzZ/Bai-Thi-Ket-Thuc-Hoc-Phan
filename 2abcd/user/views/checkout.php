<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/cart.php';
require_once __DIR__ . '/../../includes/order.php';
require_once __DIR__ . '/../../config/db.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

$cartData = Cart::getCartDetails();
$cartItems = $cartData['items'];
$cartTotal = $cartData['total'];

if (empty($cartItems)) {
    header('Location: index.php?page=cart');
    exit;
}

// Get payment methods
$paymentMethods = [];
try {
    $stmt = $conn->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY display_order");
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error loading payment methods: " . $e->getMessage());
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
    <title>Thanh toán - GAMEKEY.VN</title>
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

        .glass-effect {
            background: rgba(31, 20, 45, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-background-dark text-white min-h-screen">
    <!-- Header -->
    <header
        class="sticky top-0 z-50 glass-effect border-b border-white/10 px-4 md:px-20 py-3 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3 text-primary">
            <div class="size-8">
                <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 19.6865 45.8096 24L24 24L24 45.8096Z"
                        fill="currentColor"></path>
                </svg>
            </div>
            <h2 class="text-white text-xl font-bold">GAMEKEY.VN</h2>
        </a>
        <nav class="flex items-center gap-2 text-sm text-slate-400">
            <span>Giỏ hàng</span>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-bold">Thanh toán</span>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span>Hoàn tất</span>
        </nav>
    </header>

    <main class="max-w-[1280px] mx-auto px-4 md:px-10 py-8">
        <h1 class="text-3xl font-black mb-8">Thanh toán</h1>

        <form id="checkoutForm" class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-surface-dark rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Sản phẩm</h3>
                    <div class="space-y-4">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="flex gap-4">
                                <div class="size-16 rounded-lg bg-cover bg-center shrink-0"
                                    style="background-image: url('<?php echo htmlspecialchars($item['image'] ?: 'https://placehold.co/100x100/7f13ec/white?text=Game'); ?>');">
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </h4>
                                    <p class="text-slate-400 text-sm">x
                                        <?php echo $item['quantity']; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <?php
                                    $itemPrice = $item['price'];
                                    if (($item['discount_percent'] ?? 0) > 0) {
                                        $itemPrice = $item['price'] * (100 - $item['discount_percent']) / 100;
                                    }
                                    ?>
                                    <p class="font-bold text-primary">
                                        <?php echo formatPrice($itemPrice * $item['quantity']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-surface-dark rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Phương thức thanh toán</h3>
                    <div class="space-y-3">
                        <!-- Wallet Balance -->
                        <label
                            class="flex items-center gap-4 p-4 rounded-lg border border-white/10 hover:border-primary/50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="balance"
                                class="text-primary focus:ring-primary" checked />
                            <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                            <div class="flex-1">
                                <p class="font-bold">Số dư tài khoản</p>
                                <p class="text-slate-400 text-sm">Số dư:
                                    <?php echo formatPrice($user['balance']); ?>
                                </p>
                            </div>
                            <?php if ($user['balance'] >= $cartTotal): ?>
                                <span class="text-green-500 text-sm font-bold">Đủ tiền</span>
                            <?php else: ?>
                                <span class="text-red-500 text-sm font-bold">Thiếu
                                    <?php echo formatPrice($cartTotal - $user['balance']); ?>
                                </span>
                            <?php endif; ?>
                        </label>

                        <?php foreach ($paymentMethods as $method):
                            if ($method['method_code'] === 'balance')
                                continue; ?>
                            <label
                                class="flex items-center gap-4 p-4 rounded-lg border border-white/10 hover:border-primary/50 cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="<?php echo $method['method_code']; ?>"
                                    class="text-primary focus:ring-primary" />
                                <span class="material-symbols-outlined text-slate-400">credit_card</span>
                                <div class="flex-1">
                                    <p class="font-bold">
                                        <?php echo htmlspecialchars($method['method_name']); ?>
                                    </p>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Coupon -->
                <div class="bg-surface-dark rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Mã giảm giá</h3>
                    <div class="flex gap-3">
                        <input type="text" name="coupon_code" id="couponInput" placeholder="Nhập mã giảm giá"
                            class="flex-1 bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-primary focus:border-primary" />
                        <button type="button" onclick="applyCoupon()"
                            class="px-6 py-3 bg-white/10 hover:bg-white/20 font-bold rounded-lg transition-colors">
                            Áp dụng
                        </button>
                    </div>
                    <div id="couponMessage" class="mt-3 text-sm hidden"></div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-surface-dark rounded-xl p-6 sticky top-24">
                    <h3 class="text-xl font-bold mb-6">Tóm tắt đơn hàng</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-slate-400">
                            <span>Tạm tính</span>
                            <span>
                                <?php echo formatPrice($cartTotal); ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-slate-400" id="discountRow" style="display: none;">
                            <span>Giảm giá</span>
                            <span class="text-green-500" id="discountAmount">-0đ</span>
                        </div>
                        <div class="border-t border-white/10 pt-3 flex justify-between text-lg font-bold">
                            <span>Tổng cộng</span>
                            <span class="text-primary" id="finalTotal">
                                <?php echo formatPrice($cartTotal); ?>
                            </span>
                        </div>
                    </div>

                    <input type="hidden" id="cartTotal" value="<?php echo $cartTotal; ?>" />
                    <input type="hidden" id="discount" value="0" />

                    <button type="submit" id="submitBtn"
                        class="w-full bg-primary hover:bg-primary/80 text-white font-bold py-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">lock</span>
                        Xác nhận thanh toán
                    </button>

                    <div id="errorMessage" class="mt-4 text-red-500 text-sm text-center hidden"></div>

                    <p class="text-slate-500 text-xs text-center mt-4">
                        Bằng việc đặt hàng, bạn đồng ý với Điều khoản dịch vụ của chúng tôi
                    </p>
                </div>
            </div>
        </form>
    </main>

    <script>
        function getApiBase() {
            const path = window.location.pathname;
            const match = path.match(/^\/[^\/]+/);
            return match ? match[0] : '';
        }

        let appliedCoupon = null;

        async function applyCoupon() {
            const code = document.getElementById('couponInput').value.trim();
            const msgEl = document.getElementById('couponMessage');
            if (!code) {
                msgEl.textContent = 'Vui lòng nhập mã giảm giá';
                msgEl.className = 'mt-3 text-sm text-red-500';
                msgEl.classList.remove('hidden');
                return;
            }

            try {
                const base = getApiBase();
                const response = await fetch(`${base}/api/coupon.php?action=validate&code=${encodeURIComponent(code)}&total=${document.getElementById('cartTotal').value}`);
                const data = await response.json();

                if (data.success) {
                    appliedCoupon = data.coupon;
                    document.getElementById('discount').value = data.discount;
                    document.getElementById('discountAmount').textContent = '-' + formatPrice(data.discount);
                    document.getElementById('discountRow').style.display = 'flex';
                    document.getElementById('finalTotal').textContent = formatPrice(data.final_total);
                    msgEl.textContent = 'Áp dụng mã giảm giá thành công!';
                    msgEl.className = 'mt-3 text-sm text-green-500';
                } else {
                    msgEl.textContent = data.message || 'Mã giảm giá không hợp lệ';
                    msgEl.className = 'mt-3 text-sm text-red-500';
                }
                msgEl.classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                msgEl.textContent = 'Lỗi kiểm tra mã giảm giá';
                msgEl.className = 'mt-3 text-sm text-red-500';
                msgEl.classList.remove('hidden');
            }
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        }

        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('coupon_code', document.getElementById('couponInput').value);

            const submitBtn = document.getElementById('submitBtn');
            const errorEl = document.getElementById('errorMessage');
            
            // Client-side balance validation
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
            if (selectedPayment === 'balance') {
                const userBalance = <?php echo (float) $user['balance']; ?>;
                const cartTotal = parseFloat(document.getElementById('cartTotal').value);
                const discount = parseFloat(document.getElementById('discount').value) || 0;
                const finalTotal = cartTotal - discount;

                if (userBalance < finalTotal) {
                    errorEl.innerHTML = '<span class="material-symbols-outlined text-[16px] align-middle mr-1">error</span><strong>Thanh toán không hợp lệ</strong> — Số dư tài khoản không đủ. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.';
                    errorEl.className = 'mt-4 text-red-400 text-sm text-center p-3 bg-red-500/10 border border-red-500/20 rounded-xl';
                    errorEl.classList.remove('hidden');
                    return;
                }
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Đang xử lý...';
            errorEl.classList.add('hidden');

            try {
                const base = getApiBase();
                const response = await fetch(`${base}/api/orders.php?action=create`, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    alert('Đặt hàng thành công! Mã đơn hàng: ' + data.order_number);
                    window.location.href = 'index.php?page=orders';
                } else {
                    errorEl.innerHTML = '<span class="material-symbols-outlined text-[16px] align-middle mr-1">error</span>' + (data.message || 'Đặt hàng thất bại');
                    errorEl.className = 'mt-4 text-red-400 text-sm text-center p-3 bg-red-500/10 border border-red-500/20 rounded-xl';
                    errorEl.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                errorEl.innerHTML = '<span class="material-symbols-outlined text-[16px] align-middle mr-1">error</span>Lỗi kết nối';
                errorEl.className = 'mt-4 text-red-400 text-sm text-center p-3 bg-red-500/10 border border-red-500/20 rounded-xl';
                errorEl.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-symbols-outlined">lock</span> Xác nhận thanh toán';
            }
        });
    </script>
</body>

</html>