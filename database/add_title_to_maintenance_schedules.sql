-- Toplu Bakım Oluştur sihirbazı (BulkMaintenanceController) `title` alanını
-- maintenance_schedules tablosuna yazıyor ama bu sütun hiç eklenmemişti.
-- E2E test sırasında "Unknown column 'title'" 500 hatasıyla keşfedildi.
ALTER TABLE `maintenance_schedules`
    ADD COLUMN `title` VARCHAR(255) NULL AFTER `assigned_employee_id`;
