# Todo Listesi

Web tarafında yapılan güncellemeler (finansal birleşik sayfa, Gün Sonu, ürün markası vb.) ile uyumlu kalan işler.

---

## Tamamlanan

### Web
- [x] Finansal birleşik sayfa (`/finansal/islemler`) ve tek "Yeni İşlem" modalı (Alacak/Borç + Tek seferlik/Düzenli)
- [x] Gün Sonu sayfası (`/finansal/gun-sonu`) + Excel/PDF indirme
- [x] Ürün kodu → Ürün markası (web view ve export)
- [x] Sunucu: route/config/cache clear (proaslift deploy sonrası)

### Mobil API (Backend)
- [x] Gün Sonu: `GET /api/mobile/financial/day-end?date=YYYY-MM-DD` (işlemler + day_income, day_expense, day_net)
- [x] Ürün listesi/rapor response'ında `brand` alias (code ile aynı; "Ürün markası")
- [x] MOBILE_API_DOCUMENTATION: createRecurringPayment `category`, `day_of_month`; day-end endpoint ve ürün code/brand açıklaması

### Mobil Uygulama (asansor-mobile)
- [x] Finansal: Tek "İşlemler" listesi (UnifiedTransactions) + tür filtresi (Tümü/Alacak/Borç/Düzenli)
- [x] Yeni İşlem modalı: Alacak/Borç + "Tek seferlik" / "Düzenli" (zorunlu); düzenli için sıklık, ödeme günü, başlangıç; Alacak için bina, Borç için kategori
- [x] Gün Sonu ekranı (DayEnd): tarih seçici + işlem listesi + günlük özet (gelir/gider/net)
- [x] "Ürün Kodu" etiketleri → "Marka" (DepotScreen, CreateMaintenanceReportScreen)

### Dokümantasyon
- [x] MOBILE_API_DOCUMENTATION.md: Financial day-end endpoint, recurring category/day_of_month, ürün code/brand

### Opsiyonel (yapıldı)
- [x] Kılavuz (guide) sayfasında birleşik finansal sayfa ve Gün Sonu kullanımı

---

## Opsiyonel / İleride

- [ ] `receivables` ve `payables` tablolarına `recurring_payment_id` (docs’taki `optional_add_recurring_payment_id_to_receivables_payables.sql`)
