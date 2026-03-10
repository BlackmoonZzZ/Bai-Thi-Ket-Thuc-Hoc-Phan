<!DOCTYPE html>

<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng nhập - GAMEKEY.VN</title>
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
                        <span class="material-symbols-outlined text-3xl">videogame_asset</span>
                    </div>
                </div>
                <h1 class="text-2xl font-black text-[#111218] dark:text-white tracking-tight">Chào mừng trở lại!</h1>
                <p class="text-[#616889] dark:text-white/60 text-sm mt-1">Đăng nhập để tiếp tục hành trình game thủ.</p>
            </div>
            <form id="loginForm" class="space-y-5">
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
                    <div class="text-right mt-1">
                        <a class="text-xs text-primary font-bold hover:underline"
                            href="index.php?page=forgot_password">Quên mật khẩu?</a>
                    </div>
                </div>
                <div id="errorMessage" class="hidden text-red-500 text-sm text-center bg-red-500/10 p-3 rounded-lg">
                </div>
                <button
                    class="w-full bg-primary hover:bg-primary/80 text-white font-bold py-3 rounded-xl shadow-lg shadow-primary/25 transition-all transform active:scale-95"
                    type="submit">
                    Đăng nhập
                </button>
            </form>
            <script>
                function getApiBase() {
                    const path = window.location.pathname;
                    const match = path.match(/^\/[^\/]+/);
                    return match ? match[0] : '';
                }

                document.getElementById('loginForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const base = getApiBase();
                    const apiUrl = `${base}/api/auth.php?action=login`;
                    const errorDiv = document.getElementById('errorMessage');
                    errorDiv.classList.add('hidden');

                    try {
                        const response = await fetch(apiUrl, {
                            method: 'POST',
                            body: formData
                        });
                        const text = await response.text();
                        console.log('Response Status:', response.status);
                        console.log('Response:', text);
                        
                        // Check if response is valid JSON
                        if (!text.trim().startsWith('{')) {
                            throw new Error('API không trả về kết quả JSON hợp lệ. vui lòng kiểm tra kết nối.');
                        }
                        
                        const data = JSON.parse(text);
                        if (data.success) {
                            // Nếu là admin thì chuyển vào trang admin
                            if (data.user && (data.user.role === 'admin' || data.user.role === 'staff')) {
                                window.location.href = base + '/admin/index.php';
                            } else {
                                // User chuyển về trang chủ
                                window.location.href = 'index.php?page=home';
                            }
                        } else {
                            errorDiv.textContent = data.message || data.error || 'Đăng nhập thất bại';
                            errorDiv.classList.remove('hidden');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        errorDiv.textContent = 'Lỗi: ' + error.message;
                        errorDiv.classList.remove('hidden');
                    }
                });
            </script>
            <div class="my-6 flex items-center gap-3">
                <div class="h-px bg-[#dbdde6] dark:bg-white/10 flex-1"></div>
                <span class="text-xs text-[#616889] dark:text-white/40 uppercase font-bold">Hoặc đăng nhập với</span>
                <div class="h-px bg-[#dbdde6] dark:bg-white/10 flex-1"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button
                    class="flex items-center justify-center gap-2 py-2.5 rounded-lg border border-[#dbdde6] dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <img alt="Google" class="size-5"
                        src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" />
                    <span class="text-sm font-bold text-[#111218] dark:text-white">Google</span>
                </button>
                <button
                    class="flex items-center justify-center gap-2 py-2.5 rounded-lg border border-[#dbdde6] dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <img alt="Facebook" class="size-5"
                        src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" />
                    <span class="text-sm font-bold text-[#111218] dark:text-white">Facebook</span>
                </button>
            </div>
            <div class="mt-8 text-center">
                <p class="text-sm text-[#616889] dark:text-white/60">Chưa có tài khoản? <a
                        class="text-primary font-bold hover:underline" href="index.php?page=register">Đăng ký ngay</a>
                </p>
            </div>
            <div class="mt-4 text-center">
                <a href="index.php" class="text-sm text-slate-400 hover:text-primary transition-colors">← Quay về trang
                    chủ</a>
            </div>
        </div>
    </div>
</body>

</html>