<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Quên mật khẩu - GAMEKEY.VN</title>
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

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white min-h-screen flex flex-col">
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
        </div>
        <div class="flex flex-1 justify-end items-center gap-4">
            <a href="index.php?page=login"
                class="text-sm font-medium text-white/80 hover:text-primary transition-colors">Đăng nhập</a>
            <a href="index.php?page=register"
                class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-all">Đăng
                ký</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <div class="bg-surface-dark border border-white/10 rounded-xl p-8">
                <div class="text-center mb-8">
                    <div class="size-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Quên mật khẩu?</h1>
                    <p class="text-white/60 text-sm">Nhập email đã đăng ký để nhận link đặt lại mật khẩu</p>
                </div>

                <form id="forgotPasswordForm" onsubmit="handleForgotPassword(event)">
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" required
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all text-white placeholder:text-white/40"
                            placeholder="Nhập email của bạn">
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">send</span>
                        Gửi link đặt lại mật khẩu
                    </button>
                </form>

                <!-- Success Message (hidden by default) -->
                <div id="successMessage" class="hidden">
                    <div class="text-center">
                        <div class="size-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-green-400 text-3xl">check_circle</span>
                        </div>
                        <h2 class="text-xl font-bold mb-2">Email đã được gửi!</h2>
                        <p class="text-white/60 text-sm mb-6">Vui lòng kiểm tra hộp thư (cả thư mục spam) để tìm link
                            đặt lại mật khẩu.</p>
                        <a href="index.php?page=login" class="text-primary font-bold hover:underline">Quay lại đăng
                            nhập</a>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="index.php?page=login" class="text-sm text-white/60 hover:text-primary transition-colors">
                        ← Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/10 py-6 glass-effect">
        <div class="max-w-[1200px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs text-white/50">© 2024 GAMEKEY.VN. All rights reserved.</p>
            <div class="flex gap-6 text-xs text-white/50 font-medium">
                <a class="hover:text-primary transition-colors" href="index.php?page=privacy">Chính sách bảo mật</a>
                <a class="hover:text-primary transition-colors" href="index.php?page=terms">Điều khoản dịch vụ</a>
                <a class="hover:text-primary transition-colors" href="index.php?page=help">Trợ giúp</a>
            </div>
        </div>
    </footer>

    <script>
        function handleForgotPassword(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const form = document.getElementById('forgotPasswordForm');
            const successMessage = document.getElementById('successMessage');
            const submitBtn = document.getElementById('submitBtn');

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Đang gửi...';

            // Simulate API call
            setTimeout(() => {
                form.style.display = 'none';
                successMessage.classList.remove('hidden');
            }, 1500);
        }
    </script>
</body>

</html>