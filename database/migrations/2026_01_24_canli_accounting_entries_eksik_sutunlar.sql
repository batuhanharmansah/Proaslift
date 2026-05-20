-- =============================================================================
-- CANLI: accounting_entries eksik sütunlar (created_by, account_type_id)
-- Her iki sütun yoksa ekler; varsa atlar. Tek seferde çalıştırın.
-- MySQL 5.7+ / MariaDB 10.2+
-- =============================================================================

DELIMITER //

DROP PROCEDURE IF EXISTS add_accounting_entries_missing_columns//

CREATE PROCEDURE add_accounting_entries_missing_columns()
BEGIN
  -- 1. created_by yoksa ekle
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

  -- 2. account_type_id yoksa ekle
  IF (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'accounting_entries'
        AND COLUMN_NAME = 'account_type_id') = 0
  THEN
    ALTER TABLE `accounting_entries`
    ADD COLUMN `account_type_id` BIGINT UNSIGNED NULL AFTER `company_id`;

    ALTER TABLE `accounting_entries`
    ADD CONSTRAINT `accounting_entries_account_type_id_foreign`
      FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON DELETE SET NULL;
  END IF;
END//

DELIMITER ;

CALL add_accounting_entries_missing_columns();
DROP PROCEDURE IF EXISTS add_accounting_entries_missing_columns;


-- =============================================================================
-- ALTERNATİF: Procedure desteklenmiyorsa aşağıdakileri SIRAYLA çalıştırın.
-- "Duplicate column" hatası alırsanız o blok zaten uygulanmıştır, sonrakine geçin.
-- =============================================================================

-- 1) created_by (canlıda muhtemelen EKSİK – önce bunu çalıştırın)
/*
ALTER TABLE `accounting_entries`
ADD COLUMN `created_by` BIGINT UNSIGNED NULL AFTER `notes`;

ALTER TABLE `accounting_entries`
ADD CONSTRAINT `accounting_entries_created_by_foreign`
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
*/

-- 2) account_type_id (zaten eklediyseniz "Duplicate column" alırsınız, sorun yok)
/*
ALTER TABLE `accounting_entries`
ADD COLUMN `account_type_id` BIGINT UNSIGNED NULL AFTER `company_id`;

ALTER TABLE `accounting_entries`
ADD CONSTRAINT `accounting_entries_account_type_id_foreign`
  FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON DELETE SET NULL;
*/
