<?php
require_once __DIR__ . '/../../includes/product.php';

$productModel = new Product();
$featuredProducts = $productModel->getFeaturedProducts(12);
$saleProducts = $productModel->getSaleProducts(5);

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
    <title>GameKey Store - Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7f13ec",
                        "accent-cyan": "#00d9ff",
                        "accent-pink": "#ff006e",
                        "accent-gold": "#ffd60a",
                        "background-light": "#f7f6f8",
                        "background-dark": "#0a0510",
                        "surface-dark": "#1a1028",
                        "surface-light": "#2d1b4e",
                    },
                    fontFamily: { "display": ["Space Grotesk", "sans-serif"] },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #7f13ec 0%, #1a1028 100%)',
                        'gradient-sale': 'linear-gradient(135deg, #ff006e 0%, #7f13ec 100%)',
                        'gradient-accent': 'linear-gradient(135deg, #00d9ff 0%, #7f13ec 100%)',
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(to bottom, #0a0510, #1a1028);
        }

        .glass-effect {
            background: rgba(26, 16, 40, 0.8);
            backdrop-filter: blur(12px);
            border-color: rgba(127, 19, 236, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #7f13ec, #00d9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glow-primary {
            box-shadow: 0 0 20px rgba(127, 19, 236, 0.3);
        }

        .glow-pink {
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.3);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white transition-colors duration-300">
    <div class="layout-container flex h-full grow flex-col">
        <!-- Top Navigation Bar -->
        <header
            class="sticky top-0 z-50 glass-effect border-b border-white/10 px-4 md:px-20 py-3 flex items-center justify-between whitespace-nowrap">
            <div class="flex items-center gap-8">
                <a href="index.php" class="flex items-center gap-3 text-primary">
                    <div class="size-8">
                        <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 19.6865 45.8096 24L24 24L24 45.8096Z"
                                fill="currentColor"></path>
                        </svg>
                    </div>
                    <h2 class="text-white text-xl font-bold tracking-tight">GAMEKEY.VN</h2>
                </a>
                <nav class="hidden lg:flex items-center gap-6">
                    <a class="text-white/80 hover:text-primary text-sm font-medium transition-colors"
                        href="index.php?page=search&platform=steam">Steam</a>
                    <a class="text-white/80 hover:text-primary text-sm font-medium transition-colors"
                        href="index.php?page=search&platform=epic">Epic Games</a>
                    <a class="text-white/80 hover:text-primary text-sm font-medium transition-colors"
                        href="index.php?page=search&platform=ubisoft">Ubisoft</a>
                </nav>
            </div>
            <div class="flex flex-1 justify-end gap-6">
                <label class="hidden md:flex flex-col min-w-64 h-10">
                    <form action="index.php" method="GET"
                        class="flex w-full flex-1 items-stretch rounded-lg bg-surface-dark border border-white/10 focus-within:border-primary transition-all">
                        <input type="hidden" name="page" value="search">
                        <div class="text-slate-400 flex items-center justify-center pl-4">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </div>
                        <input name="q"
                            class="form-input flex w-full border-none bg-transparent focus:ring-0 text-white placeholder:text-slate-500 text-sm font-normal px-4"
                            placeholder="Tìm kiếm game, dlc, keys..." />
                    </form>
                </label>
                <div class="flex gap-3">
                    <a href="index.php?page=cart"
                        class="flex items-center justify-center rounded-lg h-10 w-10 bg-surface-dark border border-white/10 text-white hover:bg-primary/20 hover:text-primary transition-all">
                        <span class="material-symbols-outlined">shopping_cart</span>
                    </a>
                    <?php if (Auth::isLoggedIn()): ?>
                        <a href="index.php?page=dashboard"
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-surface-dark border border-white/10 text-white hover:bg-primary/20 hover:text-primary transition-all">
                            <span class="material-symbols-outlined">person</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=login"
                            class="flex items-center justify-center rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold hover:bg-primary/80 transition-all">
                            Đăng nhập
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <main class="flex-1 flex flex-col items-center">
            <div class="w-full max-w-[1280px] px-4 md:px-10 py-6">
                <!-- Hero Section -->
                <?php if (!empty($featuredProducts)):
                    $hero = $featuredProducts[0]; ?>
                    <section class="w-full mb-12">
                        <div class="@container">
                            <div class="relative overflow-hidden rounded-2xl group glow-primary">
                                <div class="flex min-h-[520px] flex-col gap-6 bg-cover bg-center bg-no-repeat items-start justify-end px-6 pb-12 md:px-12 md:pb-16 transition-transform duration-700 group-hover:scale-105 border border-primary/30"
                                    style='background-image: linear-gradient(135deg, rgba(127, 19, 236, 0.3) 0%, rgba(15, 10, 22, 0.95) 100%), url("<?php echo htmlspecialchars($hero['image'] ?: 'https://placehold.co/1200x600/7f13ec/white?text=GameKey'); ?>");'>
                                    <div class="flex flex-col gap-3 text-left max-w-2xl relative z-10">
                                        <span
                                            class="inline-block px-3 py-1 bg-primary text-xs font-bold uppercase tracking-widest rounded text-white self-start">Hot</span>
                                        <h1 class="text-white text-5xl font-black leading-tight tracking-tight md:text-6xl">
                                            <?php echo htmlspecialchars($hero['name']); ?>
                                        </h1>
                                        <p class="text-slate-300 text-base md:text-lg max-w-lg leading-relaxed">
                                            <?php echo htmlspecialchars(substr($hero['description'] ?? 'Trải nghiệm game tuyệt vời', 0, 150)); ?>...
                                        </p>
                                    </div>
                                    <div class="flex gap-4 relative z-10">
                                        <a href="index.php?page=product&id=<?php echo $hero['id']; ?>"
                                            class="flex items-center justify-center rounded-xl h-14 px-8 bg-gradient-to-r from-primary to-accent-cyan text-white text-base font-bold transition-all hover:scale-110 active:scale-95 shadow-lg shadow-primary/50">
                                            <?php
                                            $heroPrice = $hero['price'];
                                            if ($hero['discount_percent'] > 0) {
                                                $heroPrice = $hero['price'] * (100 - $hero['discount_percent']) / 100;
                                            }
                                            ?>
                                            <span>🛒 Mua ngay - <?php echo formatPrice($heroPrice); ?></span>
                                        </a>
                                        <a href="index.php?page=product&id=<?php echo $hero['id']; ?>"
                                            class="flex items-center justify-center rounded-xl h-14 px-8 bg-white/5 backdrop-blur-md border border-white/30 text-white text-base font-bold hover:bg-white/15 hover:border-primary/50 transition-all hover:shadow-lg hover:shadow-primary/20">
                                            <span>📖 Xem chi tiết</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Flash Sale Section -->
                <?php if (!empty($saleProducts)): ?>
                    <section class="mb-12 rounded-2xl border border-accent-pink/40 p-8 glow-pink" style="background: linear-gradient(135deg, rgba(255, 0, 110, 0.1) 0%, rgba(127, 19, 236, 0.05) 100%);">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 px-2 gap-4">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-accent-pink text-4xl animate-bounce">bolt</span>
                                <div>
                                    <h2 class="text-white text-3xl font-black tracking-tight">⚡ Flash Sale</h2>
                                    <p class="text-accent-pink text-sm font-bold">Chỉ trong hôm nay</p>
                                </div>
                            </div>
                            <div id="timer" class="flex gap-2"></div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                            <?php foreach ($saleProducts as $product):
                                $discount = $product['discount_percent'] ?? 0;
                                $saleP = $discount > 0 ? $product['price'] * (100 - $discount) / 100 : $product['price'];
                                ?>
                                <a href="index.php?page=product&id=<?php echo $product['id']; ?>"
                                    class="group flex flex-col bg-surface-light rounded-xl overflow-hidden border border-accent-pink/30 hover:border-accent-pink/70 hover:shadow-xl hover:shadow-accent-pink/20 transition-all duration-300">
                                    <!-- Image -->
                                    <div class="relative overflow-hidden">
                                        <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://placehold.co/400x225/7f13ec/white?text=Game'); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            class="w-full aspect-[16/9] object-cover transition-transform duration-500 group-hover:scale-105"
                                            onerror="this.src='https://placehold.co/400x225/1a1028/7f13ec?text=Game'">
                                        <?php if ($discount > 0): ?>
                                            <div class="absolute top-2 right-2 bg-accent-pink text-white text-[11px] font-black px-2 py-0.5 rounded-md shadow">
                                                -<?php echo $discount; ?>%
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Info -->
                                    <div class="p-3 flex flex-col gap-1.5 flex-1">
                                        <h3 class="text-white text-sm font-bold line-clamp-1 group-hover:text-accent-cyan transition-colors">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h3>
                                        <div class="flex items-center gap-1 text-xs text-slate-400">
                                            <span class="text-accent-gold text-xs">★</span>
                                            <span class="text-white font-semibold">4.8</span>
                                            <span class="text-slate-500">•</span>
                                            <span><?php echo htmlspecialchars($product['platform_name'] ?? 'Steam'); ?></span>
                                        </div>
                                        <div class="flex items-center justify-between mt-auto pt-1">
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-accent-pink text-sm font-black"><?php echo formatPrice($saleP); ?></span>
                                                <?php if ($discount > 0): ?>
                                                    <span class="text-slate-500 text-[11px] line-through"><?php echo formatPrice($product['price']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <button
                                                class="flex items-center justify-center w-7 h-7 rounded-full bg-primary/50 hover:bg-primary text-white text-xs transition-all"
                                                data-product-id="<?php echo $product['id']; ?>"
                                                onclick="event.preventDefault(); addToCart(this.dataset.productId)">
                                                <span class="material-symbols-outlined text-[15px]">shopping_cart</span>
                                            </button>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Featured Products Section -->
                <section class="mb-12">
                    <div class="flex items-center justify-between mb-8 px-2">
                        <h2 class="text-white text-3xl font-black tracking-tight">✨ Sản Phẩm Mới Nhất</h2>
                        <a class="text-accent-cyan hover:text-white font-bold flex items-center gap-1 transition-all hover:gap-2"
                            href="index.php?page=search">
                            Xem tất cả <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        <?php foreach ($featuredProducts as $product):
                            $discount = $product['discount_percent'] ?? 0;
                            $cardPrice = $discount > 0 ? $product['price'] * (100 - $discount) / 100 : $product['price'];
                        ?>
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>"
                                class="group flex flex-col bg-surface-light rounded-xl overflow-hidden border border-primary/20 hover:border-accent-cyan/50 hover:shadow-xl hover:shadow-primary/20 transition-all duration-300">
                                <!-- Image -->
                                <div class="relative overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://placehold.co/400x225/7f13ec/white?text=Game'); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="w-full aspect-[16/9] object-cover transition-transform duration-500 group-hover:scale-105"
                                        onerror="this.src='https://placehold.co/400x225/1a1028/7f13ec?text=Game'">
                                    <?php if ($discount > 0): ?>
                                        <div class="absolute top-2 right-2 bg-accent-pink text-white text-[11px] font-black px-2 py-0.5 rounded-md shadow">
                                            -<?php echo $discount; ?>%
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Info -->
                                <div class="p-3 flex flex-col gap-1.5">
                                    <h3 class="text-white text-sm font-bold line-clamp-1 group-hover:text-accent-cyan transition-colors">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h3>
                                    <div class="flex items-center gap-1 text-xs text-slate-400">
                                        <span class="text-accent-gold text-xs">★</span>
                                        <span class="text-white font-semibold">4.9</span>
                                        <span class="text-slate-500">•</span>
                                        <span><?php echo htmlspecialchars($product['platform_name'] ?? 'Steam'); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between mt-1">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-accent-pink text-sm font-black"><?php echo formatPrice($cardPrice); ?></span>
                                            <?php if ($discount > 0): ?>
                                                <span class="text-slate-500 text-[11px] line-through"><?php echo formatPrice($product['price']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <button
                                            class="flex items-center justify-center w-7 h-7 rounded-full bg-primary/50 hover:bg-primary text-white transition-all"
                                            data-product-id="<?php echo $product['id']; ?>"
                                            onclick="event.preventDefault(); addToCart(this.dataset.productId)">
                                            <span class="material-symbols-outlined text-[15px]">shopping_cart</span>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Promo Section -->
                <section class="mb-16 grid lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-surface-light rounded-2xl p-8 border border-primary/20 glow-primary">
                        <h2 class="text-white text-2xl font-bold mb-6">Top Bán Chạy</h2>
                        <div class="space-y-4">
                            <?php
                            $topProducts = array_slice($featuredProducts, 0, 3);
                            $rank = 1;
                            foreach ($topProducts as $product):
                                ?>
                                <a href="index.php?page=product&id=<?php echo $product['id']; ?>"
                                    class="flex items-center gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition-all cursor-pointer group">
                                    <span
                                        class="text-4xl font-black text-white/10 group-hover:text-primary/30 transition-colors w-12 text-center italic">
                                        <?php echo str_pad($rank++, 2, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <div class="size-16 rounded-lg bg-cover bg-center shrink-0 shadow-lg"
                                        style='background-image: url("<?php echo htmlspecialchars($product['image'] ?: 'https://placehold.co/100x100/7f13ec/white?text=G'); ?>");'>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-white font-bold group-hover:text-primary transition-colors">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h4>
                                        <p class="text-slate-400 text-xs">
                                            <?php echo htmlspecialchars($product['developer'] ?? 'Unknown'); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-baseline gap-2">
                                            <?php
                                            $salePrice = $product['price'];
                                            if ($product['discount_percent'] > 0) {
                                                $salePrice = $product['price'] * (100 - $product['discount_percent']) / 100;
                                            }
                                            ?>
                                            <p class="text-primary text-base font-bold">
                                                <?php echo formatPrice($salePrice); ?>
                                            </p>
                                            <?php if ($product['discount_percent'] > 0): ?>
                                                <p class="text-slate-500 text-xs line-through">
                                                    <?php echo formatPrice($product['price']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-slate-500 text-[10px] uppercase font-black">
                                            <?php echo htmlspecialchars($product['platform_name'] ?? 'Steam'); ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div
                        class="rounded-2xl border border-accent-gold/50 p-8 flex flex-col justify-center items-center text-center glow-pink" style="background: linear-gradient(135deg, rgba(0, 217, 255, 0.1) 0%, rgba(127, 19, 236, 0.1) 100%);">
                        <div
                            class="size-24 bg-gradient-to-br from-accent-cyan to-primary rounded-full flex items-center justify-center mb-6 shadow-2xl shadow-primary/50">
                            <span class="material-symbols-outlined text-white text-5xl">card_giftcard</span>
                        </div>
                        <h3 class="text-white text-3xl font-black mb-3">🎁 Nhận Quà Tặng Game</h3>
                        <p class="text-slate-300 text-sm mb-8 leading-relaxed">Tham gia chương trình tích điểm đổi quà hoặc nhận key
                            game miễn phí hàng tháng.</p>
                        <a href="index.php?page=dashboard"
                            class="w-full h-12 rounded-xl bg-gradient-to-r from-primary to-accent-cyan text-white font-black hover:shadow-lg hover:shadow-primary/50 transition-all uppercase tracking-widest text-sm flex items-center justify-center hover:scale-105 active:scale-95">
                            🚀 Tìm hiểu ngay
                        </a>
                    </div>
                </section>
            </div>
        </main>
        <!-- Footer -->
        <footer class="bg-surface-dark border-t border-primary/20 py-16 px-4 md:px-20" style="background: linear-gradient(to bottom, rgba(26, 16, 40, 0.8), rgba(10, 5, 16, 0.9));">
            <div class="max-w-[1280px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <div class="flex flex-col gap-6">
                    <a href="index.php" class="flex items-center gap-3 text-primary">
                        <div class="size-6">
                            <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 19.6865 45.8096 24L24 24L24 45.8096Z"
                                    fill="currentColor"></path>
                            </svg>
                        </div>
                        <h2 class="text-white text-lg font-bold tracking-tight">GAMEKEY.VN</h2>
                    </a>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Cửa hàng game key uy tín hàng đầu Việt Nam. Cung cấp key bản quyền từ Steam, Epic, Battle.net
                        với giá tốt nhất thị trường.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Khám Phá</h4>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a class="hover:text-primary transition-colors"
                                href="index.php?page=search&filter=sale">Game Giảm Giá</a></li>
                        <li><a class="hover:text-primary transition-colors" href="index.php?page=search">Tất Cả Game</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Hỗ Trợ</h4>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="index.php?page=help">Hướng Dẫn Mua
                                Hàng</a></li>
                        <li><a class="hover:text-primary transition-colors" href="index.php?page=help">Liên Hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Tài khoản</h4>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="index.php?page=login">Đăng nhập</a>
                        </li>
                        <li><a class="hover:text-primary transition-colors" href="index.php?page=register">Đăng ký</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div
                class="max-w-[1280px] mx-auto mt-16 pt-8 border-t border-white/5 text-center text-slate-500 text-[12px]">
                <p>© 2024 GAMEKEY.VN. Tất cả quyền được bảo lưu.</p>
            </div>
        </footer>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-2xl text-white text-sm font-bold opacity-0 pointer-events-none transition-all duration-300 translate-y-4"
        style="background: rgba(26,16,40,0.95); border: 1px solid rgba(127,19,236,0.4); backdrop-filter: blur(12px); min-width:260px;">
        <span id="toast-icon" class="material-symbols-outlined text-[20px]">shopping_cart</span>
        <span id="toast-msg">Đã thêm vào giỏ hàng</span>
    </div>

    <script>
        // Show toast
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toast-icon');
            const msgEl = document.getElementById('toast-msg');
            msgEl.textContent = msg;
            icon.textContent = type === 'success' ? 'check_circle' : 'error';
            icon.style.color = type === 'success' ? '#00d9ff' : '#ff006e';
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
            toast.style.pointerEvents = 'auto';
            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(1rem)';
                toast.style.pointerEvents = 'none';
            }, 2500);
        }

        // Add to cart via API
        function addToCart(productId) {
            const btn = document.querySelector(`[data-product-id="${productId}"]`);
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            }
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('api/cart.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ Đã thêm vào giỏ hàng!', 'success');
                    // Update cart count badge if present
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount && data.cart_count !== undefined) {
                        cartCount.textContent = data.cart_count;
                        cartCount.classList.remove('hidden');
                    }
                } else {
                    showToast('❌ ' + (data.message || 'Thêm thất bại'), 'error');
                }
            })
            .catch(() => showToast('❌ Có lỗi kết nối', 'error'))
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            });
        }
    </script>
    <script>
        // Countdown timer
        function updateTimer() {
            const now = new Date();
            const end = new Date();
            end.setHours(23, 59, 59);
            const diff = Math.max(0, end - now);

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((diff % (1000 * 60)) / 1000);

            const timerEl = document.getElementById('timer');
            if (timerEl) {
                timerEl.innerHTML = `
                    <div class="flex flex-col items-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-accent-cyan border border-primary/50">
                            <p class="text-white text-lg font-black">${String(hours).padStart(2, '0')}</p>
                        </div>
                        <p class="text-accent-cyan text-[10px] uppercase font-black mt-1">Giờ</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-accent-pink border border-accent-pink/50">
                            <p class="text-white text-lg font-black">${String(mins).padStart(2, '0')}</p>
                        </div>
                        <p class="text-accent-pink text-[10px] uppercase font-black mt-1">Phút</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-accent-gold to-primary border border-accent-gold/50">
                            <p class="text-white text-lg font-black">${String(secs).padStart(2, '0')}</p>
                        </div>
                        <p class="text-accent-gold text-[10px] uppercase font-black mt-1">Giây</p>
                    </div>
                `;
            }
        }
        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>

</html>