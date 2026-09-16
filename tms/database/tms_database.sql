-- =============================================================
-- Tuition Management System (TMS) - Database Schema
-- =============================================================
-- Import this file directly into phpMyAdmin / MySQL
-- =============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `tms_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tms_db`;

-- -------------------------------------------------------------
-- Table: admins
-- -------------------------------------------------------------
CREATE TABLE `admins` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `photo_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: tutors
-- -------------------------------------------------------------
CREATE TABLE `tutors` (
  `tutor_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `gender` ENUM('Male','Female','Other') DEFAULT 'Other',
  `phone` VARCHAR(20) DEFAULT NULL,
  `photo_path` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Active','Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: students
-- -------------------------------------------------------------
CREATE TABLE `students` (
  `student_id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `birthday` DATE DEFAULT NULL,
  `parent_name` VARCHAR(100) DEFAULT NULL,
  `parent_phone` VARCHAR(20) DEFAULT NULL,
  `stream` VARCHAR(100) DEFAULT NULL,
  `subject1` VARCHAR(100) DEFAULT NULL,
  `subject2` VARCHAR(100) DEFAULT NULL,
  `subject3` VARCHAR(100) DEFAULT NULL,
  `al_education` VARCHAR(150) DEFAULT NULL,
  `photo_path` VARCHAR(255) DEFAULT NULL,
  `qr_code` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Active','Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: timetable
-- -------------------------------------------------------------
CREATE TABLE `timetable` (
  `timetable_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tutor_id` INT NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `class_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `classroom` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`tutor_id`) REFERENCES `tutors`(`tutor_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: attendance
-- -------------------------------------------------------------
CREATE TABLE `attendance` (
  `attendance_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('Present','Absent','Late') DEFAULT 'Present',
  `subject` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: payments
-- -------------------------------------------------------------
CREATE TABLE `payments` (
  `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `month` VARCHAR(20) NOT NULL,
  `payment_date` DATE NOT NULL,
  `status` ENUM('Paid','Pending','Overdue') DEFAULT 'Paid',
  `notes` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: notifications  (simulated SMS/parent notification log - FR-08)
-- -------------------------------------------------------------
CREATE TABLE `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `channel` VARCHAR(20) DEFAULT 'SMS',
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: tutor_reports  (FR-14: daily class card / income report)
-- -------------------------------------------------------------
CREATE TABLE `tutor_reports` (
  `report_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tutor_id` INT NOT NULL,
  `report_date` DATE NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `students_count` INT DEFAULT 0,
  `income` DECIMAL(10,2) DEFAULT 0.00,
  `notes` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`tutor_id`) REFERENCES `tutors`(`tutor_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Table: contact_messages (Public Contact page - FR-13)
-- -------------------------------------------------------------
CREATE TABLE `contact_messages` (
  `message_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(150) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================================
-- SAMPLE DATA
-- =============================================================

-- Admin (password: admin123 -> hashed with PHP password_hash, bcrypt)
INSERT INTO `admins` (`name`, `email`, `password`) VALUES
('System Admin', 'admin@tms.com', '$2y$10$gcdb2ySmBWdIVs1jCfkrfuxBI6Hep4tcah/dUmoPrFn5opAnuu0iu'); -- admin123

-- Tutors (password: tutor123)
INSERT INTO `tutors` (`name`, `email`, `password`, `subject`, `gender`, `phone`, `status`) VALUES
('Mr. Kasun Perera', 'kasun@tms.com', '$2y$10$q262Am0j1FVFF4Z5xR55heNRnq./uQKqigzWd15.hzpJ7FZ4yvAxu', 'Mathematics', 'Male', '0771234567', 'Active'),
('Ms. Nadeesha Silva', 'nadeesha@tms.com', '$2y$10$q262Am0j1FVFF4Z5xR55heNRnq./uQKqigzWd15.hzpJ7FZ4yvAxu', 'Science', 'Female', '0777654321', 'Active'),
('Mr. Ruwan Fernando', 'ruwan@tms.com', '$2y$10$q262Am0j1FVFF4Z5xR55heNRnq./uQKqigzWd15.hzpJ7FZ4yvAxu', 'English', 'Male', '0713456789', 'Active');

-- Students (password: student123)
INSERT INTO `students` (`first_name`, `last_name`, `email`, `password`, `phone`, `birthday`, `parent_name`, `parent_phone`, `stream`, `subject1`, `subject2`, `subject3`, `al_education`, `qr_code`, `status`) VALUES
('Amal', 'Jayawardena', 'amal@student.com', '$2y$10$kEoxWcXS4eU6bH4MmmoP3eeSXhvZZs5MqDOxOXgPBfDC3UU8Yx9uC', '0761112233', '2007-05-14', 'Sunil Jayawardena', '0771112233', 'Physical Science', 'Mathematics', 'Science', 'English', 'A/L 2025', 'QR-STU-0001', 'Active'),
('Ishara', 'Gunawardena', 'ishara@student.com', '$2y$10$kEoxWcXS4eU6bH4MmmoP3eeSXhvZZs5MqDOxOXgPBfDC3UU8Yx9uC', '0762223344', '2008-02-21', 'Priya Gunawardena', '0772223344', 'Biological Science', 'Science', 'Mathematics', 'English', 'A/L 2026', 'QR-STU-0002', 'Active'),
('Dilshan', 'Rathnayake', 'dilshan@student.com', '$2y$10$kEoxWcXS4eU6bH4MmmoP3eeSXhvZZs5MqDOxOXgPBfDC3UU8Yx9uC', '0763334455', '2007-11-09', 'Kamal Rathnayake', '0773334455', 'Commerce', 'Mathematics', 'English', 'Science', 'A/L 2025', 'QR-STU-0003', 'Active');

-- Timetable
INSERT INTO `timetable` (`tutor_id`, `subject`, `class_date`, `start_time`, `end_time`, `classroom`) VALUES
(1, 'Mathematics', CURDATE(), '09:00:00', '11:00:00', 'Room A1'),
(2, 'Science', CURDATE(), '11:30:00', '13:30:00', 'Room B2'),
(3, 'English', CURDATE() + INTERVAL 1 DAY, '09:00:00', '10:30:00', 'Room C1'),
(1, 'Mathematics', CURDATE() + INTERVAL 2 DAY, '14:00:00', '16:00:00', 'Room A1');

-- Attendance
INSERT INTO `attendance` (`student_id`, `date`, `status`, `subject`) VALUES
(1, CURDATE(), 'Present', 'Mathematics'),
(2, CURDATE(), 'Present', 'Science'),
(3, CURDATE(), 'Absent', 'Mathematics'),
(1, CURDATE() - INTERVAL 1 DAY, 'Present', 'Science');

-- Payments
INSERT INTO `payments` (`student_id`, `amount`, `month`, `payment_date`, `status`, `notes`) VALUES
(1, 5000.00, 'September 2026', CURDATE(), 'Paid', 'Monthly tuition fee'),
(2, 5000.00, 'September 2026', CURDATE(), 'Paid', 'Monthly tuition fee'),
(3, 5000.00, 'September 2026', NULL, 'Pending', 'Awaiting payment');

-- Tutor Reports
INSERT INTO `tutor_reports` (`tutor_id`, `report_date`, `subject`, `students_count`, `income`, `notes`) VALUES
(1, CURDATE(), 'Mathematics', 25, 12500.00, 'Regular class conducted'),
(2, CURDATE(), 'Science', 20, 10000.00, 'Practical session included');

-- Notifications
INSERT INTO `notifications` (`student_id`, `message`, `channel`) VALUES
(1, 'Dear Parent, September 2026 tuition fee of Rs.5000 has been received. Thank you.', 'SMS'),
(2, 'Dear Parent, September 2026 tuition fee of Rs.5000 has been received. Thank you.', 'SMS');

-- =============================================================
-- NOTE: Default test login credentials
-- Admin   : admin@tms.com   / admin123
-- Tutor   : kasun@tms.com   / tutor123
-- Student : amal@student.com / student123
-- =============================================================
