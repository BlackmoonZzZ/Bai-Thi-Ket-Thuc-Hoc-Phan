<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/cart.php';

$user = Auth::getUser();
if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Use static Cart methods
$cartData = Cart::getCartDetails();
$cartItems = $cartData['items'];
$cartTotal = $cartData['total'];

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
    <title>Giỏ hàng - GAMEKEY.VN</title>
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
        <div class="flex gap-3">
            <a href="index.php?page=dashboard"
                class="flex items-center justify-center rounded-lg h-10 w-10 bg-surface-dark border border-white/10 text-white hover:bg-primary/20 hover:text-primary transition-all">
                <span class="material-symbols-outlined">person</span>
            </a>
        </div>
    </header>

    <main class="max-w-[1280px] mx-auto px-4 md:px-10 py-8">
        <h1 class="text-3xl font-black mb-8">Giỏ hàng của bạn</h1>

        <?php if (empty($cartItems)): ?>
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-6xl text-slate-600 mb-4">shopping_cart</span>
                <h3 class="text-xl font-bold text-slate-400">Giỏ hàng trống</h3>
                <p class="text-slate-500 mt-2">Thêm game vào giỏ hàng để tiến hành thanh toán</p>
                <a href="index.php"
                    class="inline-block mt-6 px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/80 transition-colors">
                    Mua game ngay
                </a>
            </div>
        <?php else: ?>
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="flex gap-4 bg-surface-dark rounded-xl p-4"
                            data-cart-item="<?php echo $item['product_id']; ?>">
                            <div class="size-24 rounded-lg bg-cover bg-center shrink-0"
                                style="background-image: url('<?php echo htmlspecialchars($item['image'] ?: 'https://placehold.co/100x100/7f13ec/white?text=Game'); ?>');">
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </h3>
                                <p class="text-slate-400 text-sm">
                                    <?php echo htmlspecialchars($item['platform_name'] ?? 'Steam'); ?>
                                </p>
                                <div class="flex items-center gap-4 mt-3">
                                    <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-1">
                                        <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)"
                                            class="text-slate-400 hover:text-white">
                                            <span class="material-symbols-outlined text-[18px]">remove</span>
                                        </button>
                                        <span class="w-8 text-center font-bold quantity">
                                            <?php echo $item['quantity']; ?>
                                        </span>
                                        <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)"
                                            class="text-slate-400 hover:text-white">
                                            <span class="material-symbols-outlined text-[18px]">add</span>
                                        </button>
                                    </div>
                                    <button onclick="removeItem(<?php echo $item['product_id']; ?>)"
                                        class="text-red-400 hover:text-red-300 text-sm font-bold">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-primary item-total">
                                    <?php echo formatPrice($item['final_price'] * $item['quantity']); ?>
                                </p>
                                <?php if (($item['discount_percent'] ?? 0) > 0): ?>
                                    <p class="text-sm text-slate-500 line-through">
                                        <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-surface-dark rounded-xl p-6 sticky top-24">
                        <h3 class="text-xl font-bold mb-6">Tóm tắt đơn hàng</h3>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-slate-400">
                                <span>Tạm tính</span>
                                <span id="subtotal">
                                    <?php echo formatPrice($cartTotal); ?>
                                </span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Giảm giá</span>
                                <span class="text-green-500">-0đ</span>
                            </div>
                            <div class="border-t border-white/10 pt-3 flex justify-between text-lg font-bold">
                                <span>Tổng cộng</span>
                                <span class="text-primary" id="total">
                                    <?php echo formatPrice($cartTotal); ?>
                                </span>
                            </div>
                        </div>

                        <a href="index.php?page=checkout"
                            class="block w-full bg-primary hover:bg-primary/80 text-white font-bold py-4 rounded-xl text-center transition-colors">
                            Tiến hành thanh toán
                        </a>

                        <a href="index.php" class="block w-full text-center text-slate-400 hover:text-primary mt-4 text-sm">
                            ← Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function getApiBase() {
            const path = window.location.pathname;
            const match = path.match(/^\/[^\/]+/);
            return match ? match[0] : '';
        }

        async function updateQuantity(productId, delta) {
            const itemEl = document.querySelector(`[data-cart-item="${productId}"]`);
            const quantityEl = itemEl.querySelector('.quantity');
            let newQty = parseInt(quantityEl.textContent) + delta;
            if (newQty < 1) newQty = 1;

            try {
                const base = getApiBase();
                const response = await fetch(`${base}/api/cart.php?action=update`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}&quantity=${newQty}`
                });
                const data = await response.json();
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function removeItem(productId) {
            if (!confirm('Xóa sản phẩm khỏi giỏ hàng?')) return;

            try {
                const base = getApiBase();
                const response = await fetch(`${base}/api/cart.php?action=remove`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}`
                });
                const data = await response.json();
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>

</html>