# Rakip Karşılaştırması — Özet Doküman
**Asansorex.com + Elevatora.com vs Proaslift**

Bu doküman iki ayrı rakip analizinden (asansorex.com ve elevatora.com) çıkan tüm önerileri, hangilerinin Proaslift'e uygulandığını, hangilerinin bilinçli olarak uygulanmadığını ve Proaslift'in mevcut tüm modüllerini tek yerde toplar.

---

## 1. Proaslift — Mevcut Tüm Modüller (Web)

Sidebar'da yer alan ana modüller:

| Modül | Route adı | Açıklama |
|---|---|---|
| Ana Sayfa | `dashboard.index` | Genel özet, KPI kartları |
| Binalar | `buildings.index` | Bina/asansör kaydı (Bina = Asansör, ayrı asansör tablosu yok) |
| Personeller | `employees.index` | Personel kayıt, profil, performans |
| Bakım Takibi | `maintenance.index` | Periyodik bakım planlama/takip |
| Rota Planlayıcı | `maintenance.route-planner.index` | Harita üzerinde bakım rotası optimizasyonu *(bu oturumda eklendi)* |
| Toplu Bakım Oluştur | `maintenance.bulk.create` | Aylık toplu bakım üretme sihirbazı *(bu oturumda eklendi)* |
| Arıza Bildirimleri | `issue-reports.index` | Arıza kayıt/takip |
| Etiket Takibi | `elevator-labels.index` | Asansör mühür/etiket sistemi, bildirim ve eskalasyon |
| Depo | `products.index` | Ürün/stok yönetimi (düz liste, konum alanı eklendi) |
| Finansal Yönetim | `financial.index` | Hesaplar, hızlı işlem, gün sonu, işlem geçmişi |
| — Alacaklar/Borçlar/Düzenli Ödemeler | `financial.receivables/payables/recurring-payments` | |
| — Finans Raporu | `financial.report` | Aylık gelir/gider/kâr, kategori + **etiket** kırılımı *(E4 bu oturumda eklendi)* |
| — Kâr / Maliyet Raporu | `financial.kar-maliyet` | Net satış − maliyet, personel/diğer maliyet ayrımı, 6 aylık trend *(E9 bu oturumda eklendi)* |
| Cariler | `cariler.index` | Birleşik müşteri/tedarikçi/personel bakiye görünümü *(bu oturumda eklendi)* |
| Çek & Senet | `checks.index` | Vade takipli çek/senet *(bu oturumda eklendi)* |
| Teklifler | `quotations.index` | Teklif oluşturma → onay → bina/alacak zinciri |
| Raporlar | `reports.index` | Bakım/finansal/müşteri/personel raporları |
| Durum Tespit Raporu (DTR) | `belgeler/dtr` (`compliance-documents.index`) | *(bu oturumda eklendi)* |
| Kurtarma Formu | `belgeler/kurtarma` (`compliance-documents.index`) | *(bu oturumda eklendi)* |
| Hakediş & Araç Takip | `hr-fleet.index` | Personel prim/mesai, araç muayene/sigorta, devamsızlık *(bu oturumda eklendi)* |
| Konum Haritası | `location-map.index` | Bina konumları haritası |
| Kullanım Kılavuzu | `guide.index` | |
| Bildirim Tercihleri | `settings.notification-preferences` | Push/SMS matrisi *(bu oturumda eklendi)* |
| Kontrol Listesi Öğeleri | `settings.checklist-items` | Bakım checklist özelleştirme *(bu oturumda eklendi)* |
| Firma Profili | `company.profile` | Logo/kaşe/marka rengi *(bu oturumda genişletildi)* |
| Müşteri Portalı | `portal.giris` | Bina bazlı salt-okunur müşteri girişi *(bu oturumda eklendi)* |

Mobil (React Native, native app): Ana Sayfa, Binalar, Personel, Bakım (+ Aktif İş/Rapor/QR), Finansal, Arıza, Depo, Etiket Takibi, Bildirimler, Firma Profili, Profil.

---

## 2. Asansorex.com Karşılaştırması — Uygulanan Maddeler (11/14)

| # | Özellik | Durum |
|---|---|---|
| 1 | Yapılandırılmış Bakım Kontrol Listesi (Checklist) | ✅ Uygulandı |
| 2 | Aylık Toplu Bakım Üretme Sihirbazı | ✅ Uygulandı |
| 3 | Rota Planlayıcı | ✅ Uygulandı |
| 4 | Bildirim Tercihleri Matrisi | ✅ Uygulandı |
| 5 | Unified Cari Hesap Mimarisi | ✅ Uygulandı (scope daraltılmış: salt-okunur özet) |
| 6 | Müşteri Portalı | ✅ Uygulandı (scope daraltılmış: salt-okunur) |
| 7 | Asansör Bazlı Bakım Ücreti | ✅ Uygulandı (çarpan mantığıyla, ayrı Asansör tablosu açmadan) |
| 8 | Çek & Senet Takibi | ✅ Uygulandı |
| 9 | Firma Özelleştirme (Logo/Kaşe/Marka Renkleri) | ✅ Uygulandı |
| 10 | Saha Bölgeleri | ❌ **Uygulanmadı** (kullanıcı talebiyle kapsam dışı) |
| 11 | DTR + Kurtarma Formu | ✅ Uygulandı |
| 12 | e-Fatura Entegrasyonu | ❌ **Uygulanmadı** (kullanıcı talebiyle kapsam dışı) |
| 13 | Hakediş / Araç Takip / Devamsızlık / Envanter Geliştirme | ✅ Uygulandı |
| 14 | Asansör Siparişleri | ❌ **Uygulanmadı** (kullanıcı talebiyle kapsam dışı) |

**Uygulanmayanların nedeni:** Kullanıcı açık talimatla "10-12-14 hariç" dedi — bilinçli kapsam dışı bırakma, teknik engel değil.

---

## 3. Elevatora.com Karşılaştırması — Değerlendirme

Elevatora, Proaslift'ten temelde farklı bir ürün: bakımın yanında **yeni asansör kurulum projesi yönetimi** (10 aşamalı pipeline), ayrı Bina/Asansör varlık modeli, tam ERP kapsamı (Cari, Sözleşme, 3 seviyeli stok taksonomisi, canlı personel GPS haritası, RBAC).

| # | Özellik | Durum | Gerekçe |
|---|---|---|---|
| E4 | **Etiket bazlı gelir/gider kategorileme** | ✅ **Uygulandı** | Düşük efor, gerçek ihtiyaç — Finans Raporu'na eklendi |
| E9 | **Kâr / Maliyet Raporu (ayrı sayfa)** | ✅ **Uygulandı** | Düşük efor, mevcut veriden üretildi — net satış/maliyet/kâr marjı + 6 aylık trend |
| E5 | Müşteri portal talep girişi (arıza/bakım talebi) | ❌ Uygulanmadı | Değerli ama Portal'ın (#6) doğal devamı — ayrı bir round'da ele alınacak, spam/rate-limit tasarımı gerekiyor |
| E3 | Ayrı Sözleşme modülü (gelir/gider sözleşmesi) | ❌ Uygulanmadı | Şu an Cariler + Building.monthly_fee ile örtük karşılanıyor, zorlama ihtiyaç değil |
| E1 | Canlı personel GPS haritası | ❌ Uygulanmadı | KVKK/gizlilik riski, pil tüketimi, öncelik değil — istenirse ayrı gündem |
| E6 | Rol bazlı yetkilendirme (RBAC) | ❌ Uygulanmadı | Auth katmanına dokunmak yüksek risk, canlı sistemde acele edilmeyecek bir değişiklik |
| E2 | Bina ≠ Asansör (ayrı asansör varlığı) | ❌ Uygulanmadı | Büyük mimari değişiklik, Proaslift'in temel veri modelini kırar |
| E7 | Proje/kurulum pipeline'ı (10 aşama) | ❌ Uygulanmadı | Farklı ürün segmenti (montaj firması ERP'si), Proaslift bakım firmalarına odaklı |
| E8 | 3 seviyeli stok taksonomisi (Kategori→Tür→Tanım) | ❌ Uygulanmadı | Küçük/orta firma için gereksiz karmaşıklık, düz liste yeterli |

---

## 4. Sonuç

- **Toplam uygulanan:** Asansorex'ten 11 madde + Elevatora'dan 2 madde (E4, E9) = **13 özellik**, tamamı canlıya alınmadan önce MAMP'ta test edilecek (bkz. `TEST_ADIMLARI_RAKIP_OZELLIKLER.md`).
- **Bilinçli kapsam dışı (asansorex):** 10, 12, 14 — kullanıcı talimatıyla.
- **Bilinçli kapsam dışı (elevatora):** E1, E2, E3, E5, E6, E7, E8 — düşük öncelik, yüksek risk veya farklı ürün segmenti olduğu için şimdilik ertelendi; hiçbiri teknik olarak imkansız değil, sadece maliyet/fayda veya risk dengesi şu an için "hayır" dedirtti.

**Yeni SQL dosyaları (MAMP'ta çalıştırılmalı):** `add_tag_to_accounting_entries.sql`
