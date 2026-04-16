-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 06:18 PM
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
-- Database: `lost_and_found`
--
CREATE DATABASE IF NOT EXISTS `lost_and_found` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lost_and_found`;

-- --------------------------------------------------------

--
-- Table structure for table `adminactions`
--

CREATE TABLE IF NOT EXISTS `adminactions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  PRIMARY KEY (`action_id`),
  KEY `admin_id` (`admin_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminactions`
--

INSERT INTO `adminactions` (`action_id`, `admin_id`, `item_id`, `action_type`, `action_date`) VALUES
(1, 5, 1, 'Verified', NULL),
(2, 5, 2, 'Approved', NULL),
(3, 5, 3, 'Reviewed', NULL),
(4, 5, 4, 'Flagged', NULL),
(5, 5, 5, 'Closed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Electronics'),
(2, 'Documents'),
(3, 'Accessories'),
(4, 'Clothing'),
(5, 'Bags');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE IF NOT EXISTS `claims` (
  `claim_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `claimed_by` int(11) DEFAULT NULL,
  `claim_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT NULL,
  `proof_description` text DEFAULT NULL,
  PRIMARY KEY (`claim_id`),
  UNIQUE KEY `item_id` (`item_id`,`claimed_by`),
  KEY `claimed_by` (`claimed_by`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`claim_id`, `item_id`, `claimed_by`, `claim_date`, `status`, `proof_description`) VALUES
(1, 2, 1, '2026-04-03', 'pending', 'Provided ID proof'),
(2, 5, 3, '2026-04-06', 'approved', 'Matched description'),
(3, 1, 4, '2026-04-07', 'rejected', 'Incorrect details');

-- --------------------------------------------------------

--
-- Table structure for table `itemimages`
--

CREATE TABLE IF NOT EXISTS `itemimages` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itemimages`
--

INSERT INTO `itemimages` (`image_id`, `item_id`, `image_url`) VALUES
(1, 1, 'phone1.jpg'),
(2, 1, 'phone2.jpg'),
(3, 2, 'wallet.jpg'),
(4, 3, 'idcard.jpg'),
(5, 4, 'bag.jpg'),
(6, 5, 'watch.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `status` enum('lost','found','claimed','closed') DEFAULT NULL,
  `date_reported` date DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `category_id` (`category_id`),
  KEY `reported_by` (`reported_by`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `title`, `description`, `category_id`, `reported_by`, `status`, `date_reported`) VALUES
(1, 'Lost Phone', 'Black Samsung phone', 1, 1, 'lost', '2026-04-01'),
(2, 'Found Wallet', 'Brown leather wallet', 3, 2, 'found', '2026-04-02'),
(3, 'Lost ID Card', 'University ID card', 2, 3, 'lost', '2026-04-03'),
(4, 'Lost Backpack', 'Blue school bag', 5, 4, 'lost', '2026-04-04'),
(5, 'Found Watch', 'Silver wrist watch', 3, 2, 'found', '2026-04-05');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `place_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `place_name`, `description`) VALUES
(1, 'Dhaka University', 'Campus area'),
(2, 'Dhanmondi', 'Residential area'),
(3, 'Gulshan', 'Commercial area'),
(4, 'Banani', 'Business district'),
(5, 'Mirpur', 'Busy residential zone');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Your lost item has a claim request', 0, '2026-04-15 17:00:18'),
(2, 2, 'You reported a found item successfully', 1, '2026-04-15 17:00:18'),
(3, 3, 'Your claim has been approved', 0, '2026-04-15 17:00:18'),
(4, 4, 'New item reported near your area', 0, '2026-04-15 17:00:18'),
(5, 5, 'Admin action completed', 1, '2026-04-15 17:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `report_type` enum('lost','found') DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `details` text DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  UNIQUE KEY `item_id` (`item_id`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `item_id`, `location_id`, `report_type`, `report_date`, `details`) VALUES
(1, 1, 1, 'lost', '2026-04-01', 'Lost near library'),
(2, 2, 2, 'found', '2026-04-02', 'Found on street'),
(3, 3, 1, 'lost', '2026-04-03', 'Lost in classroom'),
(4, 4, 5, 'lost', '2026-04-04', 'Lost on bus'),
(5, 5, 3, 'found', '2026-04-05', 'Found in office');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Rahim Uddin', 'rahim@gmail.com', '01711111111', 'pass123', 'user', '2026-04-15 17:00:18'),
(2, 'Karim Ahmed', 'karim@gmail.com', '01722222222', 'pass123', 'user', '2026-04-15 17:00:18'),
(3, 'Nusrat Jahan', 'nusrat@gmail.com', '01744444444', 'pass123', 'user', '2026-04-15 17:00:18'),
(4, 'Tanvir Hasan', 'tanvir@gmail.com', '01755555555', 'pass123', 'user', '2026-04-15 17:00:18'),
(5, 'Admin User', 'admin@gmail.com', '01733333333', 'admin123', 'admin', '2026-04-15 17:00:18');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminactions`
--
ALTER TABLE `adminactions`
  ADD CONSTRAINT `adminactions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `adminactions_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `claims_ibfk_2` FOREIGN KEY (`claimed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `itemimages`
--
ALTER TABLE `itemimages`
  ADD CONSTRAINT `itemimages_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
