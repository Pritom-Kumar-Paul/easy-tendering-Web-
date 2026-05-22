-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 03, 2025 at 08:41 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `contact_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'Pritom Paul', 'ppritom5@gmail.com', '01785654096', 'save me\r\n', '2024-11-18 14:51:56'),
(2, 'Pritom Paul', 'ppritom5@gmail.com', '01785654096', 'klnlk\r\n\r\n', '2024-11-25 18:37:23'),
(3, 'abcgd', 'ghdjg@gmail.com', '89037986', 'hjahdfja\r\n', '2024-11-26 06:55:08'),
(4, 'Pritom Paul', 'ppritom5@gmail.com', '01785654096', 'i want to bid a tender', '2025-05-07 02:12:26'),
(5, 'Pritom Paul', 'ppritom5@gmail.com', '01785654096', 'dfghjk', '2025-05-14 02:46:32'),
(6, 'Pritom Paul', 'ppritom5@gmail.com', '01785654096', 'man is great\r\n', '2025-05-18 13:24:13'),
(7, 'Pritom Paul', 'nnnn@gmail.com', '01785654096', 'hi', '2025-05-18 16:17:17'),
(8, 'Pritom Paul', 'ppritom5@gmail.com', '01785654096', 'hii', '2025-05-24 02:00:07');

-- --------------------------------------------------------

--
-- Table structure for table `message_replies`
--

CREATE TABLE `message_replies` (
  `id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT 'ID of admin/staff who replied',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_replies`
--

INSERT INTO `message_replies` (`id`, `contact_id`, `admin_id`, `subject`, `message`, `created_at`) VALUES
(1, 1, NULL, 'Re: Your inquiry', 'hi', '2025-05-16 20:48:01'),
(2, 1, NULL, 'Re: Your inquiry', 'hi', '2025-05-16 20:48:06'),
(3, 1, NULL, 'Re: Your inquiry', 'hi', '2025-05-16 20:48:09'),
(4, 1, NULL, 'Re: Your inquiry', 'how i can help you\r\n', '2025-05-16 20:52:01'),
(5, 1, NULL, 'Re: Your inquiry', 'hi', '2025-05-16 20:52:56'),
(6, 7, NULL, 'Re: Your inquiry', 'hlw', '2025-05-18 16:17:51'),
(7, 7, NULL, 'Re: Your inquiry', 'i face some problem', '2025-05-18 16:18:15'),
(8, 7, NULL, 'Re: Your inquiry', 'hlw', '2025-05-18 16:18:25'),
(9, 6, NULL, 'Re: Your inquiry', 'how i can help you', '2025-05-20 03:40:02'),
(10, 6, NULL, 'Re: Your inquiry', 'i can not bit all the tender', '2025-05-20 03:41:09'),
(11, 6, NULL, 'Re: Your inquiry', 'how i can help you', '2025-05-20 03:41:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `message_replies`
--
ALTER TABLE `message_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD CONSTRAINT `message_replies_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
