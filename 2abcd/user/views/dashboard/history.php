<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Người dùng - Lịch sử đơn hàng | GAMEKEY.VN</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#7f13ec", "background-light": "#f7f6f8", "background-dark": "#0f0a16", "surface-dark": "#1d1429", "border-dark": "#2b283d" }, fontFamily: { "display": ["Space Grotesk", "sans-serif"] }, borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" } } } }
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

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white min-h-screen">
    <div class="flex flex-col min-h-screen">
        <header
            class="border-b border-border-dark bg-background-light dark:bg-background-dark px-4 md:px-10 lg:px-20 py-3 sticky top-0 z-50">
            <div class="max-w-[1200px] mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-3">
                        <div class="text-primary size-8"><svg fill="currentColor" viewbox="0 0 48 48">
                                <path
                                    d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 11.6284 45.8096 24L24 24L24 45.8096Z">
                                </path>
                            </svg></div>
                        <h2 class="text-lg font-bold tracking-tight hidden sm:block">GameKey Store</h2>
                    </div>
                    <nav class="hidden md:flex items-center gap-6">
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="index.php?page=home">Cửa hàng</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="index.php?page=library">Thư viện</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="index.php?page=help">Tin tức</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="index.php?page=help">Hỗ trợ</a>
                    </nav>
                </div>
                <div class="flex flex-1 justify-end gap-4 items-center">
                    <div class="relative max-w-xs w-full hidden sm:block">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                        <input
                            class="w-full bg-slate-100 dark:bg-surface-dark border-none rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary transition-all"
                            placeholder="Tìm kiếm game..." type="text" />
                    </div>
                    <div class="size-10 rounded-full border-2 border-primary overflow-hidden cursor-pointer">
                        <img alt="User avatar" class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqriF3a14xVpigW6bkimNISklWk0FSVAXvxj_0P9EW7NVqeqTcF_ha9omTcCEkXeA1-nIvKsIzI3Kbvh7s54ngvG-1cAgXmPanDWiVBv3hgIkOYgQ2iJi2lcSU44ToF59vTDf4yEiqy6DGlySa6aREz7016lqjqJSFQBgcfoY_UaS2gJRQ_U4ff_30CyAc7jlR3DLNEmRhukztOL85o_wcvvFS4oMSgIQ0UTaPEFZyEmOdHuBfSHKPb4uWw7uQy-fvB4zjHF0kD5YE" />
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1 max-w-[1200px] mx-auto w-full px-4 md:px-10 lg:px-20 py-8">
            <div class="flex items-center gap-2 mb-6">
                <a class="text-slate-500 dark:text-[#a19db9] text-sm font-medium hover:text-primary"
                    href="index.php?page=home">Trang
                    chủ</a>
                <span class="text-slate-400 dark:text-[#a19db9] text-sm">/</span>
                <a class="text-slate-500 dark:text-[#a19db9] text-sm font-medium hover:text-primary"
                    href="index.php?page=profile">Người
                    dùng</a>
                <span class="text-slate-400 dark:text-[#a19db9] text-sm">/</span>
                <span class="text-slate-900 dark:text-white text-sm font-semibold">Lịch sử đơn hàng</span>
            </div>
            <div class="flex flex-col lg:flex-row gap-8">
                <aside class="w-full lg:w-64 shrink-0">
                    <div
                        class="bg-slate-100 dark:bg-surface-dark rounded-xl p-2 border border-slate-200 dark:border-border-dark">
                        <div class="p-4 border-b border-slate-200 dark:border-border-dark mb-2">
                            <h1 class="text-base font-bold">Tài khoản của tôi</h1>
                            <p class="text-slate-500 dark:text-[#a19db9] text-xs">Quản lý đơn hàng &amp; bảo mật</p>
                        </div>
                        <nav class="flex flex-col gap-1">
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 dark:hover:bg-white/5 transition-colors group"
                                href="index.php?page=dashboard">
                                <span
                                    class="material-symbols-outlined text-slate-500 group-hover:text-primary">dashboard</span>
                                <span class="text-sm font-medium">Bảng điều khiển</span>
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 dark:hover:bg-white/5 transition-colors group"
                                href="index.php?page=profile">
                                <span
                                    class="material-symbols-outlined text-slate-500 group-hover:text-primary">person</span>
                                <span class="text-sm font-medium">Thông tin cá nhân</span>
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 dark:hover:bg-white/5 transition-colors group"
                                href="index.php?page=library">
                                <span
                                    class="material-symbols-outlined text-slate-500 group-hover:text-primary">sports_esports</span>
                                <span class="text-sm font-medium">Thư viện game</span>
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-white transition-colors"
                                href="index.php?page=history">
                                <span class="material-symbols-outlined">receipt_long</span>
                                <span class="text-sm font-medium">Lịch sử giao dịch</span>
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 dark:hover:bg-white/5 transition-colors group"
                                href="index.php?page=wallet">
                                <span
                                    class="material-symbols-outlined text-slate-500 group-hover:text-primary">account_balance_wallet</span>
                                <span class="text-sm font-medium">Ví &amp; Nạp tiền</span>
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 dark:hover:bg-white/5 transition-colors group"
                                href="index.php?page=dashboard&tab=settings">
                                <span
                                    class="material-symbols-outlined text-slate-500 group-hover:text-primary">settings</span>
                                <span class="text-sm font-medium">Cài đặt</span>
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-500/10 text-red-400 transition-colors mt-2"
                                href="index.php?page=logout">
                                <span class="material-symbols-outlined">logout</span>
                                <span class="text-sm font-medium">Đăng xuất</span>
                            </a>
                        </nav>
                    </div>
                </aside>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                        <div>
                            <h2 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Lịch sử đơn hàng</h2>
                            <p class="text-slate-500 dark:text-[#a19db9] text-base">Theo dõi và quản lý các giao dịch
                                mua game của bạn.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <button
                            class="px-5 py-2 rounded-lg bg-primary text-white text-sm font-semibold transition-all shadow-lg shadow-primary/20">Tất
                            cả</button>
                        <button
                            class="px-5 py-2 rounded-lg bg-slate-100 dark:bg-surface-dark hover:bg-slate-200 dark:hover:bg-border-dark text-slate-700 dark:text-white text-sm font-medium transition-all">Thành
                            công</button>
                        <button
                            class="px-5 py-2 rounded-lg bg-slate-100 dark:bg-surface-dark hover:bg-slate-200 dark:hover:bg-border-dark text-slate-700 dark:text-white text-sm font-medium transition-all">Đã
                            hủy</button>
                        <button
                            class="px-5 py-2 rounded-lg bg-slate-100 dark:bg-surface-dark hover:bg-slate-200 dark:hover:bg-border-dark text-slate-700 dark:text-white text-sm font-medium transition-all">Chờ
                            thanh toán</button>
                    </div>
                    <div
                        class="bg-white dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-border-dark overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-[#a19db9] uppercase tracking-wider">
                                            Mã đơn</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-[#a19db9] uppercase tracking-wider">
                                            Sản phẩm</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-[#a19db9] uppercase tracking-wider">
                                            Ngày mua</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-[#a19db9] uppercase tracking-wider text-right">
                                            Tổng tiền</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-[#a19db9] uppercase tracking-wider text-center">
                                            Trạng thái</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-[#a19db9] uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-border-dark">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td
                                            class="px-6 py-5 whitespace-nowrap font-mono text-sm text-primary font-bold">
                                            #GK-88291</td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 rounded bg-slate-200 dark:bg-border-dark overflow-hidden">
                                                    <img alt="Game cover" class="w-full h-full object-cover"
                                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSCP-iMpnl0MN_uRR-hNaacyikaOHDJzdBPwgDd5UMtMLFOgyj8ZLLkgKoNRDG3mqfztqAWmlUIRPVPiXFDF9SvM5L8ZMilFrdQgjIf9Bd91WYl-6TQCvuJuMfm4enfOo0EDM7gSd7IaApFFCw1xVIEeSDWzfgsJnyF401-m2TjXFOFUrcNnZmFrcFvGtj6nHc-v_7JdhSiYvtMimJI69o7miGgB1HF3so9dMggT1zevRAnO1J7c1dl8mdrKYnMNo6vbEM8Z3dyUN" />
                                                </div><span class="text-sm font-semibold truncate max-w-[180px]">Black
                                                    Myth: Wukong</span>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-5 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            24/05/2024</td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-right">1.200.000đ
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-center"><span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Thành
                                                công</span></td>
                                        <td class="px-6 py-5 whitespace-nowrap text-right"><button
                                                class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded text-xs font-bold transition-all">Chi
                                                tiết</button></td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td
                                            class="px-6 py-5 whitespace-nowrap font-mono text-sm text-primary font-bold">
                                            #GK-88102</td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 rounded bg-slate-200 dark:bg-border-dark overflow-hidden">
                                                    <img alt="Game cover" class="w-full h-full object-cover"
                                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3l9Z8q7ll7Y0Cd7pNcqFmP8la7sP2o4gECsCGPQMyzfTdRI6e-fhk7-vnV9aahTictbZHkVHCdmyneieVZ88bC0L0UlxZKvDBh5b8IlB6-fPpnWyXQj8V4wlt1bvVHHlLd4EixLdFlzfSzErO5KjpuLo-kKFwzeRERWRfsvZyw97Z-wo8QOebbruuJf_Y6jtqdjvCjV2t49_asMy1AAKDYQKU7CpqbENLiC8VSKE3DOzBLvinf6C-FbvpsqsciK8S3L_INbDcLbEl" />
                                                </div><span
                                                    class="text-sm font-semibold truncate max-w-[180px]">Cyberpunk
                                                    2077</span>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-5 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            12/05/2024</td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-right">450.000đ
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-center"><span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400">Đã
                                                hủy</span></td>
                                        <td class="px-6 py-5 whitespace-nowrap text-right"><button
                                                class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded text-xs font-bold transition-all">Chi
                                                tiết</button></td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td
                                            class="px-6 py-5 whitespace-nowrap font-mono text-sm text-primary font-bold">
                                            #GK-87955</td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 rounded bg-slate-200 dark:bg-border-dark overflow-hidden">
                                                    <img alt="Game cover" class="w-full h-full object-cover"
                                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAkg7W0NkPvhYmfL2z39DBtoURpPqFAIbmoeUR9SJQg0a-XNeVWDlSVDHvYAyAUTegfKrBf_fUfAetnOWI2MDQzox0U4YXTSDYgvzibbHsYsCOXELzim-w9b9nUZSMfR-tyD4FFSVPA4VgVQrHXubOWxvl7hzOMEj4c0cueauX4oUSEMFNY6I1MBziXwWD-v2OJkyGFDf4nIzwPVmWgX4hIPOP4yWO_gfDzPp_Z3uLOu3TNbJWNxXqqfVO-3t_SjjvdIjLwczjnwad_" />
                                                </div><span class="text-sm font-semibold truncate max-w-[180px]">Forza
                                                    Horizon 5</span>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-5 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            01/05/2024</td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-right">890.000đ
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-center"><span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">Chờ
                                                thanh toán</span></td>
                                        <td class="px-6 py-5 whitespace-nowrap text-right"><button
                                                class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded text-xs font-bold transition-all">Thanh
                                                toán</button></td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td
                                            class="px-6 py-5 whitespace-nowrap font-mono text-sm text-primary font-bold">
                                            #GK-87220</td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 rounded bg-slate-200 dark:bg-border-dark overflow-hidden">
                                                    <img alt="Game cover" class="w-full h-full object-cover"
                                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVxG25THLwo6OZmrudOoWZhcZGdq905bIat8qDupXk-EMWCOn9SyQ0j01-7_3X-GNRXXenL6oUNHkGTwD8oQ1qrB1_fLkIZ-SPWTlXOMnw0emY89eW3czX1NNh5gMPIpOE1ULz-Tv4j9y_ok5oGF47Xb44g3UKHrL-Ujw9hUGl3KKorDoTTqXgf6B84HPIZlDQv8DCP4JhfszjsLTaKITTUlEQ37htbUBKG-gFN8vaZ1OOztigndSJI_AZ18yo6VtQRycfq9KT0zBK" />
                                                </div><span class="text-sm font-semibold truncate max-w-[180px]">Elden
                                                    Ring</span>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-5 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            20/04/2024</td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-right">1.100.000đ
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-center"><span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Thành
                                                công</span></td>
                                        <td class="px-6 py-5 whitespace-nowrap text-right"><button
                                                class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded text-xs font-bold transition-all">Chi
                                                tiết</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            class="px-6 py-4 flex items-center justify-between border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/50">
                            <p class="text-sm text-slate-500 dark:text-[#a19db9]">Hiển thị 1 - 4 của 12 đơn hàng</p>
                            <div class="flex gap-2">
                                <button
                                    class="size-8 flex items-center justify-center rounded border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark hover:bg-slate-100 dark:hover:bg-border-dark disabled:opacity-50"
                                    disabled=""><span
                                        class="material-symbols-outlined text-sm">chevron_left</span></button>
                                <button
                                    class="size-8 flex items-center justify-center rounded bg-primary text-white text-sm font-bold">1</button>
                                <button
                                    class="size-8 flex items-center justify-center rounded border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark hover:bg-slate-100 dark:hover:bg-border-dark text-sm font-bold">2</button>
                                <button
                                    class="size-8 flex items-center justify-center rounded border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark hover:bg-slate-100 dark:hover:bg-border-dark text-sm font-bold">3</button>
                                <button
                                    class="size-8 flex items-center justify-center rounded border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark hover:bg-slate-100 dark:hover:bg-border-dark"><span
                                        class="material-symbols-outlined text-sm">chevron_right</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer
            class="bg-slate-100 dark:bg-surface-dark mt-auto border-t border-slate-200 dark:border-border-dark py-10">
            <div class="max-w-[1200px] mx-auto px-4 md:px-10 lg:px-20 grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="text-primary size-6"><svg fill="currentColor" viewbox="0 0 48 48">
                                <path
                                    d="M24 45.8096C19.6865 45.8096 15.4698 44.5305 11.8832 42.134C8.29667 39.7376 5.50128 36.3314 3.85056 32.3462C2.19985 28.361 1.76794 23.9758 2.60947 19.7452C3.451 15.5145 5.52816 11.6284 8.57829 8.5783C11.6284 5.52817 15.5145 3.45101 19.7452 2.60948C23.9758 1.76795 28.361 2.19986 32.3462 3.85057C36.3314 5.50129 39.7376 8.29668 42.134 11.8833C44.5305 15.4698 45.8096 11.6284 45.8096 24L24 24L24 45.8096Z">
                                </path>
                            </svg></div>
                        <h2 class="text-lg font-bold">GameKey Store</h2>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-[#a19db9] leading-relaxed">Cửa hàng bán key game bản
                        quyền uy tín nhất Việt Nam.</p>
                </div>
                <div>
                    <h4 class="font-bold text-sm mb-4">Thông tin</h4>
                    <ul class="text-xs space-y-2 text-slate-500 dark:text-[#a19db9]">
                        <li><a class="hover:text-primary" href="index.php?page=help">Về chúng tôi</a></li>
                        <li><a class="hover:text-primary" href="index.php?page=terms">Điều khoản</a></li>
                        <li><a class="hover:text-primary" href="index.php?page=privacy">Chính sách</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-sm mb-4">Liên hệ</h4>
                    <ul class="text-xs space-y-2 text-slate-500 dark:text-[#a19db9]">
                        <li><span class="material-symbols-outlined text-sm">mail</span> support@gamekey.vn</li>
                        <li><span class="material-symbols-outlined text-sm">call</span> 1900 1234</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-sm mb-4">Theo dõi</h4>
                    <div class="flex gap-4"><a
                            class="size-8 rounded-full bg-slate-200 dark:bg-border-dark flex items-center justify-center hover:bg-primary hover:text-white transition-all"
                            href="https://facebook.com" target="_blank">f</a><a
                            class="size-8 rounded-full bg-slate-200 dark:bg-border-dark flex items-center justify-center hover:bg-primary hover:text-white transition-all"
                            href="https://twitter.com" target="_blank">x</a></div>
                </div>
            </div>
            <div
                class="max-w-[1200px] mx-auto px-4 md:px-10 lg:px-20 mt-10 pt-6 border-t border-slate-200 dark:border-border-dark text-center text-[10px] text-slate-400 uppercase tracking-widest">
                © 2024 GAMEKEY STORE. ALL RIGHTS RESERVED.</div>
        </footer>
    </div>
</body>

</html>