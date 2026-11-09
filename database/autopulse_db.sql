-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2026 at 06:14 AM
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
-- Database: `autopulse_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','receptionist','mechanic','customer') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', 'admin@autopulse.com', '0710000001', '$2y$10$fEbqCBoU1O0W6Htlcaoeg.JJJq.rEvIbV./mu52FlXB2jvYZasIRa', 'admin', 'active', '2026-11-05 04:57:57', '2026-11-05 05:07:44'),
(2, 'Nimali Perera', 'reception@autopulse.com', '0710000002', '$2y$10$RBRJDydCILKPMtG29dUQAufzOq76t6nS4XumZyN2Tv7zib1/CQxaS', 'receptionist', 'active', '2026-11-05 04:57:57', '2026-11-05 05:07:44'),
(3, 'John Silva', 'mechanic@autopulse.com', '0710000003', '$2y$10$gtN0BLTvKZXhUf8kcRu.au7o20QHfpRKVjzKfNodgQXpO10wWO/Gm', 'mechanic', 'active', '2026-11-05 04:57:57', '2026-11-05 05:07:44'),
(4, 'Kasun Fernando', 'customer@autopulse.com', '0771234567', '$2y$10$Rt7mKkg405meXzIU42iCMuLqHUO8.99/BFs9jv4czbXqE1BnJYHkG', 'customer', 'active', '2026-11-05 04:57:57', '2026-11-05 05:07:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
