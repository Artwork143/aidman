-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 03:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aidman-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `weight` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `name`, `weight`) VALUES
(1, 'Damage Severity', 0.40),
(2, 'Number of Occupants', 0.20),
(3, 'Vulnerability', 0.20),
(4, 'Income Level', 0.10),
(5, 'Special Needs', 0.10);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `image_path`) VALUES
(61, 'uploads/670f5259e998e.jpg'),
(64, 'uploads/670f53cea1890.jpg'),
(65, 'uploads/670f56186db81.jpg'),
(66, 'uploads/670f56237aaf5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `initial_quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `expiry_date` date NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `threshold_quantity` int(11) GENERATED ALWAYS AS (`initial_quantity` * 0.4) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `quantity`, `initial_quantity`, `unit`, `expiry_date`, `image_path`) VALUES
(8, 'Rice', 30, 100, 'kg', '2025-01-01', 'uploads/RICES.jpg'),
(10, 'Sardines Can', 93, 100, 'piece/s', '2025-01-01', 'uploads/Sardines.jpg'),
(16, 'Lucky Me Noodles', 93, 100, 'piece/s', '2024-01-01', 'uploads/noodles.png'),
(18, 'Pancit Canton', 92, 100, 'piece/s', '2025-01-01', 'uploads/pancit cantoon.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `damage_severity` int(11) NOT NULL,
  `number_of_occupants` int(11) NOT NULL,
  `vulnerability` int(11) NOT NULL,
  `income_level` int(11) NOT NULL,
  `special_needs` int(11) NOT NULL,
  `total_score` decimal(5,2) NOT NULL,
  `distribution_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `name`, `damage_severity`, `number_of_occupants`, `vulnerability`, `income_level`, `special_needs`, `total_score`, `distribution_date`) VALUES
(77, 'Neil Rechie Consolacion', 10, 5, 5, 6, 4, 7.00, NULL),
(78, 'Patricia Mae Rosales', 8, 3, 5, 4, 6, 5.80, NULL),
(79, 'John Paul Asoy', 5, 8, 8, 5, 5, 6.20, NULL),
(80, 'Janice Sitchon', 5, 6, 7, 5, 5, 5.60, NULL),
(113, 'Lea Canete', 3, 8, 5, 4, 5, 4.70, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_assistance`
--

CREATE TABLE `scheduled_assistance` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `pickup_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('for pickup','received') DEFAULT 'for pickup',
  `notification_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduled_assistance`
--

INSERT INTO `scheduled_assistance` (`id`, `resident_id`, `pickup_date`, `created_at`, `status`, `notification_message`) VALUES
(1, 75, '2024-11-16', '2024-11-16 00:43:51', 'for pickup', NULL),
(2, 75, '2024-11-17', '2024-11-16 00:44:05', 'for pickup', NULL),
(3, 75, '2024-11-16', '2024-11-16 00:45:46', 'for pickup', NULL),
(4, 75, '2024-11-17', '2024-11-16 00:46:18', 'for pickup', NULL),
(5, 80, '2024-11-16', '2024-11-16 00:51:16', 'received', NULL),
(6, 75, '2024-11-17', '2024-11-16 00:51:54', 'for pickup', NULL),
(7, 80, '2024-11-16', '2024-11-16 00:52:15', 'for pickup', NULL),
(8, 80, '2024-11-16', '2024-11-16 03:34:05', 'for pickup', NULL),
(9, 80, '2024-11-16', '2024-11-16 03:56:54', 'for pickup', NULL),
(10, 77, '2024-11-17', '2024-11-16 04:01:40', 'for pickup', NULL),
(11, 80, '2024-11-16', '2024-11-16 08:09:49', 'for pickup', NULL),
(12, 80, '2024-11-16', '2024-11-16 11:18:30', 'for pickup', NULL),
(13, 80, '2024-11-17', '2024-11-16 11:21:39', 'for pickup', NULL),
(14, 80, '2024-11-18', '2024-11-16 11:23:30', 'for pickup', NULL),
(15, 75, '2024-11-15', '2024-11-16 11:26:06', 'for pickup', NULL),
(16, 80, '2024-11-20', '2024-11-16 11:26:43', 'for pickup', NULL),
(17, 80, '2024-11-21', '2024-11-16 12:30:53', 'for pickup', NULL),
(18, 75, '2024-11-23', '2024-11-16 12:39:34', 'for pickup', NULL),
(19, 80, '2024-11-30', '2024-11-16 12:40:16', 'for pickup', NULL),
(20, 80, '2024-11-30', '2024-11-16 12:55:44', 'for pickup', NULL),
(21, 80, '2024-12-01', '2024-11-16 12:56:50', 'for pickup', NULL),
(22, 80, '2024-11-16', '2024-11-16 13:00:26', 'for pickup', NULL),
(23, 80, '2024-11-17', '2024-11-16 13:14:51', 'for pickup', NULL),
(24, 80, '2024-11-19', '2024-11-16 13:23:15', 'for pickup', NULL),
(25, 80, '2024-11-20', '2024-11-16 13:27:07', 'for pickup', NULL),
(26, 80, '2024-11-21', '2024-11-16 13:33:29', 'for pickup', NULL),
(27, 80, '2024-11-22', '2024-11-16 14:02:17', 'for pickup', NULL),
(28, 81, '2024-11-20', '2024-11-16 17:16:59', 'received', NULL),
(29, 81, '2024-11-20', '2024-11-17 00:31:22', 'received', NULL),
(30, 80, '2024-12-01', '2024-11-17 00:48:22', 'for pickup', 'Your assistance is scheduled for pickup on 2024-12-01.'),
(31, 80, '2024-11-17', '2024-11-17 01:20:02', 'for pickup', 'Your assistance is scheduled for pickup on 2024-11-17.'),
(32, 81, '2024-11-20', '2024-11-17 01:28:15', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(33, 80, '2024-11-19', '2024-11-17 01:43:33', 'for pickup', 'Your assistance is scheduled for pickup on 2024-11-19.'),
(34, 81, '2024-11-20', '2024-11-17 01:43:49', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(35, 81, '2024-11-20', '2024-11-17 02:56:14', 'received', 'Your assistance is scheduled for pickup on 2024-11-23.'),
(36, 81, '2024-11-20', '2024-11-17 03:38:34', 'received', 'Your assistance is scheduled for pickup on 2024-12-02.'),
(37, 81, '2024-11-20', '2024-11-17 03:44:14', 'received', 'Your assistance is scheduled for pickup on 2024-12-10.'),
(38, 80, '2024-12-28', '2024-11-17 04:02:56', 'for pickup', 'Your assistance is scheduled for pickup on 2024-12-28.'),
(39, 81, '2024-11-20', '2024-11-17 04:03:22', 'received', 'Your assistance is scheduled for pickup on 2024-12-31.'),
(40, 81, '2024-11-20', '2024-11-17 04:09:35', 'received', 'Your assistance is scheduled for pickup on 2025-01-01.'),
(41, 81, '2024-11-20', '2024-11-17 04:13:11', 'received', 'Your assistance is scheduled for pickup on 2024-11-17.'),
(42, 81, '2024-11-20', '2024-11-17 04:54:19', 'received', 'Your assistance is scheduled for pickup on 2024-11-17.'),
(43, 81, '2024-11-20', '2024-11-17 04:55:10', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(44, 81, '2024-11-20', '2024-11-17 04:56:49', 'received', 'Your assistance is scheduled for pickup on 2024-11-17.'),
(45, 81, '2024-11-20', '2024-11-17 05:35:57', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(46, 81, '2024-11-20', '2024-11-17 05:45:17', 'received', 'Your assistance is scheduled for pickup on 2024-11-21.'),
(47, 81, '2024-11-20', '2024-11-17 05:47:43', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(48, 81, '2024-11-20', '2024-11-17 05:48:11', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(49, 81, '2024-11-20', '2024-11-17 05:48:25', 'received', 'Your assistance is scheduled for pickup on 2024-11-21.'),
(50, 81, '2024-11-20', '2024-11-17 05:49:55', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(51, 81, '2024-11-20', '2024-11-17 06:07:27', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(52, 81, '2024-11-20', '2024-11-17 06:07:54', 'received', 'Your assistance is scheduled for pickup on 2024-11-21.'),
(53, 81, '2024-11-20', '2024-11-17 07:28:33', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(54, 81, '2024-11-20', '2024-11-17 13:47:04', 'received', 'Your assistance is scheduled for pickup on 2024-11-17.'),
(55, 81, '2024-11-18', '2024-11-17 14:36:35', 'received', 'Your assistance is scheduled for pickup on 2024-11-18.'),
(56, 81, '2025-12-31', '2024-11-17 14:45:20', 'received', 'Your assistance is scheduled for pickup on 2025-12-31.'),
(57, 82, '2024-01-01', '2024-11-17 14:47:29', 'for pickup', 'Your assistance is scheduled for pickup on 2024-01-01.'),
(58, 81, '2025-01-01', '2024-11-17 14:48:01', 'received', 'Your assistance is scheduled for pickup on 2025-01-01.'),
(59, 81, '2025-01-01', '2024-11-17 14:51:59', 'received', 'Your assistance is scheduled for pickup on 2025-01-01.'),
(60, 81, '2025-01-01', '2024-11-17 14:54:07', 'received', 'Your assistance is scheduled for pickup on 2025-01-01.');

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_assistance_items`
--

CREATE TABLE `scheduled_assistance_items` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduled_assistance_items`
--

INSERT INTO `scheduled_assistance_items` (`id`, `schedule_id`, `item_id`, `quantity`) VALUES
(1, 3, 8, 1),
(2, 3, 10, 1),
(3, 3, 16, 1),
(4, 3, 18, 1),
(5, 4, 8, 1),
(6, 4, 10, 1),
(7, 4, 16, 1),
(8, 4, 18, 1),
(9, 5, 8, 1),
(10, 5, 10, 1),
(11, 5, 16, 1),
(12, 5, 18, 1),
(13, 6, 8, 1),
(14, 6, 10, 1),
(15, 6, 16, 1),
(16, 6, 18, 1),
(17, 7, 8, 1),
(18, 7, 10, 1),
(19, 7, 16, 1),
(20, 7, 18, 1),
(21, 8, 8, 1),
(22, 8, 10, 1),
(23, 8, 16, 1),
(24, 8, 18, 1),
(25, 9, 8, 1),
(26, 9, 10, 1),
(27, 9, 16, 1),
(28, 9, 18, 1),
(29, 10, 8, 1),
(30, 10, 10, 1),
(31, 10, 16, 1),
(32, 10, 18, 1),
(33, 11, 8, 1),
(34, 11, 10, 1),
(35, 11, 16, 1),
(36, 11, 18, 1),
(37, 12, 8, 1),
(38, 12, 10, 1),
(39, 12, 16, 1),
(40, 12, 18, 1),
(41, 13, 8, 1),
(42, 13, 10, 1),
(43, 13, 16, 1),
(44, 13, 18, 1),
(45, 14, 8, 1),
(46, 14, 10, 1),
(47, 14, 16, 1),
(48, 14, 18, 1),
(49, 15, 8, 1),
(50, 15, 10, 1),
(51, 15, 16, 1),
(52, 15, 18, 1),
(53, 16, 8, 1),
(54, 16, 10, 1),
(55, 16, 16, 1),
(56, 16, 18, 1),
(57, 17, 8, 1),
(58, 17, 10, 1),
(59, 17, 16, 1),
(60, 17, 18, 1),
(61, 18, 8, 1),
(62, 18, 10, 1),
(63, 18, 16, 1),
(64, 19, 8, 1),
(65, 19, 10, 1),
(66, 19, 16, 1),
(67, 19, 18, 1),
(68, 20, 8, 1),
(69, 20, 10, 1),
(70, 20, 16, 1),
(71, 20, 18, 1),
(72, 21, 8, 1),
(73, 21, 10, 1),
(74, 21, 16, 1),
(75, 21, 18, 1),
(76, 22, 8, 1),
(77, 22, 10, 1),
(78, 22, 16, 1),
(79, 22, 18, 1),
(80, 23, 8, 1),
(81, 23, 10, 1),
(82, 23, 16, 1),
(83, 23, 18, 1),
(84, 24, 8, 1),
(85, 24, 10, 1),
(86, 24, 16, 1),
(87, 24, 18, 1),
(88, 25, 8, 1),
(89, 25, 10, 1),
(90, 25, 16, 1),
(91, 25, 18, 1),
(92, 26, 8, 1),
(93, 26, 10, 1),
(94, 26, 16, 1),
(95, 26, 18, 1),
(96, 27, 8, 1),
(97, 27, 10, 1),
(98, 27, 16, 1),
(99, 27, 18, 1),
(100, 28, 8, 1),
(101, 28, 10, 1),
(102, 28, 16, 1),
(103, 28, 18, 1),
(104, 29, 8, 1),
(105, 29, 10, 1),
(106, 29, 16, 1),
(107, 29, 18, 1),
(108, 30, 8, 1),
(109, 30, 10, 1),
(110, 30, 16, 1),
(111, 30, 18, 1),
(112, 31, 8, 1),
(113, 31, 10, 1),
(114, 31, 16, 1),
(115, 31, 18, 1),
(116, 32, 8, 1),
(117, 32, 10, 1),
(118, 32, 16, 1),
(119, 32, 18, 1),
(120, 33, 8, 1),
(121, 33, 10, 1),
(122, 33, 16, 1),
(123, 33, 18, 1),
(124, 34, 8, 1),
(125, 34, 10, 1),
(126, 34, 16, 1),
(127, 34, 18, 1),
(128, 35, 8, 2),
(129, 35, 10, 2),
(130, 35, 16, 2),
(131, 35, 18, 2),
(132, 36, 8, 1),
(133, 36, 10, 1),
(134, 36, 16, 1),
(135, 36, 18, 1),
(136, 37, 8, 1),
(137, 37, 10, 1),
(138, 37, 16, 1),
(139, 37, 18, 1),
(140, 38, 8, 2),
(141, 38, 10, 2),
(142, 38, 16, 2),
(143, 38, 18, 2),
(144, 39, 8, 2),
(145, 39, 10, 2),
(146, 39, 16, 2),
(147, 39, 18, 2),
(148, 40, 8, 2),
(149, 40, 10, 2),
(150, 40, 16, 2),
(151, 40, 18, 2),
(152, 41, 8, 2),
(153, 41, 10, 2),
(154, 41, 16, 2),
(155, 41, 18, 2),
(156, 42, 8, 1),
(157, 42, 10, 1),
(158, 42, 16, 1),
(159, 42, 18, 1),
(160, 43, 8, 1),
(161, 43, 10, 1),
(162, 43, 16, 1),
(163, 43, 18, 1),
(164, 44, 8, 1),
(165, 44, 10, 1),
(166, 44, 16, 1),
(167, 44, 18, 1),
(168, 45, 8, 1),
(169, 45, 10, 1),
(170, 45, 16, 1),
(171, 45, 18, 1),
(172, 46, 8, 1),
(173, 46, 10, 1),
(174, 46, 16, 1),
(175, 46, 18, 1),
(176, 47, 8, 1),
(177, 47, 10, 1),
(178, 47, 16, 1),
(179, 47, 18, 1),
(180, 48, 8, 1),
(181, 48, 10, 1),
(182, 48, 16, 1),
(183, 48, 18, 1),
(184, 49, 8, 1),
(185, 49, 10, 1),
(186, 49, 16, 1),
(187, 49, 18, 1),
(188, 50, 8, 1),
(189, 50, 10, 1),
(190, 50, 16, 1),
(191, 50, 18, 1),
(192, 51, 8, 1),
(193, 51, 10, 1),
(194, 51, 16, 1),
(195, 51, 18, 1),
(196, 52, 8, 1),
(197, 52, 10, 1),
(198, 52, 16, 1),
(199, 52, 18, 1),
(200, 53, 8, 1),
(201, 53, 10, 1),
(202, 53, 16, 1),
(203, 53, 18, 1),
(204, 54, 8, 5),
(205, 54, 10, 1),
(206, 54, 16, 1),
(207, 54, 18, 2),
(208, 55, 8, 1),
(209, 55, 10, 1),
(210, 55, 16, 1),
(211, 55, 18, 1),
(212, 56, 8, 1),
(213, 56, 10, 1),
(214, 56, 16, 1),
(215, 56, 18, 1),
(216, 57, 8, 1),
(217, 57, 10, 1),
(218, 57, 16, 1),
(219, 57, 18, 1),
(220, 58, 8, 1),
(221, 58, 10, 1),
(222, 58, 16, 1),
(223, 58, 18, 1),
(224, 59, 8, 1),
(225, 59, 10, 1),
(226, 59, 16, 1),
(227, 59, 18, 1),
(228, 60, 8, 1),
(229, 60, 10, 1),
(230, 60, 16, 1),
(231, 60, 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Official','Resident') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `password`, `role`) VALUES
(20, 'Patricia Mae Rosales', 'patriciamae@gmail.com', 'Patty', '$2y$10$Fr83o8wSKGYk7os2qdc8/.qLUFrMpDfbmQPW0O87iTJs3EhMVBd2.', 'Admin'),
(21, 'John Paul Asoy', 'Johnpaul@gmail.com', 'John', '$2y$10$LDXjNaHI2MoJiXFNkd8xWuzQ0wjVG7krx8A/9MZS6tZT6iVP5bfWa', 'Official'),
(63, 'Janice Sitchon', 'Janicesitchon@gmail.com', 'Janice', '$2y$10$wO/3Ini//2GKNER88UXq8OW9uqKW1LXWu6WgWl1yFnqVY4Wy7ZwvW', 'Official'),
(74, 'Admin', 'narutoharaz2@gmail.com', 'Admin', '$2y$10$r7oYsYbknyVYN4Og3KNuuONxc9vjsd2b0SYef6Mb9OolAg44aKmj2', 'Admin'),
(75, 'Neil Rechie M. Consolacion', 'nielrechiecons@gmail.com', 'NeilR', '$2y$10$SNNIpY4zHZ90JdQe0lp8gulrzu1J2iNc1VrVXTIufPcgEdE5/Mmpq', 'Resident'),
(77, 'jonnas d. barlas', 'jonas@gmail.com', 'jonas', '$2y$10$Bkmx4rV2aeBGnq24bjCuQe44DraG6IBiLOeYVi9Qles/wpVTwxJRa', 'Resident'),
(80, 'ben', 'ben@gmail.com', 'ben', '$2y$10$XywBJy6y36vYxdz1Iuet4OB5k/cEiBKJuLtOvMci048obnLZlELTO', 'Resident'),
(81, 'ken', 'ken@gmail.com', 'ken', '$2y$10$YkEP0mHqsrjFFdivOgdlVOFMRq9.bjQJK6xKYibFdIvwpu.L/.Epm', 'Resident'),
(82, 'cong', 'cong@gmail.com', 'cong', '$2y$10$ockI75vLRLaJyQgVZk/QEupz5MTweiJTF/UuYNCNjGEm218AEX8tq', 'Resident');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheduled_assistance`
--
ALTER TABLE `scheduled_assistance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `scheduled_assistance_items`
--
ALTER TABLE `scheduled_assistance_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `scheduled_assistance`
--
ALTER TABLE `scheduled_assistance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `scheduled_assistance_items`
--
ALTER TABLE `scheduled_assistance_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scheduled_assistance`
--
ALTER TABLE `scheduled_assistance`
  ADD CONSTRAINT `scheduled_assistance_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `scheduled_assistance_items`
--
ALTER TABLE `scheduled_assistance_items`
  ADD CONSTRAINT `scheduled_assistance_items_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `scheduled_assistance` (`id`),
  ADD CONSTRAINT `scheduled_assistance_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
