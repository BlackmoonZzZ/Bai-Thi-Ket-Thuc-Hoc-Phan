<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Ví tiền - GAMEKEY.VN</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#7f13ec", "background-light": "#f7f6f8", "background-dark": "#0f0a16", "surface-dark": "#1d1429" }, fontFamily: { "display": ["Space Grotesk", "sans-serif"] }, borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" } } } }
    </script>
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen text-[#111218] dark:text-white">
    <div class="flex flex-col min-h-screen">
        <header
            class="border-b border-solid border-b-[#f0f1f4] dark:border-b-white/10 bg-white dark:bg-background-dark px-10 py-3 sticky top-0 z-50">
            <div class="flex items-center justify-between gap-8">
                <div class="flex items-center gap-4 text-primary">
                    <div class="size-8"><svg fill="none" viewbox="0 0 48 48">
                            <path
                                d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z"
                                fill="currentColor"></path>
                        </svg></div>
                    <h2 class="text-[#111218] dark:text-white text-xl font-bold">GameKeyStore</h2>
                </div>
                <div class="hidden lg:flex items-center gap-9"><a
                        class="text-sm font-medium hover:text-primary transition-colors" href="index.php?page=home">Cửa
                        hàng</a><a class="text-sm font-medium hover:text-primary transition-colors"
                        href="index.php?page=search&filter=sale">Khuyến mãi</a><a
                        class="text-sm font-medium hover:text-primary transition-colors" href="index.php?page=help">Tin
                        tức</a><a class="text-sm font-medium hover:text-primary transition-colors"
                        href="index.php?page=help">Hỗ trợ</a></div>
                <div class="flex flex-1 justify-end gap-6">
                    <div class="flex gap-2"><button
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f0f1f4] dark:bg-white/10 hover:bg-primary hover:text-white transition-all"><span
                                class="material-symbols-outlined text-xl">shopping_cart</span></button><button
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f0f1f4] dark:bg-white/10 hover:bg-primary hover:text-white transition-all"><span
                                class="material-symbols-outlined text-xl">notifications</span></button></div>
                    <div class="size-10 ring-2 ring-primary rounded-full bg-cover"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBpbrtw392gClb3N1LANfBVDNBedrTWGiYAjPHN6ymHwU4YItDalj0JI0EppxGGhL4wlqGobs7goxntRUVihtwIPWuLTaInYeCvSPeJh478mFBaCFq0J6FNn2OPQiUvVzxH9wtnsKTDherxS3FwcJpIi-K22i87m2YINMa-S8XxzjO_jLkBxkDVXM_XCUxw1J2U88GZHcmFQUXvLkMVMFk4RK-9nnfhEswDhQDXJFIKU4sgr6SpR6PHcrXvm7YLmOHwze0qXulyCQ2Q");'>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1 mx-auto w-full px-4 lg:px-10 py-8">
            <div class="max-w-[1200px] w-full flex flex-col gap-6">
                <div
                    class="flex flex-wrap items-end justify-between gap-4 p-4 bg-white dark:bg-white/5 rounded-xl border border-[#f0f1f4] dark:border-white/10">
                    <div>
                        <p class="text-4xl font-black text-[#111218] dark:text-white">Quản lý ví</p>
                        <p class="text-[#616889] dark:text-white/60 text-base mt-2">Theo dõi số dư và các lần nạp tiền
                        </p>
                    </div>
                    <button
                        class="flex items-center justify-center h-12 px-6 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 shadow-lg shadow-primary/20"><span
                            class="material-symbols-outlined mr-2">receipt</span>Hóa đơn</button>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 flex flex-col gap-6">
                        <div
                            class="relative bg-gradient-to-br from-primary to-primary/80 rounded-xl overflow-hidden p-8 text-white">
                            <div class="absolute inset-0 opacity-10 pointer-events-none"><svg class="w-full h-full"
                                    viewbox="0 0 100 100">
                                    <circle cx="20" cy="20" r="30" fill="currentColor" />
                                    <circle cx="80" cy="80" r="40" fill="currentColor" />
                                </svg></div>
                            <div class="relative z-10 flex flex-col justify-between h-full">
                                <p class="text-white/70 text-sm">Số dư hiện tại</p>
                                <p class="text-5xl font-black">1.520.000 đ</p>
                                <div class="flex gap-3 mt-8"><button
                                        class="flex items-center justify-center gap-2 px-6 h-10 bg-white/20 hover:bg-white/30 rounded-lg font-semibold border border-white/30"><span
                                            class="material-symbols-outlined">add</span>Nạp thêm</button><button
                                        class="flex items-center justify-center gap-2 px-6 h-10 bg-white/20 hover:bg-white/30 rounded-lg font-semibold border border-white/30"><span
                                            class="material-symbols-outlined">download</span>Xuất hóa đơn</button></div>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl overflow-hidden">
                            <div class="px-6 py-4 border-b border-[#f0f1f4] dark:border-white/10">
                                <h3 class="font-bold">Lịch sử giao dịch</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead
                                        class="bg-gray-50 dark:bg-white/5 border-b border-[#f0f1f4] dark:border-white/10">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-sm font-semibold">Mã giao dịch</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold">Ngày</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold">Loại</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold">Số tiền</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#f0f1f4] dark:divide-white/10">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-6 py-4"><span
                                                    class="font-mono font-bold text-primary">#GK29012</span></td>
                                            <td class="px-6 py-4 text-sm text-[#616889] dark:text-white/60">24/05/2024
                                            </td>
                                            <td class="px-6 py-4 text-sm">Nạp qua QR Bank</td>
                                            <td class="px-6 py-4 text-sm font-bold text-emerald-600">+500.000đ</td>
                                            <td class="px-6 py-4"><span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Thành
                                                    công</span></td>
                                        </tr>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-6 py-4"><span
                                                    class="font-mono font-bold text-primary">#GK29010</span></td>
                                            <td class="px-6 py-4 text-sm text-[#616889] dark:text-white/60">23/05/2024
                                            </td>
                                            <td class="px-6 py-4 text-sm">Mua "Elden Ring"</td>
                                            <td class="px-6 py-4 text-sm font-bold text-red-600">-1.200.000đ</td>
                                            <td class="px-6 py-4"><span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Thành
                                                    công</span></td>
                                        </tr>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-6 py-4"><span
                                                    class="font-mono font-bold text-primary">#GK28995</span></td>
                                            <td class="px-6 py-4 text-sm text-[#616889] dark:text-white/60">20/05/2024
                                            </td>
                                            <td class="px-6 py-4 text-sm">Nạp qua MoMo</td>
                                            <td class="px-6 py-4 text-sm font-bold text-emerald-600">+200.000đ</td>
                                            <td class="px-6 py-4"><span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Thành
                                                    công</span></td>
                                        </tr>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-6 py-4"><span
                                                    class="font-mono font-bold text-primary">#GK28980</span></td>
                                            <td class="px-6 py-4 text-sm text-[#616889] dark:text-white/60">18/05/2024
                                            </td>
                                            <td class="px-6 py-4 text-sm">Nạp qua QR Bank</td>
                                            <td class="px-6 py-4 text-sm font-bold text-emerald-600">+1.000.000đ</td>
                                            <td class="px-6 py-4"><span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Đang
                                                    xử lý</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-6 py-4 border-t border-[#f0f1f4] dark:border-white/10 text-sm text-[#616889] dark:text-white/60">
                                Hiển thị 1 - 4 của 12 giao dịch</div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div
                            class="bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl p-6">
                            <h3 class="font-bold mb-4">Phương thức nạp nhanh</h3>
                            <div class="flex flex-col gap-3"><button
                                    class="flex items-center gap-4 p-4 border-2 border-[#f0f1f4] dark:border-white/10 rounded-lg hover:border-primary hover:bg-primary/5 transition-all">
                                    <div class="size-12 flex items-center justify-center bg-primary/10 rounded-lg"><svg
                                            class="w-6 h-6 text-primary" fill="none" viewbox="0 0 24 24">
                                            <rect width="20" height="14" x="2" y="5" rx="1" />
                                            <path stroke="currentColor" stroke-width="2" d="M12 8v4M14 10h-4" />
                                        </svg></div>
                                    <div class="text-left flex-1">
                                        <p class="font-semibold text-sm">QR Bank</p>
                                        <p class="text-xs text-[#616889] dark:text-white/60">Tự động duyệt 24/7</p>
                                    </div><span class="material-symbols-outlined">chevron_right</span>
                                </button><button
                                    class="flex items-center gap-4 p-4 border-2 border-[#f0f1f4] dark:border-white/10 rounded-lg hover:border-primary hover:bg-primary/5 transition-all">
                                    <div
                                        class="size-12 flex items-center justify-center bg-red-500/10 rounded-lg font-black text-red-500">
                                        M</div>
                                    <div class="text-left flex-1">
                                        <p class="font-semibold text-sm">MoMo</p>
                                        <p class="text-xs text-[#616889] dark:text-white/60">Xác thực ngay lập tức</p>
                                    </div><span class="material-symbols-outlined">chevron_right</span>
                                </button><button
                                    class="flex items-center gap-4 p-4 border-2 border-[#f0f1f4] dark:border-white/10 rounded-lg hover:border-primary hover:bg-primary/5 transition-all">
                                    <div class="size-12 flex items-center justify-center bg-primary/10 rounded-lg"><svg
                                            class="w-6 h-6 text-primary" fill="none" viewbox="0 0 24 24">
                                            <rect width="20" height="14" x="2" y="5" rx="1" />
                                            <path stroke="currentColor" stroke-width="2" d="M2 11h20" />
                                        </svg></div>
                                    <div class="text-left flex-1">
                                        <p class="font-semibold text-sm">Thẻ ATM/Visa</p>
                                        <p class="text-xs text-[#616889] dark:text-white/60">Qua cổng VNPAY</p>
                                    </div><span class="material-symbols-outlined">chevron_right</span>
                                </button></div>
                        </div>
                        <div
                            class="bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl p-6">
                            <div class="flex items-start gap-3"><span
                                    class="material-symbols-outlined text-primary mt-1">shield_lock</span>
                                <div>
                                    <p class="font-semibold">Thanh toán bảo mật</p>
                                    <p class="text-xs text-[#616889] dark:text-white/60 mt-1">Tất cả giao dịch được bảo
                                        vệ bởi mã hóa SSL 256-bit</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-background-dark/50 dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl p-6">
                            <div class="flex items-start gap-3"><span
                                    class="material-symbols-outlined text-primary text-3xl">headset_mic</span>
                                <div class="flex-1">
                                    <p class="font-semibold">Cần hỗ trợ?</p>
                                    <p class="text-xs text-[#616889] dark:text-white/60 mt-1">Đội hỗ trợ 24/7 của chúng
                                        tôi luôn sẵn sàng giúp bạn</p><button
                                        class="mt-3 px-4 h-8 bg-primary text-white rounded text-xs font-bold hover:bg-primary/90">Chat
                                        với hỗ trợ 24/7</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </main>
        <footer
            class="mt-auto py-10 px-10 bg-white dark:bg-background-dark border-t border-[#f0f1f4] dark:border-white/10 text-center">
            <div class="max-w-[960px] mx-auto flex flex-col items-center gap-4">
                <p class="text-[#616889] dark:text-white/60 text-sm">© 2024 GameKeyStore. Hệ thống phân phối Key Game
                    hàng đầu.</p>
                <div class="flex gap-6 text-xs font-medium uppercase tracking-widest text-[#111218] dark:text-white/80">
                    <a class="hover:text-primary" href="index.php?page=privacy">Chính sách bảo mật</a>
                    <a class="hover:text-primary" href="index.php?page=terms">Điều khoản dịch vụ</a>
                    <a class="hover:text-primary" href="index.php?page=help">Liên hệ hỗ trợ</a>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>