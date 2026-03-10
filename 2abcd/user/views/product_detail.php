<?php
require_once __DIR__ . '/../../includes/product.php';
require_once __DIR__ . '/../../includes/auth.php';

$productId = $_GET['id'] ?? null;
if (!$productId) {
    header('Location: index.php');
    exit;
}

$productModel = new Product();
$product = $productModel->getProduct($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . 'đ';
}

$discountedPrice = $product['price'];
if ($product['discount_percent'] > 0) {
    $discountedPrice = $product['price'] * (100 - $product['discount_percent']) / 100;
}

// Get related products
$relatedProducts = $productModel->getFeaturedProducts(6);
?>
<!DOCTYPE html>

<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo htmlspecialchars($product['name']); ?> - GAMEKEY.VN</title>
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
                        "background-light": "#f7f6f8",
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

<body class="bg-background-dark text-white">
    <!-- Header -->
    <header
        class="sticky top-0 z-50 glass-effect border-b border-white/10 px-4 md:px-20 py-3 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <a href="index.php" class="flex items-center gap-3 text-primary">
                <div class="size-8">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 19.6865 45.8096 24L24 24L24 45.8096Z"
                            fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="text-white text-xl font-bold tracking-tight">GAMEKEY.VN</h2>
            </a>
        </div>
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
    </header>

    <main class="max-w-[1280px] mx-auto px-4 md:px-10 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
            <a href="index.php" class="hover:text-primary transition-colors">Trang chủ</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <a href="index.php?page=search" class="hover:text-primary transition-colors">Games</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-white"><?php echo htmlspecialchars($product['name']); ?></span>
        </nav>

        <!-- Product Details -->
        <div class="grid lg:grid-cols-2 gap-12 mb-16">
            <!-- Product Image -->
            <div class="relative">
                <?php if ($product['discount_percent'] > 0): ?>
                    <div class="absolute top-4 left-4 bg-red-600 text-white text-sm font-bold px-3 py-1 rounded-full z-10">
                        -<?php echo $product['discount_percent']; ?>%
                    </div>
                <?php endif; ?>
                <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-dark">
                    <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://placehold.co/800x600/7f13ec/white?text=Game'); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover" />
                </div>
            </div>

            <!-- Product Info -->
            <div class="flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-primary/20 text-primary text-xs font-bold uppercase rounded">
                        <?php echo htmlspecialchars($product['platform_name'] ?? 'Steam'); ?>
                    </span>
                    <span class="px-3 py-1 bg-white/10 text-white/70 text-xs font-bold uppercase rounded">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Action'); ?>
                    </span>
                </div>

                <h1 class="text-4xl font-black mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center gap-1 text-yellow-400">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span
                                class="material-symbols-outlined text-[18px]"><?php echo $i < round($product['rating'] ?? 4.5) ? 'star' : 'star_border'; ?></span>
                        <?php endfor; ?>
                    </div>
                    <span class="text-slate-400 text-sm">(<?php echo $product['review_count'] ?? 0; ?> đánh giá)</span>
                </div>

                <p class="text-slate-300 leading-relaxed mb-8">
                    <?php echo nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả.')); ?>
                </p>

                <!-- Price -->
                <div class="bg-surface-dark rounded-xl p-6 mb-6">
                    <div class="flex items-baseline gap-4 mb-4">
                        <span
                            class="text-4xl font-black text-primary"><?php echo formatPrice($discountedPrice); ?></span>
                        <?php if ($product['discount_percent'] > 0): ?>
                            <span
                                class="text-xl text-slate-500 line-through"><?php echo formatPrice($product['price']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-slate-400 mb-4">
                        <span class="material-symbols-outlined text-[18px] text-green-500">check_circle</span>
                        <span><?php echo $product['stock_quantity'] ?? 0; ?> keys có sẵn</span>
                    </div>

                    <button onclick="addToCart(<?php echo $product['id']; ?>, <?php echo $product['stock_quantity'] ?? 0; ?>)"
                        class="w-full bg-primary hover:bg-primary/80 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/25 transition-all transform active:scale-95 flex items-center justify-center gap-2 mb-3">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        Thêm vào giỏ hàng
                    </button>

                    <button onclick="buyNow(<?php echo $product['id']; ?>, <?php echo $product['stock_quantity'] ?? 0; ?>)"
                        class="w-full bg-white/10 hover:bg-white/20 text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">bolt</span>
                        Mua ngay
                    </button>
                </div>

                <!-- Info -->
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-slate-400">
                        <span class="material-symbols-outlined text-primary">developer_board</span>
                        <span>Nhà phát triển: <strong
                                class="text-white"><?php echo htmlspecialchars($product['developer'] ?? 'Unknown'); ?></strong></span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-400">
                        <span class="material-symbols-outlined text-primary">calendar_month</span>
                        <span>Ngày phát hành: <strong
                                class="text-white"><?php echo $product['release_date'] ? date('d/m/Y', strtotime($product['release_date'])) : 'N/A'; ?></strong></span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-400">
                        <span class="material-symbols-outlined text-primary">vpn_key</span>
                        <span>Kích hoạt trên: <strong
                                class="text-white"><?php echo htmlspecialchars($product['platform_name'] ?? 'Steam'); ?></strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <section class="mb-16">
                <h2 class="text-2xl font-bold mb-8">Sản Phẩm Liên Quan</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <?php foreach ($relatedProducts as $related):
                        if ($related['id'] == $product['id'])
                            continue; ?>
                        <a href="index.php?page=product&id=<?php echo $related['id']; ?>" class="group flex flex-col gap-3">
                            <div class="relative overflow-hidden rounded-xl bg-surface-dark">
                                <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover transition-all duration-500 group-hover:scale-110"
                                    style='background-image: url("<?php echo htmlspecialchars($related['image'] ?: 'https://placehold.co/300x400/7f13ec/white?text=Game'); ?>");'>
                                </div>
                            </div>
                            <div>
                                <p class="text-white text-sm font-bold group-hover:text-primary transition-colors line-clamp-1">
                                    <?php echo htmlspecialchars($related['name']); ?>
                                </p>
                                <p class="text-primary text-sm font-medium">
                                    <?php
                                    $relPrice = $related['price'];
                                    if (($related['discount_percent'] ?? 0) > 0) {
                                        $relPrice = $related['price'] * (100 - $related['discount_percent']) / 100;
                                    }
                                    echo formatPrice($relPrice);
                                    ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-surface-dark border-t border-white/5 py-8 px-4 md:px-20">
        <div class="max-w-[1280px] mx-auto text-center text-slate-500 text-sm">
            <p>© 2024 GAMEKEY.VN. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>

    <script>
        function getApiBase() {
            const path = window.location.pathname;
            const match = path.match(/^\/[^\/]+/);
            return match ? match[0] : '';
        }

        async function addToCart(productId, stockQuantity) {
            <?php if (!Auth::isLoggedIn()): ?>
                window.location.href = 'index.php?page=login';
                return false;
            <?php endif; ?>

            if (stockQuantity <= 0) {
                alert('Sản phẩm này hiện đang tạm hết hàng (Out of stock)!');
                return false;
            }

            try {
                const base = getApiBase();
                const response = await fetch(`${base}/api/cart.php?action=add`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}&quantity=1`
                });
                const data = await response.json();
                if (data.success) {
                    alert('Đã thêm vào giỏ hàng!');
                    return true;
                } else {
                    alert(data.message || 'Lỗi thêm vào giỏ hàng');
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi kết nối');
                return false;
            }
        }

        async function buyNow(productId, stockQuantity) {
            <?php if (!Auth::isLoggedIn()): ?>
                window.location.href = 'index.php?page=login';
                return;
            <?php endif; ?>

            if (stockQuantity <= 0) {
                alert('Không thể thực hiện mua ngay: Sản phẩm này đã hết hàng (0 keys).');
                return;
            }

            const success = await addToCart(productId, stockQuantity);
            if(success) {
                window.location.href = 'index.php?page=checkout';
            }
        }
    </script>
</body>

</html>