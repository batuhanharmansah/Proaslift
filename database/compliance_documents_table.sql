-- DTR (Durum Tespit Raporu) + Kurtarma Formu (Özellik #11: rakip analizi
-- karşılaştırması sonucu eklendi). Tek bir tabloda 'document_type' ile ayrılır,
-- ikisi de aynı yapıya (bina + açıklama + durum + ilgili kişi) sahip.
-- phpMyAdmin'den çalıştırın.

CREATE TABLE IF NOT EXISTS `compliance_documents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `building_id` BIGINT UNSIGNED NOT NULL,
  `document_type` VARCHAR(20) NOT NULL COMMENT 'dtr | kurtarma',
  `event_date` DATE NOT NULL,
  `inspector_or_technician_name` VARCHAR(255) NULL,
  `description` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'taslak' COMMENT 'taslak | tamamlandi | imzalandi | paylasildi | onaylandi',
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `compliance_documents_company_type_index` (`company_id`, `document_type`),
  CONSTRAINT `compliance_documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compliance_documents_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
