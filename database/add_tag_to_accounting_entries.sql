-- Elevatora.com karşılaştırması Özellik E4: Etiket bazlı gelir/gider kategorileme
-- (kategori zaten sabit enum; tag serbest metin, "Yakıt", "Kira" gibi ek kırılım için)
ALTER TABLE `accounting_entries`
    ADD COLUMN `tag` VARCHAR(50) NULL AFTER `notes`;

ALTER TABLE `accounting_entries`
    ADD INDEX `accounting_entries_company_tag_index` (`company_id`, `tag`);
