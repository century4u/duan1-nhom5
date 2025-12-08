-- Migration: Thêm cột special_requirements vào bảng booking_details
-- Ngày tạo: 2025-12-06
-- Mục đích: Lưu trữ yêu cầu đặc biệt của khách hàng (ăn chay, bệnh lý, dị ứng, v.v.)

ALTER TABLE `booking_details` 
ADD COLUMN `special_requirements` TEXT NULL 
COMMENT 'Yêu cầu đặc biệt: ăn chay, dị ứng, bệnh lý, v.v.' 
AFTER `passport`;

-- Rollback script (nếu cần)
-- ALTER TABLE `booking_details` DROP COLUMN `special_requirements`;
