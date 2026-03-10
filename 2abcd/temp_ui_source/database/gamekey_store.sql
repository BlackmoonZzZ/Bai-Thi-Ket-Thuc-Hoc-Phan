-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th2 04, 2026 lúc 12:45 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `gamekey_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `target_table`, `target_id`, `changes`, `ip_address`, `created_at`) VALUES
(1, 1, 'CREATE', 'products', 1, NULL, '192.168.1.1', '2024-01-01 03:00:00'),
(2, 1, 'UPDATE', 'products', 1, NULL, '192.168.1.1', '2024-01-05 07:30:00'),
(3, 1, 'CREATE', 'coupons', 1, NULL, '192.168.1.1', '2024-01-10 02:15:00'),
(4, 1, 'UPDATE', 'users', 2, NULL, '192.168.1.1', '2024-02-15 04:45:00'),
(5, 1, 'CREATE', 'product_keys', 1, NULL, '192.168.1.1', '2024-03-20 09:20:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `session_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 2, NULL, 4, 1, '2026-02-01 03:00:00'),
(2, 3, NULL, 5, 1, '2026-02-02 04:30:00'),
(3, 5, NULL, 7, 1, '2026-02-03 02:15:00'),
(4, 7, NULL, 14, 1, '2026-02-03 07:20:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Action', 'action', 'Game hành động, bắn súng, đánh nhau', '2026-02-03 10:48:27'),
(2, 'RPG', 'rpg', 'Role-playing games - Nhập vai', '2026-02-03 10:48:27'),
(3, 'Adventure', 'adventure', 'Game phiêu lưu, khám phá', '2026-02-03 10:48:27'),
(4, 'Strategy', 'strategy', 'Game chiến thuật, xây dựng', '2026-02-03 10:48:27'),
(5, 'Sports', 'sports', 'Game thể thao', '2026-02-03 10:48:27'),
(6, 'Horror', 'horror', 'Game kinh dị, sinh tồn', '2026-02-03 10:48:27'),
(7, 'Simulation', 'simulation', 'Game mô phỏng', '2026-02-03 10:48:27'),
(8, 'Racing', 'racing', 'Game đua xe', '2026-02-03 10:48:27'),
(9, 'Puzzle', 'puzzle', 'Game xếp hình, tư duy', '2026-02-03 10:48:27'),
(10, 'Fighting', 'fighting', 'Game đối kháng, chiến đấu', '2026-02-03 10:48:27'),
(11, 'Shooter', 'shooter', 'Game bắn súng FPS/TPS', '2026-02-03 10:48:27'),
(12, 'MMO', 'mmo', 'Game online đa người chơi', '2026-02-03 10:48:27'),
(13, 'Indie', 'indie', 'Game độc lập', '2026-02-03 10:48:27'),
(14, 'Educational', 'educational', 'Game giáo dục', '2026-02-03 10:48:27'),
(15, 'Card Game', 'card-game', 'Game thẻ bài', '2026-02-03 10:48:27'),
(16, 'Music', 'music', 'Game âm nhạc, nhịp điệu', '2026-02-03 10:48:27'),
(17, 'Casual', 'casual', 'Game giải trí nhẹ nhàng', '2026-02-03 10:48:27'),
(18, 'Platformer', 'platformer', 'Game nhảy, leo trèo', '2026-02-03 10:48:27'),
(19, 'Visual Novel', 'visual-novel', 'Game kể chuyện tương tác', '2026-02-03 10:48:27'),
(20, 'Roguelike', 'roguelike', 'Game sinh ngẫu nhiên, permadeath', '2026-02-03 10:48:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `usage_limit` int(11) DEFAULT 100,
  `used_count` int(11) DEFAULT 0,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `description`, `expiry_date`, `usage_limit`, `used_count`, `min_order_amount`, `is_active`, `created_at`) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 'Giảm 10% cho khách hàng mới', '2026-12-31', 1000, 234, 0.00, 1, '2026-02-03 10:48:28'),
(2, 'SUMMER20', 'percentage', 20.00, 'Khuyến mãi mùa hè 20%', '2026-08-31', 500, 156, 0.00, 1, '2026-02-03 10:48:28'),
(3, 'VIP30', 'percentage', 30.00, 'Dành cho khách VIP', '2026-12-31', 100, 45, 500000.00, 1, '2026-02-03 10:48:28'),
(4, 'FLASH50K', 'fixed', 50000.00, 'Flash sale 50k', '2026-06-30', 300, 189, 200000.00, 1, '2026-02-03 10:48:28'),
(5, 'GAME100K', 'fixed', 100000.00, 'Giảm 100k đơn từ 500k', '2026-09-30', 200, 87, 500000.00, 1, '2026-02-03 10:48:28'),
(6, 'NEWYEAR25', 'percentage', 25.00, 'Năm mới giảm 25%', '2026-02-28', 500, 312, 0.00, 0, '2026-02-03 10:48:28'),
(7, 'SPRING15', 'percentage', 15.00, 'Mùa xuân 15%', '2026-05-31', 400, 67, 0.00, 1, '2026-02-03 10:48:28'),
(8, 'AUTUMN35', 'percentage', 35.00, 'Thu sale 35%', '2026-11-30', 250, 23, 1000000.00, 1, '2026-02-03 10:48:28'),
(9, 'BLACK10', 'percentage', 10.00, 'Black Friday 10%', '2026-12-31', 2000, 456, 0.00, 1, '2026-02-03 10:48:28'),
(10, 'CYBER25', 'percentage', 25.00, 'Cyber Monday 25%', '2026-12-31', 1000, 78, 300000.00, 1, '2026-02-03 10:48:28'),
(11, 'LUCKY888', 'fixed', 88000.00, 'Số may mắn 88k', '2026-08-08', 88, 34, 400000.00, 1, '2026-02-03 10:48:28'),
(12, 'MEGA200K', 'fixed', 200000.00, 'Mega sale 200k', '2026-12-15', 50, 12, 1500000.00, 1, '2026-02-03 10:48:28'),
(13, 'STUDENT20', 'percentage', 20.00, 'Ưu đãi sinh viên', '2026-12-31', 300, 89, 0.00, 1, '2026-02-03 10:48:28'),
(14, 'WEEKEND15', 'percentage', 15.00, 'Cuối tuần giảm 15%', '2026-12-31', 800, 234, 0.00, 1, '2026-02-03 10:48:28'),
(15, 'LOYALTY50', 'percentage', 50.00, 'Khách hàng thân thiết', '2026-12-31', 30, 8, 2000000.00, 0, '2026-02-03 10:48:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `email_type` enum('order_confirmation','key_delivery','admin_notification','other') NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `status` enum('sent','failed','pending') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `coupon_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','failed') DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `total_amount`, `discount_amount`, `coupon_id`, `payment_method`, `status`, `payment_status`, `notes`, `created_at`, `completed_at`) VALUES
(1, 'ORD-2024-0001', 2, 1299000.00, 0.00, 1, 'Wallet', 'completed', 'paid', NULL, '2024-01-15 03:30:00', '2024-01-15 10:31:00'),
(2, 'ORD-2024-0002', 3, 674250.00, 224750.00, 1, 'Wallet', 'completed', 'paid', NULL, '2024-02-20 07:15:00', '2024-02-20 14:16:00'),
(3, 'ORD-2024-0003', 4, 1299000.00, 0.00, 2, 'Wallet', 'completed', 'paid', NULL, '2024-03-10 02:45:00', '2024-03-10 09:46:00'),
(4, 'ORD-2024-0004', 5, 629000.00, 270000.00, 2, 'Wallet', 'completed', 'paid', NULL, '2024-04-05 09:20:00', '2024-04-05 16:21:00'),
(5, 'ORD-2024-0005', 6, 239400.00, 159600.00, 1, 'Wallet', 'completed', 'paid', NULL, '2024-05-12 04:00:00', '2024-05-12 11:01:00'),
(6, 'ORD-2024-0006', 7, 899000.00, 0.00, NULL, 'Wallet', 'completed', 'paid', NULL, '2024-06-08 06:30:00', '2024-06-08 13:31:00'),
(7, 'ORD-2024-0007', 8, 799000.00, 100000.00, 5, 'Wallet', 'completed', 'paid', NULL, '2024-07-22 08:45:00', '2024-07-22 15:46:00'),
(8, 'ORD-2024-0008', 9, 1349000.00, 50000.00, 4, 'Wallet', 'completed', 'paid', NULL, '2024-08-14 01:20:00', '2024-08-14 08:21:00'),
(9, 'ORD-2024-0009', 10, 1799000.00, 0.00, 1, 'Wallet', 'completed', 'paid', NULL, '2024-09-03 03:00:00', '2024-09-03 10:01:00'),
(10, 'ORD-2024-0010', 11, 999000.00, 0.00, 1, 'Wallet', 'completed', 'paid', NULL, '2024-10-18 07:30:00', '2024-10-18 14:31:00'),
(11, 'ORD-2024-0011', 12, 1299000.00, 0.00, 1, 'Wallet', 'completed', 'paid', NULL, '2024-11-25 02:15:00', '2024-11-25 09:16:00'),
(12, 'ORD-2024-0012', 13, 909000.00, 490000.00, 3, 'Wallet', 'completed', 'paid', NULL, '2024-12-02 04:45:00', '2024-12-02 11:46:00'),
(13, 'ORD-2025-0001', 14, 1699000.00, 0.00, 2, 'Wallet', 'completed', 'paid', NULL, '2025-01-07 06:00:00', '2025-01-07 13:01:00'),
(14, 'ORD-2025-0002', 15, 1049000.00, 350000.00, 2, 'Wallet', 'completed', 'paid', NULL, '2025-02-14 09:20:00', '2025-02-14 16:21:00'),
(15, 'ORD-2025-0003', 16, 1599000.00, 0.00, 2, 'Wallet', 'completed', 'paid', NULL, '2025-03-21 03:30:00', '2025-03-21 10:31:00'),
(16, 'ORD-2025-0004', 17, 899000.00, 0.00, 3, 'Wallet', 'completed', 'paid', NULL, '2025-04-18 01:45:00', '2025-04-18 08:46:00'),
(17, 'ORD-2025-0005', 18, 749250.00, 249750.00, 1, 'Wallet', 'completed', 'paid', NULL, '2025-05-25 08:10:00', '2025-05-25 15:11:00'),
(18, 'ORD-2025-0006', 19, 1299000.00, 0.00, 1, 'Wallet', 'completed', 'paid', NULL, '2025-06-30 05:25:00', '2025-06-30 12:26:00'),
(19, 'ORD-2025-0007', 20, 1699000.00, 0.00, 4, 'Wallet', 'completed', 'paid', NULL, '2025-07-14 02:50:00', '2025-07-14 09:51:00'),
(20, 'ORD-2025-0008', 21, 1399000.00, 0.00, 2, 'Wallet', 'completed', 'paid', NULL, '2025-08-22 07:15:00', '2025-08-22 14:16:00'),
(21, 'ORD-2026-0001', 2, 1499000.00, 0.00, 1, 'Wallet', 'completed', 'paid', NULL, '2026-01-10 03:00:00', '2026-01-10 10:01:00'),
(22, 'ORD-2026-0002', 3, 899000.00, 0.00, 5, 'Wallet', 'completed', 'paid', NULL, '2026-01-18 07:30:00', '2026-01-18 14:31:00'),
(23, 'ORD-2026-0003', 5, 1599000.00, 100000.00, 5, 'Wallet', 'completed', 'paid', NULL, '2026-02-02 02:15:00', '2026-02-02 09:16:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `key_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `key_id`, `price`, `created_at`) VALUES
(1, 1, 1, 1, 1299000.00, '2026-02-03 10:48:28'),
(2, 2, 2, 13, 899000.00, '2026-02-03 10:48:28'),
(3, 3, 1, 2, 1299000.00, '2026-02-03 10:48:28'),
(4, 4, 11, 1, 399000.00, '2026-02-03 10:48:28'),
(5, 5, 11, 2, 399000.00, '2026-02-03 10:48:28'),
(6, 6, 2, 15, 899000.00, '2026-02-03 10:48:28'),
(7, 7, 11, 3, 399000.00, '2026-02-03 10:48:28'),
(8, 8, 1, 6, 1299000.00, '2026-02-03 10:48:28'),
(9, 9, 11, 4, 399000.00, '2026-02-03 10:48:28'),
(10, 10, 2, 17, 899000.00, '2026-02-03 10:48:28'),
(11, 11, 1, 9, 1299000.00, '2026-02-03 10:48:28'),
(12, 12, 11, 5, 399000.00, '2026-02-03 10:48:28'),
(13, 13, 11, 6, 399000.00, '2026-02-03 10:48:28'),
(14, 14, 2, 21, 899000.00, '2026-02-03 10:48:28'),
(15, 15, 1, 12, 1299000.00, '2026-02-03 10:48:28'),
(16, 16, 11, 7, 399000.00, '2026-02-03 10:48:28'),
(17, 17, 2, 25, 899000.00, '2026-02-03 10:48:28'),
(18, 18, 1, 26, 1299000.00, '2026-02-03 10:48:28'),
(19, 19, 11, 8, 399000.00, '2026-02-03 10:48:28'),
(20, 20, 11, 9, 399000.00, '2026-02-03 10:48:28'),
(21, 21, 11, 10, 399000.00, '2026-02-03 10:48:28'),
(22, 22, 2, 29, 899000.00, '2026-02-03 10:48:28'),
(23, 23, 1, 30, 1299000.00, '2026-02-03 10:48:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `config` text DEFAULT NULL COMMENT 'Cấu hình API (JSON)',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_code`, `method_name`, `is_active`, `config`, `display_order`, `created_at`) VALUES
(1, 'bank_transfer', 'Chuyển khoản ngân hàng', 1, NULL, 1, '2026-02-04 11:42:28'),
(2, 'momo', 'Ví MoMo', 1, NULL, 2, '2026-02-04 11:42:28'),
(3, 'zalopay', 'ZaloPay', 1, NULL, 3, '2026-02-04 11:42:28'),
(4, 'vnpay', 'VNPay', 1, NULL, 4, '2026-02-04 11:42:28'),
(5, 'paypal', 'PayPal', 0, NULL, 5, '2026-02-04 11:42:28'),
(6, 'balance', 'Số dư tài khoản', 1, NULL, 6, '2026-02-04 11:42:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `platforms`
--

CREATE TABLE `platforms` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `platforms`
--

INSERT INTO `platforms` (`id`, `name`, `icon_url`, `created_at`) VALUES
(1, 'Steam', NULL, '2026-02-03 10:48:28'),
(2, 'Epic Games Store', NULL, '2026-02-03 10:48:28'),
(3, 'GOG', NULL, '2026-02-03 10:48:28'),
(4, 'Battle.net', NULL, '2026-02-03 10:48:28'),
(5, 'Ubisoft Connect', NULL, '2026-02-03 10:48:28'),
(6, 'Origin / EA App', NULL, '2026-02-03 10:48:28'),
(7, 'Xbox Game Pass PC', NULL, '2026-02-03 10:48:28'),
(8, 'PlayStation Store', NULL, '2026-02-03 10:48:28'),
(9, 'Nintendo eShop', NULL, '2026-02-03 10:48:28'),
(10, 'Microsoft Store', NULL, '2026-02-03 10:48:28'),
(11, 'Rockstar Games Launcher', NULL, '2026-02-03 10:48:28'),
(12, 'Bethesda.net', NULL, '2026-02-03 10:48:28'),
(13, 'Riot Games', NULL, '2026-02-03 10:48:28'),
(14, 'Blizzard Battle.net', NULL, '2026-02-03 10:48:28'),
(15, 'Amazon Games', NULL, '2026-02-03 10:48:28'),
(16, 'Humble Bundle', NULL, '2026-02-03 10:48:28'),
(17, 'Green Man Gaming', NULL, '2026-02-03 10:48:28'),
(18, 'G2A', NULL, '2026-02-03 10:48:28'),
(19, 'Kinguin', NULL, '2026-02-03 10:48:28'),
(20, 'CD Keys', NULL, '2026-02-03 10:48:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `trailer_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `platform_id` int(11) DEFAULT NULL,
  `developer` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `price`, `discount_percent`, `image`, `gallery_images`, `trailer_url`, `category_id`, `platform_id`, `developer`, `release_date`, `rating`, `review_count`, `stock_quantity`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Elden Ring', 'elden-ring', 'Trò chơi nhập vai hành động thế giới mở từ FromSoftware. Khám phá Lands Between đầy bí ẩn.', 1299000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1245620/header.jpg', NULL, NULL, 2, 1, 'FromSoftware', '2022-02-25', 4.90, 2500, 10, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(2, 'Cyberpunk 2077', 'cyberpunk-2077', 'RPG hành động thế giới mở lấy bối cảnh Night City. Làm lính đánh thuê V.', 899000.00, 25, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1091500/header.jpg', NULL, NULL, 2, 1, 'CD Projekt Red', '2020-12-10', 4.30, 1800, 8, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(3, 'Black Myth: Wukong', 'black-myth-wukong', 'Action RPG lấy cảm hứng Tây Du Ký. Hóa thân Ngộ Không chiến đấu yêu quái.', 1499000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2358720/header.jpg', NULL, NULL, 2, 1, 'Game Science', '2024-08-20', 4.90, 3200, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(4, 'Baldurs Gate 3', 'baldurs-gate-3', 'RPG turn-based với câu chuyện sâu sắc và gameplay đỉnh cao.', 1399000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1086940/header.jpg', NULL, NULL, 2, 1, 'Larian Studios', '2023-08-03', 4.90, 2800, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(5, 'The Witcher 3', 'witcher-3', 'Geralt of Rivia trong cuộc phiêu lưu cuối. RPG hay nhất mọi thời đại.', 499000.00, 50, 'https://cdn.cloudflare.steamstatic.com/steam/apps/292030/header.jpg', NULL, NULL, 2, 1, 'CD Projekt Red', '2015-05-19', 4.90, 4500, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(6, 'Starfield', 'starfield', 'Bethesda đưa vào vũ trụ với 1000+ hành tinh. RPG không gian hoành tráng.', 1699000.00, 10, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1716740/header.jpg', NULL, NULL, 2, 1, 'Bethesda', '2023-09-06', 4.20, 1200, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(7, 'Diablo IV', 'diablo-4', 'Quay lại địa ngục với Diablo mới nhất. Hack slash đỉnh cao.', 1499000.00, 15, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2344520/header.jpg', NULL, NULL, 2, 4, 'Blizzard', '2023-06-06', 4.40, 1600, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(8, 'Final Fantasy XVI', 'final-fantasy-16', 'Chương mới dòng game huyền thoại. Hành động nhanh, đồ họa tuyệt đẹp.', 1599000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2515020/header.jpg', NULL, NULL, 2, 8, 'Square Enix', '2024-09-17', 4.60, 980, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(9, 'Skyrim Special Edition', 'skyrim-se', 'RPG kinh điển với modding vô tận. Dragonborn cứu thế giới.', 399000.00, 60, 'https://cdn.cloudflare.steamstatic.com/steam/apps/489830/header.jpg', NULL, NULL, 2, 1, 'Bethesda', '2016-10-28', 4.80, 5200, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(10, 'Dragon Age: The Veilguard', 'dragon-age-veilguard', 'Phần mới của series Dragon Age. BioWare trở lại với RPG fantasy.', 1399000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1845910/header.jpg', NULL, NULL, 2, 2, 'BioWare', '2024-10-31', 4.50, 750, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(11, 'GTA V', 'gta-v', 'Grand Theft Auto V - Game hành động phiêu lưu bán chạy nhất.', 399000.00, 40, 'https://cdn.cloudflare.steamstatic.com/steam/apps/271590/header.jpg', NULL, NULL, 1, 11, 'Rockstar', '2015-04-14', 4.80, 6800, 10, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(12, 'Red Dead Redemption 2', 'red-dead-2', 'Miền Tây hoang dã với Arthur Morgan. Câu chuyện cảm động.', 899000.00, 30, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1174180/header.jpg', NULL, NULL, 1, 11, 'Rockstar', '2019-12-05', 4.90, 5400, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(13, 'God of War Ragnarok', 'gow-ragnarok', 'Kratos và Atreus trong cuộc chiến Ragnarok. Action tuyệt đỉnh.', 1399000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2322010/header.jpg', NULL, NULL, 1, 1, 'Santa Monica', '2024-09-19', 4.80, 2100, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(14, 'Spider-Man Remastered', 'spider-man-pc', 'Peter Parker trở thành Spider-Man. Đu dây quanh New York.', 999000.00, 25, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1817070/header.jpg', NULL, NULL, 1, 1, 'Insomniac', '2022-08-12', 4.80, 1900, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(15, 'Sekiro Shadows Die Twice', 'sekiro', 'Samurai souls-like từ FromSoftware. Nhật Bản thời phong kiến.', 999000.00, 35, 'https://cdn.cloudflare.steamstatic.com/steam/apps/814380/header.jpg', NULL, NULL, 1, 1, 'FromSoftware', '2019-03-22', 4.70, 1800, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(16, 'Assassins Creed Valhalla', 'ac-valhalla', 'Viking xâm lược Anh. Thế giới mở khổng lồ thời Trung cổ.', 799000.00, 50, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2208920/header.jpg', NULL, NULL, 1, 5, 'Ubisoft', '2020-11-10', 4.40, 2300, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(17, 'Tomb Raider Collection', 'tomb-raider', 'Lara Croft trong 3 game hay nhất. Phiêu lưu, giải đố, hành động.', 599000.00, 70, 'https://cdn.cloudflare.steamstatic.com/steam/apps/203160/header.jpg', NULL, NULL, 1, 1, 'Crystal Dynamics', '2013-03-05', 4.60, 3100, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(18, 'Dying Light 2', 'dying-light-2', 'Sinh tồn zombie với parkour. Thế giới mở trong apocalypse.', 899000.00, 40, 'https://cdn.cloudflare.steamstatic.com/steam/apps/534380/header.jpg', NULL, NULL, 1, 1, 'Techland', '2022-02-04', 4.30, 1400, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(19, 'Hitman World of Assassination', 'hitman-woa', 'Agent 47 với 3 game trong 1 package. Stealth assassination đỉnh cao.', 699000.00, 50, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1659040/header.jpg', NULL, NULL, 1, 2, 'IO Interactive', '2021-01-20', 4.70, 980, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(20, 'Saints Row 2022', 'saints-row-2022', 'Reboot series Saints Row. Open world hành động hài hước.', 599000.00, 60, 'https://cdn.cloudflare.steamstatic.com/steam/apps/742360/header.jpg', NULL, NULL, 1, 2, 'Volition', '2022-08-23', 3.80, 620, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(21, 'Call of Duty MW III', 'cod-mw3', 'Phần mới nhất COD. Campaign và Multiplayer đỉnh cao.', 1699000.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2519060/header.jpg', NULL, NULL, 11, 4, 'Infinity Ward', '2023-11-10', 4.10, 2800, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(22, 'Counter-Strike 2', 'cs2', 'CS:GO remake với Source 2. Competitive FPS số 1 thế giới.', 0.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/730/header.jpg', NULL, NULL, 11, 1, 'Valve', '2023-09-27', 4.60, 8900, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(23, 'Valorant', 'valorant', 'Tactical FPS 5v5 từ Riot. Kết hợp abilities và gunplay.', 0.00, 0, 'https://images.contentstack.io/v3/assets/bltb6530b271fddd0b1/blt5c61a32a5f8e05e5/valorant-featured.jpg', NULL, NULL, 11, 13, 'Riot Games', '2020-06-02', 4.50, 4200, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(24, 'Battlefield 2042', 'battlefield-2042', 'Chiến tranh quy mô lớn 128 players. Maps khổng lồ, xe tăng, máy bay.', 899000.00, 60, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1517290/header.jpg', NULL, NULL, 11, 6, 'DICE', '2021-11-19', 3.60, 1200, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(25, 'Overwatch 2', 'overwatch-2', 'Hero shooter 5v5 miễn phí. Đa dạng heroes và abilities.', 0.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2357570/header.jpg', NULL, NULL, 11, 4, 'Blizzard', '2022-10-04', 4.20, 3600, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(26, 'Apex Legends', 'apex-legends', 'Battle Royale với Legends độc đáo. Fast-paced, skill-based.', 0.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1172470/header.jpg', NULL, NULL, 11, 2, 'Respawn', '2019-02-04', 4.40, 5100, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(27, 'Rainbow Six Siege', 'r6-siege', 'Tactical shooter 5v5. Destructible environment, operator abilities.', 199000.00, 75, 'https://cdn.cloudflare.steamstatic.com/steam/apps/359550/header.jpg', NULL, NULL, 11, 5, 'Ubisoft', '2015-12-01', 4.30, 4800, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(28, 'PUBG Battlegrounds', 'pubg', 'Battle Royale nguyên thủy. 100 players, last man standing.', 0.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/578080/header.jpg', NULL, NULL, 11, 1, 'PUBG Corp', '2017-12-21', 4.00, 6200, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(29, 'Destiny 2', 'destiny-2', 'Looter shooter MMO. Raids, dungeons, PvP trong không gian.', 0.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1085660/header.jpg', NULL, NULL, 11, 1, 'Bungie', '2017-09-06', 4.30, 3900, 0, 'active', '2026-02-03 10:48:28', '2026-02-03 10:48:28'),
(30, 'Halo Infinite', 'halo-infinite', 'Master Chief trở lại. Campaign hay, multiplayer miễn phí.', 0.00, 0, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1240440/header.jpg', NULL, NULL, 11, 7, '343 Industries', '2021-12-08', 4.10, 2200, 2, 'active', '2026-02-03 10:48:28', '2026-02-03 13:04:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_keys`
--

CREATE TABLE `product_keys` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `key_code` varchar(255) NOT NULL,
  `status` enum('available','sold','expired') DEFAULT 'available',
  `sold_to_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sold_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_keys`
--

INSERT INTO `product_keys` (`id`, `product_id`, `key_code`, `status`, `sold_to_user_id`, `created_at`, `sold_at`) VALUES
(1, 1, 'ELDEN-RING-2024-KEY-0001', 'sold', 2, '2026-02-03 10:48:28', '2024-03-15 10:30:00'),
(2, 1, 'ELDEN-RING-2024-KEY-0002', 'sold', 3, '2026-02-03 10:48:28', '2024-04-20 14:15:00'),
(3, 1, 'ELDEN-RING-2024-KEY-0003', 'available', NULL, '2026-02-03 10:48:28', NULL),
(4, 1, 'ELDEN-RING-2024-KEY-0004', 'available', NULL, '2026-02-03 10:48:28', NULL),
(5, 1, 'ELDEN-RING-2024-KEY-0005', 'available', NULL, '2026-02-03 10:48:28', NULL),
(6, 1, 'ELDEN-RING-2024-KEY-0006', 'sold', 5, '2026-02-03 10:48:28', '2024-05-10 09:45:00'),
(7, 1, 'ELDEN-RING-2024-KEY-0007', 'available', NULL, '2026-02-03 10:48:28', NULL),
(8, 1, 'ELDEN-RING-2024-KEY-0008', 'available', NULL, '2026-02-03 10:48:28', NULL),
(9, 1, 'ELDEN-RING-2024-KEY-0009', 'sold', 7, '2026-02-03 10:48:28', '2024-06-18 16:20:00'),
(10, 1, 'ELDEN-RING-2024-KEY-0010', 'available', NULL, '2026-02-03 10:48:28', NULL),
(11, 1, 'ELDEN-RING-2024-KEY-0011', 'available', NULL, '2026-02-03 10:48:28', NULL),
(12, 1, 'ELDEN-RING-2024-KEY-0012', 'sold', 9, '2026-02-03 10:48:28', '2024-08-22 11:10:00'),
(13, 1, 'ELDEN-RING-2024-KEY-0013', 'available', NULL, '2026-02-03 10:48:28', NULL),
(14, 1, 'ELDEN-RING-2024-KEY-0014', 'available', NULL, '2026-02-03 10:48:28', NULL),
(15, 1, 'ELDEN-RING-2024-KEY-0015', 'available', NULL, '2026-02-03 10:48:28', NULL),
(16, 2, 'CYBER-2077-PUNK-KEY-0001', 'sold', 2, '2026-02-03 10:48:28', '2024-02-14 13:25:00'),
(17, 2, 'CYBER-2077-PUNK-KEY-0002', 'available', NULL, '2026-02-03 10:48:28', NULL),
(18, 2, 'CYBER-2077-PUNK-KEY-0003', 'sold', 4, '2026-02-03 10:48:28', '2024-05-20 10:15:00'),
(19, 2, 'CYBER-2077-PUNK-KEY-0004', 'available', NULL, '2026-02-03 10:48:28', NULL),
(20, 2, 'CYBER-2077-PUNK-KEY-0005', 'available', NULL, '2026-02-03 10:48:28', NULL),
(21, 2, 'CYBER-2077-PUNK-KEY-0006', 'sold', 6, '2026-02-03 10:48:28', '2024-07-08 15:30:00'),
(22, 2, 'CYBER-2077-PUNK-KEY-0007', 'available', NULL, '2026-02-03 10:48:28', NULL),
(23, 2, 'CYBER-2077-PUNK-KEY-0008', 'available', NULL, '2026-02-03 10:48:28', NULL),
(24, 2, 'CYBER-2077-PUNK-KEY-0009', 'sold', 8, '2026-02-03 10:48:28', '2024-09-12 09:45:00'),
(25, 2, 'CYBER-2077-PUNK-KEY-0010', 'available', NULL, '2026-02-03 10:48:28', NULL),
(26, 2, 'CYBER-2077-PUNK-KEY-0011', 'available', NULL, '2026-02-03 10:48:28', NULL),
(27, 2, 'CYBER-2077-PUNK-KEY-0012', 'available', NULL, '2026-02-03 10:48:28', NULL),
(28, 11, 'GTAV5-ROCK-STAR-KEY-0001', 'sold', 2, '2026-02-03 10:48:28', '2024-01-20 11:00:00'),
(29, 11, 'GTAV5-ROCK-STAR-KEY-0002', 'sold', 3, '2026-02-03 10:48:28', '2024-02-15 14:30:00'),
(30, 11, 'GTAV5-ROCK-STAR-KEY-0003', 'sold', 4, '2026-02-03 10:48:28', '2024-03-10 09:15:00'),
(31, 11, 'GTAV5-ROCK-STAR-KEY-0004', 'sold', 5, '2026-02-03 10:48:28', '2024-04-05 16:45:00'),
(32, 11, 'GTAV5-ROCK-STAR-KEY-0005', 'sold', 6, '2026-02-03 10:48:28', '2024-05-12 10:20:00'),
(33, 11, 'GTAV5-ROCK-STAR-KEY-0006', 'sold', 7, '2026-02-03 10:48:28', '2024-06-18 13:55:00'),
(34, 11, 'GTAV5-ROCK-STAR-KEY-0007', 'sold', 8, '2026-02-03 10:48:28', '2024-07-22 15:10:00'),
(35, 11, 'GTAV5-ROCK-STAR-KEY-0008', 'sold', 9, '2026-02-03 10:48:28', '2024-08-14 11:30:00'),
(36, 11, 'GTAV5-ROCK-STAR-KEY-0009', 'sold', 10, '2026-02-03 10:48:28', '2024-09-20 14:00:00'),
(37, 11, 'GTAV5-ROCK-STAR-KEY-0010', 'sold', 11, '2026-02-03 10:48:28', '2024-10-25 09:40:00'),
(38, 11, 'GTAV5-ROCK-STAR-KEY-0011', 'available', NULL, '2026-02-03 10:48:28', NULL),
(39, 11, 'GTAV5-ROCK-STAR-KEY-0012', 'available', NULL, '2026-02-03 10:48:28', NULL),
(40, 11, 'GTAV5-ROCK-STAR-KEY-0013', 'available', NULL, '2026-02-03 10:48:28', NULL),
(41, 11, 'GTAV5-ROCK-STAR-KEY-0014', 'available', NULL, '2026-02-03 10:48:28', NULL),
(42, 11, 'GTAV5-ROCK-STAR-KEY-0015', 'available', NULL, '2026-02-03 10:48:28', NULL),
(43, 11, 'GTAV5-ROCK-STAR-KEY-0016', 'available', NULL, '2026-02-03 10:48:28', NULL),
(44, 11, 'GTAV5-ROCK-STAR-KEY-0017', 'available', NULL, '2026-02-03 10:48:28', NULL),
(45, 11, 'GTAV5-ROCK-STAR-KEY-0018', 'available', NULL, '2026-02-03 10:48:28', NULL),
(46, 11, 'GTAV5-ROCK-STAR-KEY-0019', 'available', NULL, '2026-02-03 10:48:28', NULL),
(47, 11, 'GTAV5-ROCK-STAR-KEY-0020', 'available', NULL, '2026-02-03 10:48:28', NULL),
(48, 30, 'samdn12312', 'available', NULL, '2026-02-03 12:07:12', NULL),
(51, 30, '213', 'available', NULL, '2026-02-03 13:04:21', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` longtext DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `status`, `created_at`, `approved_at`) VALUES
(1, 2, 1, 5, 'Elden Ring tuyệt vời! Thế giới mở rộng lớn, boss khó nhưng rất phê. Worth every penny!', 'approved', '2024-01-20 08:00:00', '2024-01-20 16:00:00'),
(2, 3, 2, 4, 'Cyberpunk sau update đã khá hơn nhiều. Story hay, graphics đẹp.', 'approved', '2024-03-01 03:30:00', '2024-03-01 11:00:00'),
(3, 4, 1, 5, 'FromSoftware không làm tôi thất vọng. Best game 2022!', 'approved', '2024-03-15 07:20:00', '2024-03-15 15:00:00'),
(4, 5, 11, 5, 'GTA V vẫn hay sau bao năm. Online rất vui với bạn bè.', 'approved', '2024-05-20 04:15:00', '2024-05-20 12:00:00'),
(5, 6, 11, 5, 'Kinh điển là mãi mãi! Los Santos quá đỉnh.', 'approved', '2024-06-15 06:45:00', '2024-06-15 14:30:00'),
(6, 7, 2, 4, 'Night City đẹp mê hồn. Questline hấp dẫn.', 'approved', '2024-07-28 02:20:00', '2024-07-28 10:00:00'),
(7, 8, 1, 5, 'Khó nhưng addictive. Exploration tuyệt vời.', 'approved', '2024-08-20 09:30:00', '2024-08-20 17:00:00'),
(8, 9, 11, 5, 'Mua lần 3 rồi vẫn không chán. Masterpiece!', 'approved', '2024-09-10 03:45:00', '2024-09-10 11:30:00'),
(9, 10, 2, 3, 'Hay nhưng vẫn còn bugs. Cần thêm patch.', 'approved', '2024-10-25 07:00:00', '2024-10-25 15:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'GameKey Store Vietnam', '2026-02-03 10:48:28'),
(2, 'site_email', 'support@gamekey.vn', '2026-02-03 10:48:28'),
(3, 'site_phone', '1900-123-456', '2026-02-03 10:48:28'),
(4, 'site_address', '123 Nguyễn Huệ, Q1, TP.HCM', '2026-02-03 10:48:28'),
(5, 'currency', 'VND', '2026-02-03 10:48:28'),
(6, 'maintenance_mode', 'false', '2026-02-03 10:48:28'),
(7, 'enable_2fa', 'true', '2026-02-03 10:48:28'),
(8, 'admin_commission_percent', '5', '2026-02-03 10:48:28'),
(9, 'min_deposit', '50000', '2026-02-03 10:48:28'),
(10, 'max_deposit', '50000000', '2026-02-03 10:48:28'),
(11, 'payment_bank_name', 'Vietcombank', '2026-02-03 10:48:28'),
(12, 'payment_bank_account', '1234567890', '2026-02-03 10:48:28'),
(13, 'payment_bank_owner', 'CONG TY GAMEKEY VIET NAM', '2026-02-03 10:48:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `transaction_code` varchar(50) NOT NULL COMMENT 'Mã giao dịch',
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_method` enum('bank_transfer','momo','zalopay','vnpay','paypal','balance') NOT NULL DEFAULT 'bank_transfer',
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_details` text DEFAULT NULL COMMENT 'Chi tiết giao dịch (JSON)',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','locked') DEFAULT 'active',
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `fullname`, `phone`, `avatar`, `balance`, `role`, `status`, `two_factor_enabled`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'admin', 'admin@gamekey.vn', '$2y$10$H0IlXHqduThmlumq7xRCxOsmuwqexRCCnZYP5WifRYX2Z95KS65Ai', 'Admin GameKey', '0900000000', NULL, 0.00, 'admin', 'active', 0, '2023-12-31 17:00:00', '2026-02-03 10:48:28', NULL),
(2, 'user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0912345678', NULL, 2500000.00, 'customer', 'active', 0, '2024-01-15 03:30:00', '2026-02-03 10:48:28', NULL),
(3, 'user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0923456789', NULL, 1200000.00, 'customer', 'active', 0, '2024-02-20 07:15:00', '2026-02-03 10:48:28', NULL),
(4, 'gamer123', 'gamer123@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Minh Gamer', '0934567890', NULL, 500000.00, 'customer', 'active', 0, '2024-03-10 02:45:00', '2026-02-03 10:48:28', NULL),
(5, 'proplayer', 'pro@esports.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pro Player VN', '0945678901', NULL, 3000000.00, 'customer', 'active', 0, '2024-04-05 09:20:00', '2026-02-03 10:48:28', NULL),
(6, 'hoangminh', 'hoangminh@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Minh', '0956789012', NULL, 800000.00, 'customer', 'active', 0, '2024-05-12 04:00:00', '2026-02-03 10:48:28', NULL),
(7, 'thanhthao', 'thanhthao@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thanh Thảo', '0967890123', NULL, 1500000.00, 'customer', 'active', 0, '2024-06-08 06:30:00', '2026-02-03 10:48:28', NULL),
(8, 'dungpham', 'dungpham@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn Dũng', '0978901234', NULL, 600000.00, 'customer', 'active', 0, '2024-07-22 08:45:00', '2026-02-03 10:48:28', NULL),
(9, 'linhtran', 'linhtran@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thu Linh', '0989012345', NULL, 2000000.00, 'customer', 'active', 0, '2024-08-14 01:20:00', '2026-02-03 10:48:28', NULL),
(10, 'anhvu', 'anhvu@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vũ Tuấn Anh', '0990123456', NULL, 900000.00, 'customer', 'active', 0, '2024-09-03 03:00:00', '2026-02-03 10:48:28', NULL),
(11, 'myduyen', 'myduyen@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Mỹ Duyên', '0901234567', NULL, 1100000.00, 'customer', 'active', 0, '2024-10-18 07:30:00', '2026-02-03 10:48:28', NULL),
(12, 'quangnguyen', 'quangnguyen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Quang', '0912345670', NULL, 700000.00, 'customer', 'active', 0, '2024-11-25 02:15:00', '2026-02-03 10:48:28', NULL),
(13, 'hienle', 'hienle@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Thị Hiền', '0923456780', NULL, 1800000.00, 'customer', 'active', 0, '2024-12-02 04:45:00', '2026-02-03 10:48:28', NULL),
(14, 'khanhpham', 'khanhpham@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn Khánh', '0934567891', NULL, 450000.00, 'customer', 'active', 0, '2025-01-07 06:00:00', '2026-02-03 10:48:28', NULL),
(15, 'tamtran', 'tamtran@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Tâm', '0945678902', NULL, 2200000.00, 'customer', 'active', 0, '2025-02-14 09:20:00', '2026-02-03 10:48:28', NULL),
(16, 'hungdo', 'hungdo@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đỗ Văn Hùng', '0956789013', NULL, 550000.00, 'customer', 'active', 0, '2025-03-21 03:30:00', '2026-02-03 10:48:28', NULL),
(17, 'hoangnguyen', 'hoangnguyen@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Hoàng', '0967890124', NULL, 1300000.00, 'customer', 'active', 0, '2025-04-18 01:45:00', '2026-02-03 10:48:28', NULL),
(18, 'lanpham', 'lanpham@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Lan', '0978901235', NULL, 950000.00, 'customer', 'active', 0, '2025-05-25 08:10:00', '2026-02-03 10:48:28', NULL),
(19, 'tuannguyen', 'tuannguyen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Anh Tuấn', '0989012346', NULL, 1650000.00, 'customer', 'active', 0, '2025-06-30 05:25:00', '2026-02-03 10:48:28', NULL),
(20, 'viethoang', 'viethoang@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Việt', '0990123457', NULL, 400000.00, 'customer', 'active', 0, '2025-07-14 02:50:00', '2026-02-03 10:48:28', NULL),
(21, 'thuylinh', 'thuylinh@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thúy Linh', '0901234568', NULL, 2800000.00, 'customer', 'active', 0, '2025-08-22 07:15:00', '2026-02-03 10:48:28', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('deposit','purchase','refund','bonus') NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `balance_before` decimal(15,2) DEFAULT NULL,
  `balance_after` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `user_id`, `amount`, `type`, `reference_id`, `description`, `balance_before`, `balance_after`, `status`, `created_at`) VALUES
(1, 2, 5000000.00, 'deposit', NULL, 'Nạp tiền qua ngân hàng', 0.00, 5000000.00, 'completed', '2024-01-10 02:00:00'),
(2, 2, 1299000.00, 'purchase', NULL, 'Mua Elden Ring #ORD-2024-0001', 5000000.00, 3701000.00, 'completed', '2024-01-15 03:30:00'),
(3, 2, 2000000.00, 'deposit', NULL, 'Nạp tiền MoMo', 3701000.00, 5701000.00, 'completed', '2025-12-20 04:00:00'),
(4, 2, 1499000.00, 'purchase', NULL, 'Mua Black Myth Wukong #ORD-2026-0001', 5701000.00, 4202000.00, 'completed', '2026-01-10 03:00:00'),
(5, 3, 3000000.00, 'deposit', NULL, 'Nạp tiền qua ngân hàng', 0.00, 3000000.00, 'completed', '2024-02-15 03:00:00'),
(6, 3, 899000.00, 'purchase', NULL, 'Mua Cyberpunk 2077 #ORD-2024-0002', 3000000.00, 2101000.00, 'completed', '2024-02-20 07:15:00'),
(7, 3, 899000.00, 'purchase', NULL, 'Mua Cyberpunk 2077 #ORD-2026-0002', 2101000.00, 1202000.00, 'completed', '2026-01-18 07:30:00'),
(8, 4, 2000000.00, 'deposit', NULL, 'Nạp tiền đầu tiên', 0.00, 2000000.00, 'completed', '2024-03-05 01:00:00'),
(9, 4, 1299000.00, 'purchase', NULL, 'Mua Elden Ring #ORD-2024-0003', 2000000.00, 701000.00, 'completed', '2024-03-10 02:45:00'),
(10, 4, 899000.00, 'purchase', NULL, 'Mua Cyberpunk 2077 #ORD-2024-0004', 701000.00, -198000.00, 'completed', '2024-04-05 09:20:00'),
(11, 5, 4000000.00, 'deposit', NULL, 'Nạp tiền VIP', 0.00, 4000000.00, 'completed', '2024-04-01 03:00:00'),
(12, 5, 399000.00, 'purchase', NULL, 'Mua GTA V #ORD-2024-0005', 4000000.00, 3601000.00, 'completed', '2024-05-12 04:00:00'),
(13, 5, 1599000.00, 'purchase', NULL, 'Mua game #ORD-2026-0003', 3601000.00, 2002000.00, 'completed', '2026-02-02 02:15:00'),
(14, 6, 1500000.00, 'deposit', NULL, 'Nạp tiền', 0.00, 1500000.00, 'completed', '2024-06-01 02:00:00'),
(15, 7, 2000000.00, 'deposit', NULL, 'Nạp tiền', 0.00, 2000000.00, 'completed', '2024-07-15 03:00:00'),
(16, 8, 1800000.00, 'deposit', NULL, 'Nạp tiền', 0.00, 1800000.00, 'completed', '2024-08-10 04:00:00'),
(17, 9, 2500000.00, 'deposit', NULL, 'Nạp tiền', 0.00, 2500000.00, 'completed', '2024-09-01 02:30:00'),
(18, 10, 1200000.00, 'deposit', NULL, 'Nạp tiền', 0.00, 1200000.00, 'completed', '2024-10-15 03:15:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 2, 5, '2025-10-15 03:00:00'),
(2, 2, 7, '2025-11-20 07:30:00'),
(3, 3, 1, '2025-09-05 02:15:00'),
(4, 3, 12, '2025-12-10 09:45:00'),
(5, 4, 8, '2026-01-15 04:20:00'),
(6, 5, 14, '2025-08-22 06:30:00'),
(7, 6, 6, '2025-07-18 03:50:00'),
(8, 7, 13, '2026-01-25 08:10:00'),
(9, 8, 4, '2025-11-30 02:40:00'),
(10, 9, 15, '2025-12-20 07:25:00');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Chỉ mục cho bảng `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `email_type` (`email_type`),
  ADD KEY `status` (`status`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order_number` (`order_number`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `key_id` (`key_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Chỉ mục cho bảng `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `method_code` (`method_code`);

--
-- Chỉ mục cho bảng `platforms`
--
ALTER TABLE `platforms`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_platform` (`platform_id`),
  ADD KEY `idx_slug` (`slug`);
ALTER TABLE `products` ADD FULLTEXT KEY `ft_name_desc` (`name`,`description`);

--
-- Chỉ mục cho bảng `product_keys`
--
ALTER TABLE `product_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_code` (`key_code`),
  ADD KEY `sold_to_user_id` (`sold_to_user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_key_code` (`key_code`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `status` (`status`),
  ADD KEY `payment_method` (`payment_method`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `platforms`
--
ALTER TABLE `platforms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `product_keys`
--
ALTER TABLE `product_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`key_id`) REFERENCES `product_keys` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`platform_id`) REFERENCES `platforms` (`id`);

--
-- Các ràng buộc cho bảng `product_keys`
--
ALTER TABLE `product_keys`
  ADD CONSTRAINT `product_keys_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_keys_ibfk_2` FOREIGN KEY (`sold_to_user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_trans_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;