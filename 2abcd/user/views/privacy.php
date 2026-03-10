<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chính sách bảo mật - GAMEKEY.VN</title>
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
                <a class="text-white/80 hover:text-primary text-sm font-medium transition-colors"
                    href="index.php?page=help">Trợ giúp</a>
            </nav>
        </div>
        <div class="flex flex-1 justify-end items-center gap-4">
            <a href="index.php?page=cart"
                class="size-10 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 hover:bg-primary/20 hover:text-primary transition-all">
                <span class="material-symbols-outlined">shopping_cart</span>
            </a>
        </div>
    </header>

    <main class="max-w-[900px] mx-auto px-6 py-10">
        <h1 class="text-4xl font-black tracking-tight mb-8">Chính sách bảo mật</h1>

        <div class="prose prose-invert max-w-none space-y-8">
            <section class="bg-surface-dark border border-white/10 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">1. Thu thập thông tin</h2>
                <p class="text-white/70 leading-relaxed">Chúng tôi thu thập thông tin bạn cung cấp khi đăng ký tài
                    khoản, mua hàng, hoặc liên hệ hỗ trợ. Thông tin này bao gồm: tên, email, số điện thoại, địa chỉ
                    thanh toán.</p>
            </section>

            <section class="bg-surface-dark border border-white/10 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">2. Sử dụng thông tin</h2>
                <p class="text-white/70 leading-relaxed">Thông tin được sử dụng để: xử lý đơn hàng, gửi thông báo về đơn
                    hàng, cải thiện dịch vụ, và hỗ trợ khách hàng. Chúng tôi không bán hoặc chia sẻ thông tin cá nhân
                    với bên thứ ba cho mục đích marketing.</p>
            </section>

            <section class="bg-surface-dark border border-white/10 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">3. Bảo mật thông tin</h2>
                <p class="text-white/70 leading-relaxed">GAMEKEY.VN sử dụng các biện pháp bảo mật tiêu chuẩn ngành để
                    bảo vệ thông tin của bạn, bao gồm mã hóa SSL và lưu trữ an toàn.</p>
            </section>

            <section class="bg-surface-dark border border-white/10 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">4. Cookie</h2>
                <p class="text-white/70 leading-relaxed">Website sử dụng cookie để cải thiện trải nghiệm người dùng và
                    phân tích lưu lượng truy cập. Bạn có thể tắt cookie trong cài đặt trình duyệt.</p>
            </section>

            <section class="bg-surface-dark border border-white/10 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">5. Liên hệ</h2>
                <p class="text-white/70 leading-relaxed">Nếu có câu hỏi về chính sách bảo mật, vui lòng liên hệ: <a
                        href="mailto:privacy@gamekey.vn" class="text-primary hover:underline">privacy@gamekey.vn</a></p>
            </section>
        </div>

        <p class="text-white/50 text-sm mt-8">Cập nhật lần cuối: 01/01/2024</p>
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
                <a class="hover:text-primary transition-colors text-primary" href="index.php?page=privacy">Chính sách
                    bảo mật</a>
                <a class="hover:text-primary transition-colors" href="index.php?page=terms">Điều khoản dịch vụ</a>
                <a class="hover:text-primary transition-colors" href="index.php?page=help">Trợ giúp</a>
            </div>
            <p class="text-xs text-white/50">© 2024 GAMEKEY.VN. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>