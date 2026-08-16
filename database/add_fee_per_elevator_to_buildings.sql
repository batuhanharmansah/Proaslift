-- Asansör Bazlı Bakım Ücreti (Özellik #7: rakip analizi karşılaştırması sonucu eklendi).
--
-- NOT: Proaslift'te "bina" ve "asansör" iç içe tek bir kayıt (asansorex'teki gibi
-- ayrı Bina + Asansör tabloları YOK) — bu yüzden tam anlamıyla "her asansörün kendi
-- ayrı sözleşmesi" modeline geçmek büyük bir veri modeli değişikliği gerektirir.
-- Bunun yerine, birden fazla asansörü olan binalarda (elevator_count > 1) admin
-- isterse "asansör başına ücret" girebilir; toplam monthly_fee bundan otomatik
-- hesaplanır. phpMyAdmin'den çalıştırın.

ALTER TABLE `buildings`
    ADD COLUMN `fee_per_elevator` DECIMAL(10,2) NULL AFTER `monthly_fee`;
