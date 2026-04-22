-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 22, 2026 at 05:29 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ikn_cococoir`
--

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahan_baku`
--

INSERT INTO `bahan_baku` (`id`, `nama`, `satuan`, `created_at`, `updated_at`) VALUES
(1, 'cocopeatdsda', 'Kg', '2026-04-20 07:22:15', '2026-04-20 07:31:04'),
(2, 'tepung', 'Kg', '2026-04-22 00:15:42', '2026-04-22 00:15:42'),
(3, 'Lem', 'Gram', '2026-04-22 00:15:54', '2026-04-22 00:15:54'),
(4, 'Lem', 'Gram', '2026-04-22 00:43:54', '2026-04-22 00:43:54'),
(5, 'sasa', 'Kg', '2026-04-22 08:56:45', '2026-04-22 08:56:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `nama`, `telp`, `alamat`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'dsd', '23121', 'fsf', 'fs', '2026-04-20 07:41:57', '2026-04-20 07:41:57'),
(2, 'sasa', '08756755', 'gata', 'fas', '2026-04-22 05:34:14', '2026-04-22 05:34:14');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` bigint UNSIGNED NOT NULL,
  `penjualan_id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `status_bayar` enum('lunas','belum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `penjualan_id`, `nomor`, `tanggal`, `total`, `status_bayar`, `created_at`, `updated_at`) VALUES
(1, 6, 'INV-202604226', '2026-04-22', '10000.00', 'lunas', '2026-04-22 07:08:45', '2026-04-22 07:08:45'),
(2, 7, 'INV-202604227', '2026-04-22', '10000.00', 'lunas', '2026-04-22 07:38:39', '2026-04-22 07:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas_harian`
--

CREATE TABLE `kas_harian` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kas_harian`
--

INSERT INTO `kas_harian` (`id`, `tanggal`, `jenis`, `nominal`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, '2026-04-22', 'masuk', '10000.00', 'l', '2026-04-22 07:52:49', '2026-04-22 07:52:49');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `aktivitas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_18_123434_create_suppliers_table', 1),
(5, '2026_04_18_123643_create_bahan_baku_table', 1),
(6, '2026_04_18_123752_create_stok_bahan_table', 1),
(7, '2026_04_18_123828_create_pembelian_table', 1),
(8, '2026_04_18_123923_create_pembelian_detail_table', 1),
(9, '2026_04_18_124019_create_produk_table', 1),
(10, '2026_04_18_124159_create_stok_produk_table', 1),
(11, '2026_04_18_124231_create_produksi_table', 1),
(12, '2026_04_18_124256_create_produksi_detail_table', 1),
(13, '2026_04_18_124436_create_clients_table', 1),
(14, '2026_04_18_124606_create_penjualan_table', 1),
(15, '2026_04_18_124636_create_penjualan_detail_table', 1),
(16, '2026_04_18_124705_create_surat_jalan_table', 1),
(17, '2026_04_18_124804_create_invoice_table', 1),
(18, '2026_04_18_124926_create_rekening_table', 1),
(19, '2026_04_18_125009_create_transaksi_keuangan_table', 1),
(20, '2026_04_18_125040_create_kas_harian_table', 1),
(21, '2026_04_18_125129_create_log_aktivitas_table', 1);

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
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` enum('lunas','belum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `rekening_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id`, `supplier_id`, `tanggal`, `total`, `status_pembayaran`, `rekening_id`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-04-21', '40000.00', 'belum', NULL, '2026-04-21 05:34:07', '2026-04-21 05:34:07'),
(2, 2, '2026-04-21', '3000.00', 'lunas', NULL, '2026-04-21 05:55:17', '2026-04-21 07:06:22'),
(10, 2, '2026-04-21', '500.00', 'lunas', NULL, '2026-04-21 06:40:22', '2026-04-21 06:40:22'),
(11, 2, '2026-04-21', '1000.00', 'lunas', NULL, '2026-04-21 06:41:27', '2026-04-21 06:41:28'),
(12, 2, '2026-04-22', '20000.00', 'lunas', NULL, '2026-04-22 00:16:28', '2026-04-22 00:16:28'),
(13, 2, '2026-04-22', '41988.00', 'lunas', NULL, '2026-04-22 00:44:26', '2026-04-22 00:44:26');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_detail`
--

CREATE TABLE `pembelian_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `pembelian_id` bigint UNSIGNED NOT NULL,
  `bahan_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembelian_detail`
--

INSERT INTO `pembelian_detail` (`id`, `pembelian_id`, `bahan_id`, `qty`, `harga`, `subtotal`) VALUES
(1, 1, 1, '4.00', '10000.00', '40000.00'),
(2, 2, 1, '1.00', '3000.00', '3000.00'),
(10, 10, 1, '1.00', '500.00', '500.00'),
(11, 11, 1, '1.00', '1000.00', '1000.00'),
(12, 12, 2, '10.00', '1000.00', '10000.00'),
(13, 12, 3, '10.00', '1000.00', '10000.00'),
(14, 13, 1, '12.00', '1000.00', '12000.00'),
(15, 13, 4, '10.00', '999.00', '9990.00'),
(16, 13, 2, '1.00', '9999.00', '9999.00'),
(17, 13, 3, '1.00', '9999.00', '9999.00');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal` date NOT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('berhasil','cancel','return') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'berhasil',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `client_id`, `tanggal`, `total`, `status`, `created_at`, `updated_at`) VALUES
(2, NULL, '2026-04-22', '10000.00', 'berhasil', '2026-04-22 06:11:19', '2026-04-22 06:11:19'),
(3, NULL, '2026-04-22', '9000.00', 'berhasil', '2026-04-22 06:11:51', '2026-04-22 06:11:51'),
(4, NULL, '2026-04-22', '20000.00', 'return', '2026-04-22 06:24:44', '2026-04-22 06:45:13'),
(5, NULL, '2026-04-22', '10000.00', 'cancel', '2026-04-22 06:38:06', '2026-04-22 06:44:43'),
(6, NULL, '2026-04-22', '10000.00', 'berhasil', '2026-04-22 07:08:45', '2026-04-22 07:08:45'),
(7, 2, '2026-04-22', '10000.00', 'return', '2026-04-22 07:38:39', '2026-04-22 07:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_detail`
--

CREATE TABLE `penjualan_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `penjualan_id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjualan_detail`
--

INSERT INTO `penjualan_detail` (`id`, `penjualan_id`, `produk_id`, `qty`, `harga`, `subtotal`) VALUES
(2, 2, 1, '1.00', '10000.00', '10000.00'),
(3, 3, 1, '1.00', '9000.00', '9000.00'),
(4, 4, 2, '1.00', '10000.00', '10000.00'),
(5, 4, 1, '1.00', '10000.00', '10000.00'),
(6, 5, 1, '1.00', '10000.00', '10000.00'),
(7, 6, 2, '1.00', '10000.00', '10000.00'),
(8, 7, 1, '1.00', '10000.00', '10000.00');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `harga_default` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `satuan`, `created_at`, `updated_at`, `harga_default`) VALUES
(1, 'cocopeat', 'Pcs', '2026-04-20 07:30:50', '2026-04-22 05:58:34', '10000.00'),
(2, 'dasda', 'Pcs', '2026-04-22 06:23:44', '2026-04-22 06:23:44', '10000.00'),
(3, 'kertas', 'Pcs', '2026-04-22 09:16:36', '2026-04-22 09:16:36', '10000.00');

-- --------------------------------------------------------

--
-- Table structure for table `produksi`
--

CREATE TABLE `produksi` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('proses','berhasil','reject') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'proses',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produksi`
--

INSERT INTO `produksi` (`id`, `tanggal`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(6, '2026-04-22', 'berhasil', NULL, '2026-04-22 01:01:27', '2026-04-22 01:01:27'),
(7, '2026-04-22', 'berhasil', 'Berhasil diperbaiki (Repair)', '2026-04-22 01:33:30', '2026-04-22 01:55:47'),
(8, '2026-04-22', 'berhasil', NULL, '2026-04-22 01:36:25', '2026-04-22 01:38:39'),
(9, '2026-04-22', 'berhasil', NULL, '2026-04-22 01:37:47', '2026-04-22 01:37:47'),
(10, '2026-04-22', 'berhasil', NULL, '2026-04-22 01:40:48', '2026-04-22 01:40:48'),
(11, '2026-04-22', 'berhasil', NULL, '2026-04-22 01:43:39', '2026-04-22 01:43:39'),
(12, '2026-04-22', 'proses', NULL, '2026-04-22 01:49:53', '2026-04-22 01:49:53'),
(13, '2026-04-22', 'berhasil', 'kurang', '2026-04-22 01:56:59', '2026-04-22 02:12:17'),
(14, '2026-04-22', 'reject', 'Dibatalkan melalui halaman detail.', '2026-04-22 05:00:25', '2026-04-22 05:00:34'),
(15, '2026-04-22', 'berhasil', NULL, '2026-04-22 06:24:34', '2026-04-22 06:24:34'),
(16, '2026-04-22', 'berhasil', NULL, '2026-04-22 09:51:14', '2026-04-22 09:51:14');

-- --------------------------------------------------------

--
-- Table structure for table `produksi_detail`
--

CREATE TABLE `produksi_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `produksi_id` bigint UNSIGNED NOT NULL,
  `jenis` enum('bahan','produk') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produksi_detail`
--

INSERT INTO `produksi_detail` (`id`, `produksi_id`, `jenis`, `item_id`, `qty`) VALUES
(6, 6, 'bahan', 4, '1.00'),
(7, 6, 'produk', 1, '1.00'),
(8, 7, 'bahan', 3, '1.00'),
(9, 7, 'produk', 1, '1.00'),
(10, 8, 'bahan', 1, '1.00'),
(11, 8, 'produk', 1, '1.00'),
(12, 9, 'bahan', 2, '1.00'),
(13, 9, 'produk', 1, '1.00'),
(14, 10, 'bahan', 1, '1.00'),
(15, 10, 'produk', 1, '1.00'),
(16, 11, 'bahan', 1, '1.00'),
(17, 11, 'bahan', 2, '1.00'),
(18, 11, 'produk', 1, '1.00'),
(19, 12, 'bahan', 1, '2.00'),
(20, 12, 'produk', 1, '1.00'),
(21, 7, 'bahan', 3, '1.00'),
(22, 13, 'bahan', 1, '1.00'),
(23, 13, 'produk', 1, '1.00'),
(24, 13, 'bahan', 3, '1.00'),
(25, 13, 'bahan', 2, '0.99'),
(26, 14, 'bahan', 1, '1.00'),
(27, 14, 'produk', 1, '1.00'),
(28, 15, 'bahan', 3, '1.00'),
(29, 15, 'produk', 2, '4.00'),
(30, 16, 'bahan', 3, '2.00'),
(31, 16, 'bahan', 2, '2.00'),
(32, 16, 'produk', 3, '2.00');

-- --------------------------------------------------------

--
-- Table structure for table `rekening`
--

CREATE TABLE `rekening` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('kas','bank') COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo_awal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo_saat_ini` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekening`
--

INSERT INTO `rekening` (`id`, `nama`, `jenis`, `saldo_awal`, `saldo_saat_ini`, `created_at`, `updated_at`) VALUES
(1, 'kBCA', 'bank', '100000.00', '33512.00', '2026-04-20 07:47:52', '2026-04-22 00:44:26');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('pVWt691hqoLN0g5wBXcpRsVSK5d27faTRrRxU3fx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJvdDlWUkRtUUdZMWs4a0s3Wjh0anhaUDNhZ1lBT3V5UXloWTBwVUIwIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==', 1776878950);

-- --------------------------------------------------------

--
-- Table structure for table `stok_bahan`
--

CREATE TABLE `stok_bahan` (
  `id` bigint UNSIGNED NOT NULL,
  `bahan_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(12,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stok_bahan`
--

INSERT INTO `stok_bahan` (`id`, `bahan_id`, `jumlah`) VALUES
(1, 1, '12.00'),
(2, 2, '6.00'),
(3, 3, '5.00'),
(4, 4, '9.00'),
(5, 5, '1.00');

-- --------------------------------------------------------

--
-- Table structure for table `stok_produk`
--

CREATE TABLE `stok_produk` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(12,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stok_produk`
--

INSERT INTO `stok_produk` (`id`, `produk_id`, `jumlah`) VALUES
(1, 1, '3.00'),
(2, 2, '3.00'),
(3, 3, '3.00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `nama`, `telp`, `alamat`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'PT Abadi', '0812679181', 'dimana aja', 'iya terang', '2026-04-21 04:54:55', '2026-04-21 04:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan`
--

CREATE TABLE `surat_jalan` (
  `id` bigint UNSIGNED NOT NULL,
  `penjualan_id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `status_kirim` enum('pending','dikirim','diterima') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_jalan`
--

INSERT INTO `surat_jalan` (`id`, `penjualan_id`, `nomor`, `tanggal`, `status_kirim`, `created_at`, `updated_at`) VALUES
(1, 6, 'SJ-202604226', '2026-04-22', 'diterima', '2026-04-22 07:08:45', '2026-04-22 07:08:45'),
(2, 7, 'SJ-202604227', '2026-04-22', 'diterima', '2026-04-22 07:38:39', '2026-04-22 07:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_keuangan`
--

CREATE TABLE `transaksi_keuangan` (
  `id` bigint UNSIGNED NOT NULL,
  `rekening_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi_keuangan`
--

INSERT INTO `transaksi_keuangan` (`id`, `rekening_id`, `tanggal`, `jenis`, `sumber`, `nominal`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-21', 'keluar', 'pembelian_bahan', '500.00', 'Pembelian bahan baku #PB-00010', '2026-04-21 06:40:22', '2026-04-21 06:40:22'),
(2, 1, '2026-04-21', 'keluar', 'pembelian_bahan', '1000.00', 'Pembelian bahan baku #PB-00011', '2026-04-21 06:41:28', '2026-04-21 06:41:28'),
(3, 1, '2026-04-21', 'keluar', 'pembelian_bahan', '3000.00', 'Pembelian bahan baku #PB-00002', '2026-04-21 07:06:22', '2026-04-21 07:06:22'),
(4, 1, '2026-04-22', 'keluar', 'pembelian_bahan', '20000.00', 'Pembelian bahan baku #PB-00012', '2026-04-22 00:16:28', '2026-04-22 00:16:28'),
(5, 1, '2026-04-22', 'keluar', 'pembelian_bahan', '41988.00', 'Pembelian bahan baku #PB-00013', '2026-04-22 00:44:26', '2026-04-22 00:44:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$.io24KJHaSssu.BMfJ/rmuVeMKFoJcmDtjkgH1O5yB4vmO2JZfFv6', 'admin', NULL, '2026-04-20 07:08:46', '2026-04-20 07:08:46'),
(2, 'dss', 'admin1@gmail.com', NULL, '$2y$12$Ug2a.sw08Unee8hVwLKNFu1if7HaQksrGFdgqUjcEKieyNQCSKA0W', 'admin', NULL, '2026-04-22 10:28:03', '2026-04-22 10:28:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_nomor_unique` (`nomor`),
  ADD KEY `invoice_penjualan_id_foreign` (`penjualan_id`);

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
-- Indexes for table `kas_harian`
--
ALTER TABLE `kas_harian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_aktivitas_user_id_foreign` (`user_id`);

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
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_supplier_id_foreign` (`supplier_id`),
  ADD KEY `rekening_id` (`rekening_id`);

--
-- Indexes for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_detail_pembelian_id_foreign` (`pembelian_id`),
  ADD KEY `pembelian_detail_bahan_id_foreign` (`bahan_id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_client_id_foreign` (`client_id`);

--
-- Indexes for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_detail_penjualan_id_foreign` (`penjualan_id`),
  ADD KEY `penjualan_detail_produk_id_foreign` (`produk_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksi_detail`
--
ALTER TABLE `produksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produksi_detail_produksi_id_foreign` (`produksi_id`);

--
-- Indexes for table `rekening`
--
ALTER TABLE `rekening`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_bahan_bahan_id_foreign` (`bahan_id`);

--
-- Indexes for table `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_produk_produk_id_foreign` (`produk_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surat_jalan_nomor_unique` (`nomor`),
  ADD KEY `surat_jalan_penjualan_id_foreign` (`penjualan_id`);

--
-- Indexes for table `transaksi_keuangan`
--
ALTER TABLE `transaksi_keuangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_keuangan_rekening_id_foreign` (`rekening_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas_harian`
--
ALTER TABLE `kas_harian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produksi`
--
ALTER TABLE `produksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `produksi_detail`
--
ALTER TABLE `produksi_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `rekening`
--
ALTER TABLE `rekening`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stok_produk`
--
ALTER TABLE `stok_produk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi_keuangan`
--
ALTER TABLE `transaksi_keuangan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`rekening_id`) REFERENCES `rekening` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `pembelian_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD CONSTRAINT `pembelian_detail_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembelian_detail_pembelian_id_foreign` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  ADD CONSTRAINT `penjualan_detail_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penjualan_detail_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produksi_detail`
--
ALTER TABLE `produksi_detail`
  ADD CONSTRAINT `produksi_detail_produksi_id_foreign` FOREIGN KEY (`produksi_id`) REFERENCES `produksi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD CONSTRAINT `stok_bahan_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD CONSTRAINT `stok_produk_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD CONSTRAINT `surat_jalan_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_keuangan`
--
ALTER TABLE `transaksi_keuangan`
  ADD CONSTRAINT `transaksi_keuangan_rekening_id_foreign` FOREIGN KEY (`rekening_id`) REFERENCES `rekening` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
