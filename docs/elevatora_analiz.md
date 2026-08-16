# Elevatora.com — Detaylı Analiz ve Proaslift Karşılaştırması

**Kaynak:** `batuhan.elevatora.com` deneme hesabı (Metronic/Keenthemes tabanlı admin panel)
**İnceleme yöntemi:** Sidebar accordion menüsü DOM'dan tam olarak çıkarıldı, ~20 ana sayfa canlı olarak gezildi.

---

## 1. Genel Mimari Farkı (En Kritik Bulgu)

Elevatora, Proaslift'ten **temelde farklı bir iş modeli** üzerine kurulu:

| | **Proaslift** | **Elevatora** |
|---|---|---|
| Odak | Sadece **periyodik bakım aboneliği** yönetimi | Bakım + **YENİ ASANSÖR KURULUM PROJESİ** yönetimi (uçtan uca ERP) |
| Varlık modeli | `Building` = asansör bilgilerini de içerir (ayrı Asansör tablosu yok) | `Bina` ve `Asansör` **ayrı tablolar**; bir binada birden fazla asansör, her biri kendi teknik kartıyla (pano marka/model, kapı tipi, ray tipi, hız, ağırlık vb. ~25 teknik alan) |
| Cari | Yok (Bina üstünden implicit) | Ayrı **Cari** modülü (müşteri + tedarikçi birleşik, import/export destekli) |
| Sözleşme | Yok (Bina.monthly_fee ile implicit) | Ayrı **Sözleşmeler** modülü (gelir sözleşmesi / gider sözleşmesi) |
| Proje yönetimi | Yok | **10 aşamalı** kurulum pipeline'ı (Proje Havuzu → Resmi Kurum Onayları), her aşama kendi görev/dosya/personel atamasına sahip |

**Sonuç:** Elevatora, asansör **montaj firmaları** için de tasarlanmış (proje/kurulum takibi var); Proaslift ise saf **bakım firması** SaaS'ı. Bu, doğrudan kopyalanacak bir şey değil — Proaslift'in müşteri kitlesi çoğunlukla bakım firması ise, tüm proje modülünü klonlamak aşırı mühendislik olur. Ancak alt-özellikler (aşağıda) seçici olarak değerlendirilebilir.

---

## 2. Sitemap (Sidebar Accordion — 10 grup + 2 doğrudan link)

### 2.1 Projeler
- **Projeler** (`/projeler`) — proje listesi, durum filtreleri (Tümü/Devam Eden/Biten/Başlamamış), dışa aktar
- **Proje Takip** (`/proje_takip`) — operasyon tablosu: teknik bilgi (kg/kişi, durak sayısı), görev sayacı (0/10), son denetim durumu, atanan usta, başlangıç/bitiş tarihi. Kart bazlı hızlı filtre (Toplam/Aktif/Geciken/Son Kontrol Bekleyen).
- **Proje Adımları (10 aşama, kanban-vari):** Proje Havuzu → Proje Çizimi → Stok Teslimi (Ray Kapı) → Ray Kapı Takılması → Kabin Siparişi → Kabin İmalatı → Asansör Montaj Havuzu → Stok Teslimi (Asansör) → Asansör Montajı → Resmi Kurum Onayları
- **Proje Detay Sayfası** (`/proje?proje=X&sayfa=...`) — alt sekmeler: Genel Bakış, Görevler, Personeller, Dosyalar, Denetimler, Ayarlar. "Personel Ata", "Görev Oluştur" butonları; otomatik oluşan belgeler (Asansör Fiyat Formu, Kabin Sipariş Formu); proje harcamaları tablosu (personel hakediş/masraf onayı projeye bağlı).

### 2.2 Asansörler & Bakım & Arıza
- **Asansörler** (`/asansorler`) — canlı Leaflet haritası (filtre: Aktif/Pasif/Bakımı Olan/Sözleşme Bitiyor), devasa veri tablosu: ~30 sütun (etiket rengi, pano marka/model, kapı tipi/yönü/boyutu, buton tipi/marka, ray tipi/marka, hız, ağırlık ray, paten tipi, denetim tarihi + "bir sonraki denetime N gün kaldı" uyarısı, sözleşme bitiş tarihi)
- **Bakımlar** (`/bakimlar`) — Gecikmiş/Bugün/Gelecek/Atanmamış sayaçları, canlı bakım haritası, personel durumu (Çevrimiçi/Yakın Aktif/Çevrimdışı), "Bakım Planla" sihirbazı
- **Bakım Takibi (5 aşama):** Bakım Yapılacaklar → Bakım Yapılmışlar → Ödemesi Alınacak Bakımlar → Ödemesi Alınmış Bakımlar → Tamamlanmış Bakımlar
- **Arızalar** (`/arizalar`) — arıza kaydı, durum akışı (Arızaya Başlanmadı → Arıza Formu Dolduruldu → Tahsilat Tamamlandı)
- **Arıza Takibi (4 aşama):** Arızadaki Asansörler → Malzeme Tedariği → Arızası Yapım Aşamasındakiler → Arızası Giderilenler
- **Denetimler** (`/asansor_denetimler`) — TSE/periyodik denetim kayıt modülü (boş demo veri)
- **Müşteri Bakım, Arıza İstek & Talepleri** (`/asansor_musteri_talepleri`) — müşterinin doğrudan talep açabildiği bir kanal (muhtemelen müşteri portalıyla bağlantılı — Proaslift'in portal'ı salt-okunur, Elevatora'da müşteri talep girebiliyor)

### 2.3 Cari
- **Cariler** (`/cariler`) — müşteri+tedarikçi birleşik liste, "İçeri/Dışarı Aktar" (Excel import/export)
- **Cari İstatistikleri** (`/cari_istatistikleri`)

### 2.4 Muhasebe
- **Kasa** (`/kasa`) — günlük gelir/gider/net bakiye, saatlik kasa grafiği, **etikete göre gelir/gider dağılımı** (Benzin, Elektrik, Kira, Malzeme, Yakıt vb. serbest etiketleme sistemi)
- **Kasa Hareketleri** (`/kasa_hareketleri`)
- **Finans Raporu** (`/finans_raporu`)
- **Kâr / Maliyet Raporu** (`/kar_maliyet_raporu`) — ayrı bir rapor türü
- **Gelirler** / **Giderler** (`/gelirler`, `/giderler`)
- **Çekler** / **Senetler** (`/cekler`, `/senetler`) — Proaslift'te bu oturumda zaten eklendi (#8)
- **Sözleşmeler** (`/sozlesmeler`) — gelir sözleşmesi / gider sözleşmesi ayrı toplamlarla

### 2.5 Teklifler ve Faturalar
- **Teklifler** (`/teklifler`) — Onaylı/Beklemede/Red durumları; **onaylanan teklif otomatik Proje'ye dönüşüyor** (teklif→proje zinciri)
- **Satış Faturaları** / **Alış Faturaları**
- **Ürün/Hizmet Taslakları** (`/urun_hizmet_taslaklari`) — teklif hazırlarken kullanılan hazır kalem şablonları

### 2.6 Stok
- **Stok Kategorileri** → **Stok Türleri** → **Stok Tanımları** (3 seviyeli taksonomi; demo veride ~256 hazır asansör parçası/markası: kilit markaları, pano markaları vb.)
- **Stoklarım** (`/stok`) — firmanın gerçek envanteri
- **Depo** / **Depo Çıkışı** — depo bazlı stok + çıkış (proje/bakıma malzeme sarfı) takibi

### 2.7 Personel
- **Personel** (`/personeller`) — **canlı GPS/IP tabanlı personel konum haritası**, çok kademeli çevrimiçi durumu (Çevrimiçi 2dk / Yakın Aktif 10dk / Bugün Aktif / Görünür 3 gün / Çevrimdışı / Hesap Pasif)
- **Personel Performans** / **Personel İstatistikleri**
- **Personel Detay Sayfası** (`/personel_goruntule`) — alt sekmeler: Genel Bakış, Projeler, Ödemeler, Belgeler, Performans, Hesap Ayarları

### 2.8 Raporlar
- **Genel Dökümler** (`/genel_dokumler`)

### 2.9 Mobil Arayüzler (web-tabanlı, native app değil)
- **Mobil Bakım Arayüzü** — teknisyenin günlük iş listesi (Bakım/Arıza karışık), "Başlat"/"Tamamla" aksiyonları, adres + müşteri bilgisi
- **Mobil Saha Arayüzü** — proje bazlı görev takibi (usta konumları dahil)
- **Mobil Depo Arayüzü**

> **Not:** Bunlar native mobil uygulama değil, mobil tarayıcıya optimize edilmiş web arayüzleri. Proaslift'in gerçek React Native uygulaması bu noktada mimari olarak daha güçlü.

### 2.10 Doğrudan linkler
- **Satın Alım** (`/satin_alim`) — tedarikçiden satın alma süreci (stok girişiyle bağlantılı, sipariş no formatı görüldü: `DMO26-TED-2026-00XX`)
- **Sistem Ayarları** (`/sistem_ayarlari`) — Genel Ayarlar (logo/kaşe/imza/favicon — Proaslift'te #9 ile zaten var, ama Elevatora'da **ayrı "Yetkili İmzası" + "Kaşe Üstü İmza"** var), SMS Ayarları, Dosya Ayarları, **Etiket Ayarları** (asansör renk etiketleri), **Roller** (RBAC — rol bazlı yetkilendirme), Uyumsoft e-Fatura entegrasyon anahtarı (kapsam dışı bırakılan #12 ile örtüşüyor)

---

## 3. Modüller Arası Bağlantı Haritası

```
Teklif (onaylandı) ──► Proje (10 aşamalı pipeline) ──► Görevler + Dosyalar + Denetimler
                              │                              │
                              ▼                              ▼
                        Personel Ata                   Proje Harcamaları
                              │                        (personel hakediş onayı)
                              ▼
                    Mobil Saha Arayüzü (usta görür)

Bina/Asansör ──► Bakım (5 aşama) ──► Kasa/Gelir ──► Finans Raporu
             └─► Arıza (4 aşama) ──► Malzeme Tedariği ──► Depo Çıkışı ──► Stok azalır
                                                              │
                                                        Satın Alım ──► Stok artar

Cari (Müşteri/Tedarikçi) ──► Sözleşme (gelir/gider) ──► Kasa/Muhasebe
                         └─► Çek/Senet ──► Kasa vade takibi

Stok Kategorisi → Stok Türü → Stok Tanımı (katalog) ──► Stoklarım (gerçek envanter) ──► Depo
```

---

## 4. Proaslift ile Karşılaştırma — Öncelikli Fark Listesi

Bu oturumda asansorex.com karşılaştırmasından çıkan 14 maddelik listeye ek olarak, Elevatora'dan görülen **yeni** fikirler:

| # | Özellik | Elevatora'da nasıl | Proaslift'te durum | Öneri |
|---|---|---|---|---|
| E1 | **Canlı personel konum haritası** | GPS/IP ile personel anlık konumu, çok kademeli "son görülme" | Yok (sadece bina konum haritası var) | Orta efor — mobil app'ten periyodik konum ping'i + web'de harita. Gizlilik/KVKK onayı gerektirir. |
| E2 | **Ayrı Asansör entegrasyonu (Bina≠Asansör)** | Bina içinde N asansör, her biri ~25 teknik alanlı ayrı kart | Proaslift'in temel mimarisi Bina=Asansör | **Büyük mimari değişiklik** — şu an için önerilmez, ancak uzun vadede değerlendirilmeli (ayrı görev/plan konusu) |
| E3 | **Sözleşme modülü (gelir/gider ayrı)** | Cari bazlı, tutar toplamlı ayrı sayfa | Building.monthly_fee ile implicit | Düşük-orta efor, Cariler (#5) ile birlikte genişletilebilir |
| E4 | **Etiket bazlı gelir/gider kategorileme (Kasa)** | Serbest metin etiket + otomatik grafik/toplam | Proaslift'te sabit kategori yok | Düşük efor — mevcut Gün Sonu/Muhasebe ekranına `tag` alanı eklenebilir |
| E5 | **Müşteri talep girişi (Müşteri Bakım/Arıza İstek)** | Müşteri portalından talep açabiliyor | Proaslift portalı (#6) salt-okunur | Orta efor — mevcut Portal'a "Talep Oluştur" formu eklenebilir, iyi bir tamamlayıcı |
| E6 | **Rol bazlı yetkilendirme (Roller sayfası)** | Sistem Ayarları > Roller — özelleştirilebilir rol/izin matrisi | Proaslift'te sabit roller (company_admin/employee) | Orta-yüksek efor — güvenlik açısından dikkatli tasarlanmalı, acele edilmemeli |
| E7 | **Proje/kurulum takibi (10 aşamalı pipeline)** | Tam ERP modülü | Yok | **Kapsam dışı** — Proaslift bakım firmalarına odaklı, bu farklı bir ürün segmenti. Sadece kullanıcı montaj işi de yapıyorsa değerlendirilmeli. |
| E8 | **3 seviyeli stok taksonomisi + hazır katalog** | Kategori→Tür→Tanım, ~256 hazır kayıt | Proaslift: düz Ürün listesi | Düşük öncelik — küçük/orta firma için mevcut düz liste yeterli, aşırı karmaşıklaştırmaz |
| E9 | **Kâr/Maliyet Raporu (ayrı)** | Net satış − kayıtlı maliyet (alış+personel+taşeron) | Proaslift finansal raporunda kısmen var | Düşük efor — mevcut finans raporuna "maliyet dağılımı" kartı eklenebilir |

---

## 5. Genel Değerlendirme

Elevatora, **daha büyük ölçekli, çok modüllü bir asansör ERP'si** (montaj + bakım + arıza + muhasebe + stok + CRM tek çatı altında). Proaslift'in rekabet avantajı şu an **basitlik ve odaklanma** (saf bakım SaaS'ı, native mobil app, daha az tıklamayla iş bitirme). Elevatora'nın gücü **veri derinliği ve iş süreç kontrolü** (proje pipeline, canlı harita, rol yönetimi).

**Önerilen yaklaşım:** Elevatora'nın *tüm* modüllerini kopyalamak yerine, yukarıdaki E1/E3/E4/E5/E9 gibi düşük-orta efor, yüksek algılanan-değer özelliklerini seçici olarak entegre etmek — asansorex karşılaştırmasında izlenen stratejinin aynısı.
