-- Opsiyonel: Düzenli ödemelerden türetilen alacak/borç kayıtlarını ilişkilendirmek için.
-- Birleşik finansal sayfa için ZORUNLU DEĞİLDİR. Sadece ileride bu ilişki kullanılacaksa çalıştırın.

-- receivables: düzenli ödemeye bağlı alacak (örn. bina geliri)
ALTER TABLE receivables
ADD COLUMN recurring_payment_id BIGINT UNSIGNED NULL AFTER building_id,
ADD CONSTRAINT receivables_recurring_payment_id_foreign
  FOREIGN KEY (recurring_payment_id) REFERENCES recurring_payments(id) ON DELETE SET NULL;

-- payables: düzenli ödemeye bağlı borç (örn. aylık elektrik)
ALTER TABLE payables
ADD COLUMN recurring_payment_id BIGINT UNSIGNED NULL AFTER company_id,
ADD CONSTRAINT payables_recurring_payment_id_foreign
  FOREIGN KEY (recurring_payment_id) REFERENCES recurring_payments(id) ON DELETE SET NULL;

-- Geri almak için (gerekirse):
-- ALTER TABLE receivables DROP FOREIGN KEY receivables_recurring_payment_id_foreign, DROP COLUMN recurring_payment_id;
-- ALTER TABLE payables DROP FOREIGN KEY payables_recurring_payment_id_foreign, DROP COLUMN recurring_payment_id;
