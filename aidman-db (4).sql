-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2024 at 09:06 AM
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
(11, 'uploads/461109949_122163730178123455_3263037468313986409_n.jpg'),
(12, 'uploads/456243696_122158920140123455_559999452332405967_n.jpg'),
(14, 'uploads/456515230_122159198318123455_3163499503558211932_n.jpg'),
(15, 'uploads/456334342_122158919954123455_2319659671765342922_n.jpg');

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
(77, 'Consolacion Household', 10, 5, 5, 6, 3, 6.90, NULL),
(78, 'Rosales Household', 8, 10, 6, 4, 6, 7.40, NULL),
(79, 'Asoy Household', 5, 8, 8, 5, 5, 6.20, NULL),
(80, 'Sitchon Household', 5, 6, 7, 5, 5, 5.60, NULL);

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
(19, 'Neil Rechie M. Consolacion', 'narutoharaz2@gmail.com', 'NielR', '$2y$10$rQGRH/OQxJfgnV9V2F3EIe0zTlGDoMtkG4Oabfa1fsOO4dOJAbnTq', 'Admin'),
(20, 'Patricia Mae Rosales', 'patriciamae@gmail.com', 'Patty', '$2y$10$Fr83o8wSKGYk7os2qdc8/.qLUFrMpDfbmQPW0O87iTJs3EhMVBd2.', 'Official'),
(21, 'John Paul Asoy', 'Johnpaul@gmail.com', 'John', '$2y$10$LDXjNaHI2MoJiXFNkd8xWuzQ0wjVG7krx8A/9MZS6tZT6iVP5bfWa', 'Official'),
(63, 'Janice Sitchon', 'Janicesitchon@gmail.com', 'Janice', '$2y$10$wO/3Ini//2GKNER88UXq8OW9uqKW1LXWu6WgWl1yFnqVY4Wy7ZwvW', 'Official'),
(65, 'Lea Salonga', 'Lea@gmail.com', 'Lea', '$2y$10$VzVDXp64V5BV4iFBx2q2A.j48yylRBfVFbsSsaJRo.IQQI3oHDzp2', 'Resident');

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
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
