<!DOCTYPE html>

<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng ký - GAMEKEY.VN</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
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
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen flex items-center justify-center p-4">
    <div
        class="w-full max-w-md bg-white dark:bg-[#161926] rounded-2xl shadow-xl border border-[#dbdde6] dark:border-white/10 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div
                        class="size-12 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/30">
                        <span class="material-symbols-outlined text-3xl">person_add</span>
                    </div>
                </div>
                <h1 class="text-2xl font-black text-[#111218] dark:text-white tracking-tight">Tạo tài khoản mới</h1>
                <p class="text-[#616889] dark:text-white/60 text-sm mt-1">Gia nhập cộng đồng game thủ ngay hôm nay.</p>
            </div>
            <form id="registerForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-[#111218] dark:text-white mb-2">Họ và tên</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#616889]">badge</span>
                        <input
                            class="w-full bg-[#f6f6f8] dark:bg-black/20 border border-[#dbdde6] dark:border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all dark:text-white"
                            placeholder="Nguyễn Văn A" type="text" name="fullname" required />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#111218] dark:text-white mb-2">Tên đăng nhập</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#616889]">person</span>
                        <input
                            class="w-full bg-[#f6f6f8] dark:bg-black/20 border border-[#dbdde6] dark:border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all dark:text-white"
                            placeholder="username" type="text" name="username" required />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#111218] dark:text-white mb-2">Email</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#616889]">mail</span>
                        <input
                            class="w-full bg-[#f6f6f8] dark:bg-black/20 border border-[#dbdde6] dark:border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all dark:text-white"
                            placeholder="email@example.com" type="email" name="email" required />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#111218] dark:text-white mb-2">Mật khẩu</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#616889]">lock</span>
                        <input
                            class="w-full bg-[#f6f6f8] dark:bg-black/20 border border-[#dbdde6] dark:border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all dark:text-white"
                            placeholder="••••••••" type="password" name="password" required />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#111218] dark:text-white mb-2">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#616889]">lock_reset</span>
                        <input
                            class="w-full bg-[#f6f6f8] dark:bg-black/20 border border-[#dbdde6] dark:border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all dark:text-white"
                            placeholder="••••••••" type="password" name="confirm_password" required />
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <input class="rounded text-primary focus:ring-primary mt-1" type="checkbox" name="agree" required />
                    <span class="text-xs text-[#616889] dark:text-white/60">Tôi đồng ý với <a
                            class="text-primary font-bold hover:underline" href="index.php?page=terms">Điều khoản dịch
                            vụ</a> và <a class="text-primary font-bold hover:underline"
                            href="index.php?page=privacy">Chính sách bảo mật</a></span>
                </div>
                <div id="errorMessage" class="hidden text-red-500 text-sm text-center bg-red-500/10 p-3 rounded-lg">
                </div>
                <div id="successMessage"
                    class="hidden text-green-500 text-sm text-center bg-green-500/10 p-3 rounded-lg"></div>
                <button
                    class="w-full bg-primary hover:bg-primary/80 text-white font-bold py-3 rounded-xl shadow-lg shadow-primary/25 transition-all transform active:scale-95">
                    Đăng ký tài khoản
                </button>
            </form>
            <script>
                function getApiBase() {
                    const path = window.location.pathname;
                    const match = path.match(/^\/[^\/]+/);
                    return match ? match[0] : '';
                }

                document.getElementById('registerForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const base = getApiBase();
                    const apiUrl = `${base}/api/auth.php?action=register`;
                    const errorDiv = document.getElementById('errorMessage');
                    const successDiv = document.getElementById('successMessage');
                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');

                    // Validate password match
                    if (formData.get('password') !== formData.get('confirm_password')) {
                        errorDiv.textContent = 'Mật khẩu xác nhận không khớp';
                        errorDiv.classList.remove('hidden');
                        return;
                    }

                    try {
                        const response = await fetch(apiUrl, {
                            method: 'POST',
                            body: formData
                        });
                        const text = await response.text();
                        console.log('Response:', text);
                        const data = JSON.parse(text);
                        if (data.success) {
                            successDiv.textContent = 'Đăng ký thành công! Đang chuyển hướng...';
                            successDiv.classList.remove('hidden');
                            setTimeout(() => {
                                window.location.href = 'index.php?page=login';
                            }, 1500);
                        } else {
                            errorDiv.textContent = data.message || data.error || 'Đăng ký thất bại';
                            errorDiv.classList.remove('hidden');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        errorDiv.textContent = 'Lỗi kết nối: ' + error.message;
                        errorDiv.classList.remove('hidden');
                    }
                });
            </script>
            <div class="mt-8 text-center">
                <p class="text-sm text-[#616889] dark:text-white/60">Đã có tài khoản? <a
                        class="text-primary font-bold hover:underline" href="index.php?page=login">Đăng nhập</a></p>
            </div>
            <div class="mt-4 text-center">
                <a href="index.php" class="text-sm text-slate-400 hover:text-primary transition-colors">← Quay về trang
                    chủ</a>
            </div>
        </div>
    </div>
</body>

</html>