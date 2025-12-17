-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 17, 2025 at 06:09 PM
-- Server version: 8.0.39
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capsdb`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_calculate_booking_cost`$$
CREATE DEFINER=`u840482060_booking`@`127.0.0.1` PROCEDURE `sp_calculate_booking_cost` (IN `p_plan_id` INT, IN `p_additional_hours` INT, IN `p_equipment_json` JSON, IN `p_addons_json` JSON, OUT `p_total_cost` DECIMAL(10,2))   BEGIN
    DECLARE v_base_plan_cost DECIMAL(10,2);
    DECLARE v_equipment_cost DECIMAL(10,2) DEFAULT 0;
    DECLARE v_addons_cost DECIMAL(10,2) DEFAULT 0;
    DECLARE v_additional_hours_cost DECIMAL(10,2) DEFAULT 0;
    DECLARE v_maintenance_fee DECIMAL(10,2) DEFAULT 2000.00;
    
    -- Get base plan cost
    SELECT price INTO v_base_plan_cost FROM plans WHERE id = p_plan_id;
    
    -- Calculate additional hours cost (₱500 per hour)
    SET v_additional_hours_cost = p_additional_hours * 500;
    
    -- Calculate equipment cost from JSON
    -- (Logic would parse JSON and calculate)
    
    -- Calculate addons cost from JSON
    -- (Logic would parse JSON and calculate)
    
    -- Total calculation
    SET p_total_cost = v_base_plan_cost + v_equipment_cost + v_addons_cost + v_additional_hours_cost + v_maintenance_fee;
END$$

DROP PROCEDURE IF EXISTS `sp_check_equipment_availability`$$
CREATE DEFINER=`u840482060_booking`@`127.0.0.1` PROCEDURE `sp_check_equipment_availability` (IN `p_equipment_id` INT, IN `p_start_date` DATE, IN `p_end_date` DATE)   BEGIN
    SELECT 
        e.name AS equipment_name,
        e.good AS total_good,
        COALESCE(SUM(be.quantity), 0) AS total_booked,
        (e.good - COALESCE(SUM(be.quantity), 0)) AS available
    FROM equipment e
    LEFT JOIN booking_equipment be ON e.id = be.equipment_id
    LEFT JOIN bookings b ON be.booking_id = b.id
    WHERE e.id = p_equipment_id
        AND b.event_date BETWEEN p_start_date AND p_end_date
        AND b.status != 'cancelled'
    GROUP BY e.id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_plan_full_details`$$
CREATE DEFINER=`u840482060_booking`@`127.0.0.1` PROCEDURE `sp_get_plan_full_details` (IN `p_plan_id` INT)   BEGIN
    -- Plan Basic Info
    SELECT 
        p.*,
        f.name AS facility_name,
        f.icon AS facility_icon
    FROM plans p
    JOIN facilities f ON p.facility_id = f.id
    WHERE p.id = p_plan_id;
    
    -- Plan Features
    SELECT 
        pf.feature,
        pf.feature_type,
        pf.is_physical
    FROM plan_features pf
    WHERE pf.plan_id = p_plan_id
    ORDER BY pf.display_order;
    
    -- Plan Equipment
    SELECT 
        e.id AS equipment_id,
        e.name AS equipment_name,
        e.category,
        pe.quantity_included,
        pe.is_mandatory,
        pe.additional_rate,
        e.rate,
        e.available
    FROM plan_equipment pe
    JOIN equipment e ON pe.equipment_id = e.id
    WHERE pe.plan_id = p_plan_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

DROP TABLE IF EXISTS `addons`;
CREATE TABLE IF NOT EXISTS `addons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `addon_key` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `addon_key` (`addon_key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `addon_key`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES
(1, 'photo-booth', 'Photo Booth', 'Professional photo booth setup with props', 1000.00, '2025-10-15 23:18:37', '2025-10-15 23:18:37'),
(2, 'live-band', 'Live Band Setup', 'Additional power and setup for live band performance', 2000.00, '2025-10-15 23:18:37', '2025-10-15 23:18:37'),
(3, 'led-tv', 'LED Wall TV', 'Large LED display for presentations or entertainment', 2000.00, '2025-10-15 23:18:37', '2025-10-15 23:18:37'),
(4, 'spotlight', 'Professional Spotlight', 'Outsourced professional spotlight equipment', 1000.00, '2025-10-15 23:18:37', '2025-10-15 23:18:37'),
(5, 'special-lighting', 'Special Lighting Package', 'Advanced lighting setup by external provider', 3000.00, '2025-10-15 23:18:37', '2025-10-15 23:18:37'),
(6, 'sound-system', 'Premium Sound System', 'Professional grade sound system by external provider', 5000.00, '2025-10-15 23:18:37', '2025-10-15 23:18:37'),
(7, 'overtime', 'Overtime Staff Support', 'Overtime Pay for 5 staff members (weekends/holidays/after 5PM)', 5000.00, '2025-10-15 23:18:37', '2025-11-27 17:21:18'),
(9, 'additional-hours', 'Additional Hours', 'Extra hours beyond plan duration', 500.00, '2025-10-15 23:18:37', '2025-11-27 17:21:18'),
(11, 'catering', 'catering', 'asdas da', 10000.00, '2025-11-27 10:37:30', '2025-11-27 10:37:30');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_id` int UNSIGNED DEFAULT NULL,
  `plan_id` int NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `attendees` int DEFAULT NULL,
  `event_title` varchar(255) NOT NULL,
  `special_requirements` text,
  `total_cost` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed','pending_cancellation') DEFAULT 'pending',
  `booking_type` enum('student','employee','user','external') DEFAULT 'user',
  `decline_reason` varchar(100) DEFAULT NULL,
  `decline_notes` text,
  `cancellation_letter_path` varchar(500) DEFAULT NULL COMMENT 'Path to the cancellation letter file',
  `cancellation_requested_at` datetime DEFAULT NULL COMMENT 'Timestamp when user requested cancellation',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approval_notes` text,
  `additional_hours` int DEFAULT '0',
  `maintenance_fee` decimal(10,2) DEFAULT '2000.00',
  `overtime_fee` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `facility_id` (`facility_id`),
  KEY `plan_id` (`plan_id`),
  KEY `fk_bookings_approved_by` (`approved_by`),
  KEY `idx_status` (`status`),
  KEY `idx_event_date` (`event_date`),
  KEY `idx_booking_status_date` (`status`,`event_date`),
  KEY `idx_status_event_date` (`status`,`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `facility_id`, `plan_id`, `client_name`, `contact_number`, `email_address`, `organization`, `address`, `event_date`, `event_time`, `duration`, `attendees`, `event_title`, `special_requirements`, `total_cost`, `status`, `booking_type`, `decline_reason`, `decline_notes`, `cancellation_letter_path`, `cancellation_requested_at`, `created_at`, `updated_at`, `approved_at`, `approved_by`, `approval_notes`, `additional_hours`, `maintenance_fee`, `overtime_fee`) VALUES
(135, 2, 1, 'lizamae', '09810586557', 'lizamaebuenocleofe@gmail.com', 'ccs', 'buhi camarines sur', '2025-12-19', '11:40:00', '5', 300, 'party', 'none', 36015.00, 'pending', 'user', NULL, NULL, NULL, NULL, '2025-12-13 21:40:52', '2025-12-13 21:40:52', NULL, NULL, NULL, 1, 2000.00, 0.00),
(136, 12, 30, 'ivan', '09123456789', 'ivaaanjoshua@gmail.com', NULL, 'San Miguel Nabua Cam sur', '2026-01-31', '10:07:00', '4', 24, 'Seminar', NULL, 5600.00, 'pending', 'user', NULL, NULL, NULL, NULL, '2025-12-14 16:08:46', '2025-12-14 16:08:46', NULL, NULL, NULL, 1, 2000.00, 0.00),
(137, 2, 1, 'Janna May Tangtang ', '09129284359', 'jannamay.tangtang@usant.edu.ph', NULL, 'San miguel, Nabua, Camarines Sur ', '2025-12-22', '18:00:00', '4', NULL, 'party', NULL, 6000.00, 'pending', 'user', NULL, NULL, NULL, NULL, '2025-12-14 18:22:21', '2025-12-14 18:22:21', NULL, NULL, NULL, 0, 2000.00, 0.00),
(138, 4, 26, 'Hazel Ann T. lanuzga', '09917862107', 'hazellanuzga19@gmail.com', NULL, 'Goyudan, Bato Camarines Sur', '2026-02-12', '13:32:00', '4', NULL, 'Seminar ', NULL, 6000.00, 'pending', 'user', NULL, NULL, NULL, NULL, '2025-12-14 18:34:15', '2025-12-14 18:34:15', NULL, NULL, NULL, 0, 2000.00, 0.00),
(139, 2, 1, 'April Abonita', '09203197861', 'eyprilya1@gmail.com', 'ABA Inc.', 'Zone 6, San Juan Bato, Cam Sur', '2025-12-17', '08:30:00', '4', 100, 'The event aims to strengthen teamwork, recognize employee achievements, and discuss company goals and strategies for the upcoming year.', NULL, 10750.00, 'cancelled', 'user', NULL, NULL, NULL, NULL, '2025-12-14 18:44:55', '2025-12-17 16:28:32', NULL, NULL, NULL, 0, 2000.00, 0.00),
(140, 6, 18, 'Princess Ivy', '09123456789', 'ivyyy@gmail.com', NULL, 'San Miguel Nabua Camarines Sur', '2025-12-21', '09:00:00', '4', NULL, 'Christmas Party ', NULL, 33000.00, 'pending', 'user', NULL, NULL, NULL, NULL, '2025-12-14 19:04:38', '2025-12-14 19:04:38', NULL, NULL, NULL, 0, 2000.00, 0.00),
(141, 8, 28, 'Michaela Cezar', '09380582591', 'micezar@my.cspc.edu.ph', 'CCS-CSC', '', '2025-12-15', '08:00:00', '3', 50, 'CSPC - Student Connect', 'N/A', 0.00, 'cancelled', 'student', NULL, NULL, NULL, NULL, '2025-12-14 19:25:18', '2025-12-16 18:18:20', NULL, NULL, NULL, 0, 2000.00, 0.00),
(142, 4, 26, 'Sss', '09454493652', 'elladichoso1@gmail.com', NULL, 'San Miguel, Nabua, Camarines Sur', '2025-12-23', '08:00:00', '4', NULL, 'Spd', NULL, 5000.00, 'pending', 'user', NULL, NULL, NULL, NULL, '2025-12-14 20:33:04', '2025-12-14 20:33:04', NULL, NULL, NULL, 0, 2000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_addons`
--

DROP TABLE IF EXISTS `booking_addons`;
CREATE TABLE IF NOT EXISTS `booking_addons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `addon_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `addon_id` (`addon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_addons`
--

INSERT INTO `booking_addons` (`id`, `booking_id`, `addon_id`, `price`) VALUES
(26, 39, 4, 1000.00),
(27, 40, 1, 1000.00),
(28, 41, 1, 1000.00),
(31, 51, 1, 1000.00),
(32, 51, 7, 5000.00),
(33, 54, 1, 1000.00),
(41, 58, 7, 5000.00),
(42, 59, 4, 1000.00),
(43, 60, 1, 1000.00),
(44, 60, 2, 2000.00),
(45, 60, 6, 5000.00),
(46, 60, 1, 1000.00),
(47, 60, 6, 5000.00),
(48, 64, 1, 1000.00),
(49, 64, 4, 1000.00),
(50, 65, 4, 1000.00),
(51, 67, 1, 1000.00),
(52, 67, 7, 5000.00),
(53, 68, 4, 1000.00),
(54, 69, 1, 1000.00),
(55, 69, 4, 1000.00),
(56, 70, 2, 2000.00),
(57, 71, 2, 2000.00),
(58, 72, 2, 2000.00),
(59, 73, 3, 2000.00),
(60, 74, 6, 5000.00),
(61, 76, 7, 5000.00),
(62, 78, 2, 2000.00),
(63, 78, 11, 10000.00),
(64, 79, 2, 2000.00),
(65, 79, 4, 1000.00),
(67, 83, 1, 1000.00),
(68, 83, 11, 10000.00),
(69, 84, 1, 1000.00),
(70, 85, 11, 10000.00),
(71, 86, 5, 3000.00),
(72, 86, 6, 5000.00),
(73, 87, 2, 2000.00),
(74, 87, 11, 10000.00),
(75, 87, 3, 2000.00),
(76, 88, 6, 5000.00),
(77, 88, 4, 1000.00),
(78, 89, 6, 5000.00),
(79, 89, 3, 2000.00),
(80, 94, 4, 1000.00),
(81, 99, 6, 5000.00),
(82, 99, 5, 3000.00),
(83, 100, 7, 5000.00),
(84, 100, 5, 3000.00),
(85, 102, 11, 10000.00),
(86, 110, 4, 1000.00),
(87, 110, 5, 3000.00),
(88, 110, 6, 5000.00),
(89, 130, 5, 3000.00),
(90, 131, 6, 5000.00),
(91, 131, 2, 2000.00),
(92, 134, 5, 3000.00),
(93, 135, 1, 1000.00),
(94, 135, 3, 2000.00),
(95, 135, 5, 3000.00),
(96, 135, 7, 5000.00),
(97, 135, 2, 2000.00),
(98, 135, 4, 1000.00),
(99, 135, 6, 5000.00),
(100, 135, 11, 10000.00),
(101, 136, 3, 2000.00),
(102, 136, 4, 1000.00),
(103, 138, 1, 1000.00),
(104, 139, 1, 1000.00),
(105, 140, 6, 5000.00),
(106, 140, 11, 10000.00),
(107, 140, 1, 1000.00),
(108, 140, 2, 2000.00),
(109, 140, 3, 2000.00),
(110, 140, 4, 1000.00),
(111, 140, 5, 3000.00),
(112, 140, 7, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_equipment`
--

DROP TABLE IF EXISTS `booking_equipment`;
CREATE TABLE IF NOT EXISTS `booking_equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `equipment_id` int NOT NULL,
  `quantity_included` int DEFAULT '0' COMMENT 'From plan - no charge',
  `quantity_additional` int DEFAULT '0' COMMENT 'Extra requested - charged',
  `quantity` int NOT NULL COMMENT 'Total quantity',
  `rate` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL COMMENT 'Cost for additional only',
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `idx_booking_equipment` (`booking_id`,`equipment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_equipment`
--

INSERT INTO `booking_equipment` (`id`, `booking_id`, `equipment_id`, `quantity_included`, `quantity_additional`, `quantity`, `rate`, `total_cost`) VALUES
(26, 42, 4, 0, 0, 6, 7.50, 45.00),
(29, 44, 4, 0, 0, 3, 7.50, 22.50),
(30, 45, 8, 0, 0, 5, 0.00, 0.00),
(31, 45, 9, 0, 0, 3, 0.00, 0.00),
(32, 45, 10, 0, 0, 4, 0.00, 0.00),
(33, 45, 11, 0, 0, 3, 0.00, 0.00),
(34, 45, 12, 0, 0, 3, 0.00, 0.00),
(35, 45, 19, 0, 0, 3, 0.00, 0.00),
(36, 45, 20, 0, 0, 2, 0.00, 0.00),
(37, 45, 22, 0, 0, 4, 0.00, 0.00),
(38, 45, 23, 0, 0, 3, 0.00, 0.00),
(39, 46, 1, 0, 0, 20, 65.00, 1300.00),
(40, 46, 2, 0, 0, 50, 7.50, 375.00),
(41, 46, 6, 0, 0, 1, 0.00, 0.00),
(42, 46, 7, 0, 0, 2, 0.00, 0.00),
(43, 46, 8, 0, 0, 10, 0.00, 0.00),
(44, 46, 9, 0, 0, 6, 0.00, 0.00),
(45, 47, 1, 0, 0, 30, 65.00, 1950.00),
(46, 47, 2, 0, 0, 100, 7.50, 750.00),
(47, 47, 4, 0, 0, 50, 7.50, 375.00),
(48, 47, 6, 0, 0, 1, 0.00, 0.00),
(49, 47, 7, 0, 0, 2, 0.00, 0.00),
(50, 47, 8, 0, 0, 10, 0.00, 0.00),
(51, 47, 9, 0, 0, 6, 0.00, 0.00),
(52, 48, 1, 0, 0, 15, 65.00, 975.00),
(53, 48, 2, 0, 0, 40, 7.50, 300.00),
(54, 48, 6, 0, 0, 1, 0.00, 0.00),
(55, 48, 7, 0, 0, 1, 0.00, 0.00),
(66, 51, 1, 0, 0, 3, 65.00, 195.00),
(67, 51, 4, 0, 0, 7, 7.50, 52.50),
(68, 52, 4, 0, 0, 6, 7.50, 45.00),
(69, 52, 5, 0, 0, 2, 0.00, 0.00),
(70, 52, 25, 0, 0, 4, 0.00, 0.00),
(71, 53, 25, 0, 0, 24, 0.00, 0.00),
(72, 54, 4, 0, 0, 6, 7.50, 45.00),
(73, 55, 4, 0, 0, 7, 7.50, 52.50),
(74, 55, 25, 0, 0, 5, 0.00, 0.00),
(75, 56, 2, 0, 0, 4, 7.50, 30.00),
(80, 58, 2, 0, 0, 1, 7.50, 7.50),
(81, 59, 1, 0, 0, 3, 65.00, 195.00),
(82, 59, 4, 0, 0, 3, 7.50, 22.50),
(83, 60, 2, 0, 0, 2, 7.50, 15.00),
(84, 60, 3, 0, 0, 3, 10.00, 30.00),
(85, 61, 1, 0, 0, 1, 65.00, 65.00),
(86, 62, 1, 0, 0, 3, 65.00, 195.00),
(87, 62, 5, 0, 0, 4, 0.00, 0.00),
(88, 62, 16, 0, 0, 2, 0.00, 0.00),
(89, 63, 1, 0, 0, 4, 65.00, 260.00),
(90, 63, 2, 0, 0, 2, 7.50, 15.00),
(91, 63, 4, 0, 0, 2, 7.50, 15.00),
(92, 64, 1, 0, 0, 4, 65.00, 260.00),
(93, 64, 2, 0, 0, 8, 7.50, 60.00),
(94, 64, 4, 0, 0, 10, 7.50, 75.00),
(95, 65, 2, 0, 0, 2, 7.50, 15.00),
(96, 65, 4, 0, 0, 2, 7.50, 15.00),
(97, 66, 4, 0, 0, 5, 7.50, 37.50),
(98, 67, 2, 0, 0, 5, 7.50, 37.50),
(99, 68, 3, 0, 0, 2, 10.00, 20.00),
(100, 70, 2, 0, 0, 7, 7.50, 52.50),
(101, 70, 3, 0, 0, 3, 10.00, 30.00),
(102, 70, 4, 0, 0, 2, 7.50, 15.00),
(103, 71, 1, 0, 0, 3, 65.00, 195.00),
(104, 71, 2, 0, 0, 2, 7.50, 15.00),
(105, 71, 3, 0, 0, 3, 10.00, 30.00),
(106, 71, 4, 0, 0, 3, 7.50, 22.50),
(107, 72, 1, 0, 0, 3, 65.00, 195.00),
(108, 72, 4, 0, 0, 4, 7.50, 30.00),
(109, 73, 3, 0, 0, 2, 10.00, 20.00),
(110, 73, 4, 0, 0, 2, 7.50, 15.00),
(111, 74, 1, 0, 0, 2, 65.00, 130.00),
(112, 74, 4, 0, 0, 4, 7.50, 30.00),
(113, 76, 1, 0, 0, 3, 65.00, 195.00),
(114, 76, 4, 0, 0, 3, 7.50, 22.50),
(115, 77, 1, 0, 0, 3, 65.00, 195.00),
(116, 77, 2, 0, 0, 3, 7.50, 22.50),
(117, 77, 3, 0, 0, 4, 10.00, 40.00),
(118, 78, 1, 0, 0, 3, 65.00, 195.00),
(119, 78, 2, 0, 0, 2, 7.50, 15.00),
(120, 78, 3, 0, 0, 2, 10.00, 20.00),
(121, 78, 4, 0, 0, 3, 7.50, 22.50),
(122, 79, 1, 0, 0, 2, 65.00, 130.00),
(123, 79, 4, 0, 0, 2, 7.50, 15.00),
(124, 80, 5, 0, 0, 3, 0.00, 0.00),
(129, 83, 2, 0, 0, 2, 7.50, 15.00),
(130, 83, 3, 0, 0, 2, 10.00, 20.00),
(131, 84, 1, 0, 0, 2, 65.00, 130.00),
(132, 84, 4, 0, 0, 2, 7.50, 15.00),
(133, 85, 1, 0, 0, 2, 65.00, 130.00),
(134, 85, 4, 0, 0, 2, 7.50, 15.00),
(135, 86, 1, 0, 0, 5, 65.00, 325.00),
(136, 86, 2, 0, 0, 3, 7.50, 22.50),
(137, 86, 4, 0, 0, 2, 7.50, 15.00),
(138, 87, 2, 0, 0, 2, 7.50, 15.00),
(139, 87, 3, 0, 0, 4, 10.00, 40.00),
(140, 89, 1, 0, 0, 3, 65.00, 195.00),
(141, 89, 4, 0, 0, 4, 7.50, 30.00),
(142, 90, 1, 0, 0, 4, 65.00, 260.00),
(143, 90, 6, 0, 0, 4, 0.00, 0.00),
(144, 91, 1, 0, 0, 3, 65.00, 195.00),
(145, 91, 3, 0, 0, 3, 10.00, 30.00),
(146, 91, 4, 0, 0, 3, 7.50, 22.50),
(147, 93, 1, 0, 0, 4, 65.00, 260.00),
(148, 93, 6, 0, 0, 3, 0.00, 0.00),
(149, 93, 25, 0, 0, 6, 0.00, 0.00),
(150, 94, 1, 0, 0, 4, 65.00, 260.00),
(151, 94, 4, 0, 0, 5, 7.50, 37.50),
(152, 96, 13, 0, 0, 3, 0.00, 0.00),
(153, 96, 22, 0, 0, 3, 0.00, 0.00),
(154, 97, 1, 0, 0, 3, 65.00, 195.00),
(155, 97, 14, 0, 0, 3, 0.00, 0.00),
(156, 98, 1, 0, 0, 4, 65.00, 260.00),
(157, 98, 7, 0, 0, 3, 0.00, 0.00),
(158, 99, 1, 0, 0, 4, 65.00, 260.00),
(159, 99, 4, 0, 0, 5, 7.50, 37.50),
(160, 100, 1, 0, 0, 3, 65.00, 195.00),
(161, 100, 4, 0, 0, 3, 7.50, 22.50),
(162, 101, 5, 0, 0, 2, 0.00, 0.00),
(163, 102, 1, 0, 0, 4, 65.00, 260.00),
(164, 102, 4, 0, 0, 2, 7.50, 15.00),
(167, 105, 4, 0, 0, 2, 7.50, 15.00),
(168, 107, 4, 0, 0, 3, 7.50, 22.50),
(169, 109, 5, 0, 0, 3, 0.00, 0.00),
(170, 109, 20, 0, 0, 2, 0.00, 0.00),
(171, 110, 4, 0, 0, 4, 7.50, 30.00),
(172, 111, 1, 0, 0, 4, 65.00, 260.00),
(173, 111, 4, 0, 0, 3, 7.50, 22.50),
(174, 112, 2, 0, 0, 3, 7.50, 22.50),
(175, 112, 4, 0, 0, 3, 7.50, 22.50),
(176, 113, 25, 0, 0, 4, 0.00, 0.00),
(177, 114, 1, 0, 0, 3, 65.00, 195.00),
(178, 115, 8, 0, 0, 3, 0.00, 0.00),
(179, 115, 21, 0, 0, 3, 0.00, 0.00),
(180, 116, 1, 0, 0, 3, 65.00, 195.00),
(181, 116, 4, 0, 0, 3, 7.50, 22.50),
(182, 117, 2, 0, 0, 4, 7.50, 30.00),
(183, 117, 11, 0, 0, 3, 0.00, 0.00),
(184, 118, 2, 0, 0, 3, 7.50, 22.50),
(185, 119, 2, 0, 0, 3, 7.50, 22.50),
(186, 119, 10, 0, 0, 3, 0.00, 0.00),
(187, 120, 2, 0, 0, 4, 7.50, 30.00),
(188, 120, 3, 0, 0, 4, 10.00, 40.00),
(189, 121, 1, 0, 0, 3, 65.00, 195.00),
(190, 122, 2, 0, 0, 3, 7.50, 22.50),
(191, 122, 3, 0, 0, 3, 10.00, 30.00),
(192, 123, 1, 0, 0, 3, 65.00, 195.00),
(193, 124, 2, 0, 0, 4, 7.50, 30.00),
(194, 125, 1, 0, 0, 2, 65.00, 130.00),
(195, 125, 25, 0, 0, 2, 0.00, 0.00),
(196, 126, 4, 0, 0, 2, 7.50, 15.00),
(197, 127, 2, 0, 0, 3, 7.50, 22.50),
(198, 127, 4, 0, 0, 3, 7.50, 22.50),
(199, 128, 15, 0, 0, 3, 0.00, 0.00),
(200, 129, 1, 0, 0, 3, 65.00, 195.00),
(201, 129, 4, 0, 0, 3, 7.50, 22.50),
(202, 130, 3, 0, 0, 100, 10.00, 1000.00),
(203, 133, 1, 0, 0, 3, 65.00, 195.00),
(204, 133, 4, 0, 0, 2, 7.50, 15.00),
(205, 134, 3, 0, 0, 2, 10.00, 20.00),
(206, 135, 1, 0, 0, 5, 65.00, 325.00),
(207, 135, 2, 0, 0, 6, 7.50, 45.00),
(208, 135, 3, 0, 0, 5, 10.00, 50.00),
(209, 135, 4, 0, 0, 6, 7.50, 45.00),
(210, 139, 1, 0, 0, 30, 65.00, 1950.00),
(211, 139, 2, 0, 0, 100, 7.50, 750.00),
(212, 139, 3, 0, 0, 30, 10.00, 300.00),
(213, 139, 4, 0, 0, 100, 7.50, 750.00),
(214, 141, 1, 0, 0, 10, 65.00, 650.00),
(215, 141, 2, 0, 0, 50, 7.50, 375.00),
(216, 141, 4, 0, 0, 10, 7.50, 75.00),
(217, 141, 5, 0, 0, 1, 0.00, 0.00),
(218, 141, 6, 0, 0, 1, 0.00, 0.00),
(219, 141, 7, 0, 0, 2, 0.00, 0.00),
(220, 141, 8, 0, 0, 3, 0.00, 0.00),
(221, 141, 9, 0, 0, 3, 0.00, 0.00),
(222, 141, 10, 0, 0, 1, 0.00, 0.00),
(223, 141, 13, 0, 0, 1, 0.00, 0.00),
(224, 141, 18, 0, 0, 1, 0.00, 0.00),
(225, 141, 21, 0, 0, 1, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_extensions`
--

DROP TABLE IF EXISTS `booking_extensions`;
CREATE TABLE IF NOT EXISTS `booking_extensions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `extension_hours` int UNSIGNED NOT NULL COMMENT 'Number of additional hours requested',
  `extension_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Cost for the extension (hours * hourly_rate)',
  `extension_reason` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','approved','rejected','completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending' COMMENT 'Status of the extension request',
  `requested_by_id` int NOT NULL,
  `requested_by` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Name of the person requesting extension',
  `requested_at` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `payment_status` enum('pending','received','waived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending' COMMENT 'Payment status for the extension',
  `payment_order_generated` tinyint(1) DEFAULT '0' COMMENT 'Whether a payment order has been generated',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_requested_by_id` (`requested_by_id`),
  KEY `idx_approved_by` (`approved_by`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_extensions`
--

INSERT INTO `booking_extensions` (`id`, `booking_id`, `extension_hours`, `extension_cost`, `extension_reason`, `status`, `requested_by_id`, `requested_by`, `requested_at`, `approved_by`, `approved_at`, `payment_status`, `payment_order_generated`, `created_at`, `updated_at`) VALUES
(1, 87, 2, 1200.00, 'no timsad asd asd as', 'completed', 21, 'user', '2025-11-28 04:43:33', 2, '2025-11-28 04:48:32', 'received', 0, '2025-11-28 04:43:33', '2025-11-28 05:01:03'),
(2, 85, 2, 1200.00, 'sada sa', 'completed', 27, 'jomel', '2025-11-28 04:52:59', 2, '2025-11-28 04:53:25', 'received', 0, '2025-11-28 04:52:59', '2025-11-28 04:57:57'),
(3, 84, 2, 1200.00, 'das sadaasd sa', 'completed', 27, 'jomel', '2025-11-28 05:07:28', 2, '2025-11-28 05:38:59', 'received', 0, '2025-11-28 05:07:28', '2025-11-28 05:39:38'),
(4, 71, 2, 1200.00, 'dasd', 'pending', 27, 'jomel', '2025-11-28 05:08:50', NULL, NULL, 'pending', 0, '2025-11-28 05:08:50', '2025-11-28 05:08:50'),
(5, 84, 3, 1800.00, 'sad sd', 'pending', 27, 'jomel', '2025-11-28 05:38:11', NULL, NULL, 'pending', 0, '2025-11-28 05:38:11', '2025-11-28 05:38:11'),
(6, 91, 2, 1100.00, 'da wad', 'completed', 21, 'user', '2025-12-01 00:11:12', 2, '2025-12-01 00:11:43', 'received', 0, '2025-12-01 00:11:12', '2025-12-01 00:15:38'),
(7, 89, 2, 1200.00, 'daasd asd asd asd asd', 'completed', 21, 'user', '2025-12-01 00:18:25', 2, '2025-12-01 00:18:46', 'received', 0, '2025-12-01 00:18:25', '2025-12-01 00:45:59'),
(8, 89, 1, 600.00, 'DASD ASD', 'pending', 21, 'user', '2025-12-01 00:51:18', NULL, NULL, 'pending', 0, '2025-12-01 00:51:18', '2025-12-01 00:51:18'),
(9, 89, 1, 600.00, 'ASD ADSA', 'pending', 21, 'user', '2025-12-01 00:51:29', NULL, NULL, 'pending', 0, '2025-12-01 00:51:29', '2025-12-01 00:51:29'),
(10, 89, 1, 600.00, 's adas dsa', 'pending', 21, 'user', '2025-12-01 00:52:46', NULL, NULL, 'pending', 0, '2025-12-01 00:52:46', '2025-12-01 00:52:46'),
(11, 89, 1, 600.00, 'sa das d', 'pending', 21, 'user', '2025-12-01 00:52:58', NULL, NULL, 'pending', 0, '2025-12-01 00:52:58', '2025-12-01 00:52:58'),
(12, 89, 1, 600.00, 'asd as', 'pending', 21, 'user', '2025-12-01 00:53:12', NULL, NULL, 'pending', 0, '2025-12-01 00:53:12', '2025-12-01 00:53:12'),
(13, 131, 1, 700.00, 'n/a', 'completed', 27, 'jomel', '2025-12-02 05:12:58', 2, '2025-12-02 05:26:09', 'received', 0, '2025-12-02 05:12:58', '2025-12-02 05:27:09'),
(14, 100, 2, 600.00, 'asd asd asd as', 'completed', 27, 'jomel', '2025-12-02 06:29:37', 2, '2025-12-02 06:30:03', 'received', 0, '2025-12-02 06:29:37', '2025-12-02 06:30:23'),
(15, 70, 3, 2100.00, '', 'completed', 27, 'jomel', '2025-12-02 06:33:26', 2, '2025-12-02 06:34:15', 'received', 0, '2025-12-02 06:33:26', '2025-12-02 06:34:31');

-- --------------------------------------------------------

--
-- Table structure for table `booking_files`
--

DROP TABLE IF EXISTS `booking_files`;
CREATE TABLE IF NOT EXISTS `booking_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `uploaded_by` int DEFAULT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_files`
--

INSERT INTO `booking_files` (`id`, `booking_id`, `file_type`, `original_filename`, `stored_filename`, `file_path`, `file_size`, `mime_type`, `uploaded_by`, `upload_date`, `status`) VALUES
(5, 51, 'receipt', '1C8723CABCEB.png', '1763557665_444116e39172c36e0596.png', 'C:\\wamp64\\www\\presentables-main\\writable\\uploads/booking_files/51/1763557665_444116e39172c36e0596.png', 436, 'image/png', NULL, '2025-11-19 05:07:45', 'pending'),
(14, 70, 'moa', '~$apter-4 (1).docx', '1764196752_e85fae536b9baa4e8286.bin', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/70/1764196752_e85fae536b9baa4e8286.bin', 162, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NULL, '2025-11-26 22:39:12', 'pending'),
(15, 79, 'receipt', 'CHAPTER 4 (1).pdf', 'bff132b80b8b39110fba1f8d95db521a.pdf', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/79/bff132b80b8b39110fba1f8d95db521a.pdf', 1357712, 'application/pdf', NULL, '2025-11-27 16:56:13', 'pending'),
(16, 79, 'receipt', 'CHAPTER 4 (1).pdf', '5d85daa9039a982720ba759c3e905314.pdf', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/79/5d85daa9039a982720ba759c3e905314.pdf', 1357712, 'application/pdf', NULL, '2025-11-27 16:57:09', 'pending'),
(17, 76, 'receipt', 'CHAPTER 4 (1).pdf', 'a425d771112749b8f7df4731aa346c0c.pdf', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/76/a425d771112749b8f7df4731aa346c0c.pdf', 1357712, 'application/pdf', NULL, '2025-11-27 16:58:18', 'pending'),
(18, 79, 'receipt', '1C8723CABCEB (2).png', 'adfcf0b6b74e697cc210c492cda97bb3.png', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/79/adfcf0b6b74e697cc210c492cda97bb3.png', 436, 'image/png', NULL, '2025-11-27 16:58:24', 'pending'),
(19, 79, 'receipt', '1C8723CABCEB (2).png', '85d2bb64a00fa36c603bcea307d3978c.png', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/79/85d2bb64a00fa36c603bcea307d3978c.png', 436, 'image/png', NULL, '2025-11-27 16:59:25', 'pending'),
(20, 79, 'receipt', 'CHAPTER 4 (1).pdf', 'a6d896f8c48b7d3c90bfa170242e50a2.pdf', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/79/a6d896f8c48b7d3c90bfa170242e50a2.pdf', 1357712, 'application/pdf', NULL, '2025-11-27 17:00:11', 'pending'),
(21, 77, 'receipt', '1C8723CABCEB (2) (1).png', 'a828014cfd7f834c61d6cd1a606ce140.png', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/77/a828014cfd7f834c61d6cd1a606ce140.png', 436, 'image/png', NULL, '2025-11-27 17:19:57', 'pending'),
(22, 86, 'receipt', 'CHAPTER 4 (2).pdf', 'ba8487edb27c1c7bc757a59a964d3a6b.pdf', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/86/ba8487edb27c1c7bc757a59a964d3a6b.pdf', 1357712, 'application/pdf', NULL, '2025-11-27 20:34:30', 'pending'),
(23, 91, 'receipt', 'WallPaper.jpeg', '430d75143e3508cfb6170ef8d983e885.jpeg', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/booking_files/91/430d75143e3508cfb6170ef8d983e885.jpeg', 44504, 'image/jpeg', NULL, '2025-11-30 16:49:02', 'pending'),
(25, 102, 'receipt', 'CHAPTER 4 (3) (1).pdf', '87ce9df6d17776d1e8b75a4621893097.pdf', 'C:\\wamp64\\www\\capst\\writable\\uploads/booking_files/102/87ce9df6d17776d1e8b75a4621893097.pdf', 1357712, 'application/pdf', NULL, '2025-12-01 18:19:44', 'pending'),
(26, 131, 'receipt', 'CHAPTER 4 (3) (1) (1).pdf', '166eb648fdcc6e5b44006f69245b03d0.pdf', 'C:\\wamp64\\www\\capst\\writable\\uploads/booking_files/131/166eb648fdcc6e5b44006f69245b03d0.pdf', 1357712, 'application/pdf', NULL, '2025-12-01 21:13:42', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `booking_survey_responses`
--

DROP TABLE IF EXISTS `booking_survey_responses`;
CREATE TABLE IF NOT EXISTS `booking_survey_responses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `survey_token` varchar(64) NOT NULL,
  `staff_punctuality` varchar(50) DEFAULT NULL,
  `staff_courtesy_property` varchar(50) DEFAULT NULL,
  `staff_courtesy_audio` varchar(50) DEFAULT NULL,
  `staff_courtesy_janitor` varchar(50) DEFAULT NULL,
  `facility_level_expectations` varchar(50) DEFAULT NULL,
  `facility_cleanliness` varchar(50) DEFAULT NULL,
  `facility_maintenance` varchar(50) DEFAULT NULL,
  `venue_accuracy_setup` varchar(50) DEFAULT NULL,
  `venue_accuracy_space` varchar(50) DEFAULT NULL,
  `catering_quality` varchar(50) DEFAULT NULL,
  `catering_presentation` varchar(50) DEFAULT NULL,
  `catering_service` varchar(50) DEFAULT NULL,
  `overall_satisfaction` varchar(50) DEFAULT NULL,
  `most_enjoyed` longtext,
  `improvements_needed` longtext,
  `recommendation` varchar(50) DEFAULT NULL,
  `is_submitted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_token` (`survey_token`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipment_key` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('furniture','audio_visual','lighting','technical','logistics') NOT NULL DEFAULT 'furniture' COMMENT 'Equipment category',
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL COMMENT 'Purchase/replacement price',
  `rate` decimal(10,2) DEFAULT '0.00' COMMENT 'Rental rate per unit',
  `unit` varchar(50) DEFAULT 'per piece',
  `good` int DEFAULT '0',
  `damaged` int DEFAULT '0',
  `available` int DEFAULT '0' COMMENT 'Calculated: good - rented',
  `rented` int DEFAULT '0',
  `status` enum('available','out_of_stock','low_stock','maintenance','retired') DEFAULT 'available',
  `is_trackable` tinyint(1) DEFAULT '1' COMMENT 'Requires inventory tracking',
  `is_rentable` tinyint(1) DEFAULT '1' COMMENT 'Can be rented separately',
  `is_plan_includable` tinyint(1) DEFAULT '1' COMMENT 'Can be included in plans',
  `minimum_quantity` int DEFAULT '5' COMMENT 'Low stock alert threshold',
  `notes` text COMMENT 'Additional notes',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipment_key_UNIQUE` (`equipment_key`),
  KEY `idx_category` (`category`),
  KEY `idx_trackable` (`is_trackable`),
  KEY `idx_rentable` (`is_rentable`),
  KEY `idx_status` (`status`),
  KEY `idx_equipment_category_available` (`category`,`available`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `equipment_key`, `name`, `category`, `quantity`, `price`, `rate`, `unit`, `good`, `damaged`, `available`, `rented`, `status`, `is_trackable`, `is_rentable`, `is_plan_includable`, `minimum_quantity`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'tables', 'Tables', 'furniture', 200, 0.00, 65.00, 'per piece', 2, 0, 0, 13, 'available', 1, 1, 1, 20, 'Rentable tables - ₱65 per piece', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(2, 'monobloc-chairs', 'Monobloc Chairs', 'furniture', 500, 0.00, 7.50, 'per piece', 0, 2, 0, 33, 'available', 1, 1, 1, 50, 'Rentable chairs - ₱7.50 per piece', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(3, 'table-covers', 'Table Covers', 'furniture', 100, 0.00, 10.00, 'per piece', 3, 0, 0, 11, 'available', 1, 1, 1, 10, 'Table decorations - ₱10 per piece', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(4, 'chair-covers', 'Chair Covers', 'furniture', 296, 0.00, 7.50, 'per piece', 3, 2, 0, 42, 'available', 1, 1, 1, 20, 'Chair decorations - ₱7.50 per piece', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(5, 'lectern/podium', 'Lectern/Podium', 'furniture', 6, 0.00, 0.00, 'included', 6, 0, 6, 0, 'available', 1, 0, 1, 1, 'Included with venue packages', '2025-10-16 00:35:06', '2025-11-04 18:15:06'),
(6, 'multimedia-projector', 'Multimedia Projector', 'audio_visual', 10, 0.00, 0.00, 'included', 0, 7, 0, 0, 'available', 1, 0, 1, 2, 'Included with venue packages', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(7, 'microphone', 'Microphone', 'audio_visual', 20, 0.00, 0.00, 'included', 2, 2, 2, 0, 'available', 1, 0, 1, 5, 'Basic microphones included', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(8, 'parled-backdrop-lights', 'PARLED Backdrop Lights', 'lighting', 20, 0.00, 0.00, 'per piece', 10, 5, 10, 0, 'available', 1, 0, 1, 5, 'Used in Basic/Mid/Full setups - 10pcs per setup', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(9, 'frontal-lighting', 'Frontal Lighting', 'lighting', 26, 0.00, 0.00, 'per piece', 2, 2, 2, 0, 'available', 1, 0, 1, 10, '6-16pcs depending on setup', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(10, 'moving-head-lights', 'Moving Head Lights', 'lighting', 10, 0.00, 0.00, 'per unit', 4, 0, 4, 0, 'available', 1, 0, 1, 2, '2-4 units in Mid/Full/Ballroom setups', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(11, 'smoke-machine', 'Smoke Machine', 'technical', 3, 0.00, 0.00, 'per piece', 1, 0, 1, 0, 'available', 1, 0, 1, 1, 'Used in Mid/Full/Ballroom setups', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(12, 'strobe-light', 'Strobe Light', 'lighting', 3, 0.00, 0.00, 'per piece', 1, 0, 1, 0, 'available', 1, 0, 1, 1, 'Used in Full/Ballroom setups', '2025-10-16 00:35:06', '2025-12-02 06:08:03'),
(13, 'full-range-speaker-system', 'Full Range Speaker System', 'audio_visual', 12, 0.00, 0.00, 'per unit', 12, 0, 12, 0, 'available', 1, 0, 1, 3, 'Ballroom setup - 3 pairs per side (6 units)', '2025-10-16 00:35:06', NULL),
(14, 'powered-18\"-subwoofer', 'Powered 18\" Subwoofer', 'audio_visual', 4, 0.00, 0.00, 'per unit', 4, 0, 4, 0, 'available', 1, 0, 1, 1, 'Ballroom setup - 1 pair (2 units)', '2025-10-16 00:35:06', '2025-11-03 17:25:07'),
(15, 'powered-15\"-monitor-speaker', 'Powered 15\" Monitor Speaker', 'audio_visual', 6, 0.00, 0.00, 'per unit', 6, 0, 6, 0, 'available', 1, 0, 1, 2, 'Ballroom setup - 3 units', '2025-10-16 00:35:06', NULL),
(16, 'drum-set-microphone', 'Drum Set Microphone', 'audio_visual', 2, 0.00, 0.00, 'per set', 2, 0, 2, 0, 'available', 1, 0, 1, 1, 'For live band setup', '2025-10-16 00:35:06', NULL),
(17, 'wired-microphone', 'Wired Microphone', 'audio_visual', 15, 0.00, 0.00, 'per piece', 15, 0, 15, 0, 'available', 1, 0, 1, 5, 'Ballroom setup - 4 units', '2025-10-16 00:35:06', '2025-10-22 08:07:07'),
(18, 'wireless-microphone', 'Wireless Microphone', 'audio_visual', 10, 0.00, 0.00, 'per piece', 10, 0, 10, 0, 'available', 1, 0, 1, 3, 'Ballroom setup - 2 units', '2025-10-16 00:35:06', NULL),
(19, 'follow-spot', 'Follow Spot', 'lighting', 4, 0.00, 0.00, 'per unit', 4, 0, 4, 0, 'available', 1, 0, 1, 2, 'Ballroom setup - 2 units', '2025-10-16 00:35:06', '2025-11-04 01:46:49'),
(20, 'intercom-system', 'Intercom System', 'technical', 2, 0.00, 0.00, 'per set', 2, 0, 2, 0, 'available', 1, 0, 1, 1, 'Ballroom communication system', '2025-10-16 00:35:06', NULL),
(21, 'projector-screen-(white)', 'Projector Screen (White)', 'audio_visual', 5, 0.00, 0.00, 'per piece', 5, 0, 5, 0, 'available', 1, 0, 1, 2, 'Ballroom setup - Left & Right side', '2025-10-16 00:35:06', '2025-10-20 18:36:23'),
(22, 'heavy-duty-electric-fans', 'Heavy Duty Electric Fans', 'technical', 30, 0.00, 0.00, 'included', 30, 0, 30, 0, 'available', 0, 0, 0, 10, 'Gymnasium cooling', '2025-10-16 00:35:06', NULL),
(23, 'air-conditioner', 'Air Conditioner', 'technical', 20, 0.00, 0.00, 'included', 20, 0, 20, 0, 'available', 0, 0, 0, 5, 'Auditorium/AVR cooling', '2025-10-16 00:35:06', NULL),
(24, 'tv-monitor', 'TV Monitor', 'audio_visual', 10, 0.00, 0.00, 'included', 10, 0, 10, 0, 'available', 1, 0, 1, 2, 'TV monitor for classrooms', '2025-10-16 03:07:23', NULL),
(25, NULL, 'shoes', 'furniture', 122, 232.00, 0.00, 'per piece', 122, 0, 122, 0, 'available', 1, 1, 1, 5, NULL, '2025-10-17 00:33:08', '2025-10-21 04:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_schedule`
--

DROP TABLE IF EXISTS `equipment_schedule`;
CREATE TABLE IF NOT EXISTS `equipment_schedule` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `equipment_id` int NOT NULL,
  `event_date` date NOT NULL,
  `total_quantity` int NOT NULL DEFAULT '0',
  `booked_quantity` int NOT NULL DEFAULT '0',
  `available_quantity` int GENERATED ALWAYS AS ((`total_quantity` - `booked_quantity`)) STORED,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_equipment_date` (`equipment_id`,`event_date`),
  KEY `idx_equipment_id` (`equipment_id`),
  KEY `idx_event_date` (`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_schedule`
--

INSERT INTO `equipment_schedule` (`id`, `equipment_id`, `event_date`, `total_quantity`, `booked_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-11-29', 187, 0, '2025-11-27 20:12:38', '2025-11-27 20:12:38'),
(2, 2, '2025-11-29', 467, 0, '2025-11-27 20:12:38', '2025-11-27 20:12:38'),
(3, 3, '2025-11-29', 89, 0, '2025-11-27 20:12:38', '2025-11-27 20:12:38'),
(4, 4, '2025-11-29', 0, 0, '2025-11-27 20:12:38', '2025-11-27 20:12:38'),
(5, 25, '2025-11-29', 122, 0, '2025-11-27 20:12:38', '2025-11-27 20:12:38'),
(6, 1, '2026-03-01', 187, 0, '2025-11-27 20:14:40', '2025-11-27 20:14:40'),
(7, 2, '2026-03-01', 467, 0, '2025-11-27 20:14:40', '2025-11-27 20:14:40'),
(8, 3, '2026-03-01', 89, 0, '2025-11-27 20:14:40', '2025-11-27 20:14:40'),
(9, 4, '2026-03-01', 252, 0, '2025-11-27 20:14:40', '2025-11-27 20:14:40'),
(10, 25, '2026-03-01', 122, 0, '2025-11-27 20:14:40', '2025-11-27 20:14:40'),
(11, 1, '2025-11-28', 187, 0, '2025-11-27 21:20:02', '2025-11-27 21:20:02'),
(12, 2, '2025-11-28', 467, 0, '2025-11-27 21:20:03', '2025-11-27 21:20:03'),
(13, 3, '2025-11-28', 89, 0, '2025-11-27 21:20:03', '2025-11-27 21:20:03'),
(14, 4, '2025-11-28', 252, 0, '2025-11-27 21:20:03', '2025-11-27 21:20:03'),
(15, 25, '2025-11-28', 122, 0, '2025-11-27 21:20:03', '2025-11-27 21:20:03'),
(16, 1, '2025-12-05', 187, 0, '2025-11-27 21:23:31', '2025-11-27 21:23:31'),
(17, 2, '2025-12-05', 467, 0, '2025-11-27 21:23:31', '2025-11-27 21:23:31'),
(18, 3, '2025-12-05', 89, 0, '2025-11-27 21:23:31', '2025-11-27 21:23:31'),
(19, 4, '2025-12-05', 252, 0, '2025-11-27 21:23:31', '2025-11-27 21:23:31'),
(20, 25, '2025-12-05', 122, 0, '2025-11-27 21:23:31', '2025-11-27 21:23:31'),
(21, 1, '2026-01-10', 187, 4, '2025-11-27 21:23:38', '2025-12-01 00:10:16'),
(22, 2, '2026-01-10', 467, 0, '2025-11-27 21:23:38', '2025-11-27 21:23:38'),
(23, 3, '2026-01-10', 89, 0, '2025-11-27 21:23:38', '2025-11-27 21:23:38'),
(24, 4, '2026-01-10', 252, 5, '2025-11-27 21:23:38', '2025-12-01 00:10:16'),
(25, 25, '2026-01-10', 122, 0, '2025-11-27 21:23:38', '2025-11-27 21:23:38'),
(26, 1, '2026-01-28', 187, 0, '2025-11-27 21:24:08', '2025-11-27 21:24:08'),
(27, 2, '2026-01-28', 467, 0, '2025-11-27 21:24:08', '2025-11-27 21:24:08'),
(28, 3, '2026-01-28', 89, 0, '2025-11-27 21:24:08', '2025-11-27 21:24:08'),
(29, 4, '2026-01-28', 252, 0, '2025-11-27 21:24:08', '2025-11-27 21:24:08'),
(30, 25, '2026-01-28', 122, 0, '2025-11-27 21:24:08', '2025-11-27 21:24:08'),
(31, 1, '2026-01-11', 187, 4, '2025-11-27 21:27:21', '2025-11-30 23:07:57'),
(32, 2, '2026-01-11', 467, 0, '2025-11-27 21:27:21', '2025-11-27 21:27:21'),
(33, 3, '2026-01-11', 89, 0, '2025-11-27 21:27:21', '2025-11-27 21:27:21'),
(34, 4, '2026-01-11', 252, 5, '2025-11-27 21:27:21', '2025-11-30 23:07:57'),
(35, 25, '2026-01-11', 122, 0, '2025-11-27 21:27:21', '2025-11-27 21:27:21'),
(36, 1, '2026-02-10', 187, 0, '2025-11-27 21:27:26', '2025-11-27 21:27:26'),
(37, 2, '2026-02-10', 467, 0, '2025-11-27 21:27:26', '2025-11-27 21:27:26'),
(38, 3, '2026-02-10', 89, 0, '2025-11-27 21:27:26', '2025-11-27 21:27:26'),
(39, 4, '2026-02-10', 252, 0, '2025-11-27 21:27:26', '2025-11-27 21:27:26'),
(40, 25, '2026-02-10', 122, 0, '2025-11-27 21:27:26', '2025-11-27 21:27:26'),
(41, 1, '2026-03-07', 187, 0, '2025-11-27 21:27:31', '2025-11-27 21:27:31'),
(42, 2, '2026-03-07', 467, 0, '2025-11-27 21:27:31', '2025-11-27 21:27:31'),
(43, 3, '2026-03-07', 89, 0, '2025-11-27 21:27:31', '2025-11-27 21:27:31'),
(44, 4, '2026-03-07', 252, 0, '2025-11-27 21:27:31', '2025-11-27 21:27:31'),
(45, 25, '2026-03-07', 122, 0, '2025-11-27 21:27:31', '2025-11-27 21:27:31'),
(46, 1, '2026-03-28', 187, 0, '2025-11-27 21:27:40', '2025-11-27 21:27:40'),
(47, 2, '2026-03-28', 467, 2, '2025-11-27 21:27:40', '2025-11-28 01:57:55'),
(48, 3, '2026-03-28', 89, 2, '2025-11-27 21:27:40', '2025-11-28 01:57:55'),
(49, 4, '2026-03-28', 252, 0, '2025-11-27 21:27:40', '2025-11-27 21:27:40'),
(50, 25, '2026-03-28', 122, 0, '2025-11-27 21:27:40', '2025-11-27 21:27:40'),
(51, 1, '2025-12-27', 187, 3, '2025-11-27 21:29:00', '2025-11-30 14:41:28'),
(52, 2, '2025-12-27', 467, 2, '2025-11-27 21:29:00', '2025-11-27 21:30:34'),
(53, 3, '2025-12-27', 89, 3, '2025-11-27 21:29:00', '2025-11-30 14:41:28'),
(54, 4, '2025-12-27', 252, 3, '2025-11-27 21:29:00', '2025-11-30 14:41:28'),
(55, 25, '2025-12-27', 122, 0, '2025-11-27 21:29:00', '2025-11-27 21:29:00'),
(56, 1, '0002-12-27', 187, 0, '2025-11-27 21:32:06', '2025-11-27 21:32:06'),
(57, 2, '0002-12-27', 467, 0, '2025-11-27 21:32:06', '2025-11-27 21:32:06'),
(58, 3, '0002-12-27', 89, 0, '2025-11-27 21:32:06', '2025-11-27 21:32:06'),
(59, 4, '0002-12-27', 252, 0, '2025-11-27 21:32:06', '2025-11-27 21:32:06'),
(60, 25, '0002-12-27', 122, 0, '2025-11-27 21:32:06', '2025-11-27 21:32:06'),
(61, 1, '0020-12-27', 187, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(62, 2, '0020-12-27', 467, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(63, 3, '0020-12-27', 89, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(64, 4, '0020-12-27', 252, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(65, 25, '0020-12-27', 122, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(66, 1, '0202-12-27', 187, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(67, 2, '0202-12-27', 467, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(68, 3, '0202-12-27', 89, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(69, 4, '0202-12-27', 252, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(70, 25, '0202-12-27', 122, 0, '2025-11-27 21:32:07', '2025-11-27 21:32:07'),
(71, 1, '2025-12-02', 187, 5, '2025-11-27 21:59:15', '2025-11-28 02:52:58'),
(72, 2, '2025-12-02', 467, 2, '2025-11-27 21:59:15', '2025-11-28 04:24:12'),
(73, 3, '2025-12-02', 89, 4, '2025-11-27 21:59:15', '2025-11-28 04:24:12'),
(74, 4, '2025-12-02', 252, 2, '2025-11-27 21:59:15', '2025-11-28 02:52:58'),
(75, 25, '2025-12-02', 122, 0, '2025-11-27 21:59:15', '2025-11-27 21:59:15'),
(76, 1, '2025-12-01', 187, 0, '2025-11-27 23:47:44', '2025-11-27 23:47:44'),
(77, 2, '2025-12-01', 467, 0, '2025-11-27 23:47:44', '2025-11-27 23:47:44'),
(78, 3, '2025-12-01', 89, 0, '2025-11-27 23:47:44', '2025-11-27 23:47:44'),
(79, 4, '2025-12-01', 252, 0, '2025-11-27 23:47:44', '2025-11-27 23:47:44'),
(80, 25, '2025-12-01', 122, 0, '2025-11-27 23:47:44', '2025-11-27 23:47:44'),
(81, 1, '2026-01-04', 187, 0, '2025-11-27 23:47:55', '2025-11-27 23:47:55'),
(82, 2, '2026-01-04', 467, 0, '2025-11-27 23:47:55', '2025-11-27 23:47:55'),
(83, 3, '2026-01-04', 89, 0, '2025-11-27 23:47:55', '2025-11-27 23:47:55'),
(84, 4, '2026-01-04', 252, 0, '2025-11-27 23:47:55', '2025-11-27 23:47:55'),
(85, 25, '2026-01-04', 122, 0, '2025-11-27 23:47:55', '2025-11-27 23:47:55'),
(86, 1, '2026-03-29', 187, 2, '2025-11-27 23:48:29', '2025-11-27 23:48:31'),
(87, 2, '2026-03-29', 467, 0, '2025-11-27 23:48:29', '2025-11-27 23:48:29'),
(88, 3, '2026-03-29', 89, 0, '2025-11-27 23:48:29', '2025-11-27 23:48:29'),
(89, 4, '2026-03-29', 252, 2, '2025-11-27 23:48:29', '2025-11-27 23:48:31'),
(90, 25, '2026-03-29', 122, 0, '2025-11-27 23:48:29', '2025-11-27 23:48:29'),
(91, 1, '2025-12-04', 187, 2, '2025-11-28 01:44:38', '2025-11-28 01:44:38'),
(92, 4, '2025-12-04', 252, 3, '2025-11-28 01:44:38', '2025-11-28 01:44:38'),
(93, 1, '2026-02-02', 187, 2, '2025-11-28 02:00:26', '2025-11-28 02:00:26'),
(94, 4, '2026-02-02', 252, 2, '2025-11-28 02:00:26', '2025-11-28 02:00:26'),
(95, 1, '2026-02-24', 187, 2, '2025-11-28 02:09:13', '2025-11-28 02:09:13'),
(96, 4, '2026-02-24', 252, 2, '2025-11-28 02:09:13', '2025-11-28 02:09:13'),
(97, 1, '2025-12-07', 187, 0, '2025-11-28 06:40:26', '2025-11-28 06:40:26'),
(98, 2, '2025-12-07', 467, 0, '2025-11-28 06:40:26', '2025-11-28 06:40:26'),
(99, 3, '2025-12-07', 89, 0, '2025-11-28 06:40:26', '2025-11-28 06:40:26'),
(100, 4, '2025-12-07', 252, 0, '2025-11-28 06:40:26', '2025-11-28 06:40:26'),
(101, 25, '2025-12-07', 122, 0, '2025-11-28 06:40:26', '2025-11-28 06:40:26'),
(102, 1, '2026-02-07', 187, 0, '2025-11-28 06:40:53', '2025-11-28 06:40:53'),
(103, 2, '2026-02-07', 467, 0, '2025-11-28 06:40:53', '2025-11-28 06:40:53'),
(104, 3, '2026-02-07', 89, 0, '2025-11-28 06:40:53', '2025-11-28 06:40:53'),
(105, 4, '2026-02-07', 252, 0, '2025-11-28 06:40:53', '2025-11-28 06:40:53'),
(106, 25, '2026-02-07', 122, 0, '2025-11-28 06:40:53', '2025-11-28 06:40:53'),
(107, 1, '2026-02-12', 187, 3, '2025-11-28 06:41:33', '2025-11-28 06:41:50'),
(108, 2, '2026-02-12', 467, 0, '2025-11-28 06:41:33', '2025-11-28 06:41:33'),
(109, 3, '2026-02-12', 89, 0, '2025-11-28 06:41:33', '2025-11-28 06:41:33'),
(110, 4, '2026-02-12', 252, 4, '2025-11-28 06:41:33', '2025-11-28 06:41:50'),
(111, 25, '2026-02-12', 122, 0, '2025-11-28 06:41:33', '2025-11-28 06:41:33'),
(112, 1, '2025-12-13', 187, 0, '2025-11-30 13:47:29', '2025-11-30 13:47:29'),
(113, 2, '2025-12-13', 467, 0, '2025-11-30 13:47:29', '2025-11-30 13:47:29'),
(114, 3, '2025-12-13', 89, 0, '2025-11-30 13:47:29', '2025-11-30 13:47:29'),
(115, 4, '2025-12-13', 252, 0, '2025-11-30 13:47:29', '2025-11-30 13:47:29'),
(116, 25, '2025-12-13', 122, 0, '2025-11-30 13:47:29', '2025-11-30 13:47:29'),
(117, 1, '2025-12-17', 187, 30, '2025-11-30 13:47:34', '2025-12-14 18:44:55'),
(118, 2, '2025-12-17', 467, 100, '2025-11-30 13:47:34', '2025-12-14 18:44:55'),
(119, 3, '2025-12-17', 89, 30, '2025-11-30 13:47:34', '2025-12-14 18:44:55'),
(120, 4, '2025-12-17', 252, 100, '2025-11-30 13:47:34', '2025-12-14 18:44:55'),
(121, 25, '2025-12-17', 122, 0, '2025-11-30 13:47:34', '2025-11-30 13:47:34'),
(122, 1, '2025-12-26', 187, 0, '2025-11-30 13:47:36', '2025-11-30 13:47:36'),
(123, 2, '2025-12-26', 467, 0, '2025-11-30 13:47:36', '2025-11-30 13:47:36'),
(124, 3, '2025-12-26', 89, 2, '2025-11-30 13:47:36', '2025-12-12 09:56:54'),
(125, 4, '2025-12-26', 252, 0, '2025-11-30 13:47:36', '2025-11-30 13:47:36'),
(126, 25, '2025-12-26', 122, 0, '2025-11-30 13:47:36', '2025-11-30 13:47:36'),
(127, 1, '2026-01-18', 187, 3, '2025-12-01 03:13:18', '2025-12-01 03:13:18'),
(128, 4, '2026-01-18', 252, 3, '2025-12-01 03:13:18', '2025-12-01 03:13:18'),
(129, 1, '2025-12-25', 187, 0, '2025-12-01 03:30:21', '2025-12-01 03:30:21'),
(130, 2, '2025-12-25', 467, 0, '2025-12-01 03:30:21', '2025-12-01 03:30:21'),
(131, 3, '2025-12-25', 89, 0, '2025-12-01 03:30:21', '2025-12-01 03:30:21'),
(132, 4, '2025-12-25', 252, 0, '2025-12-01 03:30:21', '2025-12-01 03:30:21'),
(133, 25, '2025-12-25', 122, 0, '2025-12-01 03:30:21', '2025-12-01 03:30:21'),
(134, 1, '2025-12-30', 187, 3, '2025-12-01 03:30:42', '2025-12-12 09:42:15'),
(135, 2, '2025-12-30', 467, 0, '2025-12-01 03:30:42', '2025-12-01 03:30:42'),
(136, 3, '2025-12-30', 89, 0, '2025-12-01 03:30:42', '2025-12-01 03:30:42'),
(137, 4, '2025-12-30', 252, 2, '2025-12-01 03:30:42', '2025-12-12 09:42:15'),
(138, 25, '2025-12-30', 122, 0, '2025-12-01 03:30:42', '2025-12-01 03:30:42'),
(139, 4, '2026-01-03', 252, 4, '2025-12-02 01:36:34', '2025-12-02 01:36:34'),
(140, 3, '2025-12-15', 89, 100, '2025-12-02 04:38:14', '2025-12-02 04:38:14'),
(141, 1, '2025-12-15', 187, 0, '2025-12-02 04:46:01', '2025-12-02 04:46:01'),
(142, 2, '2025-12-15', 467, 0, '2025-12-02 04:46:01', '2025-12-02 04:46:01'),
(143, 4, '2025-12-15', 252, 0, '2025-12-02 04:46:01', '2025-12-02 04:46:01'),
(144, 25, '2025-12-15', 122, 0, '2025-12-02 04:46:01', '2025-12-02 04:46:01'),
(145, 1, '2026-01-03', 187, 0, '2025-12-02 04:46:06', '2025-12-02 04:46:06'),
(146, 2, '2026-01-03', 467, 0, '2025-12-02 04:46:06', '2025-12-02 04:46:06'),
(147, 3, '2026-01-03', 89, 0, '2025-12-02 04:46:06', '2025-12-02 04:46:06'),
(148, 25, '2026-01-03', 122, 0, '2025-12-02 04:46:06', '2025-12-02 04:46:06'),
(149, 1, '2025-12-19', 187, 5, '2025-12-02 04:53:58', '2025-12-13 21:40:52'),
(150, 2, '2025-12-19', 467, 6, '2025-12-02 04:53:58', '2025-12-13 21:40:52'),
(151, 3, '2025-12-19', 89, 5, '2025-12-02 04:53:58', '2025-12-13 21:40:52'),
(152, 4, '2025-12-19', 252, 6, '2025-12-02 04:53:58', '2025-12-13 21:40:52'),
(153, 25, '2025-12-19', 122, 0, '2025-12-02 04:53:58', '2025-12-02 04:53:58'),
(154, 1, '2025-12-31', 187, 0, '2025-12-02 05:11:30', '2025-12-02 05:11:30'),
(155, 2, '2025-12-31', 467, 0, '2025-12-02 05:11:30', '2025-12-02 05:11:30'),
(156, 3, '2025-12-31', 89, 0, '2025-12-02 05:11:30', '2025-12-02 05:11:30'),
(157, 4, '2025-12-31', 252, 0, '2025-12-02 05:11:30', '2025-12-02 05:11:30'),
(158, 25, '2025-12-31', 122, 0, '2025-12-02 05:11:30', '2025-12-02 05:11:30'),
(159, 1, '2025-12-14', 187, 0, '2025-12-02 05:11:32', '2025-12-02 05:11:32'),
(160, 2, '2025-12-14', 467, 0, '2025-12-02 05:11:32', '2025-12-02 05:11:32'),
(161, 3, '2025-12-14', 89, 0, '2025-12-02 05:11:32', '2025-12-02 05:11:32'),
(162, 4, '2025-12-14', 252, 0, '2025-12-02 05:11:32', '2025-12-02 05:11:32'),
(163, 25, '2025-12-14', 122, 0, '2025-12-02 05:11:32', '2025-12-02 05:11:32'),
(164, 1, '2025-12-18', 0, 0, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(165, 2, '2025-12-18', 0, 0, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(166, 3, '2025-12-18', 0, 0, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(167, 4, '2025-12-18', 0, 0, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(168, 25, '2025-12-18', 122, 0, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(169, 1, '2026-03-24', 0, 0, '2025-12-12 04:19:18', '2025-12-12 04:19:18'),
(170, 2, '2026-03-24', 0, 0, '2025-12-12 04:19:18', '2025-12-12 04:19:18'),
(171, 3, '2026-03-24', 0, 0, '2025-12-12 04:19:18', '2025-12-12 04:19:18'),
(172, 4, '2026-03-24', 0, 0, '2025-12-12 04:19:18', '2025-12-12 04:19:18'),
(173, 25, '2026-03-24', 122, 0, '2025-12-12 04:19:18', '2025-12-12 04:19:18'),
(174, 1, '2026-01-31', 0, 0, '2025-12-14 16:07:31', '2025-12-14 16:07:31'),
(175, 2, '2026-01-31', 0, 0, '2025-12-14 16:07:31', '2025-12-14 16:07:31'),
(176, 3, '2026-01-31', 0, 0, '2025-12-14 16:07:31', '2025-12-14 16:07:31'),
(177, 4, '2026-01-31', 0, 0, '2025-12-14 16:07:31', '2025-12-14 16:07:31'),
(178, 25, '2026-01-31', 122, 0, '2025-12-14 16:07:31', '2025-12-14 16:07:31'),
(179, 1, '2025-12-22', 0, 0, '2025-12-14 18:20:47', '2025-12-14 18:20:47'),
(180, 2, '2025-12-22', 0, 0, '2025-12-14 18:20:47', '2025-12-14 18:20:47'),
(181, 3, '2025-12-22', 0, 0, '2025-12-14 18:20:47', '2025-12-14 18:20:47'),
(182, 4, '2025-12-22', 0, 0, '2025-12-14 18:20:47', '2025-12-14 18:20:47'),
(183, 25, '2025-12-22', 122, 0, '2025-12-14 18:20:47', '2025-12-14 18:20:47'),
(184, 1, '2025-12-21', 0, 0, '2025-12-14 19:04:36', '2025-12-14 19:04:36'),
(185, 2, '2025-12-21', 0, 0, '2025-12-14 19:04:36', '2025-12-14 19:04:36'),
(186, 3, '2025-12-21', 0, 0, '2025-12-14 19:04:36', '2025-12-14 19:04:36'),
(187, 4, '2025-12-21', 0, 0, '2025-12-14 19:04:36', '2025-12-14 19:04:36'),
(188, 25, '2025-12-21', 122, 0, '2025-12-14 19:04:36', '2025-12-14 19:04:36'),
(189, 1, '2025-12-28', 0, 0, '2025-12-14 20:14:57', '2025-12-14 20:14:57'),
(190, 2, '2025-12-28', 0, 0, '2025-12-14 20:14:57', '2025-12-14 20:14:57'),
(191, 3, '2025-12-28', 0, 0, '2025-12-14 20:14:57', '2025-12-14 20:14:57'),
(192, 4, '2025-12-28', 0, 0, '2025-12-14 20:14:57', '2025-12-14 20:14:57'),
(193, 25, '2025-12-28', 122, 0, '2025-12-14 20:14:57', '2025-12-14 20:14:57'),
(194, 1, '2026-01-05', 0, 0, '2025-12-14 20:23:13', '2025-12-14 20:23:13'),
(195, 2, '2026-01-05', 0, 0, '2025-12-14 20:23:13', '2025-12-14 20:23:13'),
(196, 3, '2026-01-05', 0, 0, '2025-12-14 20:23:13', '2025-12-14 20:23:13'),
(197, 4, '2026-01-05', 0, 0, '2025-12-14 20:23:13', '2025-12-14 20:23:13'),
(198, 25, '2026-01-05', 122, 0, '2025-12-14 20:23:13', '2025-12-14 20:23:13'),
(199, 1, '2025-12-20', 0, 0, '2025-12-14 20:27:20', '2025-12-14 20:27:20'),
(200, 2, '2025-12-20', 0, 0, '2025-12-14 20:27:20', '2025-12-14 20:27:20'),
(201, 3, '2025-12-20', 0, 0, '2025-12-14 20:27:20', '2025-12-14 20:27:20'),
(202, 4, '2025-12-20', 0, 0, '2025-12-14 20:27:20', '2025-12-14 20:27:20'),
(203, 25, '2025-12-20', 122, 0, '2025-12-14 20:27:20', '2025-12-14 20:27:20'),
(204, 1, '2025-12-23', 0, 0, '2025-12-14 20:31:40', '2025-12-14 20:31:40'),
(205, 2, '2025-12-23', 0, 0, '2025-12-14 20:31:40', '2025-12-14 20:31:40'),
(206, 3, '2025-12-23', 0, 0, '2025-12-14 20:31:40', '2025-12-14 20:31:40'),
(207, 4, '2025-12-23', 0, 0, '2025-12-14 20:31:40', '2025-12-14 20:31:40'),
(208, 25, '2025-12-23', 122, 0, '2025-12-14 20:31:40', '2025-12-14 20:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `facility_id` int NOT NULL,
  `facility_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `duration` varchar(50) NOT NULL,
  `attendees` int DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `special_requirements` text,
  `approval_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_notes` text,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `facility_id` (`facility_id`),
  KEY `event_date` (`event_date`),
  KEY `idx_event_date_status` (`event_date`,`status`),
  KEY `idx_facility_date` (`facility_id`,`event_date`),
  KEY `idx_status_date` (`status`,`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `booking_id`, `event_title`, `client_name`, `contact_number`, `email_address`, `organization`, `address`, `facility_id`, `facility_name`, `event_date`, `event_time`, `duration`, `attendees`, `total_cost`, `special_requirements`, `approval_date`, `approval_notes`, `status`, `created_at`, `updated_at`) VALUES
(8, 44, 'ADASASDA D', 'student', '1123123123123123', 'student@gmail.com', 'css', 'sadas wda dasd asd awd as', 1, '', '2025-11-16', '13:05:00', '4', 300, 7000.00, 'wda da', '2025-11-18 12:38:55', 'asdsa das ', 'scheduled', '2025-11-18 04:38:55', '2025-11-19 06:17:31'),
(9, 42, 'ad a das das', 'jasddjasadsja', '1231234123123', 'jomeltienes00@gmail.com', 'awda d', 'ad asdas dwad', 1, '', '2025-11-16', '04:11:00', '11', 6, 18545.00, 'a wdas sa das', '2025-11-19 03:21:10', '', 'completed', '2025-11-18 19:21:10', '2025-11-27 09:07:11'),
(10, 46, 'Student Orientation Seminar', 'Maria Santos', '09171234567', 'maria.santos@example.com', 'University Student Council', 'Iriga City, Camarines Sur', 2, 'University Gymnasium', '2025-11-14', '14:00:00', '4 hours', 150, 8000.00, 'Need additional chairs and tables', '2025-11-12 06:11:19', 'Approved for student event', 'completed', '2025-11-12 06:11:19', '2025-11-19 06:11:19'),
(11, 47, 'Annual Company Conference', 'Roberto Cruz', '09281234567', 'roberto.cruz@company.com', 'Tech Solutions Inc.', 'Naga City, Camarines Sur', 1, 'University Auditorium', '2025-11-12', '08:00:00', '8 hours', 300, 15000.00, 'Need full audio-visual setup', '2025-11-09 06:11:19', 'Approved - corporate event', 'completed', '2025-11-09 06:11:19', '2025-11-19 06:11:19'),
(12, 48, 'Community Birthday Celebration', 'Ana Reyes', '09391234567', 'ana.reyes@gmail.com', 'Community Organization', 'San Nicolas, Iriga City', 4, 'Function Hall (ACAD Bldg.)', '2025-11-09', '16:00:00', '4 hours', 80, 3000.00, 'Simple setup needed', '2025-11-07 06:11:19', 'Approved for community event', 'completed', '2025-11-07 06:11:19', '2025-11-19 06:11:19'),
(14, 41, 'adwdad', 'SIXTO', '673555075', 'jomeltienes00@gmail.com', 'css', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2025-11-27', '19:28:00', '4', 6, 10060.00, 'asd asd sad as', '2025-11-19 08:42:47', 'QDAS', 'scheduled', '2025-11-19 00:42:47', '2025-11-19 00:42:47'),
(15, 54, 'wd awd a', 'dwa daw', '09086343764', 'jomeltienes00@gmail.com', 'css', 'wadad aw awd', 2, '', '2025-11-28', '05:42:00', '5', 6, 10545.00, 'dadwd a', '2025-11-19 23:10:10', '', 'scheduled', '2025-11-19 15:10:10', '2025-11-19 15:10:10'),
(17, 68, 'asdawdad', 'dadwdawdas da', '673555075', 'sasdadaas@gmail.com', 'AWDAD', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-01-04', '05:26:00', '4', 2, 16020.00, 'asd asd asd asd', '2025-11-25 15:54:27', '', 'scheduled', '2025-11-25 15:54:27', '2025-11-25 15:54:27'),
(20, 71, 'dwad asd asdas', 'dwa daw sd ad as', '673555075', 'jomeltienes00@gmail.com', 'asd asd as', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-05-03', '16:35:00', '11', 5, 32062.50, 'asdasd asd asda wdas dasd asd', '2025-11-26 21:52:08', '', 'completed', '2025-11-26 21:52:08', '2025-12-01 22:08:03'),
(22, 64, 'ADASASDA D', 'kiela', '673555075', 'jomeltienees00@gmail.com', 'css', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-01-31', '04:59:00', '11', 4, 25895.00, 'asdas dasd asd as', '2025-11-26 22:37:52', '', 'scheduled', '2025-11-26 22:37:52', '2025-11-26 22:37:52'),
(23, 70, 'sixto xcxx', 'SIXTO', '673555075', 'jomeltienes00@gmail.com', 'css', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-01-10', '12:14:00', '11', 5, 26897.50, 'ads wda daw', '2025-11-26 22:38:27', '', 'scheduled', '2025-11-26 22:38:27', '2025-11-26 22:38:27'),
(24, 65, 'asdawdad', 'kiela', '673555075', 'jomeltienes00@gmail.com', 'akp', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2025-11-27', '05:10:00', '7', 4, 17530.00, 'aASDAS DSA DSA', '2025-11-26 22:49:41', '', 'scheduled', '2025-11-26 22:49:41', '2025-11-26 22:49:41'),
(25, 80, ' aasd asd ds as das', 'Desiree', '0908634373', 'faculty@cspc.edu.ph', 'd ad asads asd asd', 'ad asd sa asd as dasd as', 1, '', '2026-03-31', '23:00:00', '4', 3, 0.00, ' asd asd ads asd asd', '2025-11-27 17:23:06', '', 'scheduled', '2025-11-27 17:23:06', '2025-11-27 17:23:06'),
(28, 83, 'asdawdad', 'd aasdas', '0898979789', 'jomeltienes00@gmaill.com', 'css', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-03-28', '05:57:00', '11', 4, 34835.00, 'as dasd a', '2025-11-27 17:58:08', '', 'scheduled', '2025-11-27 17:58:08', '2025-11-27 17:58:08'),
(29, 84, '232aasd asd', 'dwa daw', '673555075', 'jomeltienes00@gmail.com', 'asads asd', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-02-02', '01:53:00', '7', 3, 11945.00, 'dasas das', '2025-11-27 18:00:34', '', 'scheduled', '2025-11-27 18:00:34', '2025-11-27 18:00:34'),
(30, 85, 'sad as d', 'kiela', '673555075', 'jomeltienes00@gmail.com', 'akp', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 1, '', '2026-02-24', '02:12:00', '8', 3, 32145.00, 'sa dasd as dsad sa', '2025-11-27 18:11:08', '', 'scheduled', '2025-11-27 18:11:08', '2025-11-27 18:11:08'),
(31, 79, 'aasd asdas', 'Desiree', '0908634373', 'faculty@cspc.edu.ph', 'dasd  da', 'ad asd as asd asd asd asdas asd asd assad ', 2, '', '2026-03-29', '15:47:00', '11', 4, 31795.00, 'a sdas ads', '2025-11-27 22:37:49', '', 'scheduled', '2025-11-27 22:37:49', '2025-11-27 22:37:49'),
(32, 93, 'asd asd as d', 'asd asda', '12312123121431', 'jomeltienes00@gmail.com', 'qeqwe qw', 'as dasd sad asda', 1, '', '2025-12-05', '14:06:00', '4', 5, 0.00, 'a das dasd asd a', '2025-11-30 19:06:08', '', 'scheduled', '2025-11-30 19:06:08', '2025-11-30 19:06:08'),
(33, 100, 'asda sdas', 'SIXTO', '12313114', 'jomeltienes00@gmail.com', 'css', 'asd asd asd asd as', 8, '', '2026-01-18', '03:18:00', '11', 4, 11417.50, 'asd asd', '2025-11-30 19:13:27', '', 'scheduled', '2025-11-30 19:13:27', '2025-11-30 19:13:27'),
(34, 102, 'as dasd asd', 'Desiree', '0908634373', 'faculty@cspc.edu.ph', 'css', 'asd as d asads asdas dsa', 8, '', '2025-12-30', '07:30:00', '8', 3, 12575.00, ' asdas', '2025-11-30 19:31:16', '', 'scheduled', '2025-11-30 19:31:16', '2025-11-30 19:31:16'),
(35, 131, 'BYCIT', 'jomel', '009808652', 'jomeltienes00@gmail.com', 'CSS', 'san niclas iriga city', 1, '', '2025-12-14', '09:11:00', '6', 300, 24100.00, 'n/a', '2025-12-01 21:28:00', '', 'scheduled', '2025-12-01 21:28:00', '2025-12-01 21:28:00'),
(36, 130, 'asdawdad', 'SIXTO', '+34673555075', 'jomeltienes00@gmail.com', 'AWDAD', 'Avenida La Cornisa 31 08392 Sant Andreu de Llavaneres', 4, '', '2025-12-15', '04:42:00', '7', 3, 10350.00, 'SAD ASDASD', '2025-12-11 20:15:39', '', 'scheduled', '2025-12-12 04:15:39', '2025-12-12 04:15:39');

-- --------------------------------------------------------

--
-- Table structure for table `event_guests`
--

DROP TABLE IF EXISTS `event_guests`;
CREATE TABLE IF NOT EXISTS `event_guests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(50) DEFAULT NULL,
  `qr_code` varchar(100) NOT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `attended` tinyint(1) DEFAULT '0',
  `attendance_time` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_code` (`qr_code`),
  KEY `booking_id` (`booking_id`),
  KEY `qr_code_2` (`qr_code`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_guests`
--

INSERT INTO `event_guests` (`id`, `booking_id`, `guest_name`, `guest_email`, `guest_phone`, `qr_code`, `qr_code_path`, `attended`, `attendance_time`, `created_at`) VALUES
(1, 42, 'jomel', 'jomeltienes00@gmail.com', '', 'F019F343A5B5', 'uploads/qr-codes/F019F343A5B5.png', 1, '2025-11-19 04:22:04', '2025-11-19 04:17:02'),
(2, 42, 'jomel', 'jotienes@gmail.com', '', '5E2D7BAA5665', 'uploads/qr-codes/5E2D7BAA5665.png', 0, NULL, '2025-11-19 04:30:02'),
(3, 42, 'jomel', 'jotienes@my.cspc.edu.ph', '', '248BBE229837', 'uploads/qr-codes/248BBE229837.png', 0, NULL, '2025-11-19 04:30:27'),
(4, 42, 'florian', 'monteflorian88@gmail.com', '', '5AAF8DFA021C', 'uploads/qr-codes/5AAF8DFA021C.png', 0, NULL, '2025-11-19 04:37:47'),
(5, 42, 'jomel', 'kurosecs@gmail.com', '', '1C8723CABCEB', 'uploads/qr-codes/1C8723CABCEB.png', 1, '2025-11-19 04:52:58', '2025-11-19 04:51:07'),
(6, 54, 'sixto', 'jomeltienes00@gmail.com', '090833413', '6942BEA30352', 'uploads/qr-codes/6942BEA30352.png', 1, '2025-11-24 16:46:58', '2025-11-19 23:11:23'),
(7, 54, 'jayvee', 'jayveesias@cspc.edu.ph', '673555075', '33B9918FBC43', 'uploads/qr-codes/33B9918FBC43.png', 1, '2025-11-20 01:57:03', '2025-11-20 01:50:36'),
(8, 131, 'jomel', 'jomeltienes00@gmail.com', '1231313123', '942F0773A561', 'uploads/qr-codes/942F0773A561.png', 1, '2025-12-02 05:49:49', '2025-12-02 05:28:25'),
(9, 130, 'jomel', 'monteflorian88@gmail.com', '', '726E549FBC69', 'uploads/qr-codes/726E549FBC69.png', 0, NULL, '2025-12-12 04:16:05'),
(10, 130, 'florian', 'jomeltienes00@gmail.com', 'asdas', '706ABDA61FA3', 'uploads/qr-codes/706ABDA61FA3.png', 1, '2025-12-12 04:18:24', '2025-12-12 04:16:31'),
(11, 130, 'jomel', 'jotienes@my.cspc.edu.ph', '673555075', '4729BA73131A', 'uploads/qr-codes/4729BA73131A.png', 1, '2025-12-12 10:30:52', '2025-12-12 10:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `extension_files`
--

DROP TABLE IF EXISTS `extension_files`;
CREATE TABLE IF NOT EXISTS `extension_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_extension_id` int UNSIGNED NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stored_filename` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_by` int NOT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_extension_id` (`booking_extension_id`),
  KEY `idx_uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `extension_files`
--

INSERT INTO `extension_files` (`id`, `booking_extension_id`, `original_filename`, `stored_filename`, `file_path`, `file_size`, `mime_type`, `uploaded_by`, `upload_date`, `created_at`, `updated_at`) VALUES
(1, 7, '1764520820_1a7a711da421238fe036.jpeg', '1764520820_1a7a711da421238fe036.jpeg', 'C:\\wamp64\\www\\CI4-PROJECT-main\\writable\\uploads/extensions/1764520820_1a7a711da421238fe036.jpeg', 44504, 'image/jpeg', 2, '2025-11-30 16:40:20', '2025-11-30 16:40:20', '2025-11-30 16:40:20'),
(3, 13, '1764624422_83bcaa69175bf04a7f98.pdf', '1764624422_83bcaa69175bf04a7f98.pdf', 'C:\\wamp64\\www\\capst\\writable\\uploads/extensions/1764624422_83bcaa69175bf04a7f98.pdf', 1357712, 'application/pdf', 2, '2025-12-01 21:27:02', '2025-12-01 21:27:02', '2025-12-01 21:27:02'),
(4, 14, '1764628217_9f5aa54873194d3d48ec.pdf', '1764628217_9f5aa54873194d3d48ec.pdf', 'C:\\wamp64\\www\\capst\\writable\\uploads/extensions/1764628217_9f5aa54873194d3d48ec.pdf', 1357712, 'application/pdf', 2, '2025-12-01 22:30:17', '2025-12-01 22:30:17', '2025-12-01 22:30:17'),
(5, 15, '1764628466_31978ad5a472087d32d0.pdf', '1764628466_31978ad5a472087d32d0.pdf', 'C:\\wamp64\\www\\capst\\writable\\uploads/extensions/1764628466_31978ad5a472087d32d0.pdf', 1357712, 'application/pdf', 2, '2025-12-01 22:34:26', '2025-12-01 22:34:26', '2025-12-01 22:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `facilitator_checklists`
--

DROP TABLE IF EXISTS `facilitator_checklists`;
CREATE TABLE IF NOT EXISTS `facilitator_checklists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `event_id` int NOT NULL,
  `facilitator_id` int NOT NULL,
  `facilitator_name` varchar(255) DEFAULT NULL,
  `notes` text,
  `signature` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_submitted_at` (`submitted_at`),
  KEY `idx_booking_event` (`booking_id`,`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilitator_checklists`
--

INSERT INTO `facilitator_checklists` (`id`, `booking_id`, `event_id`, `facilitator_id`, `facilitator_name`, `notes`, `signature`, `submitted_at`, `created_at`) VALUES
(1, 37, 6, 23, 'facilitator', NULL, NULL, '2025-11-19 13:47:23', '2025-11-19 13:47:23'),
(2, 42, 9, 20, 'studentss', NULL, NULL, '2025-11-19 16:42:36', '2025-11-19 16:42:36'),
(3, 38, 7, 23, 'facilitator', NULL, NULL, '2025-11-19 22:46:20', '2025-11-19 22:46:20'),
(4, 34, 21, 23, 'facilitator', NULL, NULL, '2025-11-27 06:34:50', '2025-11-27 06:34:50'),
(5, 44, 8, 23, 'facilitator', NULL, NULL, '2025-11-27 13:01:29', '2025-11-27 13:01:29'),
(6, 42, 9, 23, 'facilitator', NULL, NULL, '2025-11-27 17:07:09', '2025-11-27 17:07:09'),
(7, 54, 15, 23, 'facilitator', NULL, NULL, '2025-12-01 05:55:35', '2025-12-01 05:55:35'),
(8, 71, 20, 23, 'facilitator', NULL, NULL, '2025-12-02 06:07:25', '2025-12-02 06:07:25');

-- --------------------------------------------------------

--
-- Table structure for table `facilitator_checklist_items`
--

DROP TABLE IF EXISTS `facilitator_checklist_items`;
CREATE TABLE IF NOT EXISTS `facilitator_checklist_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `checklist_id` int NOT NULL,
  `equipment_id` int NOT NULL,
  `equipment_name` varchar(255) NOT NULL,
  `expected_quantity` int NOT NULL,
  `actual_quantity` int NOT NULL,
  `equipment_condition` enum('good','damaged','maintenance','missing') NOT NULL DEFAULT 'good',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `remarks` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `checklist_id` (`checklist_id`),
  KEY `equipment_id` (`equipment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilitator_checklist_items`
--

INSERT INTO `facilitator_checklist_items` (`id`, `checklist_id`, `equipment_id`, `equipment_name`, `expected_quantity`, `actual_quantity`, `equipment_condition`, `is_available`, `remarks`, `created_at`) VALUES
(1, 1, 6, 'Multimedia Projector', 1, 1, 'good', 1, '', '2025-11-19 13:47:23'),
(2, 1, 7, 'Microphone', 1, 1, 'damaged', 0, '', '2025-11-19 13:47:23'),
(3, 2, 4, 'Chair Covers', 6, 6, 'good', 1, '', '2025-11-19 16:42:36'),
(4, 2, 6, 'Multimedia Projector', 1, 1, 'good', 1, '', '2025-11-19 16:42:36'),
(5, 2, 7, 'Microphone', 2, 2, 'good', 1, '', '2025-11-19 16:42:36'),
(6, 2, 8, 'PARLED Backdrop Lights', 10, 10, 'good', 1, '', '2025-11-19 16:42:36'),
(7, 2, 9, 'Frontal Lighting', 6, 6, 'damaged', 0, '', '2025-11-19 16:42:36'),
(8, 3, 6, 'Multimedia Projector', 2, 2, 'good', 1, '', '2025-11-19 22:46:20'),
(9, 3, 7, 'Microphone', 2, 1, 'good', 1, '', '2025-11-19 22:46:20'),
(10, 4, 6, 'Multimedia Projector', 1, 1, 'damaged', 0, '', '2025-11-27 06:34:50'),
(11, 4, 7, 'Microphone', 2, 1, 'good', 1, '', '2025-11-27 06:34:50'),
(12, 5, 4, 'Chair Covers', 3, 1, 'good', 1, '', '2025-11-27 13:01:29'),
(13, 5, 6, 'Multimedia Projector', 1, 1, 'damaged', 0, '', '2025-11-27 13:01:29'),
(14, 5, 7, 'Microphone', 2, 1, 'good', 1, '', '2025-11-27 13:01:29'),
(15, 6, 4, 'Chair Covers', 6, 1, 'damaged', 0, '', '2025-11-27 17:07:09'),
(16, 6, 6, 'Multimedia Projector', 1, 1, 'damaged', 0, '', '2025-11-27 17:07:09'),
(17, 6, 7, 'Microphone', 2, 1, 'good', 1, '', '2025-11-27 17:07:09'),
(18, 6, 8, 'PARLED Backdrop Lights', 10, 1, 'damaged', 0, '', '2025-11-27 17:07:09'),
(19, 6, 9, 'Frontal Lighting', 6, 1, 'damaged', 0, '', '2025-11-27 17:07:09'),
(20, 7, 4, 'Chair Covers', 6, 1, 'damaged', 0, '', '2025-12-01 05:55:35'),
(21, 7, 6, 'Multimedia Projector', 1, 1, 'damaged', 0, '', '2025-12-01 05:55:35'),
(22, 7, 7, 'Microphone', 2, 1, 'good', 1, '', '2025-12-01 05:55:35'),
(23, 8, 2, 'Monobloc Chairs', 2, 1, 'damaged', 0, '', '2025-12-02 06:07:25'),
(24, 8, 3, 'Table Covers', 3, 1, 'good', 1, '', '2025-12-02 06:07:25'),
(25, 8, 4, 'Chair Covers', 3, 1, 'good', 1, '', '2025-12-02 06:07:25'),
(26, 8, 6, 'Multimedia Projector', 1, 1, 'damaged', 0, '', '2025-12-02 06:07:25'),
(27, 8, 7, 'Microphone', 2, 1, 'good', 1, '', '2025-12-02 06:07:25');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
CREATE TABLE IF NOT EXISTS `facilities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_key` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `is_maintenance` tinyint(1) DEFAULT '0',
  `additional_hours_rate` decimal(10,2) NOT NULL DEFAULT '500.00',
  `extended_hour_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facility_key` (`facility_key`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `facility_key`, `name`, `icon`, `description`, `is_active`, `is_maintenance`, `additional_hours_rate`, `extended_hour_rate`, `created_at`, `updated_at`) VALUES
(1, 'auditorium', 'University Auditorium', '🎭', 'miss ko na sha', 0, 0, 700.00, 500.00, '2025-10-15 23:18:37', '2025-12-12 10:43:12'),
(2, 'gymnasium', 'University Gymnasium', '🏀', '', 1, 0, 550.00, 500.00, '2025-10-15 23:18:37', '2025-11-30 07:22:19'),
(4, 'function-hall', 'Function Hall (ACAD Bldg.)', '🏛️', NULL, 1, 0, 450.00, NULL, '2025-10-15 23:18:37', '2025-11-25 22:42:34'),
(6, 'pearl-restaurant', 'Pearl Mini Restaurant', '🍽️', NULL, 1, 0, 400.00, NULL, '2025-10-15 23:18:37', '2025-11-25 22:42:34'),
(7, 'staff-house', 'Staff House Rooms', '🏠', NULL, 1, 0, 350.00, NULL, '2025-10-15 23:18:37', '2025-11-25 22:42:34'),
(8, 'classrooms', 'Classrooms', '📖', NULL, 1, 0, 300.00, NULL, '2025-10-15 23:18:37', '2025-11-25 22:42:34'),
(12, 'library', 'AVR Library', '📚', 'avr library', 1, 0, 300.00, 500.00, '2025-12-12 10:02:50', '2025-12-12 10:02:50');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-11-27-000001', 'CreateBookingSurveyResponsesTable', 'default', 'App\\Database\\Migrations', 1764194250, 1);

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
CREATE TABLE IF NOT EXISTS `plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_id` int NOT NULL,
  `plan_key` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `facility_id` (`facility_id`),
  KEY `idx_plan_key` (`plan_key`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `facility_id`, `plan_key`, `name`, `duration`, `price`, `created_at`, `updated_at`) VALUES
(1, 2, 'gym-basic-4h', 'Basic Package', '4 hours', 4000.00, '2025-10-16 00:25:02', '2025-11-25 18:40:33'),
(2, 2, 'gym-basic-8h', 'Basic Package', '8 hours', 12000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(3, 2, 'gym-basic-setup-4h', 'Basic Setup', '4 hours', 8000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(4, 2, 'gym-basic-setup-8h', 'Basic Setup', '8 hours', 15000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(5, 2, 'gym-mid-setup-4h', 'Mid Side Setup', '4 hours', 10000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(6, 2, 'gym-mid-setup-8h', 'Mid Side Setup', '8 hours', 20000.00, '2025-10-16 00:25:02', '2025-11-25 18:52:10'),
(7, 2, 'gym-full-setup-4h', 'Full Setup', '4 hours', 13000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(8, 2, 'gym-full-setup-8h', 'Full Setup', '8 hours', 25000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(9, 2, 'gym-ballroom', 'Ballroom/Live Band Setup', '8 hours', 35000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(10, 1, 'aud-basic-4h', 'Basic Package', '4 hours', 7000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(11, 1, 'aud-basic-8h', 'Basic Package', '8 hours', 12000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(12, 1, 'aud-basic-setup-4h', 'Basic Setup', '4 hours', 8000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(13, 1, 'aud-basic-setup-8h', 'Basic Setup', '8 hours', 15000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(14, 1, 'aud-mid-setup-4h', 'Mid Side Setup', '4 hours', 10000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(15, 1, 'aud-mid-setup-8h', 'Mid Side Setup', '8 hours', 20000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(16, 1, 'aud-full-setup-4h', 'Full Setup', '4 hours', 13000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(17, 1, 'aud-full-setup-8h', 'Full Setup', '8 hours', 25000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(18, 6, 'pearl-4h', 'Standard Package', '4 hours', 2000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(19, 6, 'pearl-8h', 'Full Day Package', '8 hours', 3000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(20, 7, 'staff-daily', 'Daily Rate', '1 day', 400.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(21, 7, 'staff-monthly', 'Monthly Rate (Employees Only)', '1 month', 1500.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(26, 4, 'function-4h', 'Standard Package', '4 hours', 3000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(27, 4, 'function-8h', 'Full Day Package', '8 hours', 5000.00, '2025-10-16 00:25:02', '2025-10-16 00:25:02'),
(28, 8, 'classroom-8h', 'Classroom Rental', '8 hours', 300.00, '2025-10-16 03:07:23', '2025-10-16 03:07:23'),
(30, 12, 'libhrrary-3', 'Basic', '3 hours', 300.00, '2025-12-12 10:12:53', '2025-12-12 10:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `plan_equipment`
--

DROP TABLE IF EXISTS `plan_equipment`;
CREATE TABLE IF NOT EXISTS `plan_equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_id` int NOT NULL COMMENT 'FK to plans',
  `equipment_id` int NOT NULL COMMENT 'FK to equipment',
  `quantity_included` int DEFAULT '1' COMMENT 'Units included in base plan',
  `is_mandatory` tinyint(1) DEFAULT '1' COMMENT 'Always included, cannot remove',
  `additional_rate` decimal(10,2) DEFAULT NULL COMMENT 'Rate for extra units',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_plan_equipment` (`plan_id`,`equipment_id`),
  KEY `plan_id` (`plan_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `idx_plan_equipment_lookup` (`plan_id`,`equipment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plan_equipment`
--

INSERT INTO `plan_equipment` (`id`, `plan_id`, `equipment_id`, `quantity_included`, `is_mandatory`, `additional_rate`, `created_at`, `updated_at`) VALUES
(3, 2, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(4, 2, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(5, 3, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(6, 3, 9, 6, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(7, 3, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(8, 3, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(9, 4, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(10, 4, 9, 6, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(11, 4, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(12, 4, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(13, 5, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(14, 5, 9, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(15, 5, 10, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(16, 5, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(17, 5, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(18, 5, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(25, 7, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(26, 7, 9, 16, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(27, 7, 10, 4, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(28, 7, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(29, 7, 12, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(30, 7, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(31, 7, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(32, 8, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(33, 8, 9, 16, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(34, 8, 10, 4, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(35, 8, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(36, 8, 12, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(37, 8, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(38, 8, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(39, 9, 13, 6, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(40, 9, 14, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(41, 9, 15, 3, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(42, 9, 16, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(43, 9, 17, 4, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(44, 9, 18, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(45, 9, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(46, 9, 9, 16, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(47, 9, 10, 6, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(48, 9, 19, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(49, 9, 20, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(50, 9, 12, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(51, 9, 21, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(52, 10, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(53, 10, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(54, 11, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(55, 11, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(56, 12, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(57, 12, 9, 6, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(58, 12, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(59, 12, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(60, 13, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(61, 13, 9, 6, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(62, 13, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(63, 13, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(64, 14, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(65, 14, 9, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(66, 14, 10, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(67, 14, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(68, 14, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(69, 14, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(70, 15, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(71, 15, 9, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(72, 15, 10, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(73, 15, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(74, 15, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(75, 15, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(76, 16, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(77, 16, 9, 16, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(78, 16, 10, 4, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(79, 16, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(80, 16, 12, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(81, 16, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(82, 16, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(83, 17, 8, 10, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(84, 17, 9, 16, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(85, 17, 10, 4, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(86, 17, 11, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(87, 17, 12, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(88, 17, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(89, 17, 7, 2, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(102, 26, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(103, 26, 7, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(104, 27, 6, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(105, 27, 7, 1, 1, NULL, '2025-10-16 00:35:34', '2025-10-16 00:35:34'),
(106, 28, 1, 10, 1, NULL, '2025-10-16 03:07:23', '2025-10-16 03:07:23'),
(107, 28, 2, 30, 1, NULL, '2025-10-16 03:07:23', '2025-10-16 03:07:23'),
(108, 28, 24, 1, 1, NULL, '2025-10-16 03:07:23', '2025-10-16 03:07:23'),
(109, 28, 22, 5, 1, NULL, '2025-10-16 03:07:23', '2025-10-16 03:07:23'),
(114, 1, 6, 1, 1, NULL, '2025-11-25 18:40:33', '2025-11-25 18:40:33'),
(115, 1, 7, 2, 1, NULL, '2025-11-25 18:40:33', '2025-11-25 18:40:33'),
(116, 6, 6, 1, 1, NULL, '2025-11-25 18:52:10', '2025-11-25 18:52:10'),
(117, 6, 7, 2, 1, NULL, '2025-11-25 18:52:10', '2025-11-25 18:52:10'),
(118, 6, 8, 10, 1, NULL, '2025-11-25 18:52:10', '2025-11-25 18:52:10'),
(119, 6, 9, 10, 1, NULL, '2025-11-25 18:52:10', '2025-11-25 18:52:10'),
(120, 6, 10, 2, 1, NULL, '2025-11-25 18:52:10', '2025-11-25 18:52:10'),
(121, 6, 11, 1, 1, NULL, '2025-11-25 18:52:10', '2025-11-25 18:52:10'),
(123, 30, 7, 1, 1, NULL, '2025-12-12 10:12:53', '2025-12-12 10:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `plan_features`
--

DROP TABLE IF EXISTS `plan_features`;
CREATE TABLE IF NOT EXISTS `plan_features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_id` int NOT NULL,
  `feature` text NOT NULL,
  `feature_type` enum('amenity','service','access','description','timing') NOT NULL DEFAULT 'amenity' COMMENT 'Type of feature for categorization',
  `is_physical` tinyint(1) DEFAULT '0' COMMENT 'If false, not a trackable physical item',
  `display_order` int DEFAULT '0' COMMENT 'Display order',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `plan_id` (`plan_id`),
  KEY `idx_feature_type` (`feature_type`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plan_features`
--

INSERT INTO `plan_features` (`id`, `plan_id`, `feature`, `feature_type`, `is_physical`, `display_order`, `created_at`) VALUES
(3, 2, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(4, 2, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(5, 3, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(6, 3, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(7, 4, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(8, 4, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(9, 5, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(10, 5, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(13, 7, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(14, 7, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(15, 8, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(16, 8, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(17, 9, 'Spacious Parking Area', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(18, 9, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(19, 10, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(20, 10, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(21, 10, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(22, 11, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(23, 11, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(24, 11, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(25, 12, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(26, 12, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(27, 12, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(28, 13, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(29, 13, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(30, 13, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(31, 14, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(32, 14, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(33, 14, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(34, 15, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(35, 15, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(36, 15, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(37, 16, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(38, 16, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(39, 16, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(40, 17, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(41, 17, 'Spacious Parking Area', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(42, 17, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(43, 18, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(44, 19, 'Air Conditioners', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(45, 20, 'Check-in Time: 2:00 PM', 'timing', 0, 1, '2025-10-16 03:07:24'),
(46, 20, 'Check-out Time: 12:00 Noon', 'timing', 0, 2, '2025-10-16 03:07:24'),
(47, 20, 'Private Shower Room', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(48, 20, 'Telephone', 'amenity', 0, 4, '2025-10-16 03:07:24'),
(49, 20, 'Cable TV', 'amenity', 0, 5, '2025-10-16 03:07:24'),
(50, 20, 'Internet Access', 'amenity', 0, 6, '2025-10-16 03:07:24'),
(51, 20, 'Air Conditioner', 'amenity', 0, 7, '2025-10-16 03:07:24'),
(52, 21, 'For PSUB employees only', 'description', 0, 1, '2025-10-16 03:07:24'),
(53, 21, 'Private Shower Room', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(54, 21, 'Telephone', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(55, 21, 'Cable TV', 'amenity', 0, 4, '2025-10-16 03:07:24'),
(56, 21, 'Internet Access', 'amenity', 0, 5, '2025-10-16 03:07:24'),
(57, 21, 'Air Conditioner', 'amenity', 0, 6, '2025-10-16 03:07:24'),
(62, 26, 'Air Conditioner', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(63, 27, 'Air Conditioner', 'amenity', 0, 1, '2025-10-16 03:07:24'),
(64, 28, 'Tables and chairs included', 'description', 0, 1, '2025-10-16 03:07:24'),
(65, 28, 'Electric fan', 'amenity', 0, 2, '2025-10-16 03:07:24'),
(66, 28, 'TV monitor', 'amenity', 0, 3, '2025-10-16 03:07:24'),
(69, 1, 'Spacious Parking Area', 'amenity', 0, 1, '2025-11-25 18:40:33'),
(70, 1, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-11-25 18:40:33'),
(71, 6, 'Spacious Parking Area', 'amenity', 0, 1, '2025-11-25 18:52:10'),
(72, 6, 'Free use of the Campus Parks/Grounds for pre-event photoshoot', 'amenity', 0, 2, '2025-11-25 18:52:10'),
(75, 30, 'free parking lot', 'amenity', 0, 1, '2025-12-12 02:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'maintenance_fee', '2000.00', '2025-11-25 18:47:00', '2025-11-27 17:21:18'),
(2, 'overtime_rate', '5000.00', '2025-11-25 18:47:00', '2025-11-27 17:21:18'),
(3, 'extended_hours_rate', '500.00', '2025-11-25 18:47:00', '2025-11-27 17:21:18');

-- --------------------------------------------------------

--
-- Table structure for table `signatories`
--

DROP TABLE IF EXISTS `signatories`;
CREATE TABLE IF NOT EXISTS `signatories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `field_label` varchar(255) DEFAULT NULL,
  `field_value` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`,`field_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `signatories`
--

INSERT INTO `signatories` (`id`, `template_name`, `field_key`, `field_label`, `field_value`, `updated_at`) VALUES
(1, 'moa_template.docx', '###VP_FINANCE###', '###VP_FINANCE###', 'JOMEL S. PENETRANTE', '2025-12-17 17:29:10'),
(2, 'moa_template.docx', '###SPMO_WITNESS1###', '###SPMO_WITNESS1###', 'MARY CHIE A. DE LA CRUZ, CPA, MBA', '2025-12-17 17:28:40'),
(3, 'moa_template.docx', '###ACCOUNTANT###', '###ACCOUNTANT###', 'KAREN H. CRUZATA, CPA', '2025-12-17 17:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `student_booking_files`
--

DROP TABLE IF EXISTS `student_booking_files`;
CREATE TABLE IF NOT EXISTS `student_booking_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `upload_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_file_type` (`file_type`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','student','facilitator','employee') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `google_id` varchar(255) DEFAULT NULL COMMENT 'Google OAuth ID for users logging in with Google',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `contact_number`, `password`, `role`, `created_at`, `updated_at`, `google_id`) VALUES
(2, 'test', 'test@gmail.com', '673555075', '$2y$10$GMgDl0.RHkCrqbd/ZAsWuO6QMFCq56Rm7X3.wwRVMf1zPzDTuAFyi', 'admin', '2025-10-15 23:18:37', '2025-10-16 13:36:06', NULL),
(17, 'jomeltienes@gmail.com', 'jomel@gmail.com', '098723213', '$2y$10$vF5VA0GxKkXuS93NDy7nXeURX/IDOhUwtLbrajvEeDlei/4SGLmti', 'student', '2025-10-21 12:34:54', '2025-10-21 12:34:54', NULL),
(31, 'ivan', 'ivaaanjoshua@gmail.com', '09123456789', '$2y$10$r0URaRslW9GgRMdFmfUAzOTtzYJ50gZbRUv69IbwpZ6AdWlsv./dm', 'user', '2025-12-14 16:05:47', '2025-12-14 16:05:47', NULL),
(32, 'April Boragay Abonita', 'apabonita@my.cspc.edu.ph', '09203197861', '$2y$10$ttl12tMmpPf4MM6uxZi/zuLJfWzLeDFnC6ljhJ3wn776Lga0dp.Pu', 'student', '2025-12-14 18:12:33', '2025-12-14 18:12:33', NULL),
(33, 'Janna May Tangtang ', 'jannamay.tangtang@usant.edu.ph', '09129284359', '$2y$10$EWNrB.wc7ikh7f5NBHK4/.D7UZ/bcBDMX2pATXrClyUpCtVsLFfLa', 'user', '2025-12-14 18:16:58', '2025-12-14 18:16:58', NULL),
(34, 'Hazel Ann T. lanuzga', 'hazellanuzga19@gmail.com', '09917862107', '$2y$10$W42dCStJAbfgKvu9r7a5yeFA24JOxmloN7v9BvUK8abcgFQtA3oBW', 'user', '2025-12-14 18:29:29', '2025-12-14 18:29:29', NULL),
(35, 'April Abonita', 'eyprilya1@gmail.com', '09203197861', '$2y$10$zgqVw1LZZD2NoVQ5JjPeBOUzsqqXSYQtONwmvQhWBcabqeRakvWkC', 'user', '2025-12-14 18:40:49', '2025-12-14 18:40:49', NULL),
(36, 'Ivan Joshua Jornales ', 'ivanjoshua@gmail.com', '09123456789', '$2y$10$UHuSDLSxgsnUYQHLobwb0eidTOhgA4a/K/NyLdF4iPX70iwC5d4ye', 'user', '2025-12-14 18:54:00', '2025-12-14 18:54:00', NULL),
(37, 'Princess Ivy', 'ivyyy@gmail.com', '09123456789', '$2y$10$X4vxy5tkU168OmTgAj2RXurY05gcbjQGiVGHLCW4kWbma8S1Arh9u', 'user', '2025-12-14 19:00:56', '2025-12-14 19:00:56', NULL),
(38, 'Michaela Cezar', 'micezar@my.cspc.edu.ph', '09380582591', '$2y$10$0lm8VnLZROI7cCbA6zgBJuD9MWTPE.6JFKjYfMxedQNVbgNKzXVoi', 'student', '2025-12-14 19:03:20', '2025-12-14 19:03:20', NULL),
(42, 'Johnmark Fabilane ', 'johnmarkfabilane@gmail.com', '09505364538', '$2y$10$NrciHr7Y3Vs8CMVB1vZpteyhj1v61hAe3zjHDVP3TKlal32n.fO1K', 'user', '2025-12-17 18:00:55', '2025-12-17 18:00:55', NULL),
(43, 'Raia Lazim', 'lazimraimona@gmail.com', '09101216634', '$2y$10$Y5bHcBUJYviph1dgpWz9.uOPKGE2zDmMbkn9dbuVocrPgInyBMNY.', 'user', '2025-12-17 18:03:10', '2025-12-17 18:03:10', NULL),
(44, 'Angel Aaron Granali', 'agranali15@gmail.com', '09668535854', '$2y$10$Hlp5DjbXpxLD0W7p.n4kzO7N4AWFDhJ7CPZoXe.VSGVG6EuQXz9Na', 'user', '2025-12-17 18:06:50', '2025-12-17 18:06:50', NULL),
(45, 'Stephanie Castillo', 'stcastillo@my.cspc.edu.ph', '09766028208', '$2y$10$ZVsiJByD2hkr/o29Nnls9.9r/gzvylnnEgT.vL.rnSuPAdLA1LjpK', 'student', '2025-12-17 18:46:23', '2025-12-17 18:46:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `view_booking_summary`
--

DROP TABLE IF EXISTS `view_booking_summary`;
CREATE TABLE IF NOT EXISTS `view_booking_summary` (
  `booking_id` int DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `equipment_count` bigint DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `event_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facility_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed','pending_cancellation') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `total_equipment_quantity` decimal(32,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_equipment_availability`
--

DROP TABLE IF EXISTS `view_equipment_availability`;
CREATE TABLE IF NOT EXISTS `view_equipment_availability` (
  `available` bigint DEFAULT NULL,
  `category` enum('furniture','audio_visual','lighting','technical','logistics') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_status` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `damaged` int DEFAULT NULL,
  `good` int DEFAULT NULL,
  `id` int DEFAULT NULL,
  `is_rentable` tinyint(1) DEFAULT NULL,
  `is_trackable` tinyint(1) DEFAULT NULL,
  `minimum_quantity` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `rented` int DEFAULT NULL,
  `total_quantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_plan_complete_details`
--

DROP TABLE IF EXISTS `view_plan_complete_details`;
CREATE TABLE IF NOT EXISTS `view_plan_complete_details` (
  `category` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facility_icon` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facility_id` int DEFAULT NULL,
  `facility_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_trackable` bigint DEFAULT NULL,
  `item_name` mediumtext COLLATE utf8mb4_unicode_ci,
  `item_type` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_id` int DEFAULT NULL,
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
