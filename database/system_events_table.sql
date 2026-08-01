-- Sistem Sağlığı İzleme Tablosu (system_events)
-- phpMyAdmin üzerinden manuel olarak çalıştırın. Laravel migration dosyası DEĞİLDİR,
-- bu yüzden `php artisan migrate` bu tabloyu oluşturmaz/takip etmez.

CREATE TABLE IF NOT EXISTS `system_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source` VARCHAR(20) NOT NULL COMMENT 'web | mobile',
  `type` VARCHAR(50) NOT NULL COMMENT 'exception | queue_failed | throttle_blocked | mobile_crash | mobile_api_error | health_check',
  `severity` VARCHAR(20) NOT NULL DEFAULT 'warning' COMMENT 'critical | warning | info',
  `message` TEXT NOT NULL,
  `stack_trace` LONGTEXT NULL,
  `context` JSON NULL COMMENT 'url, route, method, user_id, company_id, app_version, device, screen vb.',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `system_events_source_type_index` (`source`, `type`),
  INDEX `system_events_severity_index` (`severity`),
  INDEX `system_events_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
