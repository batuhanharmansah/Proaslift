-- Migration: 2026_01_24_080000_add_created_by_to_accounting_entries_if_missing
-- accounting_entries tablosuna created_by sütununu ekler (yoksa).
-- Veritabanı: MySQL / MariaDB

-- =============================================================================
-- UP: Sütun ve foreign key ekleme
-- Not: Sütun zaten varsa ALTER hata verir; o durumda sadece UP içindeki
--      ilk satırı atlayın veya "IF NOT EXISTS" kullanan sürümü kullanın.
-- =============================================================================

-- 1. created_by sütununu ekle (notes'tan sonra)
ALTER TABLE `accounting_entries`
ADD COLUMN `created_by` BIGINT UNSIGNED NULL AFTER `notes`;

-- 2. users tablosuna foreign key ekle
ALTER TABLE `accounting_entries`
ADD CONSTRAINT `accounting_entries_created_by_foreign`
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;


-- =============================================================================
-- DOWN: Geri alma (sütun ve foreign key kaldırma)
-- =============================================================================

-- 1. Foreign key'i kaldır
-- ALTER TABLE `accounting_entries`
-- DROP FOREIGN KEY `accounting_entries_created_by_foreign`;

-- 2. Sütunu kaldır
-- ALTER TABLE `accounting_entries`
-- DROP COLUMN `created_by`;


-- =============================================================================
-- İsteğe bağlı: Sadece sütun yoksa ekleyen (idempotent) UP örneği
-- MySQL 8+ veya MariaDB 10.5+ için procedure ile:
-- =============================================================================
/*
DELIMITER //

DROP PROCEDURE IF EXISTS add_created_by_to_accounting_entries//

CREATE PROCEDURE add_created_by_to_accounting_entries()
BEGIN
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'accounting_entries'
        AND COLUMN_NAME = 'created_by') = 0
  THEN
    ALTER TABLE `accounting_entries`
    ADD COLUMN `created_by` BIGINT UNSIGNED NULL AFTER `notes`;

    ALTER TABLE `accounting_entries`
    ADD CONSTRAINT `accounting_entries_created_by_foreign`
      FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
  END IF;
END//

DELIMITER ;

CALL add_created_by_to_accounting_entries();
DROP PROCEDURE IF EXISTS add_created_by_to_accounting_entries;
*/
