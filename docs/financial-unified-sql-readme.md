# Finansal Birleşik Sayfa – Veritabanı Notları

## 404 alıyorsanız (`/finansal/islemler` veya `/finansal/gun-sonu`)

Sunucuda **route cache** kullanılıyorsa ve bu sayfalar yeni eklendiyse, cache eski olabilir. Deploy sonrası sunucuda şunları çalıştırın:

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

İsterseniz sonrasında tekrar cache alabilirsiniz: `php artisan route:cache`

---

## Zorunlu migration yok

**Alacaklar, Borçlar ve Düzenli Ödemeler** birleşik sayfası (`/finansal/islemler`) mevcut tabloları kullanır:

- `receivables`
- `payables`
- `recurring_payments`
- `account_types`
- `buildings`

Bu sayfayı kullanmak için **ek bir veritabanı migration’ı çalıştırmanız gerekmez.**

---

## Opsiyonel migration (ileride kullanım için)

İleride “düzenli ödemeden otomatik türetilen alacak/borç” gibi bir özellik eklerseniz, alacak ve borç kayıtlarını düzenli ödemeye bağlamak için aşağıdaki sütunlar eklenebilir:

- `receivables.recurring_payment_id` (nullable, FK → `recurring_payments.id`)
- `payables.recurring_payment_id` (nullable, FK → `recurring_payments.id`)

Bu değişiklik **şu anki birleşik sayfa için gerekli değildir.** İsterseniz `database/sql/optional_add_recurring_payment_id_to_receivables_payables.sql` dosyasını çalıştırabilirsiniz.
