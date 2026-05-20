# Bildirimler – Canlı Sunucuya Atılacak Dosyalar

Bu liste, mobil uygulamanın **500** aldığı bildirim endpoint'lerini düzeltmek ve bildirim sistemini canlıda çalıştırmak için **web tarafında** canlıya atmanız gereken tüm dosyaları içerir.

---

## 1. Hata Özeti (500 Sebepleri)

| Endpoint | HTTP | Olası sebep |
|----------|------|-------------|
| `GET /api/mobile/notifications/unread-count` | 500 | `notifications` tablosu canlıda yok veya `company_id` null |
| `GET /api/mobile/notifications?page=1&...` | 500 | Aynı sebep |

**Yapılan düzeltmeler (bu repoda):**
- Controller, tablo yoksa veya `company_id` null ise artık **500 yerine 200 + boş veri** dönüyor (mobil uygulama çökmez).
- Canlıda **mutlaka** `notifications` migration'ı çalıştırılmalı; yoksa sadece boş liste/sayı döner.

---

## 2. Canlıya Atılacak Dosya Listesi (Bildirim ile ilgili)

### Zorunlu (500’ü kaldırmak ve bildirimleri açmak için)

| # | Dosya yolu | Açıklama |
|---|------------|----------|
| 1 | `app/Models/Notification.php` | Bildirim modeli |
| 2 | `app/Http/Controllers/Api/Mobile/NotificationController.php` | Mobil bildirim API controller (güncel, hata toleranslı) |
| 3 | `app/Services/NotificationService.php` | Bildirim oluşturma servisi (bakım/arıza/ödeme vb.) |
| 4 | `routes/api.php` | Mobil notification route'ları burada da tanımlı |
| 5 | `routes/mobile-api.php` | Tam mobil notification route seti |
| 6 | `database/migrations/2026_01_22_000001_create_notifications_table.php` | **ÖNEMLİ:** `notifications` tablosunu oluşturur |

### Bağımlılık (bildirim tetikleyen modüller için)

| # | Dosya yolu | Açıklama |
|---|------------|----------|
| 7 | `app/Observers/MaintenanceScheduleObserver.php` | Bakım planlanınca/tamamlanınca bildirim |
| 8 | `app/Http/Controllers/Api/Mobile/IssueReportController.php` | Arıza bildirimi oluşturulunca bildirim |
| 9 | `app/Http/Controllers/Api/Mobile/FinancialController.php` | Ödeme alınınca bildirim |
| 10 | `app/Console/Commands/SendMaintenanceReminders.php` | Bakım hatırlatma bildirimleri |
| 11 | `app/Console/Commands/SendPaymentReminders.php` | Ödeme hatırlatma bildirimleri |

### Web arayüzü (bildirim sayfaları – varsa)

Bildirimler için **ayrı bir web sayfası** (Blade) yok; mobil API JSON döner. Web’de sadece asansör etiketine bağlı bildirim listesi var:

| # | Dosya yolu | Açıklama |
|---|------------|----------|
| 12 | `app/Http/Controllers/ElevatorLabelController.php` | `notifications()` metodu – asansör etiket bildirimleri |
| 13 | `routes/web.php` | `/{elevatorLabel}/notifications` route’u burada |

(ElevatorLabel ile ilgili view’lar: `resources/views/elevator-labels/*` – sadece asansör etiket detayında bildirim kısmı varsa ilgili blade’leri de atın.)

---

## 3. Canlıda Yapılacak Adımlar

1. **Dosyaları at:** Yukarıdaki tablodaki dosyaları canlı sunucudaki `asansor-web` projesine kopyalayın (aynı yollara).
2. **Migration çalıştır (önemli):**
   ```bash
   cd /path/to/asansor-web
   php artisan migrate
   ```
   Özellikle `2026_01_22_000001_create_notifications_table` migration’ının çalıştığından emin olun. Tablo yoksa API 500 yerine boş dönecek ama bildirim kaydı oluşmaz.
3. **Cache temizle:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```
4. **Kontrol:** Mobil uygulamadan tekrar giriş yapıp:
   - `GET /api/mobile/notifications/unread-count` → 200 + `data.total_unread` vb.
   - `GET /api/mobile/notifications?page=1&per_page=20&...` → 200 + liste (boş da olabilir)

---

## 4. Kısa Dosya Yolu Listesi (kopyala-yapıştır)

```
app/Models/Notification.php

app/Http/Controllers/Api/Mobile/NotificationController.php
app/Services/NotificationService.php

app/Observers/MaintenanceScheduleObserver.php

app/Http/Controllers/Api/Mobile/IssueReportController.php

app/Http/Controllers/Api/Mobile/FinancialController.php

app/Console/Commands/SendMaintenanceReminders.php
app/Console/Commands/SendPaymentReminders.php

app/Http/Controllers/ElevatorLabelController.php

routes/api.php
routes/mobile-api.php
routes/web.php
database/migrations/2026_01_22_000001_create_notifications_table.php
```

---

**Not:** Loglarda ayrıca `GET /api/location-map/location-checks/{id}` için **404** görünüyor; bu bildirimle ilgili değil, konum kontrolü endpoint’i. İsterseniz ayrı bir checklist ile ele alın.
