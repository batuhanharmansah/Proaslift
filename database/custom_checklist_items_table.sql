-- Firma özel bakım kontrol listesi maddeleri (Özellik #1: rakip analizi karşılaştırması sonucu)
-- Mevcut sabit checklist'e (4 bölüm: machine_room/floors/cabin_interior_top/shaft_interior)
-- firmanın kendi ek maddelerini eklemesini sağlar. phpMyAdmin'den çalıştırın.

CREATE TABLE IF NOT EXISTS `custom_checklist_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `section_id` VARCHAR(50) NOT NULL COMMENT 'machine_room | floors | cabin_interior_top | shaft_interior',
  `item_key` VARCHAR(100) NOT NULL COMMENT 'benzersiz slug, örn. custom_<company_id>_<sıra>',
  `title` VARCHAR(500) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_checklist_items_company_item_key_unique` (`company_id`, `item_key`),
  INDEX `custom_checklist_items_company_section_index` (`company_id`, `section_id`),
  CONSTRAINT `custom_checklist_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
