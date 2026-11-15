-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 12, 2026 at 12:46 PM
-- Server version: 5.7.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:3;', 1794484581),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1794484581;', 1794484581);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `institution_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Peralatan Praktek', NULL, '2026-11-03 03:18:39', '2026-11-03 04:07:08'),
(2, 1, 'Mebel / Furnitur', NULL, '2026-11-03 03:44:20', '2026-11-03 04:06:18'),
(3, 1, 'Peralatan Kebersihan', NULL, '2026-11-03 04:04:29', '2026-11-03 04:06:58'),
(4, 1, 'Peralatan Tata Udara', NULL, '2026-11-03 04:06:48', '2026-11-03 04:06:48'),
(5, 1, 'Peralatan Audiovisual', NULL, '2026-11-03 04:07:22', '2026-11-03 04:07:22'),
(6, 1, 'Peralatan Jaringan Komputer', NULL, '2026-11-07 23:42:52', '2026-11-07 23:42:52'),
(7, 1, 'Peralatan Keamanan', NULL, '2026-11-07 23:50:38', '2026-11-07 23:50:38'),
(8, 1, 'Peralatan Penyimpanan Elektronik', NULL, '2026-11-08 00:39:22', '2026-11-08 00:48:21'),
(9, 1, 'Cable', NULL, '2026-11-08 00:47:26', '2026-11-08 00:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `institution_id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'RPL', 'Jurusan Rekayasa Perangkat Lunak', NULL, 1, '2026-11-03 03:16:33', '2026-11-12 00:57:42'),
(2, 2, 'Umum', 'Umum', NULL, 1, '2026-11-07 18:48:32', '2026-11-12 00:58:02'),
(3, 1, 'TKJ', 'Teknologi Jaringan Komputer', NULL, 1, '2026-11-07 23:07:19', '2026-11-12 00:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `code`, `name`, `address`, `phone`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SMK1', 'SMKN 1 Kotabaru', NULL, NULL, NULL, 1, '2026-11-03 03:16:15', '2026-11-12 03:22:57'),
(2, 'SMP1', 'SMPN 1 Kotabaru', NULL, NULL, NULL, 1, '2026-11-07 18:41:02', '2026-11-12 03:23:04');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `institution_id`, `category_id`, `code`, `name`, `description`, `unit`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'SMK1-001', 'Laptop', NULL, 'unit', '2026-11-03 03:22:54', '2026-11-03 04:07:43'),
(2, 1, 5, 'SMK1-002', 'Proyektor', NULL, 'unit', '2026-11-03 03:44:47', '2026-11-03 04:08:41'),
(3, 1, 1, 'SMK1-003', 'Monitor', 'Monitor PC', 'unit', '2026-11-03 03:48:06', '2026-11-03 04:09:01'),
(4, 1, 4, 'SMK1-004', 'Pendingin Ruangan', NULL, 'unit', '2026-11-07 23:20:05', '2026-11-07 23:20:05'),
(5, 1, 1, 'SMK1-005', 'Personal Computer', NULL, 'unit', '2026-11-07 23:41:18', '2026-11-07 23:41:18'),
(6, 1, 6, 'SMK1-006', 'Router', NULL, 'unit', '2026-11-07 23:43:41', '2026-11-07 23:43:41'),
(7, 1, 7, 'SMK1-007', 'CCTV', NULL, 'unit', '2026-11-07 23:51:20', '2026-11-07 23:51:20'),
(8, 1, 1, 'SMK1-008', 'Camera', NULL, 'unit', '2026-11-08 00:06:10', '2026-11-08 00:06:10'),
(9, 1, 1, 'SMK1-009', 'Battery', NULL, 'unit', '2026-11-08 00:34:31', '2026-11-08 00:34:31'),
(10, 1, 8, 'SMK1-010', 'Memory', NULL, 'unit', '2026-11-08 00:39:59', '2026-11-08 00:40:25'),
(11, 1, 1, 'SMK1-011', 'Cable', NULL, 'unit', '2026-11-08 00:49:34', '2026-11-08 00:49:34');

-- --------------------------------------------------------

--
-- Table structure for table `item_transfers`
--

CREATE TABLE `item_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('room','department') COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_department_id` bigint(20) UNSIGNED NOT NULL,
  `to_department_id` bigint(20) UNSIGNED NOT NULL,
  `from_room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transferred_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_transfer_items`
--

CREATE TABLE `item_transfer_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `item_transfer_id` bigint(20) UNSIGNED NOT NULL,
  `item_unit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_units`
--

CREATE TABLE `item_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_number` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `purchase_year` smallint(5) UNSIGNED DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `funding_source` enum('BOSP','BOSDA','Bantuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition` enum('good','minor_damage','major_damage') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_units`
--

INSERT INTO `item_units` (`id`, `institution_id`, `item_id`, `department_id`, `room_id`, `code`, `serial_number`, `description`, `purchase_year`, `purchase_price`, `funding_source`, `condition`, `condition_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 'RPL-001', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:24:53', '2026-11-03 03:59:15'),
(2, 1, 1, 1, 1, 'RPL-002', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:25:38', '2026-11-03 03:59:27'),
(3, 1, 1, 1, 1, 'RPL-003', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:31:08', '2026-11-03 03:59:32'),
(4, 1, 1, 1, 1, 'RPL-004', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:31:27', '2026-11-03 03:59:36'),
(5, 1, 1, 1, 1, 'RPL-005', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:31:48', '2026-11-03 03:59:41'),
(6, 1, 1, 1, 1, 'RPL-006', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:31:57', '2026-11-03 03:59:46'),
(7, 1, 1, 1, 1, 'RPL-007', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:32:03', '2026-11-03 03:59:50'),
(8, 1, 1, 1, 1, 'RPL-008', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:32:12', '2026-11-03 03:59:55'),
(9, 1, 1, 1, 1, 'RPL-009', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:32:37', '2026-11-03 03:59:59'),
(10, 1, 1, 1, 1, 'RPL-010', NULL, 'Laptop Lenovo Ideapad Slim 3 15\r\nProcessor: Intel Core i5-13420H (8 Core / 12 Thread, hingga 4.6GHz)\r\nGraphics: Integrated Intel UHD Graphics\r\nMemory: 8GB DDR5-4800 (Soldered)\r\nStorage: 512GB SSD M.2 NVMe (Tersedia slot tambahan untuk upgrade)\r\nDisplay: 14 inci WUXGA (1920x1200) IPS, Anti-glare, 300 nits', 2025, 9500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:32:54', '2026-11-03 04:00:04'),
(11, 1, 3, 1, 1, 'RPL-011', NULL, 'LG UltraWide 34WR50QK-B\r\nUkuran & Layar: 34 inci Curved UltraWide (Kurvatur 1800R), Rasio Aspek 21:9\r\nResolusi: WQHD (3440 x 1440)\r\nTipe Panel: VA Panel\r\nGamut Warna & HDR: sRGB 99% (Typ.) dengan dukungan HDR10\r\nKecerahan & Refresh Rate: 300 cd/m² (Typ.), Refresh Rate Maksimal 100 Hz, Waktu Respon 5 ms (GtG)', 2025, 7500000.00, 'BOSP', 'good', NULL, '2026-11-03 03:50:33', '2026-11-03 03:58:26'),
(12, 1, 4, 1, 1, 'RPL-012', NULL, 'AC Panasonic', 2025, NULL, 'BOSP', 'good', NULL, '2026-11-07 23:22:09', '2026-11-07 23:22:09'),
(13, 1, 2, 1, 1, 'RPL-013', NULL, 'Proyektor Epson', 2025, NULL, 'BOSP', 'good', NULL, '2026-11-07 23:24:09', '2026-11-07 23:24:09'),
(14, 1, 7, 1, 1, 'RPL-014', NULL, 'Xiaomi Smart Camera CCTV C500\r\n\r\n* Resolusi Kamera: 3.5K (3200 x 1800 piksel) / 6 MP\r\n* Sudut Pandang / Rotasi: 360° (Pan & Tilt)\r\n* Konektivitas Nirkabel: Wi-Fi IEEE 802.11a/b/g/n/ac/ax (2.4 GHz / 5 GHz), Bluetooth 5.0\r\n* Input Daya: 5V / 2A\r\n* Penyimpanan: Kartu microSD (8 GB hingga 256 GB), Cloud Storage, dan NAS (Network Attached Storage)\r\n* Suhu Operasional: -10°C hingga 40°C\r\n* Fitur Keamanan: AI Human & Pet Detection, Night Vision Warna Cahaya Rendah, Two-Way Audio\r\n* Garansi: Garansi Resmi Xiaomi Indonesia 1 Tahun', 2026, 1028000.00, 'BOSP', 'good', NULL, '2026-11-07 23:54:13', '2026-11-07 23:54:13'),
(15, 1, 1, 1, 1, 'RPL-015', NULL, 'Laptop Lenovo IdeaPad Slim 3 Intel Core i3-1315U\r\n\r\n* Prosesor: Intel Core i3-1315U (6 Cores / 8 Threads, P-core up to 4.5GHz, E-core up to 3.3GHz, 10MB Cache)\r\n* Grafis: Integrated Intel UHD Graphics\r\n* Memori RAM: 8GB Soldered LPDDR5-4800 (Non-upgradable)\r\n* Penyimpanan: 256GB SSD M.2 2242 PCIe 4.0x4 NVMe\r\n* Layar: 14 Inci FHD (1920 x 1080) TN 250nits Anti-glare\r\n* Sistem Operasi: Windows 11 Home Single Language + Microsoft 365 Basic & Office Home 2024\r\n* Kamera & Keamanan: HD 720p dengan Privacy Shutter (Penutup Kamera Fisik)\r\n* Audio & Mikrofon: User-facing stereo speakers 1.5W x2 dengan Dolby Audio, Dual Microphone Array\r\n* Konektivitas: Wi-Fi 6, Bluetooth 5.2, 2x USB 3.2 Gen 1, 1x USB-C 3.2 Gen 1 (Support Data Transfer, Power Delivery, & DisplayPort 1.2), HDMI 1.4, SD Card Reader, Audio Combo Jack\r\n* Baterai: 47Wh dengan Adaptor Daya 65W Round Tip\r\n* Garansi: Garansi Resmi Lenovo Indonesia 1 Tahun + 2 Tahun Premium Care & Accidental Damage Protection (ADP)', 2026, 10500000.00, 'BOSP', 'good', NULL, '2026-11-07 23:58:01', '2026-11-07 23:58:01'),
(16, 1, 8, 1, 1, 'RPL-016', NULL, 'Webcam Logitech MX Brio 4K Ultra HD\r\n\r\n* Berat: 200 gram\r\n* Dimensi: 7,00 cm x 5,00 cm x 2,00 cm\r\n* Kelengkapan Fisik: Unit webcam dengan mounting clip yang dapat dilepas, kabel USB-C ke USB-C 3.0, serta penutup privasi mekanis internal (Integrated Privacy Shutter)', 2026, 3960000.00, 'BOSP', 'good', NULL, '2026-11-07 23:59:32', '2026-11-08 00:06:32'),
(17, 1, 7, 1, 1, 'RPL-017', NULL, 'Memory Card SanDisk Ultra MicroSD 256GB Class 10 UHS-I A1 150MB/s Garansi Resmi\r\n* Berat: 10 gram\r\n* Dimensi: 2,00 cm x 2,00 cm x 2,00 cm', 2026, 1044000.00, 'BOSP', 'good', NULL, '2026-11-08 00:09:54', '2026-11-08 00:09:54'),
(18, 1, 1, 1, 1, 'RPL-018', NULL, 'Laptop Lenovo IdeaPad Slim 3i 14IPH11\r\n* Prosesor: Intel Core Ultra 5 322 (6 Cores / 6 Threads, Max Turbo up to 4.4GHz, 12MB Cache)\r\n* NPU (AI Engine): Up to 46 TOPS (Copilot+ PC Ready)\r\n* Grafis: Integrated Intel Graphics\r\n* Memori RAM: 16GB SODIMM DDR5-5600\r\n* Penyimpanan: 512GB SSD M.2 2242 PCIe 4.0x4 NVMe\r\n* Layar: 14 Inci WUXGA (1920 x 1200) IPS 400nits, Anti-glare, 60Hz, 45% NTSC, Aspect Ratio 16:10\r\n* Kamera: FHD 1080p + IR Camera dengan Privacy Shutter\r\n* Konektivitas & Port: Wi-Fi 6, Bluetooth 5.3, 2x USB-A (USB 5Gbps), 1x USB-C (USB 5Gbps with PD & DisplayPort 1.2), HDMI 1.4, SD Card Reader, Audio Jack\r\n* Baterai & Daya: 60Wh (Rapid Charge Boost), Adaptor Daya / Charger 65W\r\n* Berat & Dimensi: 1,5 kg / 20,00 cm x 15,00 cm x 2,00 cm\r\n* Kelengkapan Fisik: Unit Laptop, Adaptor Daya 65W, Tas Ransel Lenovo ECO Backpack B210', 2026, 20000000.00, 'BOSP', 'good', NULL, '2026-11-08 00:12:11', '2026-11-08 00:12:11'),
(19, 1, 1, 1, 1, 'RPL-019', NULL, 'Laptop Axio MyBook Pro K7.2 (16N5)\r\nProsesor: Intel Core i7 Gen 11\r\nMemori RAM: 16GB DDR\r\nPenyimpanan: 512GB SSD\r\nLayar: 14 Inci FHD\r\nKonektivitas: Wi-Fi, Bluetooth\r\nSistem Operasi: Windows 10', 2026, 21979000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:28:09', '2026-11-08 00:28:09'),
(20, 1, 1, 1, 1, 'RPL-020', NULL, 'Laptop Axio MyBook Pro K7.2 (16N5)\r\nProsesor: Intel Core i7 Gen 11\r\nMemori RAM: 16GB DDR\r\nPenyimpanan: 512GB SSD\r\nLayar: 14 Inci FHD\r\nKonektivitas: Wi-Fi, Bluetooth\r\nSistem Operasi: Windows 10', 2026, 21979000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:29:17', '2026-11-08 00:29:17'),
(21, 1, 1, 1, 1, 'RPL-021', NULL, 'Laptop Axio MyBook Pro K7.2 (16N5)\r\nProsesor: Intel Core i7 Gen 11\r\nMemori RAM: 16GB DDR\r\nPenyimpanan: 512GB SSD\r\nLayar: 14 Inci FHD\r\nKonektivitas: Wi-Fi, Bluetooth\r\nSistem Operasi: Windows 10', 2026, 21979000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:29:29', '2026-11-08 00:29:29'),
(22, 1, 1, 1, 1, 'RPL-022', NULL, 'Laptop Axio MyBook Pro K7.2 (16N5)\r\nProsesor: Intel Core i7 Gen 11\r\nMemori RAM: 16GB DDR\r\nPenyimpanan: 512GB SSD\r\nLayar: 14 Inci FHD\r\nKonektivitas: Wi-Fi, Bluetooth\r\nSistem Operasi: Windows 10', 2026, 21979000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:29:45', '2026-11-08 00:29:45'),
(23, 1, 5, 1, 1, 'RPL-023', NULL, 'Komputer Client MyPC Pro L5 (8N5-22-W)\r\nProsesor: Intel Core i5\r\nGrafis: Integrated VGA\r\nMemori RAM: 8GB\r\nPenyimpanan: 512GB SSD\r\nLayar: 21.5 Inci FHD\r\nKonektivitas: Wi-Fi\r\nSistem Operasi: Windows 11 Pro', 2026, 24531000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:30:52', '2026-11-08 00:30:52'),
(24, 1, 5, 1, 1, 'RPL-024', NULL, 'Komputer Client MyPC Pro L5 (8N5-22-W)\r\nProsesor: Intel Core i5\r\nGrafis: Integrated VGA\r\nMemori RAM: 8GB\r\nPenyimpanan: 512GB SSD\r\nLayar: 21.5 Inci FHD\r\nKonektivitas: Wi-Fi\r\nSistem Operasi: Windows 11 Pro', 2026, 24531000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:31:15', '2026-11-08 00:31:15'),
(25, 1, 5, 1, 1, 'RPL-025', NULL, 'Komputer Client MyPC Pro L5 (8N5-22-W)\r\nProsesor: Intel Core i5\r\nGrafis: Integrated VGA\r\nMemori RAM: 8GB\r\nPenyimpanan: 512GB SSD\r\nLayar: 21.5 Inci FHD\r\nKonektivitas: Wi-Fi\r\nSistem Operasi: Windows 11 Pro', 2026, 24531000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:31:25', '2026-11-08 00:31:25'),
(26, 1, 5, 1, 1, 'RPL-026', NULL, 'Komputer Client MyPC Pro L5 (8N5-22-W)\r\nProsesor: Intel Core i5\r\nGrafis: Integrated VGA\r\nMemori RAM: 8GB\r\nPenyimpanan: 512GB SSD\r\nLayar: 21.5 Inci FHD\r\nKonektivitas: Wi-Fi\r\nSistem Operasi: Windows 11 Pro', 2026, 24531000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:31:35', '2026-11-08 00:31:35'),
(27, 1, 2, 1, 1, 'RPL-027', NULL, 'LCD Proyektor Projector Acer X1\r\nResolusi & Teknologi: XGA (1024 x 768), DLP Technology\r\nKecerahan & Kontras: 4000 ANSI Lumens, 20.000:1 Dynamic Black\r\nAspect Ratio: 4:3 (Native), 16:9 (Supported)\r\nUmur Lampu (Lamp Life): 6.000 Jam (Standard), 10.000 Jam (ECO), 15.000 Jam (ExtremeEco)\r\nInput Port: VGA, HDMI, Composite Video (RCA), PC Audio (Stereo Mini Jack)\r\nOutput Port: PC Audio (Stereo Mini Jack), DC Out (5V/1A, USB Type-A)', 2026, 7379280.00, 'Bantuan', 'good', NULL, '2026-11-08 00:32:46', '2026-11-08 00:32:46'),
(28, 1, 9, 1, 1, 'RPL-028', NULL, 'Uninterruptible Power Supply (UPS)\r\nAC Input: Voltage 140 - 300V, Frequency 50 Hz ± 10%\r\nInverter Output: Waveform Synthesized Sinewave, Voltage 230 V ± 10%, Frequency 50 Hz ± 1%\r\nIndicator / Display: LCD Monochrome\r\nKapasitas Baterai: 2 x 7,2Ah, 12V\r\nBerat & Dimensi: 10,0 kg (Gross) / 8,0 kg (Netto), 298 x 101 x 142 mm (DxWxH)', 2026, 1534000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:34:04', '2026-11-08 00:34:59'),
(29, 1, 9, 1, 1, 'RPL-029', NULL, 'Uninterruptible Power Supply (UPS)\r\nAC Input: Voltage 140 - 300V, Frequency 50 Hz ± 10%\r\nInverter Output: Waveform Synthesized Sinewave, Voltage 230 V ± 10%, Frequency 50 Hz ± 1%\r\nIndicator / Display: LCD Monochrome\r\nKapasitas Baterai: 2 x 7,2Ah, 12V\r\nBerat & Dimensi: 10,0 kg (Gross) / 8,0 kg (Netto), 298 x 101 x 142 mm (DxWxH)', 2026, 1534000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:35:07', '2026-11-08 00:35:07'),
(30, 1, 9, 1, 1, 'RPL-030', NULL, 'Uninterruptible Power Supply (UPS)\r\nAC Input: Voltage 140 - 300V, Frequency 50 Hz ± 10%\r\nInverter Output: Waveform Synthesized Sinewave, Voltage 230 V ± 10%, Frequency 50 Hz ± 1%\r\nIndicator / Display: LCD Monochrome\r\nKapasitas Baterai: 2 x 7,2Ah, 12V\r\nBerat & Dimensi: 10,0 kg (Gross) / 8,0 kg (Netto), 298 x 101 x 142 mm (DxWxH)', 2026, 1534000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:35:16', '2026-11-08 00:35:16'),
(31, 1, 9, 1, 1, 'RPL-031', NULL, 'Uninterruptible Power Supply (UPS)\r\nAC Input: Voltage 140 - 300V, Frequency 50 Hz ± 10%\r\nInverter Output: Waveform Synthesized Sinewave, Voltage 230 V ± 10%, Frequency 50 Hz ± 1%\r\nIndicator / Display: LCD Monochrome\r\nKapasitas Baterai: 2 x 7,2Ah, 12V\r\nBerat & Dimensi: 10,0 kg (Gross) / 8,0 kg (Netto), 298 x 101 x 142 mm (DxWxH)', 2026, 1534000.00, 'Bantuan', 'good', NULL, '2026-11-08 00:35:29', '2026-11-08 00:35:29'),
(32, 1, 11, 3, 2, 'TKJ-001', NULL, 'Kabel UTP Belden\r\nCat 5E panjang 305 meter', 2025, 2500000.00, 'BOSP', 'good', NULL, '2026-11-08 00:50:58', '2026-11-08 00:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `item_unit_photos`
--

CREATE TABLE `item_unit_photos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_unit_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `borrower_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `borrower_identifier` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `borrower_department` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `borrower_contact` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `borrowed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `due_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_items`
--

CREATE TABLE `loan_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `item_unit_id` bigint(20) UNSIGNED NOT NULL,
  `active_item_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `condition_out` enum('good','minor_damage','major_damage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes_out` text COLLATE utf8mb4_unicode_ci,
  `returned_at` timestamp NULL DEFAULT NULL,
  `condition_in` enum('good','minor_damage','major_damage') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes_in` text COLLATE utf8mb4_unicode_ci,
  `returned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_11_02_000001_create_institutions_table', 1),
(4, '2026_11_02_000002_create_departments_table', 1),
(5, '2026_11_02_000003_create_categories_table', 1),
(6, '2026_11_02_000004_create_items_table', 1),
(7, '2026_11_02_000005_create_users_table', 1),
(8, '2026_11_03_000001_create_rooms_table', 1),
(9, '2026_11_03_000002_create_item_units_table', 1),
(10, '2026_11_03_000003_create_loans_table', 1),
(11, '2026_11_03_000004_create_loan_items_table', 1),
(12, '2026_11_03_000005_create_item_transfers_table', 1),
(13, '2026_11_03_000006_create_item_transfer_items_table', 1),
(14, '2026_11_03_000007_add_description_to_item_units_table', 2),
(15, '2026_11_08_000001_create_item_unit_photos_table', 3),
(16, '2026_11_08_000002_drop_is_active_from_items_and_item_units', 4),
(17, '2026_11_08_000003_drop_disposal_fields_from_item_units', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `institution_id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, '001', 'Lab RPL', NULL, 1, '2026-11-03 03:23:20', '2026-11-03 03:23:20'),
(2, 1, '002', 'Lab TKJ', NULL, 1, '2026-11-08 00:45:53', '2026-11-08 00:45:53');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('x4jWb62hZO18bzt38EmXKlcL35qirxhRQb9hsea4', 2, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmZ3lxYjRBbTJENU1kdTJPUTJGZ29xTklhaTNTaG1JazJKTEZianZOIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3VzZXJzIiwicm91dGUiOiJ1c2Vycy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwicGFzc3dvcmRfaGFzaF93ZWIiOiJiMjkwYTgyNWNkYjg3ZTVjN2EyNTYzNTFjOWMzMWRmMTQzMTIzYmY1OTIxNzIyMzZjNGFhMzFjZTVkZmM2ZGFkIn0=', 1794487567);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','admin','operator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operator',
  `institution_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `institution_id`, `department_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@inventory.com', 'superadmin', NULL, NULL, '2026-11-03 03:14:57', '$2y$12$Cz3A1BICGwm82n5b.n.syOyLbeBMRMUmmoJvYrBa5NS5w1rQGqIOu', NULL, '2026-11-03 03:14:57', '2026-11-03 03:14:57'),
(2, 'SMKN 1 Kotabaru', 'smkn1@inventory.com', 'admin', 1, NULL, NULL, '$2y$12$MkepPGvxTTB1FYAKDI/LxunTYf2AHaEiA1oQynRbrE9QlD1YvwnJS', NULL, '2026-11-03 03:17:29', '2026-11-07 23:37:24'),
(3, 'Operator RPL', 'rpl@inventory.com', 'operator', 1, 1, NULL, '$2y$12$aERsBrI0NJZof7s8nUsBxuwTLX3zsgXcvjSt1M4oo/TN1cnoPvDka', NULL, '2026-11-03 03:18:24', '2026-11-07 23:37:03'),
(4, 'Operator SMP', 'smpn1@gmail.com', 'operator', 2, 2, NULL, '$2y$12$qyGXvc5.5udMj/ELI7ROL.d8zhvDwhqXvFIxBCxtKpbsgV46rZpMW', NULL, '2026-11-07 18:49:15', '2026-11-07 18:49:15'),
(5, 'Operator TKJ', 'tkj@inventory.com', 'operator', 1, 3, NULL, '$2y$12$fBoea/IrWo6eLfBTSh2UXeIzpI8AmcpsKuLo5WVvzNI0PfeLc5LVC', NULL, '2026-11-07 23:10:27', '2026-11-07 23:37:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_institution_id_name_unique` (`institution_id`,`name`),
  ADD UNIQUE KEY `categories_institution_id_id_unique` (`institution_id`,`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_institution_id_code_unique` (`institution_id`,`code`),
  ADD UNIQUE KEY `departments_institution_id_id_unique` (`institution_id`,`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institutions_code_unique` (`code`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `items_institution_id_code_unique` (`institution_id`,`code`),
  ADD UNIQUE KEY `items_institution_id_id_unique` (`institution_id`,`id`),
  ADD KEY `items_institution_id_category_id_foreign` (`institution_id`,`category_id`);

--
-- Indexes for table `item_transfers`
--
ALTER TABLE `item_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_transfers_institution_id_code_unique` (`institution_id`,`code`),
  ADD UNIQUE KEY `item_transfers_institution_id_id_unique` (`institution_id`,`id`),
  ADD KEY `item_transfers_recorded_by_foreign` (`recorded_by`),
  ADD KEY `item_transfers_approved_by_foreign` (`approved_by`),
  ADD KEY `item_transfers_institution_id_from_department_id_foreign` (`institution_id`,`from_department_id`),
  ADD KEY `item_transfers_institution_id_to_department_id_foreign` (`institution_id`,`to_department_id`),
  ADD KEY `item_transfers_institution_id_from_room_id_foreign` (`institution_id`,`from_room_id`),
  ADD KEY `item_transfers_institution_id_to_room_id_foreign` (`institution_id`,`to_room_id`);

--
-- Indexes for table `item_transfer_items`
--
ALTER TABLE `item_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_transfer_items_item_transfer_id_item_unit_id_unique` (`item_transfer_id`,`item_unit_id`),
  ADD KEY `item_transfer_items_institution_id_item_transfer_id_foreign` (`institution_id`,`item_transfer_id`),
  ADD KEY `item_transfer_items_institution_id_item_unit_id_foreign` (`institution_id`,`item_unit_id`);

--
-- Indexes for table `item_units`
--
ALTER TABLE `item_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_units_institution_id_code_unique` (`institution_id`,`code`),
  ADD UNIQUE KEY `item_units_institution_id_id_unique` (`institution_id`,`id`),
  ADD KEY `item_units_institution_id_department_id_index` (`institution_id`,`department_id`),
  ADD KEY `item_units_institution_id_item_id_foreign` (`institution_id`,`item_id`),
  ADD KEY `item_units_institution_id_room_id_foreign` (`institution_id`,`room_id`);

--
-- Indexes for table `item_unit_photos`
--
ALTER TABLE `item_unit_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_unit_photos_item_unit_id_foreign` (`item_unit_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loans_institution_id_code_unique` (`institution_id`,`code`),
  ADD UNIQUE KEY `loans_institution_id_id_unique` (`institution_id`,`id`),
  ADD KEY `loans_recorded_by_foreign` (`recorded_by`),
  ADD KEY `loans_institution_id_department_id_foreign` (`institution_id`,`department_id`);

--
-- Indexes for table `loan_items`
--
ALTER TABLE `loan_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loan_items_loan_id_item_unit_id_unique` (`loan_id`,`item_unit_id`),
  ADD UNIQUE KEY `loan_items_active_item_unit_id_unique` (`active_item_unit_id`),
  ADD KEY `loan_items_returned_by_foreign` (`returned_by`),
  ADD KEY `loan_items_institution_id_loan_id_foreign` (`institution_id`,`loan_id`),
  ADD KEY `loan_items_institution_id_item_unit_id_foreign` (`institution_id`,`item_unit_id`),
  ADD KEY `loan_items_institution_id_active_item_unit_id_foreign` (`institution_id`,`active_item_unit_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_institution_id_code_unique` (`institution_id`,`code`),
  ADD UNIQUE KEY `rooms_institution_id_id_unique` (`institution_id`,`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_department_id_foreign` (`department_id`),
  ADD KEY `users_institution_id_department_id_index` (`institution_id`,`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `item_transfers`
--
ALTER TABLE `item_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_transfer_items`
--
ALTER TABLE `item_transfer_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_units`
--
ALTER TABLE `item_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `item_unit_photos`
--
ALTER TABLE `item_unit_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_items`
--
ALTER TABLE `loan_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_institution_id_category_id_foreign` FOREIGN KEY (`institution_id`,`category_id`) REFERENCES `categories` (`institution_id`, `id`),
  ADD CONSTRAINT `items_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Constraints for table `item_transfers`
--
ALTER TABLE `item_transfers`
  ADD CONSTRAINT `item_transfers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `item_transfers_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `item_transfers_institution_id_from_department_id_foreign` FOREIGN KEY (`institution_id`,`from_department_id`) REFERENCES `departments` (`institution_id`, `id`),
  ADD CONSTRAINT `item_transfers_institution_id_from_room_id_foreign` FOREIGN KEY (`institution_id`,`from_room_id`) REFERENCES `rooms` (`institution_id`, `id`),
  ADD CONSTRAINT `item_transfers_institution_id_to_department_id_foreign` FOREIGN KEY (`institution_id`,`to_department_id`) REFERENCES `departments` (`institution_id`, `id`),
  ADD CONSTRAINT `item_transfers_institution_id_to_room_id_foreign` FOREIGN KEY (`institution_id`,`to_room_id`) REFERENCES `rooms` (`institution_id`, `id`),
  ADD CONSTRAINT `item_transfers_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `item_transfer_items`
--
ALTER TABLE `item_transfer_items`
  ADD CONSTRAINT `item_transfer_items_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `item_transfer_items_institution_id_item_transfer_id_foreign` FOREIGN KEY (`institution_id`,`item_transfer_id`) REFERENCES `item_transfers` (`institution_id`, `id`),
  ADD CONSTRAINT `item_transfer_items_institution_id_item_unit_id_foreign` FOREIGN KEY (`institution_id`,`item_unit_id`) REFERENCES `item_units` (`institution_id`, `id`);

--
-- Constraints for table `item_units`
--
ALTER TABLE `item_units`
  ADD CONSTRAINT `item_units_institution_id_department_id_foreign` FOREIGN KEY (`institution_id`,`department_id`) REFERENCES `departments` (`institution_id`, `id`),
  ADD CONSTRAINT `item_units_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `item_units_institution_id_item_id_foreign` FOREIGN KEY (`institution_id`,`item_id`) REFERENCES `items` (`institution_id`, `id`),
  ADD CONSTRAINT `item_units_institution_id_room_id_foreign` FOREIGN KEY (`institution_id`,`room_id`) REFERENCES `rooms` (`institution_id`, `id`);

--
-- Constraints for table `item_unit_photos`
--
ALTER TABLE `item_unit_photos`
  ADD CONSTRAINT `item_unit_photos_item_unit_id_foreign` FOREIGN KEY (`item_unit_id`) REFERENCES `item_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_institution_id_department_id_foreign` FOREIGN KEY (`institution_id`,`department_id`) REFERENCES `departments` (`institution_id`, `id`),
  ADD CONSTRAINT `loans_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `loans_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `loan_items`
--
ALTER TABLE `loan_items`
  ADD CONSTRAINT `loan_items_institution_id_active_item_unit_id_foreign` FOREIGN KEY (`institution_id`,`active_item_unit_id`) REFERENCES `item_units` (`institution_id`, `id`),
  ADD CONSTRAINT `loan_items_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `loan_items_institution_id_item_unit_id_foreign` FOREIGN KEY (`institution_id`,`item_unit_id`) REFERENCES `item_units` (`institution_id`, `id`),
  ADD CONSTRAINT `loan_items_institution_id_loan_id_foreign` FOREIGN KEY (`institution_id`,`loan_id`) REFERENCES `loans` (`institution_id`, `id`),
  ADD CONSTRAINT `loan_items_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `users_institution_id_department_id_foreign` FOREIGN KEY (`institution_id`,`department_id`) REFERENCES `departments` (`institution_id`, `id`),
  ADD CONSTRAINT `users_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
