-- =============================================================================
-- Bakım Onayı (SMS + Public Form) — Natro / phpMyAdmin tek seferlik SQL
-- Tarih: 2026-05-21
-- Proje: asansor-web
--
-- Kullanım:
--   1. Natro panel → phpMyAdmin → veritabanınızı seçin
--   2. SQL sekmesine bu dosyanın tamamını yapıştırıp çalıştırın
--   3. Hata alırsanız: "Duplicate column" / "Table already exists" → o satır
--      zaten uygulanmış demektir; devam edebilir veya o bloğu atlayabilirsiniz
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1) maintenance_reports — onay alanları
-- -----------------------------------------------------------------------------

SET @db := DATABASE();

-- approval_status (completion_status varsa onun ardina, yoksa tablo sonuna)
SET @has_completion := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'completion_status'
);
SET @sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'approval_status') = 0,
        IF(@has_completion > 0,
            'ALTER TABLE `maintenance_reports` ADD COLUMN `approval_status` ENUM(''onay_bekliyor'',''onaylandi'') NOT NULL DEFAULT ''onaylandi'' COMMENT ''Bina yoneticisi SMS onayi'' AFTER `completion_status`',
            'ALTER TABLE `maintenance_reports` ADD COLUMN `approval_status` ENUM(''onay_bekliyor'',''onaylandi'') NOT NULL DEFAULT ''onaylandi'' COMMENT ''Bina yoneticisi SMS onayi'''
        ),
        'SELECT ''approval_status zaten var'' AS info'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- approved_by_name
SET @sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'approved_by_name') = 0,
        'ALTER TABLE `maintenance_reports` ADD COLUMN `approved_by_name` VARCHAR(255) NULL DEFAULT NULL AFTER `approval_status`',
        'SELECT ''approved_by_name zaten var'' AS info'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- approved_at
SET @sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'approved_at') = 0,
        'ALTER TABLE `maintenance_reports` ADD COLUMN `approved_at` TIMESTAMP NULL DEFAULT NULL AFTER `approved_by_name`',
        'SELECT ''approved_at zaten var'' AS info'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- approval_ip
SET @sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'maintenance_reports' AND COLUMN_NAME = 'approval_ip') = 0,
        'ALTER TABLE `maintenance_reports` ADD COLUMN `approval_ip` VARCHAR(45) NULL DEFAULT NULL AFTER `approved_at`',
        'SELECT ''approval_ip zaten var'' AS info'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mevcut raporlar onayli kabul edilir (guvenli guncelleme)
UPDATE `maintenance_reports`
SET `approval_status` = 'onaylandi'
WHERE `approval_status` IS NULL OR `approval_status` = '';

-- completion_status kolonu yoksa (cok eski DB): kolonlari tablo sonuna eklemek icin
-- asagidaki blok sadece approval_status hala yoksa calisir — manuel kontrol
-- ALTER TABLE maintenance_reports ADD COLUMN approval_status ... ;  (AFTER olmadan)

-- -----------------------------------------------------------------------------
-- 2) building_approval_tokens — SMS link tokenlari (binaya gore)
-- -----------------------------------------------------------------------------

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
    KEY `building_approval_tokens_company_id_foreign` (`company_id`),
    CONSTRAINT `building_approval_tokens_company_id_foreign`
        FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
    CONSTRAINT `building_approval_tokens_building_id_foreign`
        FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3) sms_logs — gonderim kayitlari
-- -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sms_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `building_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `phone_masked` VARCHAR(32) NOT NULL,
    `message_type` VARCHAR(64) NOT NULL,
    `provider` VARCHAR(32) NOT NULL,
    `status` VARCHAR(32) NOT NULL,
    `error` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `sms_logs_building_id_message_type_created_at_index` (`building_id`, `message_type`, `created_at`),
    KEY `sms_logs_company_id_foreign` (`company_id`),
    CONSTRAINT `sms_logs_company_id_foreign`
        FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
    CONSTRAINT `sms_logs_building_id_foreign`
        FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4) Laravel migrations tablosu (opsiyonel — artisan migrate kullanmiyorsaniz)
--    Ayni migration tekrar calismasin diye kayit ekler
-- -----------------------------------------------------------------------------

SET @batch := (SELECT IFNULL(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_05_21_000001_add_approval_fields_to_maintenance_reports_table', @batch
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_05_21_000001_add_approval_fields_to_maintenance_reports_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_05_21_000002_create_building_approval_tokens_table', @batch
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_05_21_000002_create_building_approval_tokens_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_05_21_000003_create_sms_logs_table', @batch
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_05_21_000003_create_sms_logs_table'
);

SET FOREIGN_KEY_CHECKS = 1;

-- Bitti.
SELECT 'Bakim onay SQL basariyla tamamlandi.' AS sonuc;
