-- =============================================================================
-- EKSİK TABLOLAR/SÜTUNLAR: Bu dosyalar migration olarak yazılmış ama hiç SQL'e
-- dönüştürülmemiş (2026-02 ile 2026-07 arası). E2E test sırasında
-- "mobile_device_tokens tablosu yok" hatasıyla keşfedildi — bu da Teklifler,
-- Onay SMS zinciri gibi modüllerin de canlıda çökebileceği anlamına geliyor.
-- Tamamı IF NOT EXISTS / idempotent yazıldı, tek seferde çalıştırılabilir.
-- =============================================================================

-- 1) Mobil push bildirim token'ları (bakım/arıza/teklif bildirimleri bu tabloya yazar)
CREATE TABLE IF NOT EXISTS `mobile_device_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `company_id` BIGINT UNSIGNED NULL,
  `token` VARCHAR(255) NOT NULL,
  `platform` VARCHAR(20) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_seen_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile_device_tokens_token_unique` (`token`),
  KEY `mobile_device_tokens_user_id_foreign` (`user_id`),
  KEY `mobile_device_tokens_company_id_foreign` (`company_id`),
  CONSTRAINT `mobile_device_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mobile_device_tokens_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Bina onay linki token'ları (SMS ile gönderilen onay linki)
CREATE TABLE IF NOT EXISTS `building_approval_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `building_id` BIGINT UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `last_sms_sent_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `building_approval_tokens_token_unique` (`token`),
  KEY `building_approval_tokens_building_id_expires_at_index` (`building_id`, `expires_at`),
  CONSTRAINT `building_approval_tokens_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `building_approval_tokens_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) SMS gönderim logları
CREATE TABLE IF NOT EXISTS `sms_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NULL,
  `building_id` BIGINT UNSIGNED NULL,
  `phone_masked` VARCHAR(32) NOT NULL,
  `message_type` VARCHAR(64) NOT NULL,
  `provider` VARCHAR(32) NOT NULL,
  `status` VARCHAR(32) NOT NULL,
  `error` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_logs_building_id_message_type_created_at_index` (`building_id`, `message_type`, `created_at`),
  CONSTRAINT `sms_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sms_logs_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) Teklif (Quotation) modülü — 5 tablo
CREATE TABLE IF NOT EXISTS `quotation_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` ENUM('maintenance','modernization','installation','repair') NOT NULL,
  `description` TEXT NULL,
  `default_validity_days` SMALLINT UNSIGNED NOT NULL DEFAULT 15,
  `default_currency` VARCHAR(10) NOT NULL DEFAULT 'TRY',
  `scope_inclusions` TEXT NULL,
  `scope_exclusions` TEXT NULL,
  `terms` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_templates_company_id_type_is_active_index` (`company_id`, `type`, `is_active`),
  CONSTRAINT `quotation_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quotations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `quote_no` VARCHAR(60) NOT NULL,
  `type` ENUM('maintenance','modernization','installation','repair') NOT NULL,
  `status` ENUM('draft','sent','viewed','accepted','rejected','expired','converted','cancelled') NOT NULL DEFAULT 'draft',
  `building_id` BIGINT UNSIGNED NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_contact_name` VARCHAR(255) NULL,
  `customer_phone` VARCHAR(50) NULL,
  `customer_email` VARCHAR(255) NULL,
  `customer_address` TEXT NULL,
  `issue_report_id` BIGINT UNSIGNED NULL,
  `maintenance_schedule_id` BIGINT UNSIGNED NULL,
  `elevator_label_id` BIGINT UNSIGNED NULL,
  `template_id` BIGINT UNSIGNED NULL,
  `valid_until` DATE NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'TRY',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `vat_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `grand_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `scope_summary` TEXT NULL,
  `scope_inclusions` TEXT NULL,
  `scope_exclusions` TEXT NULL,
  `terms` TEXT NULL,
  `notes` TEXT NULL,
  `internal_notes` TEXT NULL,
  `public_token` VARCHAR(96) NOT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `viewed_at` TIMESTAMP NULL DEFAULT NULL,
  `accepted_at` TIMESTAMP NULL DEFAULT NULL,
  `rejected_at` TIMESTAMP NULL DEFAULT NULL,
  `converted_at` TIMESTAMP NULL DEFAULT NULL,
  `converted_receivable_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_public_token_unique` (`public_token`),
  UNIQUE KEY `quotations_company_id_quote_no_unique` (`company_id`, `quote_no`),
  KEY `quotations_company_id_type_status_index` (`company_id`, `type`, `status`),
  KEY `quotations_company_id_building_id_index` (`company_id`, `building_id`),
  CONSTRAINT `quotations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotations_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_issue_report_id_foreign` FOREIGN KEY (`issue_report_id`) REFERENCES `issue_reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_maintenance_schedule_id_foreign` FOREIGN KEY (`maintenance_schedule_id`) REFERENCES `maintenance_schedules` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_elevator_label_id_foreign` FOREIGN KEY (`elevator_label_id`) REFERENCES `elevator_labels` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `quotation_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_converted_receivable_id_foreign` FOREIGN KEY (`converted_receivable_id`) REFERENCES `receivables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quotation_units` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` BIGINT UNSIGNED NOT NULL,
  `building_id` BIGINT UNSIGNED NULL,
  `elevator_code` VARCHAR(100) NULL,
  `location` VARCHAR(255) NULL,
  `elevator_type` VARCHAR(100) NULL,
  `brand` VARCHAR(100) NULL,
  `model` VARCHAR(100) NULL,
  `capacity_kg` INT UNSIGNED NULL,
  `capacity_person` INT UNSIGNED NULL,
  `speed` VARCHAR(50) NULL,
  `stop_count` INT UNSIGNED NULL,
  `door_type` VARCHAR(100) NULL,
  `drive_type` VARCHAR(100) NULL,
  `machine_room` VARCHAR(50) NULL,
  `current_label_color` VARCHAR(50) NULL,
  `last_control_date` DATE NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `quotation_units_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotation_units_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quotation_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` BIGINT UNSIGNED NOT NULL,
  `quotation_unit_id` BIGINT UNSIGNED NULL,
  `category` ENUM('service','material','labor','inspection','discount') NOT NULL DEFAULT 'service',
  `item_code` VARCHAR(100) NULL,
  `description` TEXT NOT NULL,
  `brand` VARCHAR(100) NULL,
  `model` VARCHAR(100) NULL,
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1,
  `unit` VARCHAR(50) NOT NULL DEFAULT 'adet',
  `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount_rate` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `vat_rate` DECIMAL(5,2) NOT NULL DEFAULT 20,
  `line_subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `vat_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `line_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_items_quotation_id_sort_order_index` (`quotation_id`, `sort_order`),
  CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotation_items_quotation_unit_id_foreign` FOREIGN KEY (`quotation_unit_id`) REFERENCES `quotation_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quotation_acceptances` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` BIGINT UNSIGNED NOT NULL,
  `action` ENUM('accepted','rejected') NOT NULL,
  `accepted_by_name` VARCHAR(255) NOT NULL,
  `accepted_by_phone` VARCHAR(50) NULL,
  `accepted_by_email` VARCHAR(255) NULL,
  `accepted_ip` VARCHAR(45) NULL,
  `user_agent` VARCHAR(500) NULL,
  `customer_note` TEXT NULL,
  `terms_snapshot` TEXT NULL,
  `accepted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `quotation_acceptances_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5) Eksik olabilecek sütunlar (idempotent — varsa atlar)
DELIMITER //
DROP PROCEDURE IF EXISTS add_missing_post_cutover_columns//
CREATE PROCEDURE add_missing_post_cutover_columns()
BEGIN
  -- companies.tax_number
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'tax_number') = 0
  THEN
    ALTER TABLE `companies` ADD COLUMN `tax_number` VARCHAR(50) NULL AFTER `address`;
  END IF;

  -- maintenance_reports.building_id / title
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'building_id') = 0
  THEN
    ALTER TABLE `maintenance_reports` ADD COLUMN `building_id` BIGINT UNSIGNED NULL AFTER `company_id`;
    ALTER TABLE `maintenance_reports` ADD CONSTRAINT `maintenance_reports_building_id_foreign`
      FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;
  END IF;

  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'title') = 0
  THEN
    ALTER TABLE `maintenance_reports` ADD COLUMN `title` VARCHAR(255) NULL AFTER `building_id`;
  END IF;

  -- maintenance_reports onay alanları
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'approval_status') = 0
  THEN
    ALTER TABLE `maintenance_reports`
      ADD COLUMN `approval_status` ENUM('onay_bekliyor','onaylandi') NOT NULL DEFAULT 'onaylandi' AFTER `completion_status`,
      ADD COLUMN `approved_by_name` VARCHAR(255) NULL AFTER `approval_status`,
      ADD COLUMN `approved_at` TIMESTAMP NULL DEFAULT NULL AFTER `approved_by_name`,
      ADD COLUMN `approval_ip` VARCHAR(45) NULL AFTER `approved_at`;
  END IF;

  -- maintenance_schedules.issue_report_id
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'maintenance_schedules' AND COLUMN_NAME = 'issue_report_id') = 0
  THEN
    ALTER TABLE `maintenance_schedules` ADD COLUMN `issue_report_id` BIGINT UNSIGNED NULL AFTER `company_id`;
    ALTER TABLE `maintenance_schedules` ADD CONSTRAINT `maintenance_schedules_issue_report_id_foreign`
      FOREIGN KEY (`issue_report_id`) REFERENCES `issue_reports` (`id`) ON DELETE SET NULL;
  END IF;

  -- employees.tc_no kaldırıldı (varsa sil)
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employees' AND COLUMN_NAME = 'tc_no') > 0
  THEN
    ALTER TABLE `employees` DROP COLUMN `tc_no`;
  END IF;
END//
DELIMITER ;

CALL add_missing_post_cutover_columns();
DROP PROCEDURE IF EXISTS add_missing_post_cutover_columns;

-- 6) subscription_plans slug düzeltmesi (companies.subscription_plan enum'ıyla uyumlu hale getirir)
UPDATE `subscription_plans` SET `slug` = 'orta' WHERE `slug` = 'professional';
UPDATE `subscription_plans` SET `slug` = 'super' WHERE `slug` = 'enterprise';
