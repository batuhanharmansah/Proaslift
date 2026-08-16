-- Firma Özelleştirme (Özellik #9: rakip analizi karşılaştırması sonucu eklendi).
-- Logo, kaşe/imza görseli ve marka renkleri. phpMyAdmin'den çalıştırın.

ALTER TABLE `companies`
    ADD COLUMN `logo_path` VARCHAR(255) NULL AFTER `notes`,
    ADD COLUMN `stamp_path` VARCHAR(255) NULL AFTER `logo_path`,
    ADD COLUMN `brand_primary_color` VARCHAR(7) NULL AFTER `stamp_path`,
    ADD COLUMN `brand_secondary_color` VARCHAR(7) NULL AFTER `brand_primary_color`;
