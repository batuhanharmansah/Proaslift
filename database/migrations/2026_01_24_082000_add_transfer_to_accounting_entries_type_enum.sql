-- Migration: 2026_01_24_082000_add_transfer_to_accounting_entries_type_enum
-- accounting_entries.type ENUM'a 'transfer' ekler.
-- Transfer işlemi yapabilmek için canlıda bu SQL'i çalıştırın.

-- =============================================================================
-- UP: type ENUM'a 'transfer' ekleme
-- =============================================================================

ALTER TABLE `accounting_entries`
MODIFY COLUMN `type` ENUM('gelir', 'gider', 'maas', 'vergi', 'sigorta', 'transfer') NOT NULL;


-- =============================================================================
-- DOWN: Geri alma (transfer kaldırılır; transfer kayıtları varsa önce güncelleyin)
-- =============================================================================

-- ALTER TABLE `accounting_entries`
-- MODIFY COLUMN `type` ENUM('gelir', 'gider', 'maas', 'vergi', 'sigorta') NOT NULL;
