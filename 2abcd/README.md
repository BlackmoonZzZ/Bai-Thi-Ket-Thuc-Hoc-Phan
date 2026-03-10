# 🎮 GameKey Store - Cửa Hàng Key Bản Quyền

Chào mừng bạn đến với **GameKey Store**, một nền tảng thương mại điện tử hiện đại chuyên cung cấp key trò chơi bản quyền (Steam, Epic Games, Ubisoft, v.v.). Dự án được xây dựng với mục tiêu mang lại trải nghiệm mua sắm mượt mà, an toàn và tối ưu cho game thủ.

---

## ✨ Tính Năng Nổi Bật

### 🛒 Dành Cho Người Dùng
- **Cửa hàng hiện đại:** Giao diện Dark mode sang trọng, responsive mượt mà với Tailwind CSS.
- **Tìm kiếm & Bộ lọc:** Tìm kiếm game theo tên, danh mục, nền tảng hoặc lọc theo giá/khuyến mãi.
- **Giỏ hàng thông minh:** Thêm sản phẩm nhanh, áp dụng mã giảm giá (Coupon).
- **Hệ thống Ví (Wallet):** Nạp tiền, chuyển tiền giữa các tài khoản, thanh toán đơn hàng bằng số dư ví.
- **Thư viện Game:** Quản lý danh sách game đã mua và xem key bản quyền trực tiếp.
- **Thông báo Real-time:** Toast notification khi thêm vào giỏ hàng hoặc thực hiện giao dịch.

### 🛡️ Dành Cho Quản Trị Viên (Admin)
- **Quản lý Sản phẩm:** CRUD sản phẩm, quản lý kho key, thiết lập giảm giá.
- **Quản lý Đơn hàng:** Theo dõi tình trạng đơn hàng, xử lý hoàn tiền.
- **Quản lý Người dùng:** Kiểm soát tài khoản, duyệt yêu cầu nạp tiền.
- **Hệ thống Coupon:** Tạo và quản lý các mã giảm giá, giới hạn lượt dùng và thời hạn.

---

## 🛠️ Công Nghệ Sử Dụng

- **Backend:** PHP 7.4+ (Cấu trúc OOP, kiến trúc MVC đơn giản).
- **Database:** MySQL (Sử dụng PDO extension để bảo mật SQL Injection).
- **Frontend:** HTML5, CSS3, JavaScript (ES6+), Tailwind CSS (CDN).
- **Icons & Fonts:** Material Symbols Outlined, Google Fonts (Space Grotesk).

---

## 📁 Cấu Trúc Thư Mục

```text
2abcd/
├── admin/              # Giao diện và logic dành cho quản trị viên
├── api/                # Các điểm cuối API xử lý yêu cầu AJAX
├── assets/             # Hình ảnh, CSS, JS dùng chung
├── config/             # Cấu hình hệ thống (Database connection)
├── includes/           # Chứa các Class core (Auth, Cart, Product, Order, Wallet...)
├── user/               # Mã nguồn trang Storefront dành cho khách hàng
│   ├── views/          # Các file giao diện (home, cart, profile, library...)
│   └── index.php       # Router chính của trang user
├── uploads/            # Thư mục lưu trữ hình ảnh sản phẩm tải lên
├── index.php           # File entry point duy nhất của toàn bộ ứng dụng
└── README.md           # Hướng dẫn dự án
```

---

## 🚀 Hướng Dẫn Cài Đặt

1. **Yêu cầu hệ thống:**
   - XAMPP/WAMP (PHP >= 7.4, MySQL).
   
2. **Setup Database:**
   - Tạo database mới trong phpMyAdmin tên là `gamekey_store`.
   - Import file SQL từ thư mục backup (nếu có) hoặc chạy script khởi tạo.

3. **Cấu hình kết nối:**
   - Mở file `config/database.php` và cập nhật thông tin kết nối của bạn:
     ```php
     private $host = "localhost";
     private $db_name = "gamekey_store";
     private $username = "root";
     private $password = "";
     ```

4. **Chạy ứng dụng:**
   - Copy thư mục code vào `htdocs`.
   - Truy cập qua trình duyệt: `http://localhost/2abcd/`.

---

## 📝 Thông Tin Liên Hệ
Nếu bạn gặp vấn đề hoặc có thắc mắc, vui lòng liên hệ admin của hệ thống để được hỗ trợ.

---
*Dự án được tối ưu hóa cho tốc độ và bảo mật.* 🚀
