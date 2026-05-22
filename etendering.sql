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
-- Database: `etendering`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bid`
--

CREATE TABLE `tbl_bid` (
  `b_id` int(11) NOT NULL,
  `b_tender_id` int(11) DEFAULT NULL,
  `b_vendor_id` int(11) DEFAULT NULL,
  `b_amount` decimal(15,2) DEFAULT NULL,
  `b_proposal` text DEFAULT NULL,
  `b_submission_date` datetime DEFAULT current_timestamp(),
  `b_status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_bid`
--

INSERT INTO `tbl_bid` (`b_id`, `b_tender_id`, `b_vendor_id`, `b_amount`, `b_proposal`, `b_submission_date`, `b_status`) VALUES
(6, 22, 13, 4.00, 'need this', '2025-05-14 08:10:03', 3),
(7, 21, 13, 55.00, 'hgyy', '2025-05-17 00:16:50', 3),
(12, 23, 13, 33.00, 'www', '2025-05-18 21:57:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_messages`
--

CREATE TABLE `tbl_messages` (
  `msg_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `tender_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tender`
--

CREATE TABLE `tbl_tender` (
  `t_id` int(11) NOT NULL,
  `t_type_construction` tinyint(1) DEFAULT NULL,
  `t_type_supply` tinyint(1) DEFAULT NULL,
  `t_type_service` tinyint(1) DEFAULT NULL,
  `t_budget` decimal(15,2) DEFAULT NULL,
  `t_location` text DEFAULT NULL,
  `t_contact_person` text NOT NULL,
  `t_contact_person_phone_number` text NOT NULL,
  `t_description` text DEFAULT NULL,
  `t_deadline` date NOT NULL,
  `t_awarded_vendor` int(11) DEFAULT NULL,
  `t_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tender`
--

INSERT INTO `tbl_tender` (`t_id`, `t_type_construction`, `t_type_supply`, `t_type_service`, `t_budget`, `t_location`, `t_contact_person`, `t_contact_person_phone_number`, `t_description`, `t_deadline`, `t_awarded_vendor`, `t_status`) VALUES
(21, 0, 1, 1, 66.00, 'Bonpara', 'Pronob Paul', '01597536842', 'Phone projector glass 12 pic any model', '2025-05-27', NULL, 1),
(22, 1, 1, 0, 5.00, 'saidpur', 'Pritom Paul', '01300000000', 'ffff', '2025-05-17', 13, 2),
(23, 1, 1, 0, 3.00, 'saidpur', 'Pritom Paul', '01785654096', 'sdfghj', '2025-05-29', NULL, 1),
(24, 1, 1, 1, 3.00, 'Rajshahi', 'sulata', '01715270445', '1 blue shirt', '2025-05-31', NULL, 1),
(25, 1, 1, 1, 102.00, 'Rajshahi', 'Pritom Paul', '01785654096', 'motherbord b250 asus', '2025-05-23', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `u_id` int(11) NOT NULL,
  `u_first_name` varchar(25) NOT NULL,
  `u_last_name` varchar(25) NOT NULL,
  `u_email` varchar(100) NOT NULL,
  `u_phone` text NOT NULL,
  `u_occupation` varchar(20) NOT NULL,
  `u_blood_group` varchar(20) NOT NULL,
  `u_know_swimming` text NOT NULL,
  `u_present_area` text NOT NULL,
  `u_company` varchar(100) DEFAULT NULL,
  `u_business_type` varchar(50) DEFAULT NULL,
  `u_tax_id` varchar(50) DEFAULT NULL,
  `u_password` text NOT NULL,
  `u_image` blob NOT NULL,
  `u_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`u_id`, `u_first_name`, `u_last_name`, `u_email`, `u_phone`, `u_occupation`, `u_blood_group`, `u_know_swimming`, `u_present_area`, `u_company`, `u_business_type`, `u_tax_id`, `u_password`, `u_image`, `u_role`) VALUES
(8, 'Pritom', 'Paul', 'ppritom5@gmail.com', '01785654096', 'Student ', 'O+', '1', 'Saidpur', NULL, NULL, NULL, '25d55ad283aa400af464c76d713c07ad', 0x313434383238303433343538343937313733313939333937332e706e67, 1),
(13, 'Prodip', 'Paul', 'shefa5@gmail.com', '01785654096', 'Business', 'A+', '1', 'Natore', '', '', '', '25d55ad283aa400af464c76d713c07ad', 0x3633343136363136313734363930313733313530383239302e6a7067, 2),
(14, 'medha', 'saha', 'medha5@gmail.com', '01785654096', 'Student', 'B+', '0', 'saidpur', '', '', '', '25d55ad283aa400af464c76d713c07ad', '', 3),
(16, 'Pritom', 'Paul', 'ppritom54@gmail.com', '01785654096', 'Business', 'O+', '1', 'qwe', 'w', '', '', 'f638f4354ff089323d1a5f78fd8f63ca', '', 2),
(17, 'Pritom', 'Paul', 'ppritom6@gmail.com', '01785654096', 'Student', 'A+', '0', 'hgjhgj', '', '', '', 'dd4b21e9ef71e1291183a46b913ae6f2', '', 2),
(18, 'Pritom', 'Paul', 'ppritom5', '01785654096', 'Student', 'A+', '0', 'ddd', '', '', '', '1bbd886460827015e5d605ed44252251', '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tenders`
--

CREATE TABLE `tenders` (
  `id` int(11) NOT NULL,
  `tender_name` varchar(255) NOT NULL,
  `tender_type` varchar(100) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `strict_deadline` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_orders`
--

CREATE TABLE `user_orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tender_id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `amount_due` decimal(15,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_payments`
--

CREATE TABLE `user_payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_bid`
--
ALTER TABLE `tbl_bid`
  ADD PRIMARY KEY (`b_id`),
  ADD KEY `b_tender_id` (`b_tender_id`),
  ADD KEY `b_vendor_id` (`b_vendor_id`);

--
-- Indexes for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `tender_id` (`tender_id`);

--
-- Indexes for table `tbl_tender`
--
ALTER TABLE `tbl_tender`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`u_id`);

--
-- Indexes for table `tenders`
--
ALTER TABLE `tenders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tender_id` (`tender_id`);

--
-- Indexes for table `user_payments`
--
ALTER TABLE `user_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_bid`
--
ALTER TABLE `tbl_bid`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_tender`
--
ALTER TABLE `tbl_tender`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tenders`
--
ALTER TABLE `tenders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_orders`
--
ALTER TABLE `user_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bid`
--
ALTER TABLE `tbl_bid`
  ADD CONSTRAINT `tbl_bid_ibfk_1` FOREIGN KEY (`b_tender_id`) REFERENCES `tbl_tender` (`t_id`),
  ADD CONSTRAINT `tbl_bid_ibfk_2` FOREIGN KEY (`b_vendor_id`) REFERENCES `tbl_user` (`u_id`);

--
-- Constraints for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  ADD CONSTRAINT `tbl_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `tbl_user` (`u_id`),
  ADD CONSTRAINT `tbl_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `tbl_user` (`u_id`),
  ADD CONSTRAINT `tbl_messages_ibfk_3` FOREIGN KEY (`tender_id`) REFERENCES `tbl_tender` (`t_id`);

--
-- Constraints for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD CONSTRAINT `user_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`u_id`),
  ADD CONSTRAINT `user_orders_ibfk_2` FOREIGN KEY (`tender_id`) REFERENCES `tbl_tender` (`t_id`);

--
-- Constraints for table `user_payments`
--
ALTER TABLE `user_payments`
  ADD CONSTRAINT `user_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `user_orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
