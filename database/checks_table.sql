-- Çek & Senet Takibi (Özellik #8: rakip analizi karşılaştırması sonucu eklendi).
-- phpMyAdmin'den çalıştırın.

CREATE TABLE IF NOT EXISTS `checks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(20) NOT NULL COMMENT 'cek | senet',
  `direction` VARCHAR(10) NOT NULL COMMENT 'gelen | giden',
  `counterparty_name` VARCHAR(255) NOT NULL COMMENT 'cari adı (müşteri/tedarikçi vb.)',
  `serial_number` VARCHAR(100) NULL,
  `bank_name` VARCHAR(150) NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `due_date` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'bekliyor' COMMENT 'bekliyor | tahsil_edildi | odendi | karsiliksiz | iade | ciro_edildi',
  `building_id` BIGINT UNSIGNED NULL,
  `notes` TEXT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `checks_company_status_index` (`company_id`, `status`),
  INDEX `checks_company_due_date_index` (`company_id`, `due_date`),
  CONSTRAINT `checks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checks_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
