-- Müşteri Portalı (Özellik #6: rakip analizi karşılaştırması sonucu eklendi).
-- Ana User/auth tablolarına HİÇ dokunulmadı — tamamen izole, ayrı bir tablo ve
-- ayrı bir session tabanlı giriş akışı (guard config değişikliği yok, mevcut
-- kimlik doğrulama sistemi için sıfır risk). phpMyAdmin'den çalıştırın.

CREATE TABLE IF NOT EXISTS `building_portal_accounts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `building_id` BIGINT UNSIGNED NOT NULL,
  `phone` VARCHAR(20) NOT NULL COMMENT 'Login için kullanılır, +90 dahil normalize edilmiş',
  `password` VARCHAR(255) NOT NULL COMMENT 'Hashed (bcrypt)',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `building_portal_accounts_building_id_unique` (`building_id`),
  UNIQUE KEY `building_portal_accounts_phone_unique` (`phone`),
  CONSTRAINT `building_portal_accounts_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
