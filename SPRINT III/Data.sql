-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 30, 2025 at 10:48 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webtech_2025A_zeinab_hamidou`
--

-- --------------------------------------------------------

--
-- Table structure for table `connection_requests`
--

CREATE TABLE `connection_requests` (
  `id` int UNSIGNED NOT NULL,
  `sender_id` int UNSIGNED NOT NULL,
  `receiver_id` int UNSIGNED NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connection_requests`
--

INSERT INTO `connection_requests` (`id`, `sender_id`, `receiver_id`, `status`, `created_at`) VALUES
(1, 4, 3, 'accepted', '2025-11-29 19:52:31'),
(2, 5, 3, 'accepted', '2025-11-30 20:31:30'),
(3, 5, 4, 'pending', '2025-11-30 20:33:30'),
(4, 6, 4, 'accepted', '2025-11-30 21:45:53'),
(5, 7, 3, 'accepted', '2025-11-30 22:00:44'),
(6, 7, 4, 'pending', '2025-11-30 22:00:47'),
(7, 7, 5, 'accepted', '2025-11-30 22:00:49'),
(8, 8, 4, 'accepted', '2025-11-30 22:18:28'),
(9, 5, 8, 'accepted', '2025-11-30 22:18:52');

-- --------------------------------------------------------

--
-- Table structure for table `datingAppUsers`
--

CREATE TABLE `datingAppUsers` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gender` enum('female','male','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bio` text,
  `profile_picture` varchar(255) DEFAULT 'lotus.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `datingAppUsers`
--

INSERT INTO `datingAppUsers` (`id`, `name`, `email`, `password`, `role`, `username`, `gender`, `bio`, `profile_picture`) VALUES
(3, 'Zeinab Amadou', 'zeinab.hamidou@ashesi.edu.gh', '$2y$10$mK8kfeaA8G3HQLEr6DJSdOjfsvwRcIkPx8xM4PLvAVbwbytYtHtny', 'user', 'Zeina', 'female', '', 'lotus.png'),
(4, 'Cindy Wanyika Kilonzo', 'cindy.kilonzo@ashesi.edu.gh', '$2y$10$C2i11WSey1.0A6bVWO4SCOAT/7JL1p.ONJ9hLt2nKrv1K7yLSnWx2', 'user', 'Cindy', 'female', '', 'lotus.png'),
(5, 'Aku kekeli', 'akukekeli20@gmail.com', '$2y$10$VSCw1zpP6hqAxWeWTYkycekQ5/gtMEa871WQlgL6BKvKF271gZ47m', 'user', 'keke_aku', 'female', NULL, 'lotus.png'),
(6, 'Ebube Ikeji', 'ebubechukwu.ikeji@ashesi.edu.gh', '$2y$10$HwZlu4UC6FI0rRX3JG7dG.ar4mbflolDJlXIWK6a1yY9lfvEzZ1/u', 'user', 'Ebube', 'female', NULL, 'lotus.png'),
(7, 'Mari Eib', 'seshieabunu19357@gmail.com', '$2y$10$uD.P65ECh8EW6r31ve.b9OXBJJkqKqW7RHgQYZVpiEqfpOP5KAsVe', 'user', 'rissa', 'female', NULL, 'lotus.png'),
(8, 'Ademide', 'ademide.adebanjo@ashesi.edu.gh', '$2y$10$9QWCJCe6G6oCGZQRekwEg.2PfZhZfdbKqn/158FlJ3qJ5ceIuglJO', 'user', 'Mizzy', 'male', 'Adorable', 'lotus.png');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int UNSIGNED NOT NULL,
  `sender_id` int UNSIGNED NOT NULL,
  `receiver_id` int UNSIGNED NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 4, 'Hello', 1, '2025-11-29 19:52:50'),
(2, 4, 3, 'Hey', 1, '2025-11-29 19:53:07'),
(3, 3, 4, 'Hi', 1, '2025-11-29 20:01:25'),
(4, 4, 3, '123', 1, '2025-11-29 20:01:53'),
(5, 3, 4, 'Hello', 1, '2025-11-29 20:30:28'),
(6, 3, 4, 'hey', 1, '2025-11-30 20:14:58'),
(7, 4, 3, 'hello Zeinab', 1, '2025-11-30 20:15:46'),
(8, 3, 4, 'Hello', 1, '2025-11-30 20:16:20'),
(9, 3, 5, 'hey', 1, '2025-11-30 20:35:42'),
(10, 5, 3, 'HALLOOOO', 0, '2025-11-30 20:35:45'),
(11, 4, 6, 'Hey', 1, '2025-11-30 21:46:47'),
(12, 6, 4, 'Ok', 1, '2025-11-30 21:47:01'),
(13, 7, 3, 'hi', 1, '2025-11-30 22:02:37'),
(14, 3, 7, 'Hello', 0, '2025-11-30 22:02:43'),
(15, 8, 4, 'Hello', 1, '2025-11-30 22:20:00'),
(16, 4, 8, 'Hi', 1, '2025-11-30 22:20:09'),
(17, 8, 4, 'How are you doing?', 1, '2025-11-30 22:20:12'),
(18, 4, 8, 'great', 0, '2025-11-30 22:20:26'),
(19, 8, 5, 'Hi how you doing?', 0, '2025-11-30 22:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `sender_id` int UNSIGNED DEFAULT NULL,
  `type` enum('request','request_accepted') NOT NULL,
  `reference_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `sender_id`, `type`, `reference_id`, `created_at`) VALUES
(1, 5, 3, 'request_accepted', 2, '2025-11-30 20:35:23'),
(2, 6, 4, 'request_accepted', 4, '2025-11-30 21:46:28'),
(3, 7, 3, 'request_accepted', 5, '2025-11-30 22:02:26'),
(4, 5, 8, 'request_accepted', 9, '2025-11-30 22:19:15'),
(5, 8, 4, 'request_accepted', 8, '2025-11-30 22:19:43'),
(6, 7, 5, 'request_accepted', 7, '2025-11-30 22:20:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `connection_requests`
--
ALTER TABLE `connection_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_connection` (`sender_id`,`receiver_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `datingAppUsers`
--
ALTER TABLE `datingAppUsers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `connection_requests`
--
ALTER TABLE `connection_requests`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `datingAppUsers`
--
ALTER TABLE `datingAppUsers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `connection_requests`
--
ALTER TABLE `connection_requests`
  ADD CONSTRAINT `connection_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `datingAppUsers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connection_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `datingAppUsers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `datingAppUsers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `datingAppUsers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `datingAppUsers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `datingAppUsers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
