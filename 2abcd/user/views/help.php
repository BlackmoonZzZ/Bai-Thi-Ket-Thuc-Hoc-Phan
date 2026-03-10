<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Trợ giúp & Hướng dẫn - GAMEKEY.VN</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
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
                    fontFamily: {
                        "display": ["Space Grotesk", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
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

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white min-h-screen">
    <!-- Header -->
    <header
        class="sticky top-0 z-50 glass-effect border-b border-white/10 px-4 md:px-20 py-3 flex items-center justify-between">
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
                    href="index.php?page=home">Cửa hàng</a>
                <a class="text-white/80 hover:text-primary text-sm font-medium transition-colors"
                    href="index.php?page=library">Thư viện</a>
                <a class="text-white/80 hover:text-primary text-sm font-medium transition-colors"
                    href="index.php?page=wallet">Ví tiền</a>
                <a class="text-primary text-sm font-bold border-b-2 border-primary pb-1" href="index.php?page=help">Trợ
                    giúp</a>
            </nav>
        </div>
        <div class="flex flex-1 justify-end items-center gap-4">
            <a href="index.php?page=cart"
                class="size-10 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 hover:bg-primary/20 hover:text-primary transition-all">
                <span class="material-symbols-outlined">shopping_cart</span>
            </a>
            <a href="index.php?page=dashboard"
                class="h-10 px-3 bg-surface-dark rounded-lg border border-white/10 flex items-center gap-2 hover:border-primary/50 transition-all">
                <div class="size-7 rounded-full bg-primary/20 overflow-hidden">
                    <img class="w-full h-full object-cover" alt="Avatar"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuB__lQ3FbV6vsvQOiGTmCmL64R0JlJYalrPpsjPjWfkQqFmR3Nn0ztzKsuNeif7E1RK9oLeOrgJBg5JqBWcMpZYPxN24X0VF0ihDbWeTNFdOfZ8jUoRkvD4xnnF7hj7QMBhEqE95zLVs23MkUQcAbjzKy7FtfKLT2J9C3wLD_UIZyRbyXSQazTjG8AQNeyC8DX9rwM8v0hNNDg6pMPkTjYjr1eBj5qxIrd1iFUMqKVz2zkVsfkGrvDVu5-v1lGQWq_mu3elvhd3iH6R" />
                </div>
                <span class="text-xs font-bold text-white">Tài khoản</span>
            </a>
        </div>
    </header>

    <main class="max-w-[1200px] mx-auto px-6 py-10">
        <!-- Page Title -->
        <div class="mb-10">
            <h1 class="text-4xl font-black tracking-tight mb-2">Trung tâm trợ giúp</h1>
            <p class="text-white/60">Tìm câu trả lời cho các câu hỏi thường gặp và hướng dẫn sử dụng</p>
        </div>

        <?php
        $topic = isset($_GET['topic']) ? $_GET['topic'] : 'general';
        ?>

        <!-- Topic Tabs -->
        <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
            <a href="index.php?page=help&topic=general"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= $topic === 'general' ? 'bg-primary text-white' : 'bg-surface-dark text-white/60 hover:text-white' ?>">
                Tổng quan
            </a>
            <a href="index.php?page=help&topic=activation"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= $topic === 'activation' ? 'bg-primary text-white' : 'bg-surface-dark text-white/60 hover:text-white' ?>">
                Kích hoạt Key
            </a>
            <a href="index.php?page=help&topic=payment"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= $topic === 'payment' ? 'bg-primary text-white' : 'bg-surface-dark text-white/60 hover:text-white' ?>">
                Thanh toán
            </a>
            <a href="index.php?page=help&topic=faq"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= $topic === 'faq' ? 'bg-primary text-white' : 'bg-surface-dark text-white/60 hover:text-white' ?>">
                FAQ
            </a>
        </div>

        <!-- Content based on topic -->
        <?php if ($topic === 'activation'): ?>
            <!-- Activation Guide -->
            <div class="space-y-6">
                <div class="bg-surface-dark border border-white/10 rounded-xl p-6">
                    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">vpn_key</span>
                        Hướng dẫn kích hoạt Key Game
                    </h2>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="size-10 shrink-0 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                                1</div>
                            <div>
                                <h3 class="font-bold mb-1">Sao chép Key</h3>
                                <p class="text-white/60 text-sm">Vào thư viện game của bạn, di chuột vào game cần kích hoạt
                                    và nhấn nút "Lấy Key". Sau đó nhấn biểu tượng copy để sao chép mã key.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="size-10 shrink-0 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                                2</div>
                            <div>
                                <h3 class="font-bold mb-1">Mở Steam/Epic/Launcher tương ứng</h3>
                                <p class="text-white/60 text-sm">Mở ứng dụng launcher phù hợp với key game của bạn (Steam,
                                    Epic Games, GOG, EA App,...).</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="size-10 shrink-0 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                                3</div>
                            <div>
                                <h3 class="font-bold mb-1">Kích hoạt sản phẩm</h3>
                                <p class="text-white/60 text-sm">Đối với Steam: Vào menu Games → Activate a Product on Steam
                                    → Dán key và nhấn Next.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="size-10 shrink-0 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                                4</div>
                            <div>
                                <h3 class="font-bold mb-1">Tải và chơi game</h3>
                                <p class="text-white/60 text-sm">Sau khi kích hoạt thành công, game sẽ được thêm vào thư
                                    viện. Bạn có thể tải xuống và bắt đầu chơi ngay!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-primary/10 border border-primary/30 rounded-xl p-6">
                    <h3 class="font-bold mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">info</span>
                        Lưu ý quan trọng
                    </h3>
                    <ul class="text-white/70 text-sm space-y-2">
                        <li>• Mỗi key chỉ có thể kích hoạt một lần duy nhất</li>
                        <li>• Key đã kích hoạt không thể hoàn trả hoặc chuyển nhượng</li>
                        <li>• Kiểm tra kỹ region/khu vực của key trước khi mua</li>
                        <li>• Liên hệ hỗ trợ ngay nếu gặp vấn đề khi kích hoạt</li>
                    </ul>
                </div>
            </div>

        <?php elseif ($topic === 'payment'): ?>
            <!-- Payment Guide -->
            <div class="space-y-6">
                <div class="bg-surface-dark border border-white/10 rounded-xl p-6">
                    <h2 class="text-2xl font-bold mb-4">Phương thức thanh toán</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <h3 class="font-bold mb-2">💳 Ví điện tử</h3>
                            <p class="text-white/60 text-sm">Thanh toán qua MoMo, ZaloPay, VNPay với ưu đãi đặc biệt</p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <h3 class="font-bold mb-2">🏦 Chuyển khoản ngân hàng</h3>
                            <p class="text-white/60 text-sm">Hỗ trợ tất cả ngân hàng Việt Nam qua QR Code</p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <h3 class="font-bold mb-2">💰 Số dư ví GAMEKEY</h3>
                            <p class="text-white/60 text-sm">Nạp tiền vào ví và thanh toán nhanh chóng</p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <h3 class="font-bold mb-2">🎁 Thẻ quà tặng</h3>
                            <p class="text-white/60 text-sm">Sử dụng mã thẻ quà tặng GAMEKEY để thanh toán</p>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($topic === 'faq'): ?>
            <!-- FAQ -->
            <div class="space-y-4">
                <div class="bg-surface-dark border border-white/10 rounded-xl overflow-hidden">
                    <details class="group">
                        <summary class="p-4 cursor-pointer flex items-center justify-between font-bold hover:bg-white/5">
                            Key game có thời hạn sử dụng không?
                            <span
                                class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="p-4 pt-0 text-white/60 text-sm">
                            Không, key game không có thời hạn sử dụng. Bạn có thể kích hoạt bất cứ lúc nào sau khi mua.
                        </div>
                    </details>
                </div>

                <div class="bg-surface-dark border border-white/10 rounded-xl overflow-hidden">
                    <details class="group">
                        <summary class="p-4 cursor-pointer flex items-center justify-between font-bold hover:bg-white/5">
                            Tôi có thể hoàn trả key đã mua không?
                            <span
                                class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="p-4 pt-0 text-white/60 text-sm">
                            Key chưa kích hoạt có thể được hoàn trả trong vòng 7 ngày. Key đã kích hoạt không thể hoàn trả.
                        </div>
                    </details>
                </div>

                <div class="bg-surface-dark border border-white/10 rounded-xl overflow-hidden">
                    <details class="group">
                        <summary class="p-4 cursor-pointer flex items-center justify-between font-bold hover:bg-white/5">
                            Làm sao để liên hệ hỗ trợ?
                            <span
                                class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="p-4 pt-0 text-white/60 text-sm">
                            Bạn có thể liên hệ qua email support@gamekey.vn hoặc chat trực tiếp trên website 24/7.
                        </div>
                    </details>
                </div>

                <div class="bg-surface-dark border border-white/10 rounded-xl overflow-hidden">
                    <details class="group">
                        <summary class="p-4 cursor-pointer flex items-center justify-between font-bold hover:bg-white/5">
                            Key có hoạt động ở Việt Nam không?
                            <span
                                class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="p-4 pt-0 text-white/60 text-sm">
                            Tất cả key tại GAMEKEY.VN đều là key Global hoặc key VN, hoạt động tại Việt Nam. Thông tin
                            region được ghi rõ trên mỗi sản phẩm.
                        </div>
                    </details>
                </div>
            </div>

        <?php else: ?>
            <!-- General Help -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="index.php?page=help&topic=activation"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-all group">
                    <div
                        class="size-12 bg-primary/20 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-symbols-outlined">vpn_key</span>
                    </div>
                    <h3 class="font-bold mb-2">Kích hoạt Key</h3>
                    <p class="text-white/60 text-sm">Hướng dẫn chi tiết cách kích hoạt key game trên các nền tảng</p>
                </a>

                <a href="index.php?page=help&topic=payment"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-all group">
                    <div
                        class="size-12 bg-primary/20 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <h3 class="font-bold mb-2">Thanh toán</h3>
                    <p class="text-white/60 text-sm">Các phương thức thanh toán được hỗ trợ và hướng dẫn nạp tiền</p>
                </a>

                <a href="index.php?page=help&topic=faq"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-all group">
                    <div
                        class="size-12 bg-primary/20 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-symbols-outlined">help</span>
                    </div>
                    <h3 class="font-bold mb-2">FAQ</h3>
                    <p class="text-white/60 text-sm">Câu hỏi thường gặp và giải đáp thắc mắc</p>
                </a>

                <a href="mailto:support@gamekey.vn"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-all group">
                    <div
                        class="size-12 bg-primary/20 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-symbols-outlined">mail</span>
                    </div>
                    <h3 class="font-bold mb-2">Liên hệ hỗ trợ</h3>
                    <p class="text-white/60 text-sm">Gửi email hoặc chat trực tiếp với đội ngũ hỗ trợ 24/7</p>
                </a>

                <a href="index.php?page=library"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-all group">
                    <div
                        class="size-12 bg-primary/20 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-symbols-outlined">folder</span>
                    </div>
                    <h3 class="font-bold mb-2">Thư viện game</h3>
                    <p class="text-white/60 text-sm">Quản lý và xem các key game đã mua</p>
                </a>

                <a href="index.php?page=wallet"
                    class="bg-surface-dark border border-white/10 rounded-xl p-6 hover:border-primary/50 transition-all group">
                    <div
                        class="size-12 bg-primary/20 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </div>
                    <h3 class="font-bold mb-2">Quản lý ví</h3>
                    <p class="text-white/60 text-sm">Nạp tiền và quản lý số dư ví GAMEKEY</p>
                </a>
            </div>
        <?php endif; ?>

        <!-- Contact Section -->
        <div
            class="mt-12 bg-gradient-to-r from-primary/20 to-surface-dark border border-primary/30 rounded-xl p-8 text-center">
            <h2 class="text-2xl font-bold mb-2">Cần hỗ trợ thêm?</h2>
            <p class="text-white/60 mb-6">Đội ngũ hỗ trợ của chúng tôi luôn sẵn sàng giúp đỡ bạn 24/7</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="mailto:support@gamekey.vn"
                    class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-lg transition-all inline-flex items-center gap-2">
                    <span class="material-symbols-outlined">mail</span>
                    support@gamekey.vn
                </a>
                <a href="tel:19001234"
                    class="px-6 py-3 bg-surface-dark border border-white/10 hover:bg-white/10 font-bold rounded-lg transition-all inline-flex items-center gap-2">
                    <span class="material-symbols-outlined">call</span>
                    1900 1234
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-white/10 py-10 glass-effect">
        <div class="max-w-[1200px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2 opacity-50">
                <div class="size-6 text-primary">
                    <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 19.6865 45.8096 24L24 24L24 45.8096Z"
                            fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold tracking-tight text-white">GAMEKEY.VN</h2>
            </div>
            <div class="flex gap-8 text-xs text-white/50 font-medium">
                <a class="hover:text-primary transition-colors" href="index.php?page=privacy">Chính sách bảo mật</a>
                <a class="hover:text-primary transition-colors" href="index.php?page=terms">Điều khoản dịch vụ</a>
                <a class="hover:text-primary transition-colors" href="index.php?page=help">Trợ giúp</a>
            </div>
            <p class="text-xs text-white/50">© 2024 GAMEKEY.VN. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>