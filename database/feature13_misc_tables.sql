-- Özellik #13: Hakediş, Araç Takip, Devamsızlık, Envanter lokasyonu
-- (rakip analizi karşılaştırması sonucu eklendi). phpMyAdmin'den çalıştırın.

-- 1) Envanter — çoklu lokasyon (Product zaten cost_price/sale_price ile maliyet/kâr'a sahip)
ALTER TABLE `products`
    ADD COLUMN `location` VARCHAR(150) NULL AFTER `supplier`;

-- 2) Hakediş — teknisyene iş bazlı ek kazanç/prim
CREATE TABLE IF NOT EXISTS `employee_bonuses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `bonus_date` DATE NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'prim' COMMENT 'prim | mesai | ikramiye | diger',
  `amount` DECIMAL(10,2) NOT NULL,
  `description` VARCHAR(500) NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `employee_bonuses_company_employee_index` (`company_id`, `employee_id`),
  CONSTRAINT `employee_bonuses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_bonuses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Araç Takip
CREATE TABLE IF NOT EXISTS `company_vehicles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `plate` VARCHAR(20) NOT NULL,
  `brand_model` VARCHAR(150) NULL,
  `driver_employee_id` BIGINT UNSIGNED NULL,
  `inspection_due_date` DATE NULL,
  `insurance_due_date` DATE NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `company_vehicles_company_id_index` (`company_id`),
  CONSTRAINT `company_vehicles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_vehicles_driver_employee_id_foreign` FOREIGN KEY (`driver_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) Devamsızlık
CREATE TABLE IF NOT EXISTS `employee_absences` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'izinsiz' COMMENT 'izinli | izinsiz | raporlu | yillik_izin',
  `note` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `employee_absences_company_employee_index` (`company_id`, `employee_id`),
  CONSTRAINT `employee_absences_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_absences_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
