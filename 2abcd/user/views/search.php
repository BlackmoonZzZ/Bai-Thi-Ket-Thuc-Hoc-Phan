<?php
require_once __DIR__ . '/../../includes/product.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

$productModel = new Product();

// Get search parameters
$query = $_GET['q'] ?? '';
$categoryId = $_GET['category'] ?? '';
$platformId = $_GET['platform'] ?? '';
$filter = $_GET['filter'] ?? '';
$page = max(1, intval($_GET['p'] ?? 1));
$perPage = 12;

// Build filters
$filters = [];
if (!empty($query)) {
    $filters['search'] = $query;
}
if ($filter === 'sale') {
    $filters['only_sale'] = true;
}
if (!empty($categoryId)) {
    $filters['category_id'] = $categoryId;
}
if (!empty($platformId)) {
    // Platform filter can be a name (from homepage links like "steam") or an ID
    if (!is_numeric($platformId)) {
        // Resolve platform name to ID
        try {
            $stmt = $conn->prepare("SELECT id FROM platforms WHERE LOWER(name) LIKE ?");
            $stmt->execute(['%' . strtolower($platformId) . '%']);
            $platformRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($platformRow) {
                $filters['platform_id'] = $platformRow['id'];
            }
        } catch (Exception $e) {
            error_log("Platform lookup error: " . $e->getMessage());
        }
    } else {
        $filters['platform_id'] = $platformId;
    }
}

// Get products and count
$products = $productModel->getProducts($filters, $perPage, ($page - 1) * $perPage);
$totalProducts = $productModel->getTotalCount($filters);

$totalPages = ceil($totalProducts / $perPage);

// Get categories and platforms for filters
$categories = [];
$platforms = [];
try {
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT pl.id, pl.name, COUNT(p.id) as product_count FROM platforms pl LEFT JOIN products p ON pl.id = p.platform_id AND p.status = 'active' GROUP BY pl.id, pl.name HAVING product_count > 0 ORDER BY product_count DESC");
    $platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error loading filters: " . $e->getMessage());
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
    <title>Tìm kiếm Game - GAMEKEY.VN</title>
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
        <div class="flex flex-1 justify-end gap-6">
            <form action="index.php" method="GET" class="hidden md:flex flex-col min-w-64 h-10">
                <div
                    class="flex w-full flex-1 items-stretch rounded-lg bg-surface-dark border border-white/10 focus-within:border-primary transition-all">
                    <input type="hidden" name="page" value="search">
                    <div class="text-slate-400 flex items-center justify-center pl-4">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                    <input name="q" value="<?php echo htmlspecialchars($query); ?>"
                        class="form-input flex w-full border-none bg-transparent focus:ring-0 text-white placeholder:text-slate-500 text-sm font-normal px-4"
                        placeholder="Tìm kiếm game..." />
                </div>
            </form>
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

    <main class="max-w-[1280px] mx-auto px-4 md:px-10 py-8">
        <div class="flex gap-8">
            <!-- Sidebar Filters -->
            <aside class="hidden lg:block w-64 shrink-0">
                <div class="sticky top-24 bg-surface-dark rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-6">Bộ lọc</h3>

                    <!-- Categories -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-slate-400 uppercase mb-3">Thể loại</h4>
                        <div class="space-y-2">
                            <a href="index.php?page=search"
                                class="block text-sm <?php echo empty($categoryId) ? 'text-primary font-bold' : 'text-slate-300 hover:text-primary'; ?> transition-colors">
                                Tất cả
                            </a>
                            <?php foreach (array_slice($categories, 0, 10) as $cat): ?>
                                <a href="index.php?page=search&category=<?php echo $cat['id']; ?>"
                                    class="block text-sm <?php echo $categoryId == $cat['id'] ? 'text-primary font-bold' : 'text-slate-300 hover:text-primary'; ?> transition-colors">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Platforms -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-slate-400 uppercase mb-3">Nền tảng</h4>
                        <div class="space-y-2">
                            <?php foreach (array_slice($platforms, 0, 10) as $plat): ?>
                                <a href="index.php?page=search&platform=<?php echo $plat['id']; ?><?php echo !empty($query) ? '&q=' . urlencode($query) : ''; ?><?php echo !empty($categoryId) ? '&category=' . $categoryId : ''; ?>"
                                    class="block text-sm <?php echo $platformId == $plat['id'] || (is_string($platformId) && !is_numeric($platformId) && stripos($plat['name'], $platformId) !== false) ? 'text-primary font-bold' : 'text-slate-300 hover:text-primary'; ?> transition-colors">
                                    <?php echo htmlspecialchars($plat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quick Filters -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-slate-400 uppercase mb-3">Khác</h4>
                        <div class="space-y-2">
                            <a href="index.php?page=search&filter=sale"
                                class="block text-sm <?php echo $filter === 'sale' ? 'text-primary font-bold' : 'text-slate-300 hover:text-primary'; ?> transition-colors">
                                🔥 Đang giảm giá
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl font-bold">
                            <?php if (!empty($query)): ?>
                                Kết quả tìm kiếm: "
                                <?php echo htmlspecialchars($query); ?>"
                            <?php elseif ($filter === 'sale'): ?>
                                Game Đang Giảm Giá
                            <?php else: ?>
                                Tất Cả Game
                            <?php endif; ?>
                        </h1>
                        <p class="text-slate-400 text-sm mt-1">
                            <?php echo $totalProducts; ?> sản phẩm
                        </p>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="text-center py-20">
                        <span class="material-symbols-outlined text-6xl text-slate-600 mb-4">search_off</span>
                        <h3 class="text-xl font-bold text-slate-400">Không tìm thấy sản phẩm</h3>
                        <p class="text-slate-500 mt-2">Thử tìm kiếm với từ khóa khác</p>
                    </div>
                <?php else: ?>
                    <!-- Products -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <?php foreach ($products as $product): ?>
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="group flex flex-col gap-3">
                                <div class="relative overflow-hidden rounded-xl bg-surface-dark">
                                    <?php if (($product['discount_percent'] ?? 0) > 0): ?>
                                        <div
                                            class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-full z-10">
                                            -
                                            <?php echo $product['discount_percent']; ?>%
                                        </div>
                                    <?php endif; ?>
                                    <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover transition-all duration-500 group-hover:scale-110 group-hover:opacity-80"
                                        style='background-image: url("<?php echo htmlspecialchars($product['image'] ?: 'https://placehold.co/300x400/7f13ec/white?text=Game'); ?>");'>
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span
                                            class="bg-white text-primary font-bold px-4 py-2 rounded-lg translate-y-4 group-hover:translate-y-0 transition-transform">Xem
                                            chi tiết</span>
                                    </div>
                                </div>
                                <div>
                                    <p
                                        class="text-white text-sm font-bold group-hover:text-primary transition-colors line-clamp-1">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </p>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <?php
                                        $displayPrice = $product['price'];
                                        if (($product['discount_percent'] ?? 0) > 0) {
                                            $displayPrice = $product['price'] * (100 - $product['discount_percent']) / 100;
                                        }
                                        ?>
                                        <p class="text-primary text-sm font-bold">
                                            <?php echo formatPrice($displayPrice); ?>
                                        </p>
                                        <?php if (($product['discount_percent'] ?? 0) > 0): ?>
                                            <p class="text-slate-500 text-xs line-through">
                                                <?php echo formatPrice($product['price']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-slate-500 text-[11px] font-medium mt-1">
                                        <?php echo htmlspecialchars($product['platform_name'] ?? 'Steam'); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="flex justify-center gap-2 mt-12">
                            <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
                                <a href="index.php?page=search&q=<?php echo urlencode($query); ?>&category=<?php echo $categoryId; ?>&filter=<?php echo $filter; ?>&p=<?php echo $i; ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-lg <?php echo $i == $page ? 'bg-primary text-white' : 'bg-surface-dark text-white hover:bg-primary/30'; ?> transition-colors font-bold">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-surface-dark border-t border-white/5 py-8 px-4 md:px-20 mt-16">
        <div class="max-w-[1280px] mx-auto text-center text-slate-500 text-sm">
            <p>© 2024 GAMEKEY.VN. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>
</body>

</html>