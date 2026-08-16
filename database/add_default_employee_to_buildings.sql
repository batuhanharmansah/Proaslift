-- Bina için "Varsayılan Teknisyen" alanı (Özellik #2: Toplu Bakım sihirbazında
-- "bina varsayılanı" atama stratejisi için kullanılır). phpMyAdmin'den çalıştırın.

ALTER TABLE `buildings`
    ADD COLUMN `default_employee_id` BIGINT UNSIGNED NULL AFTER `status`,
    ADD CONSTRAINT `buildings_default_employee_id_foreign`
        FOREIGN KEY (`default_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
