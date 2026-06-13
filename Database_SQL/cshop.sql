-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql207.infinityfree.com
-- Generation Time: Jun 13, 2026 at 02:38 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42052196_cshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(255) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `cart_id`, `product_id`, `quantity`, `added_at`, `status`) VALUES
(1, 41, 50, 1, '2026-06-04 05:56:22', 'active'),
(2, 41, 51, 1, '2026-06-04 05:56:24', 'removed'),
(3, 41, 51, 1, '2026-06-07 03:40:32', 'removed'),
(4, 41, 50, 1, '2026-06-07 04:15:14', 'removed'),
(5, 28, 53, 1, '2026-06-07 04:47:41', 'removed'),
(6, 28, 53, 1, '2026-06-07 04:48:04', 'removed'),
(7, 29, 56, 1, '2026-06-08 01:53:06', 'removed'),
(8, 41, 56, 1, '2026-06-08 01:53:23', 'removed'),
(9, 41, 57, 1, '2026-06-08 01:54:05', 'removed'),
(10, 28, 64, 1, '2026-06-08 02:27:37', 'removed'),
(11, 28, 58, 1, '2026-06-08 02:32:21', 'removed'),
(12, 28, 65, 1, '2026-06-08 02:33:52', 'removed'),
(13, 28, 56, 1, '2026-06-08 02:34:15', 'removed'),
(14, 28, 58, 2, '2026-06-08 02:38:01', 'removed'),
(15, 28, 53, 1, '2026-06-08 02:42:34', 'removed'),
(16, 28, 54, 1, '2026-06-08 02:42:37', 'removed'),
(17, 28, 58, 1, '2026-06-08 02:42:46', 'removed'),
(18, 28, 57, 1, '2026-06-08 02:42:53', 'removed'),
(19, 28, 60, 1, '2026-06-08 02:42:56', 'removed'),
(20, 28, 64, 1, '2026-06-08 02:42:59', 'removed'),
(21, 28, 61, 1, '2026-06-08 02:43:01', 'removed'),
(22, 28, 57, 8, '2026-06-08 03:03:39', 'removed'),
(23, 28, 65, 1, '2026-06-08 03:03:55', 'removed'),
(24, 28, 66, 1, '2026-06-08 06:14:02', 'removed'),
(25, 28, 55, 1, '2026-06-08 06:52:09', 'removed'),
(26, 28, 51, 1, '2026-06-08 14:35:14', 'removed'),
(27, 50, 64, 1, '2026-06-09 01:22:50', 'removed'),
(28, 50, 65, 1, '2026-06-09 01:24:04', 'removed'),
(29, 50, 63, 1, '2026-06-09 01:25:28', 'removed'),
(30, 50, 64, 1, '2026-06-09 01:26:19', 'removed'),
(31, 59, 65, 1, '2026-06-09 02:14:46', 'removed'),
(32, 59, 64, 1, '2026-06-09 02:16:55', 'removed'),
(33, 59, 64, 1, '2026-06-09 02:19:21', 'active'),
(34, 63, 65, 1, '2026-06-09 02:45:49', 'removed'),
(35, 63, 64, 2, '2026-06-09 04:01:19', 'removed'),
(36, 43, 60, 1, '2026-06-10 07:43:21', 'removed'),
(37, 43, 59, 1, '2026-06-10 07:43:25', 'removed'),
(38, 43, 74, 1, '2026-06-10 08:35:25', 'removed'),
(39, 43, 59, 1, '2026-06-10 08:38:09', 'removed'),
(40, 43, 61, 1, '2026-06-10 08:38:12', 'removed'),
(41, 43, 64, 1, '2026-06-10 08:38:14', 'removed'),
(42, 43, 65, 1, '2026-06-10 08:39:07', 'removed'),
(43, 43, 68, 1, '2026-06-10 08:50:22', 'removed'),
(44, 43, 75, 1, '2026-06-10 08:51:37', 'removed'),
(45, 43, 72, 1, '2026-06-10 08:52:51', 'removed'),
(46, 43, 75, 1, '2026-06-10 08:55:53', 'removed'),
(47, 37, 74, 1, '2026-06-10 09:25:35', 'removed'),
(48, 37, 75, 1, '2026-06-10 09:25:37', 'removed'),
(49, 37, 73, 1, '2026-06-10 09:25:40', 'removed'),
(50, 37, 76, 1, '2026-06-11 08:15:37', 'removed'),
(51, 37, 76, 1, '2026-06-11 08:16:58', 'removed'),
(52, 25, 75, 1, '2026-06-11 08:47:05', 'removed'),
(53, 37, 75, 1, '2026-06-11 08:52:25', 'removed'),
(54, 63, 58, 1, '2026-06-11 08:53:40', 'removed'),
(55, 63, 76, 1, '2026-06-11 08:53:45', 'removed'),
(56, 63, 61, 1, '2026-06-11 08:54:24', 'removed'),
(57, 63, 60, 1, '2026-06-11 08:54:30', 'removed'),
(58, 63, 58, 1, '2026-06-11 08:56:02', 'removed'),
(59, 25, 67, 1, '2026-06-11 08:58:53', 'removed'),
(60, 43, 76, 1, '2026-06-11 09:11:13', 'removed'),
(61, 43, 73, 1, '2026-06-11 09:12:31', 'removed');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Nha', 'nhomnha65@gmail.com', 'laptop', 'ážáž¾áž áž¶áž„ážšáž”ážŸáŸ‹áž¢áŸ’áž“áž€áž˜áž¶áž“áž€áž¶ážšáž”áž‰áŸ’áž…áž»áŸ‡ážáž˜áŸ’áž›áŸƒážŠáŸ‚ážšáž‘áŸ', '2026-06-08 02:22:13'),
(2, 'SosyNara', 'nara@gmail.com', 'áž˜áž¶áž“áž”áŸ’ážšáž˜áž¼áž›ážŸáž·áž“áž¢ážáŸ‹', 'áž“áŸ…áž€áŸ’áž“áž»áž„áž áž¶áž„áž”áž„áž˜áž¶áž“áž”áž‰áŸ’áž…áž»áŸ‡ážáž˜áŸ’áž›áŸƒáž€áž»áŸ†áž–áŸ’áž™áž¼áž‘áŸážšáž¢áž¸ážáŸ’áž›áŸ‡áž¢ážáŸ‹', '2026-06-08 02:24:38'),
(3, 'Makara', 'makara542@gmail.com', 'VGA', 'ážáž¾áž¢áŸ’áž“áž€áž˜áž¶áž“áž›áž€áŸ‹ GPU Asus Rog RTX 5090ti 32G ážŠáŸ‚ážšáž‘áŸ?', '2026-06-08 02:26:35'),
(4, 'Nha', 'nhomnha65@gmail.com', 'Jg sur', 'ážáž¾áž áž¶áž„ážšáž”ážŸáŸ‹áž¢áŸ’áž“áž€áž˜áž¶áž“áž€áž¶ážšáž”áž‰áŸ’áž…áž»áŸ‡ážáž˜áŸ’áž›áŸƒážŠáŸ‚ážšáž‘áŸ', '2026-06-08 02:29:38'),
(6, 'ážŸáŸ€áž„áž¢áŸáž„', 'rabombasticccccc@gmail.com', 'laptop', 'laptop 120$', '2026-06-09 02:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(123, 29, 50, '2026-06-04 04:47:57'),
(124, 29, 52, '2026-06-04 04:48:13'),
(125, 29, 51, '2026-06-04 04:48:23'),
(126, 41, 51, '2026-06-04 06:58:32'),
(127, 41, 50, '2026-06-04 06:58:35'),
(128, 50, 64, '2026-06-09 01:27:00'),
(129, 50, 63, '2026-06-09 01:27:22'),
(130, 50, 62, '2026-06-09 01:27:26'),
(131, 50, 65, '2026-06-09 01:27:31'),
(132, 59, 63, '2026-06-09 02:16:15'),
(133, 59, 62, '2026-06-09 02:16:22'),
(134, 59, 64, '2026-06-09 02:16:25'),
(135, 63, 64, '2026-06-09 02:45:14'),
(136, 63, 65, '2026-06-09 02:45:32'),
(137, 63, 63, '2026-06-09 02:45:35'),
(140, 43, 59, '2026-06-10 06:57:59'),
(141, 43, 61, '2026-06-10 06:58:04'),
(142, 43, 56, '2026-06-10 06:58:11'),
(143, 63, 54, '2026-06-10 07:19:26'),
(144, 43, 63, '2026-06-10 07:19:54'),
(145, 43, 54, '2026-06-10 07:19:56'),
(146, 43, 74, '2026-06-10 07:30:47'),
(149, 43, 60, '2026-06-10 07:41:35'),
(150, 43, 57, '2026-06-10 07:41:39'),
(156, 63, 76, '2026-06-11 08:29:32'),
(157, 63, 58, '2026-06-11 08:30:12'),
(158, 63, 74, '2026-06-11 08:39:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `phone`, `address`, `total_amount`, `payment_method`, `status`, `order_date`, `created_at`) VALUES
(3, 28, 'sa', '012345678', 'sombor/kpt', '179.00', 'aba', 'paid', '2026-06-08 02:27:58', '2026-06-08 02:27:58'),
(4, 28, 'AlexBraro', '017641363', 'sombor/prasatsombor/kpt', '3999.00', 'aba', 'paid', '2026-06-08 02:32:56', '2026-06-08 02:32:56'),
(6, 28, 'AlexBraro', '012345678', 'sombor', '7998.00', 'aba', 'paid', '2026-06-08 02:38:20', '2026-06-08 02:38:20'),
(7, 28, 'SosyNara', '017641363', 'sombor/prasatsombor/kpt', '6496.00', 'aba', 'paid', '2026-06-08 02:43:45', '2026-06-08 02:43:45'),
(9, 28, 'SosyNara', '12345678', 'kampong thom', '899.00', 'aba', 'paid', '2026-06-08 06:52:43', '2026-06-08 06:52:43'),
(12, 50, 'Nhaaa', '074273424', 'Prasat Sombour', '60.00', 'aba', 'paid', '2026-06-09 01:24:27', '2026-06-09 01:24:27'),
(13, 50, 'Nhaa', '0882572765', 'Kampong Thom', '599.00', 'aba', 'paid', '2026-06-09 01:25:46', '2026-06-09 01:25:46'),
(14, 50, 'Nha', '0882572765', 'Kampong Thom', '179.00', 'aba', 'paid', '2026-06-09 01:26:37', '2026-06-09 01:26:37'),
(17, 43, 'Makara', '0882572765', 'Kampong Thom', '274.00', 'aba', 'paid', '2026-06-10 07:46:39', '2026-06-10 07:46:39'),
(18, 43, 'Makara', '0634632353', 'Prasat Sambour', '450.00', 'aba', 'paid', '2026-06-10 08:35:39', '2026-06-10 08:35:39'),
(24, 63, 'MKK', '09857467', 'Prasat Sambour', '4403.00', 'aba', 'paid', '2026-06-11 09:03:19', '2026-06-11 09:03:19'),
(25, 43, 'Makara', '0634632353', 'Kampong Thom', '270.00', 'aba', 'paid', '2026-06-11 09:11:34', '2026-06-11 09:11:34'),
(26, 43, 'Nhaaa', '0882572765', 'KPT', '450.00', 'aba', 'paid', '2026-06-11 09:13:16', '2026-06-11 09:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cpu` varchar(100) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `ssd` varchar(50) DEFAULT NULL,
  `gpu` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'in_stock',
  `description` text DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `category` varchar(50) DEFAULT 'Laptop'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `cpu`, `ram`, `ssd`, `gpu`, `price`, `image_main`, `created_at`, `status`, `description`, `qty`, `category`) VALUES
(50, 'Rog 9', 'Ryzen 9', '32GB', '512GB SSD', 'RTX 5090', '100.00', 'prod_6a127bfc547b49.159784451779596284.png', '2026-05-24 04:18:04', 'in_stock', NULL, 89, 'Laptop'),
(51, 'Monitor', 'Ryzen 9', '32GB', '', '35', '100.00', 'prod_6a127c2ba68de4.606420351779596331.png', '2026-05-24 04:18:51', 'in_stock', NULL, 94, 'Desktop'),
(52, 'mouse', '', '', '', '', '0.09', 'prod_6a128a50986b22.020259681779599952.png', '2026-05-24 05:19:12', 'in_stock', '', 10, 'Accessories'),
(53, 'MSI katana', 'i7 12400KF', '16G', '1B', 'RTX 5070ti', '1400.00', 'prod_6a2122eaf21447.055149261780556522.webp', '2026-06-04 07:02:02', 'in_stock', NULL, 22, 'Laptop'),
(54, 'Asus Tuf F15', 'i7 12400KF', '32G', '1B', 'RTX 4060ti', '759.00', 'prod_6a261f039fb837.987761451780883203.png', '2026-06-08 01:46:43', 'in_stock', NULL, 33, 'Laptop'),
(55, 'Asus Tuf A15', 'i9 14400KF', '32G', '2B', 'RTX 5060ti', '899.00', 'prod_6a261f69c84ee2.760337351780883305.png', '2026-06-08 01:48:26', 'in_stock', NULL, 22, 'Laptop'),
(56, 'Keyboard Aula F75', '', '', '', '', '45.00', 'prod_6a261fe26987c9.612340841780883426.webp', '2026-06-08 01:50:26', 'in_stock', NULL, 25, 'Accessories'),
(57, 'Keyboard Aula F65', '', '', '', '', '25.00', 'prod_6a26205101a584.057535631780883537.webp', '2026-06-08 01:52:17', 'in_stock', NULL, 14, 'Accessories'),
(58, 'Vga Rtx 5090', '', '', '', '', '3999.00', 'prod_6a262170521825.656890281780883824.png', '2026-06-08 01:57:04', 'in_stock', NULL, 0, 'Accessories'),
(59, 'Keyboard gravastar', '', '', '', '', '235.00', 'prod_6a2621f8953c25.488714871780883960.jpg', '2026-06-08 01:59:20', 'in_stock', NULL, 4, 'Accessories'),
(60, 'Logitech ', '', '', '', '', '39.00', 'prod_6a26226028e8d6.212539721780884064.webp', '2026-06-08 02:01:03', 'in_stock', NULL, 13, 'Accessories'),
(61, 'Logitech 502', '', '', '', '', '95.00', 'prod_6a26229aad3ea2.682655491780884122.webp', '2026-06-08 02:02:02', 'in_stock', NULL, 20, 'Accessories'),
(62, 'Dell Office ', 'i5 10400', '8', '512', 'Intel G800', '150.00', 'prod_6a26232f7ff215.013046351780884271.png', '2026-06-08 02:04:31', 'in_stock', NULL, 7, 'Desktop'),
(63, 'MSI Thin B15U', 'i5 12400KF', '16G', '512', 'RTX 3050 4g', '599.00', 'prod_6a2623967f0130.229541931780884374.webp', '2026-06-08 02:06:14', 'in_stock', NULL, 13, 'Laptop'),
(64, 'Razer ', '', '', '', '', '179.00', 'prod_6a2623fb9bf933.832839711780884475.jpg', '2026-06-08 02:07:55', 'in_stock', NULL, 14, 'Accessories'),
(65, 'Webcam iiyama 10PRO', '', '', '', '', '60.00', 'prod_6a2625d6007c17.422284601780884950.jpg', '2026-06-08 02:15:49', 'in_stock', NULL, 5, 'Accessories'),
(67, 'Asus Vivobook 14', 'i7 12400KF', '16G', '512', 'RTX 2050 4g', '599.00', 'prod_6a290e96888b89.183516341781075606.png', '2026-06-10 07:13:26', 'in_stock', NULL, 13, 'Laptop'),
(68, 'Acer Aspire', 'i5 12400KF', '16G', '512', 'RTX 2050 4g', '399.00', 'prod_6a290f18e36393.674401511781075736.avif', '2026-06-10 07:15:37', 'in_stock', NULL, 6, 'Laptop'),
(69, 'Lenovo LOQ 15', 'i7 12400KF', '16G', '1B', 'RTX 4060ti', '675.00', 'prod_6a290fdf28e1f0.269407561781075935.jpg', '2026-06-10 07:18:55', 'in_stock', NULL, 12, 'Laptop'),
(70, 'All in one Dell insinde', 'i7 12400KF', '16G', '1B', '', '375.00', 'prod_6a2910da543da0.524267201781076186.webp', '2026-06-10 07:23:06', 'in_stock', NULL, 16, 'Desktop'),
(71, 'All in one HP', 'i7 12400KF', '16G', '1B', '', '325.00', 'prod_6a291116b56554.546823741781076246.avif', '2026-06-10 07:24:06', 'in_stock', NULL, 15, 'Desktop'),
(72, 'All in one Asus', 'i5 12400KF', '16G', '512', '', '450.00', 'prod_6a29117cce2ef7.135729661781076348.png', '2026-06-10 07:25:49', 'in_stock', NULL, 18, 'Desktop'),
(73, 'All in one lenovo', 'i5 12400KF', '16G', '512', '', '450.00', 'prod_6a2911c75458a5.824691771781076423.jpg', '2026-06-10 07:27:03', 'in_stock', NULL, 9, 'Desktop'),
(74, 'All in one Acer', 'i5 12400KF', '16G', '512', '', '450.00', 'prod_6a291277667af8.17505866.png', '2026-06-10 07:27:27', 'in_stock', NULL, 15, 'Desktop'),
(75, 'All in one HP', 'i5 12400KF', '16G', '512', '', '450.00', 'prod_6a291257621785.843578811781076567.webp', '2026-06-10 07:29:27', 'in_stock', NULL, 20, 'Desktop'),
(76, 'Keyboard gravastar', '', '', '', '', '270.00', 'prod_6a292ed5745db2.968347231781083861.jpg', '2026-06-10 09:31:01', 'in_stock', NULL, 12, 'Accessories'),
(77, 'Keyboard Aula F65', '', '', '', '', '270.00', 'prod_6a292ffe1531c4.780029721781084158.webp', '2026-06-10 09:35:58', 'in_stock', 'Color : Black Boy zin', 14, 'Accessories');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `phone_number`, `password`, `google_id`, `role`, `created_at`, `profile_picture`) VALUES
(25, 'webme232024', 'webme232024@gmail.com', 'Web Me', NULL, '', '115038788741811273801', 'admin', '2026-05-29 22:51:49', 'https://lh3.googleusercontent.com/a/ACg8ocIa7jwQ70fx-aHqMkPf_cprLIy_ZJNxGB6tvoxjnrzUrImZsA=s96-c'),
(27, 'ohsea', 'engseang264@gmail.com', NULL, NULL, '$2y$10$9p4dnnuWwxIK5Nra4/yahe6gde560Dqtn6Fvu3M.aRjqU4FUQrnui', NULL, 'user', '2026-05-30 00:12:58', NULL),
(28, 'rabombasticccccc', 'rabombasticccccc@gmail.com', 'Ra BomBastic', NULL, '$2y$10$voaEIJP1y6gcw3qoojt7x.yZDqE/8t.2H4ZE/TKADqQ3K1X6x.am.', '116227929461769221349', 'admin', '2026-05-30 00:55:08', 'https://lh3.googleusercontent.com/a/ACg8ocKE-th3UTQpJMhW-70f0XH90w1YlF_IYkazp7jBx2pLn2jsmoZK=s96-c'),
(37, 'eng', 'abc1122@gmail.com', '', '', '$2y$10$3wYMSDp1uP/0zr69y0WC3OcgOsfGx/7dWa6eMp1mA5HG.UyiRQdH2', '', 'user', '2026-06-02 22:18:03', ''),
(43, 'nhomnha65', 'nhomnha65@gmail.com', 'nhom nha', NULL, '$2y$10$dNPO3VmJ4u9wIf8z1dcBeex1ts//CwYC53AFQXV/ZoXvps4SWw3N.', '111616240601239299709', 'user', '2026-06-08 00:21:00', 'https://lh3.googleusercontent.com/a/ACg8ocJvryDiroGTjtpCQuo_Y7hw6bHfmdHRk7iasRRoichkaA0BSw=s96-c'),
(46, 'R', 'rabombastic@gmail.com', NULL, NULL, '$2y$10$PPh6qp3.O4Gv8doHKdAmbeVvGHnXbpQvIgEAGY.Kc3kYs.HywRaG2', NULL, 'user', '2026-06-08 01:22:49', NULL),
(47, 'Nhom', 'nhom65@gmail.com', NULL, NULL, '$2y$10$Uh5.WrAUCwKROgXfCvhYiOo2qEmQnuCWMHkBE6FvVCHh6hHHa7B42', NULL, 'user', '2026-06-08 01:43:02', NULL),
(48, 'bb', 'bb@gmail.com', NULL, NULL, '$2y$10$Z/FqaEIIiBKAudInGqBGWuznUGd3Tl1ThWvpGyvmecAzAsnNnDKEe', NULL, 'user', '2026-06-08 01:43:16', NULL),
(49, 'bbb', 'bbb@gmail.com', NULL, NULL, '$2y$10$6s.5Y0kgLT0EKl7gEC0/U.4snZOWKJraLVJzDeuen6hDkaeOHPwR.', NULL, 'user', '2026-06-08 01:44:37', NULL),
(50, 'Nhaaa', 'nhaa65@gmail.com', NULL, NULL, '$2y$10$Y2.ygiOkPjtHU4zHaa1bGur.KfpH6yfJb301KAL5TXqKcIWWc0liy', NULL, 'user', '2026-06-08 18:22:30', NULL),
(56, 'seangbrai', 'nara41@gmail.com', NULL, NULL, '$2y$10$FfF5R/C3AEbJqoskkCp9lOjE.thNMZugRoXKQzAMAC8YDAbS5iWvO', NULL, 'user', '2026-06-08 18:46:12', NULL),
(63, 'sraa9923', 'sraa9923@gmail.com', 'Makara', NULL, '', '108296107267123142779', 'admin', '2026-06-08 19:40:26', 'https://lh3.googleusercontent.com/a/ACg8ocJkcoPOT9z2v74TjGdgW0Y0Y8R4GTEY9RaKAQjo-EGAjqvcGA=s96-c'),
(74, 'eng02', 'webme2324@gmail.com', NULL, NULL, '$2y$10$iAVSKilnMgALyRmbrY98Felfmhbj8WRZNsTGVTocY.XVb7fggt.DO', NULL, 'user', '2026-06-10 01:23:14', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_fk` (`cart_id`),
  ADD KEY `product_id_fk` (`product_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
