-- Bildirim Tercihleri Matrisi (Özellik #4: rakip analizi karşılaştırması sonucu eklendi).
-- Firma bazlı, olay tipi × kanal (push/sms) açık/kapalı tercihi. Satır yoksa varsayılan AÇIK
-- kabul edilir (bkz. NotificationPreference::isEnabled). phpMyAdmin'den çalıştırın.

CREATE TABLE IF NOT EXISTS `notification_preferences` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `event_key` VARCHAR(60) NOT NULL,
  `channel` VARCHAR(20) NOT NULL COMMENT 'push | sms',
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_preferences_unique` (`company_id`, `event_key`, `channel`),
  CONSTRAINT `notification_preferences_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
