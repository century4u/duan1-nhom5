-- ===============================================
-- Database Schema cho Dự án Tour Du lịch
-- Database: nhom5
-- Tạo bởi: Antigravity AI
-- Ngày: 2025-12-07
-- ===============================================

-- Tạo database (nếu chưa có)
CREATE DATABASE IF NOT EXISTS `nhom5` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nhom5`;

SET FOREIGN_KEY_CHECKS = 0;

-- ===============================================
-- Bảng: users
-- ===============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','staff','guide','customer') DEFAULT 'customer',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: tours
-- ===============================================
DROP TABLE IF EXISTS `tours`;
CREATE TABLE `tours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `departure_location` varchar(100) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Số ngày',
  `price` decimal(15,2) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `min_participants` int(11) DEFAULT NULL,
  `status` enum('draft','active','inactive','completed') DEFAULT 'active',
  `tour_category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: tour_schedules
-- ===============================================
DROP TABLE IF EXISTS `tour_schedules`;
CREATE TABLE `tour_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `activities` text COMMENT 'JSON array of activities',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: departure_schedules
-- ===============================================
DROP TABLE IF EXISTS `departure_schedules`;
CREATE TABLE `departure_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `meeting_point` varchar(255) DEFAULT NULL,
  `available_slots` int(11) DEFAULT NULL,
  `booked_slots` int(11) DEFAULT 0,
  `status` enum('scheduled','confirmed','departed','completed','cancelled') DEFAULT 'scheduled',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: guides (Hướng dẫn viên)
-- ===============================================
DROP TABLE IF EXISTS `guides`;
CREATE TABLE `guides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL COMMENT 'Comma separated',
  `experience_years` int(11) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `status` enum('available','busy','inactive') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: guide_tour_history
-- ===============================================
DROP TABLE IF EXISTS `guide_tour_history`;
CREATE TABLE `guide_tour_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guide_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `departure_schedule_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('assigned','completed','cancelled') DEFAULT 'assigned',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guide_id` (`guide_id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: bookings
-- ===============================================
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `tour_id` int(11) NOT NULL,
  `departure_schedule_id` int(11) DEFAULT NULL,
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `contact_name` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `num_adults` int(11) DEFAULT 0,
  `num_children` int(11) DEFAULT 0,
  `total_price` decimal(15,2) DEFAULT NULL,
  `deposit_amount` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','confirmed','deposit','paid','completed','cancelled') DEFAULT 'pending',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `tour_id` (`tour_id`),
  KEY `departure_schedule_id` (`departure_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: booking_details
-- ===============================================
DROP TABLE IF EXISTS `booking_details`;
CREATE TABLE `booking_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `id_card` varchar(20) DEFAULT NULL,
  `passport` varchar(20) DEFAULT NULL,
  `special_requirements` text COMMENT 'Yêu cầu đặc biệt: ăn chay, dị ứng, bệnh lý, v.v.',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: checkins
-- ===============================================
DROP TABLE IF EXISTS `checkins`;
CREATE TABLE `checkins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_detail_id` int(11) NOT NULL,
  `departure_schedule_id` int(11) DEFAULT NULL,
  `status` enum('pending','checked_in','late','absent','cancelled') DEFAULT 'pending',
  `checkin_time` timestamp NULL DEFAULT NULL,
  `notes` text,
  `checked_by` int(11) DEFAULT NULL COMMENT 'user_id của người check-in',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_detail_id` (`booking_detail_id`),
  KEY `checked_by` (`checked_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng: booking_status_history
-- ===============================================
DROP TABLE IF EXISTS `booking_status_history`;
CREATE TABLE `booking_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- Bảng bổ sung khác
-- ===============================================

DROP TABLE IF EXISTS `tour_images`;
CREATE TABLE `tour_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tour_policies`;
CREATE TABLE `tour_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `policy_type` varchar(50) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tour_prices`;
CREATE TABLE `tour_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `price_type` enum('adult','child','infant') DEFAULT 'adult',
  `price` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tour_suppliers`;
CREATE TABLE `tour_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `room_assignments`;
CREATE TABLE `room_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_detail_id` int(11) NOT NULL,
  `hotel_name` varchar(255) DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_detail_id` (`booking_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `operation_reports`;
CREATE TABLE `operation_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `departure_schedule_id` int(11) DEFAULT NULL,
  `report_date` date NOT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `content` text,
  `total_revenue` decimal(15,2) DEFAULT NULL,
  `total_cost` decimal(15,2) DEFAULT NULL,
  `profit` decimal(15,2) DEFAULT NULL,
  `status` enum('draft','submitted','approved') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `guide_availability`;
CREATE TABLE `guide_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guide_id` int(11) NOT NULL,
  `available_from` date NOT NULL,
  `available_to` date NOT NULL,
  `status` enum('available','booked','unavailable') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guide_id` (`guide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `schedule_assignments`;
CREATE TABLE `schedule_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departure_schedule_id` int(11) NOT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `departure_schedule_id` (`departure_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `schedule_notifications`;
CREATE TABLE `schedule_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================
-- DỮ LIỆU MẪU
-- ===============================================

-- Users
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `role`, `status`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@tourapp.com', 'Quản trị viên', '0901234567', 'admin', 'active'),
(2, 'guide1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'guide1@tourapp.com', 'Nguyễn Văn Hướng', '0912345678', 'guide', 'active'),
(3, 'customer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer1@email.com', 'Trần Thị Lan', '0923456789', 'customer', 'active');

-- Tours
INSERT INTO `tours` (`id`, `name`, `code`, `description`, `departure_location`, `destination`, `duration`, `price`, `max_participants`, `min_participants`, `status`) VALUES
(1, 'Tour Đà Lạt 3N2Đ', 'DL3N2D', 'Khám phá thành phố ngàn hoa', 'TP. Hồ Chí Minh', 'Đà Lạt', 3, 2500000, 40, 10, 'active'),
(2, 'Tour Phú Quốc 4N3Đ', 'PQ4N3D', 'Nghỉ dưỡng đảo ngọc', 'TP. Hồ Chí Minh', 'Phú Quốc', 4, 4500000, 30, 10, 'active'),
(3, 'Tour Hà Nội - Hạ Long 2N1Đ', 'HL2N1D', 'Vịnh Hạ Long di sản thế giới', 'Hà Nội', 'Hạ Long', 2, 1800000, 45, 15, 'active');

-- Tour Schedules
INSERT INTO `tour_schedules` (`tour_id`, `day_number`, `title`, `description`, `activities`) VALUES
(1, 1, 'Ngày 1: TP.HCM - Đà Lạt', 'Khởi hành sáng sớm, tham quan chợ Đà Lạt', '[\"Khởi hành 6h sáng\",\"Tham quan thác Datanla\",\"Chợ đêm Đà Lạt\"]'),
(1, 2, 'Ngày 2: Tham quan Đà Lạt', 'Tham quan các điểm nổi tiếng', '[\"Hồ Xuân Hương\",\"Dinh Bảo Đại\",\"Vườn hoa thành phố\"]'),
(1, 3, 'Ngày 3: Đà Lạt - TP.HCM', 'Mua sắm và về', '[\"Chợ Đà Lạt\",\"Mua đặc sản\",\"Về TP.HCM\"]');

-- Departure Schedules
INSERT INTO `departure_schedules` (`id`, `tour_id`, `departure_date`, `departure_time`, `return_date`, `meeting_point`, `available_slots`, `booked_slots`, `status`) VALUES
(1, 1, '2025-12-15', '06:00:00', '2025-12-17', 'Bến xe Miền Đông', 40, 15, 'confirmed'),
(2, 2, '2025-12-20', '08:00:00', '2025-12-23', 'Sân bay Tân Sơn Nhất', 30, 8, 'scheduled'),
(3, 3, '2025-12-18', '07:00:00', '2025-12-19', 'Điểm hẹn Hoàn Kiếm', 45, 20, 'confirmed');

-- Guides
INSERT INTO `guides` (`id`, `user_id`, `full_name`, `phone`, `email`, `license_number`, `languages`, `experience_years`, `rating`, `status`) VALUES
(1, 2, 'Nguyễn Văn Hướng', '0912345678', 'guide1@tourapp.com', 'HDV001234', 'Tiếng Việt, English', 5, 4.8, 'available'),
(2, NULL, 'Lê Thị Mai', '0987654321', 'mai.hdv@email.com', 'HDV005678', 'Tiếng Việt, English, 中文', 8, 4.9, 'available');

-- Guide Tour History
INSERT INTO `guide_tour_history` (`guide_id`, `tour_id`, `departure_schedule_id`, `start_date`, `end_date`, `status`, `notes`) VALUES
(1, 1, 1, '2025-12-15', '2025-12-17', 'assigned', 'HDV chính cho tour Đà Lạt'),
(2, 3, 3, '2025-12-18', '2025-12-19', 'assigned', NULL);

-- Bookings
INSERT INTO `bookings` (`id`, `user_id`, `tour_id`, `departure_schedule_id`, `contact_name`, `contact_email`, `contact_phone`, `num_adults`, `num_children`, `total_price`, `deposit_amount`, `status`) VALUES
(1, 3, 1, 1, 'Trần Thị Lan', 'customer1@email.com', '0923456789', 2, 1, 6250000, 2000000, 'confirmed'),
(2, NULL, 1, 1, 'Phạm Văn An', 'pva@email.com', '0934567890', 3, 0, 7500000, 3000000, 'deposit'),
(3, NULL, 3, 3, 'Nguyễn Thị Hoa', 'hoa.nt@email.com', '0945678901', 2, 2, 5400000, 1500000, 'confirmed');

-- Booking Details
INSERT INTO `booking_details` (`booking_id`, `fullname`, `gender`, `birthdate`, `id_card`, `passport`, `special_requirements`) VALUES
(1, 'Trần Thị Lan', 'Nữ', '1985-05-15', '079085012345', NULL, 'Ăn chay'),
(1, 'Nguyễn Văn Minh', 'Nam', '1983-08-20', '079083054321', NULL, NULL),
(1, 'Trần Văn Bình', 'Nam', '2010-03-10', NULL, NULL, 'Trẻ em, cần chăm sóc đặc biệt'),
(2, 'Phạm Văn An', 'Nam', '1990-01-25', '079090012789', NULL, NULL),
(2, 'Phạm Thị Lan', 'Nữ', '1992-06-30', '079092067890', NULL, 'Dị ứng hải sản'),
(2, 'Phạm Văn Tuấn', 'Nam', '1988-11-12', '079088112345', NULL, NULL),
(3, 'Nguyễn Thị Hoa', 'Nữ', '1987-04-18', '079087041234', NULL, NULL),
(3, 'Lê Văn Hùng', 'Nam', '1985-07-22', '079085072345', NULL, 'Khó đi lại, cần hỗ trợ'),
(3, 'Nguyễn Văn Long', 'Nam', '2012-09-05', NULL, NULL, NULL),
(3, 'Nguyễn Thị My', 'Nữ', '2014-12-15', NULL, NULL, NULL);

-- Checkins
INSERT INTO `checkins` (`booking_detail_id`, `departure_schedule_id`, `status`, `checkin_time`, `notes`, `checked_by`) VALUES
(1, 1, 'checked_in', '2025-12-15 05:45:00', 'Đã đến sớm', 2),
(2, 1, 'checked_in', '2025-12-15 05:50:00', NULL, 2),
(3, 1, 'late', '2025-12-15 06:15:00', 'Đến muộn 15 phút', 2),
(4, 1, 'pending', NULL, NULL, NULL),
(5, 1, 'pending', NULL, NULL, NULL);

-- ===============================================
-- KẾT THÚC
-- ===============================================
