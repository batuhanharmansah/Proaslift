-- system_events tablosuna kategori sütunu ekler (Sistem Sağlığı sayfasındaki
-- hataları anlamlı gruplara ayırabilmek için). phpMyAdmin üzerinden çalıştırın.

ALTER TABLE `system_events`
    ADD COLUMN `category` VARCHAR(50) NULL AFTER `type`,
    ADD INDEX `system_events_category_index` (`category`);
