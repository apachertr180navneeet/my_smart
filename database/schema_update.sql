-- ==============================================================================
-- DATABASE SCHEMA UPDATE SCRIPT
-- Project: My Smart App (Iqonic Handyman Backend)
-- Description: Applies all schema updates, new tables, columns, indexes, and constraints.
-- Safe & Idempotent: Can be run safely on existing or partially updated databases.
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- STEP 1: Temporary Helper Procedures for Idempotent Operations
-- ------------------------------------------------------------------------------

-- Procedure to safely ADD column only if it doesn't already exist
DROP PROCEDURE IF EXISTS `add_column_if_not_exists`;

DELIMITER $$
CREATE PROCEDURE `add_column_if_not_exists`(
    IN tbl_name VARCHAR(64),
    IN col_name VARCHAR(64),
    IN col_def TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = tbl_name 
          AND COLUMN_NAME = col_name
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl_name, '` ADD COLUMN `', col_name, '` ', col_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

-- Procedure to safely MODIFY column only if it already exists
DROP PROCEDURE IF EXISTS `modify_column_if_exists`;

DELIMITER $$
CREATE PROCEDURE `modify_column_if_exists`(
    IN tbl_name VARCHAR(64),
    IN col_name VARCHAR(64),
    IN col_def TEXT
)
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = tbl_name 
          AND COLUMN_NAME = col_name
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl_name, '` MODIFY COLUMN `', col_name, '` ', col_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

-- ------------------------------------------------------------------------------
-- STEP 2: Create New Tables
-- ------------------------------------------------------------------------------

-- 2.1 Provider Requirements Table
CREATE TABLE IF NOT EXISTS `provider_requirements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider_id` BIGINT UNSIGNED NOT NULL,
  `handyman_id` BIGINT UNSIGNED DEFAULT NULL,
  `key` VARCHAR(50) NOT NULL,
  `file` VARCHAR(255) NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `remarks` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_requirements_provider_id_handyman_id_key_unique` (`provider_id`, `handyman_id`, `key`),
  KEY `provider_requirements_handyman_id_foreign` (`handyman_id`),
  CONSTRAINT `provider_requirements_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `provider_requirements_handyman_id_foreign` FOREIGN KEY (`handyman_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2 Spatie Media Library Table
CREATE TABLE IF NOT EXISTS `media` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  `uuid` CHAR(36) DEFAULT NULL,
  `collection_name` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(255) DEFAULT NULL,
  `disk` VARCHAR(255) NOT NULL,
  `conversions_disk` VARCHAR(255) DEFAULT NULL,
  `size` BIGINT UNSIGNED NOT NULL,
  `manipulations` JSON NOT NULL,
  `custom_properties` JSON NOT NULL,
  `generated_conversions` JSON NOT NULL,
  `responsive_images` JSON NOT NULL,
  `order_column` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`, `model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- STEP 3: Add New Columns to Existing Tables
-- ------------------------------------------------------------------------------

-- Users table
CALL add_column_if_not_exists('users', 'dob', 'DATE NULL');
CALL add_column_if_not_exists('users', 'is_phone_verified', 'TINYINT NOT NULL DEFAULT 0');
CALL add_column_if_not_exists('users', 'stripe_customer_id', 'VARCHAR(255) NULL');

-- Services table
CALL add_column_if_not_exists('services', 'service_preferences', 'JSON NULL');

-- Bookings table
CALL add_column_if_not_exists('bookings', 'service_preference', 'VARCHAR(30) NULL');
CALL add_column_if_not_exists('bookings', 'booking_address_id', 'BIGINT UNSIGNED NULL');

-- Payment Gateways table
CALL add_column_if_not_exists('payment_gateways', 'title', 'VARCHAR(255) NULL');
CALL add_column_if_not_exists('payment_gateways', 'type', 'VARCHAR(255) NULL');
CALL add_column_if_not_exists('payment_gateways', 'status', 'TINYINT NOT NULL DEFAULT 1');
CALL add_column_if_not_exists('payment_gateways', 'is_test', 'TINYINT NOT NULL DEFAULT 1');
CALL add_column_if_not_exists('payment_gateways', 'value', 'LONGTEXT NULL');
CALL add_column_if_not_exists('payment_gateways', 'live_value', 'LONGTEXT NULL');

-- Provider Slot Mappings table
CALL add_column_if_not_exists('provider_slot_mappings', 'days', 'VARCHAR(255) NULL');
CALL add_column_if_not_exists('provider_slot_mappings', 'start_at', 'TIME NULL');
CALL add_column_if_not_exists('provider_slot_mappings', 'end_at', 'TIME NULL');

-- Booking Statuses table
CALL add_column_if_not_exists('booking_statuses', 'value', 'VARCHAR(255) NULL');
CALL add_column_if_not_exists('booking_statuses', 'label', 'VARCHAR(255) NULL');
CALL add_column_if_not_exists('booking_statuses', 'sequence', 'INT NOT NULL DEFAULT 0');

-- Booking Activities table
CALL add_column_if_not_exists('booking_activities', 'datetime', 'DATETIME NULL');
CALL add_column_if_not_exists('booking_activities', 'activity_type', 'VARCHAR(255) NULL');
CALL add_column_if_not_exists('booking_activities', 'activity_message', 'TEXT NULL');
CALL add_column_if_not_exists('booking_activities', 'activity_data', 'JSON NULL');

-- ------------------------------------------------------------------------------
-- STEP 4: Soft Deletes (`deleted_at`) & Timestamps
-- ------------------------------------------------------------------------------

-- Tables requiring deleted_at + timestamps
CALL add_column_if_not_exists('booking_extra_charges', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_extra_charges', 'created_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_extra_charges', 'updated_at', 'TIMESTAMP NULL');

CALL add_column_if_not_exists('booking_handyman_mappings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_handyman_mappings', 'created_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_handyman_mappings', 'updated_at', 'TIMESTAMP NULL');

CALL add_column_if_not_exists('booking_package_mappings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_package_mappings', 'created_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_package_mappings', 'updated_at', 'TIMESTAMP NULL');

CALL add_column_if_not_exists('booking_coupon_mappings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_coupon_mappings', 'created_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_coupon_mappings', 'updated_at', 'TIMESTAMP NULL');

-- Tables requiring deleted_at
CALL add_column_if_not_exists('provider_address_mappings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_activities', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('banks', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('banner_payments', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('booking_address_mappings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('commission_earnings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('documents', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('handyman_ratings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('mail_template_content_mappings', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('mail_templates', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('notification_templates', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('provider_documents', 'deleted_at', 'TIMESTAMP NULL');
CALL add_column_if_not_exists('service_zones', 'deleted_at', 'TIMESTAMP NULL');

-- ------------------------------------------------------------------------------
-- STEP 5: Modify Existing Column Types & Nullability (Only if Column Exists)
-- ------------------------------------------------------------------------------

-- Allow nullable name in payment_gateways (only if column exists)
CALL modify_column_if_exists('payment_gateways', 'name', 'VARCHAR(255) NULL');

-- Allow nullable name in booking_statuses (only if column exists)
CALL modify_column_if_exists('booking_statuses', 'name', 'VARCHAR(255) NULL');

-- Make bookings.status varchar(255) with default 'pending' (only if column exists)
CALL modify_column_if_exists('bookings', 'status', 'VARCHAR(255) DEFAULT \'pending\'');

-- Make bookings.tax nullable with default 0 (only if column exists)
CALL modify_column_if_exists('bookings', 'tax', 'DOUBLE NULL DEFAULT 0');

-- ------------------------------------------------------------------------------
-- STEP 6: Seed Default Payment Gateways (Stripe, Razorpay, COD)
-- ------------------------------------------------------------------------------
INSERT INTO `payment_gateways` (`id`, `title`, `type`, `status`, `is_test`, `value`, `live_value`, `created_at`, `updated_at`)
VALUES
(1, 'Cash on Delivery', 'cash', 1, 0, NULL, NULL, NOW(), NOW()),
(2, 'Stripe Payment', 'stripe', 1, 1, '{"stripe_url":"","stripe_key":"","stripe_publickey":""}', NULL, NOW(), NOW()),
(3, 'Razor Pay', 'razorPay', 1, 1, '{"razor_url":"","razor_key":"","razor_secret":""}', NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE `type` = VALUES(`type`), `title` = VALUES(`title`);

-- ------------------------------------------------------------------------------
-- STEP 7: Register Migrations in Laravel's `migrations` Table
-- Prevents Laravel `php artisan migrate` or `/update-db` from re-executing them.
-- ------------------------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2024_01_01_000001_create_testing_schema', 1),
('2024_10_01_000002_add_dob_phone_verified_stripe_to_users_table', 1),
('2024_10_01_000003_fix_payment_gateways_table', 1),
('2024_10_01_000004_add_service_preferences_to_services_table', 1),
('2024_10_01_000005_add_service_preference_to_bookings_table', 1),
('2024_10_01_000006_create_provider_requirements_table', 1),
('2024_10_01_000007_add_deleted_at_to_provider_address_mappings_table', 1),
('2024_10_01_000008_fix_provider_slot_mappings_table', 1),
('2024_10_01_000009_fix_booking_extra_charges_table', 1),
('2024_10_01_000010_fix_booking_statuses_and_booking_table', 1),
('2024_10_01_000011_fix_booking_mappings_tables', 1),
('2024_10_01_000012_add_missing_deleted_at_to_tables', 1),
('2024_10_01_000013_fix_booking_activities_table', 1),
('2026_10_01_160503_create_media_table', 1);

-- ------------------------------------------------------------------------------
-- STEP 8: Cleanup Helper Procedures
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `add_column_if_not_exists`;
DROP PROCEDURE IF EXISTS `modify_column_if_exists`;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- END OF SCHEMA UPDATE
-- ==============================================================================
