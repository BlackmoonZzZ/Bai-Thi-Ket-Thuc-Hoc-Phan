<!DOCTYPE html>

<html class="dark" lang="vi">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Danh sách Game yêu thích - GameStore</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
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

    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .glass-effect {
      background: rgba(31, 20, 45, 0.7);
      backdrop-filter: blur(10px);
    }
  </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
  <!-- TopNavBar -->
  <header
    class="flex items-center justify-between border-b border-solid border-slate-200 dark:border-[#282b39] px-10 py-4 bg-background-light dark:bg-background-dark sticky top-0 z-50">
    <div class="flex items-center gap-8">
      <div class="flex items-center gap-4">
        <div class="size-6 text-primary">
          <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4H17.3334V17.3334H30.6666V30.6666H44V44H4V4Z" fill="currentColor"></path>
          </svg>
        </div>
        <h2 class="text-xl font-bold leading-tight tracking-[-0.015em]">GameStore</h2>
      </div>
      <nav class="hidden md:flex items-center gap-9">
        <a class="text-sm font-medium hover:text-primary transition-colors" href="index.php?page=home">Cửa hàng</a>
        <a class="text-sm font-medium hover:text-primary transition-colors" href="index.php?page=library">Thư viện</a>
        <a class="text-sm font-medium hover:text-primary transition-colors" href="index.php?page=help">Cộng đồng</a>
        <a class="text-sm font-medium hover:text-primary transition-colors" href="index.php?page=help">Hỗ trợ</a>
      </nav>
    </div>
    <div class="flex flex-1 justify-end gap-6 items-center">
      <label class="hidden sm:flex flex-col min-w-40 h-10 max-w-64">
        <div class="flex w-full flex-1 items-stretch rounded-lg h-full overflow-hidden">
          <div class="text-slate-400 flex bg-slate-100 dark:bg-[#282b39] items-center justify-center pl-4">
            <span class="material-symbols-outlined text-[20px]">search</span>
          </div>
          <input
            class="form-input flex w-full border-none bg-slate-100 dark:bg-[#282b39] focus:ring-0 placeholder:text-slate-400 px-4 pl-2 text-base font-normal"
            placeholder="Tìm kiếm game..." value="" />
        </div>
      </label>
      <div class="flex items-center gap-4">
        <span class="material-symbols-outlined cursor-pointer hover:text-primary">shopping_cart</span>
        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 border-2 border-primary"
          data-alt="Ảnh đại diện người dùng với khung xanh"
          style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDSfUO7NF_VPGrK3-UydpP1EMsWwhr7FZnfvG3JBlo_VcDSBwPlvH-r5AV59K5lRWXEFDLVCzaub0V3lJrOTwBrwfOzKbP2nYY7NeFZAM4PmfGcy3m1apj9lW8w642TOcSiA28sNE_4neFST_U-QKx2jTpkejSRYx7KzIYYsGJ2EtA9ODbfzcEz9rV38T4NykTUTMXKXiwU65VV2B-2hhgGIu9P8FRkY9oxLA92suFQfJs_uaJ_CIXA_mcDr5JEAjaqfl0ciCQDfxMg");'>
        </div>
      </div>
    </div>
  </header>
  <main class="max-w-[1200px] mx-auto px-4 py-8">
    <!-- PageHeading -->
    <div class="flex flex-wrap justify-between items-end gap-4 mb-8">
      <div class="flex flex-col gap-2">
        <h1 class="text-4xl font-black leading-tight tracking-[-0.033em]">Danh sách Game yêu thích</h1>
        <p class="text-slate-500 dark:text-[#9da1b9] text-base font-normal">Bạn đang theo dõi 12 trò chơi. Nhận thông
          báo ngay khi có khuyến mãi mới.</p>
      </div>
      <div class="flex gap-3">
        <button
          class="flex items-center gap-2 rounded-lg h-10 px-4 bg-slate-100 dark:bg-[#282b39] hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors font-bold text-sm">
          <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
          <span>Dọn dẹp</span>
        </button>
      </div>
    </div>
    <!-- Chips / Filters -->
    <div class="flex gap-3 mb-6 flex-wrap">
      <button class="flex h-10 items-center justify-center gap-x-2 rounded-lg bg-primary text-white px-4">
        <span class="text-sm font-medium">Tất cả</span>
      </button>
      <button
        class="flex h-10 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-[#282b39] px-4 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <span class="text-sm font-medium">Đang giảm giá</span>
        <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
      </button>
      <button
        class="flex h-10 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-[#282b39] px-4 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <span class="text-sm font-medium">Khoảng giá</span>
        <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
      </button>
      <button
        class="flex h-10 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-[#282b39] px-4 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <span class="text-sm font-medium">Thể loại</span>
        <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
      </button>
    </div>
    <!-- Main Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
      <!-- Game Card 1 -->
      <div
        class="group flex flex-col gap-4 bg-white dark:bg-[#1a1d2e] p-3 rounded-xl border border-slate-200 dark:border-slate-800 transition-all hover:border-primary">
        <div class="relative w-full aspect-[16/9] bg-center bg-cover rounded-lg overflow-hidden"
          data-alt="Ảnh bìa Cyberpunk 2077 với ánh đèn neon"
          style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCVlzJw4pHz6LyRlBsinFuMpmcGU4a7usmMkBduID8Eg0f6e0yLhkNLIZUYi0cDVMu4IwttZuCOkXlOsg5jcfZB6lPyFBIpO9GQoJ-5PuaSVjovdpPuqsZrmLIBc3vB4BbBfnAW5ZAx0AjFHg6_N17Y2PbamRORW_catDXZufM10JOdSNgx3fO8JhGIDgIqIeVe3OpnSuO-rKaRlFHm36X_ENeCFUq2AfsmoxHSO9EyRwi7rCqwHlZ5eebrF_hwslG17MyJU9oHoK6T");'>
          <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">-50%</div>
        </div>
        <div class="flex flex-col gap-1">
          <h3 class="font-bold text-lg truncate group-hover:text-primary transition-colors">Cyberpunk 2077</h3>
          <div class="flex items-center gap-2">
            <span class="text-lg font-bold">495.000đ</span>
            <span class="text-slate-400 line-through text-sm">990.000đ</span>
          </div>
        </div>
        <div class="flex flex-col gap-2 mt-auto">
          <button
            class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
            Thêm vào giỏ
          </button>
          <div
            class="flex items-center justify-between text-xs text-slate-500 dark:text-[#9da1b9] pt-2 border-t border-slate-100 dark:border-slate-800">
            <span>Báo giá sâu hơn</span>
            <label
              class="relative flex h-[20px] w-[36px] cursor-pointer items-center rounded-full bg-slate-200 dark:bg-[#282b39] has-[:checked]:bg-primary transition-colors">
              <input checked="" class="peer invisible absolute" type="checkbox" />
              <div
                class="absolute left-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4">
              </div>
            </label>
          </div>
        </div>
      </div>
      <!-- Game Card 2 -->
      <div
        class="group flex flex-col gap-4 bg-white dark:bg-[#1a1d2e] p-3 rounded-xl border border-slate-200 dark:border-slate-800 transition-all hover:border-primary">
        <div class="relative w-full aspect-[16/9] bg-center bg-cover rounded-lg overflow-hidden"
          data-alt="Ảnh bìa Elden Ring phong cảnh hùng vĩ"
          style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCS7jdUi8BHzGZkrOT-FEwnJ2zg0qzwie6rszw9Pq7QftvxT63ez7QB-xzodtP0VpNs8_8eJ419CsHWF3Yg3L1o65MsTjGRMn-Psq4hTXCIlBj1DVCH5Ym_rYcnDsWWxqfM-Ll-A2QEebwIPPC2Z105_psrgDwPeMYwkXEJzIIodEeVq4z5oOOBct-KZ4MI7LgmGMuQtDWR4hwmzdZIagAgwPyg97jz0YHaDwYo0t3htnHFAxc9d-zTuWo0YKSmPY3DmjWOjiNKzsbQ");'>
        </div>
        <div class="flex flex-col gap-1">
          <h3 class="font-bold text-lg truncate group-hover:text-primary transition-colors">Elden Ring</h3>
          <div class="flex items-center gap-2">
            <span class="text-lg font-bold">800.000đ</span>
          </div>
        </div>
        <div class="flex flex-col gap-2 mt-auto">
          <button
            class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
            Thêm vào giỏ
          </button>
          <div
            class="flex items-center justify-between text-xs text-slate-500 dark:text-[#9da1b9] pt-2 border-t border-slate-100 dark:border-slate-800">
            <span>Báo giá khi có sale</span>
            <label
              class="relative flex h-[20px] w-[36px] cursor-pointer items-center rounded-full bg-slate-200 dark:bg-[#282b39] has-[:checked]:bg-primary transition-colors">
              <input class="peer invisible absolute" type="checkbox" />
              <div
                class="absolute left-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4">
              </div>
            </label>
          </div>
        </div>
      </div>
      <!-- Game Card 3 -->
      <div
        class="group flex flex-col gap-4 bg-white dark:bg-[#1a1d2e] p-3 rounded-xl border border-slate-200 dark:border-slate-800 transition-all hover:border-primary">
        <div class="relative w-full aspect-[16/9] bg-center bg-cover rounded-lg overflow-hidden"
          data-alt="Ảnh bìa Spider-Man hành động trên không"
          style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCVPJxeTKqrMmtt-BFRQapbo73EMfUeNGpt-d6Z4kILL2b6vDKckeaIxCVGBBJLyBNOtqBUbduNK3jkneiGfa-vl_QE-NJU9qro42-oSdMZ23Ovo2cfwwjGUAIAEqUII2f7b4XKuBPh4HwaSMdaPxhVtTI9_vs8XSE9LmCLJkoxdvdc2lbRL7nnvB3klsmTQsKgGj-c5y-SwdJD5pHMxj_EpxZZVFVj1VZL9606SfUb1LMUNEw25QqbajdpGVhMhINZVnhHxfqAbvwO");'>
          <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">-33%</div>
        </div>
        <div class="flex flex-col gap-1">
          <h3 class="font-bold text-lg truncate group-hover:text-primary transition-colors">Spider-Man Remastered</h3>
          <div class="flex items-center gap-2">
            <span class="text-lg font-bold">737.000đ</span>
            <span class="text-slate-400 line-through text-sm">1.100.000đ</span>
          </div>
        </div>
        <div class="flex flex-col gap-2 mt-auto">
          <button
            class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
            Thêm vào giỏ
          </button>
          <div
            class="flex items-center justify-between text-xs text-slate-500 dark:text-[#9da1b9] pt-2 border-t border-slate-100 dark:border-slate-800">
            <span>Báo giá sâu hơn</span>
            <label
              class="relative flex h-[20px] w-[36px] cursor-pointer items-center rounded-full bg-slate-200 dark:bg-[#282b39] has-[:checked]:bg-primary transition-colors">
              <input checked="" class="peer invisible absolute" type="checkbox" />
              <div
                class="absolute left-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4">
              </div>
            </label>
          </div>
        </div>
      </div>
      <!-- Game Card 4 -->
      <div
        class="group flex flex-col gap-4 bg-white dark:bg-[#1a1d2e] p-3 rounded-xl border border-slate-200 dark:border-slate-800 transition-all hover:border-primary">
        <div class="relative w-full aspect-[16/9] bg-center bg-cover rounded-lg overflow-hidden"
          data-alt="Ảnh bìa Final Fantasy phong cách anime"
          style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBFelM2G1nwVVuVLryYR0ZaT2ei6L44cN27fJHqv7GvpuMF50Lkmx_hM0C1NsekyOgpD1ghnZiZKc3ab5aBGR2CaKJVEohF3Kv19mKX_wT_fUjSL_KOW-UEgGWUPnp8hrYFRsSMIFjgF39VRL6K1NNax3RHQFnZr6kvUvMWXe0H10sJujQlpG8u4bsdetxwDoPSwhxoO3KW2tjc68JpwXgjJaypZE9jh694Ih9HDgetoJmsAdZlqzbDzvDH-9uzUOY6pz74g9xjc8aw");'>
          <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">-25%</div>
        </div>
        <div class="flex flex-col gap-1">
          <h3 class="font-bold text-lg truncate group-hover:text-primary transition-colors">Final Fantasy VII Remake
          </h3>
          <div class="flex items-center gap-2">
            <span class="text-lg font-bold">1.350.000đ</span>
            <span class="text-slate-400 line-through text-sm">1.800.000đ</span>
          </div>
        </div>
        <div class="flex flex-col gap-2 mt-auto">
          <button
            class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
            Thêm vào giỏ
          </button>
          <div
            class="flex items-center justify-between text-xs text-slate-500 dark:text-[#9da1b9] pt-2 border-t border-slate-100 dark:border-slate-800">
            <span>Báo giá sâu hơn</span>
            <label
              class="relative flex h-[20px] w-[36px] cursor-pointer items-center rounded-full bg-slate-200 dark:bg-[#282b39] has-[:checked]:bg-primary transition-colors">
              <input class="peer invisible absolute" type="checkbox" />
              <div
                class="absolute left-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4">
              </div>
            </label>
          </div>
        </div>
      </div>
    </div>
    <!-- ActionPanel / Notification Banner -->
    <div class="p-4 mb-12">
      <div
        class="flex flex-col md:flex-row items-center justify-between gap-6 rounded-xl border border-primary/30 bg-primary/5 p-6">
        <div class="flex items-center gap-5">
          <div class="bg-primary/20 p-3 rounded-full text-primary">
            <span class="material-symbols-outlined text-[32px]">mail</span>
          </div>
          <div class="flex flex-col gap-1">
            <p class="text-lg font-bold leading-tight">Quản lý thông báo tập trung</p>
            <p class="text-slate-500 dark:text-[#9da1b9] text-base font-normal">Tự động gửi email khi bất kỳ game nào
              trong danh sách giảm giá trên 50%.</p>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <span class="text-sm font-medium opacity-70">Bật thông báo</span>
          <label
            class="relative flex h-[31px] w-[51px] cursor-pointer items-center rounded-full bg-slate-300 dark:bg-[#282b39] has-[:checked]:bg-primary transition-colors">
            <input checked="" class="peer invisible absolute" type="checkbox" />
            <div
              class="absolute left-1 h-6 w-6 rounded-full bg-white shadow-md transition-transform peer-checked:translate-x-5">
            </div>
          </label>
        </div>
      </div>
    </div>
    <!-- Pagination / Load More -->
    <div class="flex justify-center pb-12">
      <button
        class="px-8 py-3 bg-slate-100 dark:bg-[#282b39] rounded-lg font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
        Xem thêm 8 trò chơi khác
      </button>
    </div>
  </main>
  <footer class="border-t border-slate-200 dark:border-[#282b39] py-10 px-10 text-center text-slate-500 text-sm">
    <div class="flex justify-center gap-8 mb-4">
      <a class="hover:text-primary" href="index.php?page=privacy">Chính sách bảo mật</a>
      <a class="hover:text-primary" href="index.php?page=terms">Điều khoản dịch vụ</a>
      <a class="hover:text-primary" href="index.php?page=help">Liên hệ</a>
    </div>
    <p>© 2024 GameStore. All rights reserved.</p>
  </footer>
</body>

</html>