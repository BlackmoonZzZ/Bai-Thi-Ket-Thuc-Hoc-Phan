<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Thư viện Key - GAMEKEY.VN</title>
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
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f0f1f4] dark:bg-white/10 hover:bg-primary hover:text-white transition-all relative"><span
                                class="material-symbols-outlined text-xl">notifications</span></button></div>
                    <div class="size-10 ring-2 ring-primary rounded-full bg-cover"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBpbrtw392gClb3N1LANfBVDNBedrTWGiYAjPHN6ymHwU4YItDalj0JI0EppxGGhL4wlqGobs7goxntRUVihtwIPWuLTaInYeCvSPeJh478mFBaCFq0J6FNn2OPQiUvVzxH9wtnsKTDherxS3FwcJpIi-K22i87m2YINMa-S8XxzjO_jLkBxkDVXM_XCUxw1J2U88GZHcmFQUXvLkMVMFk4RK-9nnfhEswDhQDXJFIKU4sgr6SpR6PHcrXvm7YLmOHwze0qXulyCQ2Q");'>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1 mx-auto w-full px-4 lg:px-10 py-8">
            <div class="max-w-[1200px] gap-6 flex flex-col">
                <div
                    class="flex flex-wrap items-end justify-between gap-4 p-4 bg-white dark:bg-white/5 rounded-xl border border-[#f0f1f4] dark:border-white/10">
                    <div>
                        <p class="text-4xl font-black text-[#111218] dark:text-white">Thư viện của tôi</p>
                        <p class="text-[#616889] dark:text-white/60 text-base mt-2">Quản lý các game đã mua</p>
                    </div>
                    <button
                        class="flex items-center justify-center h-12 px-6 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 shadow-lg shadow-primary/20"><span
                            class="material-symbols-outlined mr-2">history</span>Lịch sử</button>
                </div>
                <div class="flex flex-col md:flex-row gap-4 w-full">
                    <div class="flex-1"><label class="flex w-full">
                            <div class="flex w-full flex-1 items-stretch rounded-xl h-12 shadow-sm">
                                <div
                                    class="text-[#616889] flex border border-r-0 border-[#f0f1f4] dark:border-white/10 bg-white dark:bg-white/5 items-center pl-4">
                                    <span class="material-symbols-outlined">search</span>
                                </div><input
                                    class="flex-1 resize-none rounded-r-xl text-[#111218] dark:text-white focus:outline-0 focus:ring-1 focus:ring-primary border border-l-0 border-[#f0f1f4] dark:border-white/10 bg-white dark:bg-white/5 px-4 text-base placeholder:text-[#616889]"
                                    placeholder="Tìm game..." value="" />
                            </div>
                        </label></div>
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        <div class="flex h-10 shrink-0 items-center px-5 bg-primary text-white rounded-lg">
                            <p class="text-sm font-semibold">Tất cả</p>
                        </div>
                        <div
                            class="flex h-10 shrink-0 items-center px-5 bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <p class="text-sm font-medium">Steam</p>
                        </div>
                        <div
                            class="flex h-10 shrink-0 items-center px-5 bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <p class="text-sm font-medium">Epic Games</p>
                        </div>
                        <div
                            class="flex h-10 shrink-0 items-center px-5 bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <p class="text-sm font-medium">EA App</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div
                        class="flex flex-col bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md group">
                        <div class="relative w-full aspect-video overflow-hidden bg-cover bg-center"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBiBMJaEUJo9B7UB5pQfnWoRTeSeZXBvQENBwP6pGZ9rh9vwrT8qYGEdT13zN1WxM386Y9KMimRGrc90DWgrBD0VEy8kZAnMJ-5rS_JRqTN3BNA3Wt7xEFMZDjLK-FjlpG0oGwW7mGkkrlN_20JOGe-cNEgAKWdvfDwXv_64Pxl74snUC4h24izhW0OlvfLVQBTG2cTzy6d5Y4cJiMEJLyRFx0r81P6p4HETFGnsNH8H3vdpbPRlbcWbynrZirD8zqXQzJPakmBniqD");'>
                            <div
                                class="absolute top-2 right-2 px-2 py-1 bg-black/60 rounded text-white text-[10px] font-bold">
                                Steam</div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-lg font-bold">Elden Ring</p><span
                                    class="material-symbols-outlined text-[#616889] cursor-pointer hover:text-primary">info</span>
                            </div>
                            <p class="text-[#616889] dark:text-white/60 text-xs">Mua: 12/10/2023</p>
                            <div
                                class="mt-4 p-3 bg-background-light dark:bg-white/5 rounded-lg border border-dashed border-[#d1d5db] dark:border-white/20">
                                <p class="text-[10px] uppercase font-bold text-[#616889] mb-1">Mã kích hoạt</p>
                                <div class="flex items-center justify-between"><span
                                        class="font-mono font-bold text-primary">ABCD-1234-EFGH</span><button
                                        class="p-1 hover:text-primary"><span
                                            class="material-symbols-outlined text-sm">content_copy</span></button></div>
                            </div>
                            <div class="flex gap-2 mt-2"><button
                                    class="flex-1 h-9 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90">Kích
                                    hoạt</button><button
                                    class="flex h-9 w-9 items-center justify-center border border-[#f0f1f4] dark:border-white/10 rounded-lg"><span
                                        class="material-symbols-outlined text-lg">help</span></button></div>
                        </div>
                    </div>
                    <div
                        class="flex flex-col bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md group">
                        <div class="relative w-full aspect-video overflow-hidden bg-cover bg-center"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAwVMur9iEyYdTFVOqcRJO_vnLUdmWMiHJG7wcmlRWA1pDwomlt8JzL5ReBzZRVoKMzDAHRlewvs8V2OJAeMjUDOJb9JvXDzwA4CMJwuQyq3152G6QpnHfu24dlmkCu3VZHIWuVOxO2fY3oLqKO65CxrbQAn_pfq14ILBrIVQp6GzscxeXwHgbM2b4f9FW7ITCtJuAnOBtf8c9rBkjDdGRPn57ItvKxEFiVKnXcrRRDJuIwbY3yEPi_EjTtg4ix5i0TTfoPMiTNcrGC");'>
                            <div
                                class="absolute top-2 right-2 px-2 py-1 bg-black/60 rounded text-white text-[10px] font-bold">
                                GOG</div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-lg font-bold">Cyberpunk 2077</p><span
                                    class="material-symbols-outlined text-[#616889] cursor-pointer hover:text-primary">info</span>
                            </div>
                            <p class="text-[#616889] dark:text-white/60 text-xs">Mua: 05/09/2023</p>
                            <div
                                class="mt-4 p-3 bg-background-light dark:bg-white/5 rounded-lg border border-dashed border-[#d1d5db] dark:border-white/20">
                                <p class="text-[10px] uppercase font-bold text-[#616889] mb-1">Mã kích hoạt</p>
                                <div class="flex items-center justify-between"><span
                                        class="font-mono font-bold text-primary">GOG7-XY99-BVCX</span><button
                                        class="p-1 hover:text-primary"><span
                                            class="material-symbols-outlined text-sm">content_copy</span></button></div>
                            </div>
                            <div class="flex gap-2 mt-2"><button
                                    class="flex-1 h-9 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90">Kích
                                    hoạt</button><button
                                    class="flex h-9 w-9 items-center justify-center border border-[#f0f1f4] dark:border-white/10 rounded-lg"><span
                                        class="material-symbols-outlined text-lg">help</span></button></div>
                        </div>
                    </div>
                    <div
                        class="flex flex-col bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md group">
                        <div class="relative w-full aspect-video overflow-hidden bg-cover bg-center"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDDAq6AWKgB_UOdZIv89CjZBqp3_sidTlSYAqk7NIAq-ARzOKuqOHWgpb_T3X5TEaM39GM6zrDbaoqhSmmay12SJWpb3Okd8MUvWC5H2k1eM3vvq0EE7GayzCrJYOGdjcAtFT2kQd3qzGl-XhmHU_j9vC3voDHFIzrQJJW4EuM7OIK7fBPY-_1lNrmdIfX-56atDNbvnON2iJVvME3v1qMrRP2tqapPKdbRL0kNu20RHft8yvo_M9LvAq9EGtYaCqJKtyywwgDerfXZ");'>
                            <div
                                class="absolute top-2 right-2 px-2 py-1 bg-black/60 rounded text-white text-[10px] font-bold">
                                EA App</div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-lg font-bold">FC 24</p><span
                                    class="material-symbols-outlined text-[#616889] cursor-pointer hover:text-primary">info</span>
                            </div>
                            <p class="text-[#616889] dark:text-white/60 text-xs">Mua: 20/08/2023</p>
                            <div
                                class="mt-4 p-3 bg-background-light dark:bg-white/5 rounded-lg border border-dashed border-[#d1d5db] dark:border-white/20">
                                <p class="text-[10px] uppercase font-bold text-[#616889] mb-1">Mã kích hoạt</p>
                                <div class="flex items-center justify-between"><span
                                        class="font-mono font-bold text-primary">EA24-KKLL-9001</span><button
                                        class="p-1 hover:text-primary"><span
                                            class="material-symbols-outlined text-sm">content_copy</span></button></div>
                            </div>
                            <div class="flex gap-2 mt-2"><button
                                    class="flex-1 h-9 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90">Kích
                                    hoạt</button><button
                                    class="flex h-9 w-9 items-center justify-center border border-[#f0f1f4] dark:border-white/10 rounded-lg"><span
                                        class="material-symbols-outlined text-lg">help</span></button></div>
                        </div>
                    </div>
                    <div
                        class="flex flex-col bg-white dark:bg-white/5 border border-[#f0f1f4] dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md group">
                        <div class="relative w-full aspect-video overflow-hidden bg-cover bg-center"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCA_F8RPSUKJjwI4L5Tx_5iPupCNFWhxdsn0NNmDZBQfi8v1zpgFMoBa_OWDe7cSyuW_2_UNjVrojpU1c_RyVfHyM7Lxgw_f2Ny7vAgSnGO2Oor5j5ESDtgK1l397njEu5qtpgbnEJsPZNSZjII8VnfXV9B30KNXtX_9MRKs4dCyDPMX-SeKtr4VR7m1YSApb6Sgj-GgGXokKprIp9XVdAwsFrHXSsOP-PVGwEUZcDAiWdnLb8lo6CkpmS2uUbKXPxCIcYI3HScZr0q");'>
                            <div
                                class="absolute top-2 right-2 px-2 py-1 bg-black/60 rounded text-white text-[10px] font-bold">
                                Steam</div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-lg font-bold">RE4 Remake</p><span
                                    class="material-symbols-outlined text-[#616889] cursor-pointer hover:text-primary">info</span>
                            </div>
                            <p class="text-[#616889] dark:text-white/60 text-xs">Mua: 15/07/2023</p>
                            <div
                                class="mt-4 p-3 bg-background-light dark:bg-white/5 rounded-lg border border-dashed border-[#d1d5db] dark:border-white/20">
                                <p class="text-[10px] uppercase font-bold text-[#616889] mb-1">Mã kích hoạt</p>
                                <div class="flex items-center justify-between"><span
                                        class="font-mono font-bold text-primary">RE4R-RE4R-RE4R</span><button
                                        class="p-1 hover:text-primary"><span
                                            class="material-symbols-outlined text-sm">content_copy</span></button></div>
                            </div>
                            <div class="flex gap-2 mt-2"><button
                                    class="flex-1 h-9 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90">Kích
                                    hoạt</button><button
                                    class="flex h-9 w-9 items-center justify-center border border-[#f0f1f4] dark:border-white/10 rounded-lg"><span
                                        class="material-symbols-outlined text-lg">help</span></button></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center py-10"><button
                        class="flex items-center justify-center gap-2 px-8 h-12 border border-primary text-primary font-bold rounded-xl hover:bg-primary/5"><span>Xem
                            thêm game</span><span class="material-symbols-outlined">expand_more</span></button></div>
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