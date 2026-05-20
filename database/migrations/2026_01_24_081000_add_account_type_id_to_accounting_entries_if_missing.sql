-- Migration: 2026_01_24_081000_add_account_type_id_to_accounting_entries_if_missing
-- accounting_entries tablosuna account_type_id sütununu ekler (yoksa).
-- Veritabanı: MySQL / MariaDB

-- =============================================================================
-- UP: Sütun ve foreign key ekleme
-- =============================================================================

ALTER TABLE `accounting_entries`
ADD COLUMN `account_type_id` BIGINT UNSIGNED NULL AFTER `company_id`;

ALTER TABLE `accounting_entries`
ADD CONSTRAINT `accounting_entries_account_type_id_foreign`
  FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON DELETE SET NULL;


-- =============================================================================
-- DOWN: Geri alma
-- =============================================================================

-- ALTER TABLE `accounting_entries`
-- DROP FOREIGN KEY `accounting_entries_account_type_id_foreign`;

-- ALTER TABLE `accounting_entries`
-- DROP COLUMN `account_type_id`;
