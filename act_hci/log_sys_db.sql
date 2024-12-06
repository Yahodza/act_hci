-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2024 at 07:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `log_sys_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sex` varchar(10) DEFAULT NULL,
  `phonenumber` varchar(15) DEFAULT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `username`, `email`, `password`, `profile_photo`, `status`, `created_at`, `sex`, `phonenumber`, `user_role_id`, `suffix`, `middlename`, `lastname`) VALUES
(14, 'goldy', 'gold', 'gold@e.com', '$2y$10$9KvK6QYJvccTy6nDysAdjOkTcNREkYEXRbDm5q2iGe4N.sHHt9SEq', 'default.jpg', 'active', '2024-10-25 02:24:12', 'F', '23213', NULL, '', 'asd', 'sd'),
(15, 'prime', 'prime', 'prime@e.com', '$2y$10$l2yl0j4sfAEAOqp/ArJbt.XC9I16BzL4bK4gP0Z/teW7x0y/gYhkO', 'uploads/426742589_805833734891957_1016670025858348915_n.jpg', 'active', '2024-10-25 02:27:56', NULL, NULL, NULL, NULL, NULL, ''),
(18, 'Myla', 'Myla', 'myla@edu.com', '$2y$10$5COUNISb66R0kUT.vM6Ptuqq5iZz.qeG/IvzKTZXZohZBrbsfOLuW', 'default.jpg', 'active', '2024-11-06 13:28:11', 'F', '2321', NULL, '', 'Pers', 'Pres'),
(21, 'user', 'asd', 'asd@gmail.com', '$2y$10$viSBwQl.30fPKo7jRyc6O.K8Yg4r5byAkCFg8wWT6nYbeTkrrrmzi', 'default.jpg', 'active', '2024-11-06 15:00:56', 'f', '', NULL, '', 'user', 'user'),
(33, 'hh', 'hh', 'hh@gmail.com', '$2y$10$CrWWUm8rPh2W0nbtoJ4lbeXYSLPG.5grDTxrzOp86emTWwse6quEa', 'default.jpg', 'active', '2024-11-09 11:45:06', 'F', '342', NULL, '', 'hh', 'hh'),
(34, 'Yahodza', 'Yass', 'yass@gmail.com', '$2y$10$S8rSjJ64OEmfSYlP1fLdrOfCe1p/KaejJPzlDnYMqqZ0D6HB/srg.', 'default.jpg', 'active', '2024-11-09 14:06:49', 'M', '12345678', NULL, '', 'Pers', 'Ulangkaya');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
