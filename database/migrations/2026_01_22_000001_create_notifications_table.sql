-- Migration: 2026_01_22_000001_create_notifications_table
-- notifications tablosunu oluşturur.
-- Veritabanı: MySQL 5.7+ / MariaDB 10.2+
--
-- Güvenle çalıştırın: Tablo ZATEN VARSA hiçbir şey yapmaz (IF NOT EXISTS).
-- Tablo YOKSA oluşturur. information_schema erişimi gerekmez.

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Bildirimin gönderildiği kullanıcı',
  `company_id` BIGINT UNSIGNED NOT NULL COMMENT 'Şirket bazlı filtreleme için',

  `type` ENUM('maintenance', 'issue', 'financial', 'employee', 'system', 'general') NOT NULL COMMENT 'Bakım, Arıza, Finansal, Personel, Sistem, Genel',
  `priority` ENUM('critical', 'high', 'medium', 'low') NOT NULL DEFAULT 'medium' COMMENT 'Kritik, Yüksek, Orta, Düşük',

  `title` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,

  `data` TEXT NULL COMMENT 'Action data - JSON string (screen, params, vb.)',
  `related_entity_type` VARCHAR(255) NULL COMMENT 'building, maintenance, issue, vb.',
  `related_entity_id` BIGINT UNSIGNED NULL,

  `read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL,

  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,

  INDEX `notifications_user_id_read_index` (`user_id`, `read`),
  INDEX `notifications_company_id_read_index` (`company_id`, `read`),
  INDEX `notifications_type_read_index` (`type`, `read`),
  INDEX `notifications_priority_read_index` (`priority`, `read`),
  INDEX `notifications_created_at_index` (`created_at`),
  INDEX `notifications_related_entity_index` (`related_entity_type`(191), `related_entity_id`),

  CONSTRAINT `notifications_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_company_id_foreign`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- DOWN: Tablo silme
-- =============================================================================

-- DROP TABLE IF EXISTS `notifications`;
