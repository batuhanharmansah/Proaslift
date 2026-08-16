# Asansorex.com — Rakip Analiz Dokümanı

İncelenen hesap: "Mehmet Asansör" (deneme hesabı, Başlangıç paketi)
İnceleme tarihi: 2026-08-10 (oturum içi)

## Tam Sitemap (Sidebar)

### Günlük İşler
- Kontrol Paneli — /dashboard
- Hızlı Müşteri+Bina Ekle — /hizli-kurulum
- Tahsilat Al — /tahsilat

### Bina & Müşteri
- Müşteriler — /customers
- Binalar — /buildings
- Asansörler — /elevators
- TSE Muayene Takibi — /elevators/tse
- Bölgeler — /bolgeler

### Operasyon
- Bakım (Planlı) — /maintenance
- Bakım Takvimi — /maintenance/calendar
- Rota Planlayıcı — /operations/rota-planlayici
- Toplu Bakım — /bakim-toplu
- Bakım Ücretleri — /bakim-ucretleri
- Arıza Bildirimleri — /fault-reports
- Asansör Siparişleri — /elevator-orders

### Cari & Finans
- Cariler — /cariler
- Kasalar — /kasalar
- Çek-Senet — /cek-senet
- Finans — /finance
- Faturalar — /faturalar

### Teklifler & Belgeler
- Sözleşmeler — /contracts
- Teklifler — /quotes
- Revizyon Teklifleri — /revizyon-teklifleri
- ATF — /atf
- DTR — /dtr
- Kurtarma Formu — /form-belgeler?doc_type=rescue_form
- Eğitim Raporu — /form-belgeler?doc_type=education_report
- Checklist Şablonları — /settings/checklist-templates

### Envanter
- Envanter/Stok — /inventory
- Stok Hareketleri — /inventory/transactions
- Kategoriler — /inventory/categories
- Düşük Stok — /inventory/low-stock
- Tedarikçiler — /suppliers
- Lokasyonlar — /locations

### Projeler / İşler
- Projeler — /projeler
- Yeni Proje — /projeler/yeni

### Personel (HRM)
- Kullanıcılar — /users
- Hakedişler — /hakedisler
- Personel Ödeme — /personel-odeme
- Devamsızlık — /devamsizlik

### Saha
- Araçlar — /araclar
- Personel Konum Takibi — /saha/personel-takip

### İletişim — SMS
- SMS Özet & Bakiye — /sms
- Toplu SMS Gönder — /sms/compose
- Otomatik Tercihler — /sms/preferences
- SMS Geçmişi — /sms/logs

### Raporlar
- Günlük Özet — /raporlar/gunluk-ozet
- Personel Tahsilat — /raporlar/personel-tahsilat
- Personel Aktivite — /raporlar/personel-aktivite
- Cari Bakiye — /raporlar/cari-bakiye
- Kasa Hareket — /raporlar/kasa-hareket
- Bakım Tahsilat (Bakım Takip Matrisi) — /raporlar/bakim-tahsilat
- Personel Raporu — /raporlar/personel
- Çek-Senet Vade — /raporlar/cek-senet
- Çek Portföyü — /raporlar/cek-portfoy
- Borç Yaşlandırma — /raporlar/borc-yaslandirma
- Tahsilat Özeti — /raporlar/tahsilat-ozeti

### Yönetim
- Ayarlar — /settings

---

## Sayfa Detayları

### Müşteriler (/customers, /customers/create)
- Liste filtreleri: Tip (Kurumsal/Bireysel), Portal (Tüm/Aktif/Yok), Sırala (Ad A-Z/Z-A/En yeni)
- Excel (CSV) export, tablo: Ad/Ünvan, Tip, Telefon, Portal durumu
- **Yeni Müşteri formu çok kapsamlı:**
  - Tip: Kurumsal (Apartman/Site Yönetimi) / Bireysel
  - Telefon ZORUNLU — müşteri portalı login'i için kullanılıyor
  - E-Fatura/e-Arşiv alanları: Vergi No/TCKN, Vergi Dairesi, İl, İlçe, Adres
  - **Bina devir mekanizması:** Bir binayı bu müşteriye atarken, önceki sahibinden devralınıyorsa "Devir onayı" + "Cari devri" seçenekleri var. Cari devrinde, binanın borçları DVR-B<binaId> hareketiyle, müşterinin genel bakiyesi (son binası devrediliyorsa) DVR-G<musteriId> hareketiyle yeni müşteriye taşınıyor, eskisinde sıfırlanıyor.
  - **Müşteri Portalı:** Checkbox ile direkt admin panelinden müşteriye portal şifresi atanabiliyor — müşteri kendi binalarını/arızalarını/faturalarını görebiliyor.

### Binalar (/buildings, /buildings/create)
- Liste filtreleri: Sahip (Tümü/Cari Atanmış/Sahipsiz), Sırala (Ad/Şehir/En yeni)
- **Yeni Bina formu:**
  - Bina Tipi: Apartman/İş Merkezi/Site/Hastane/Otel/AVM/Okul/Yurt/Karma/Diğer (Proaslift'te yok, generic)
  - **Saha Bölgesi** — Rota Planlayıcı'da bölgeye göre filtreleme için (Proaslift'te yok)
  - Bina Sahibi (Cari) — 1 bina = 1 müşteri kuralı, bakım/arıza/faturalar sadece bu müşteriye bağlanıyor
  - Bina Yöneticisi (müşteriden farklıysa ayrı iletişim bilgisi)
  - **Varsayılan Teknisyen** — toplu bakım üretiminde otomatik atama için (Proaslift'te yok)
  - **Enlem/Boylam + interaktif Leaflet/OpenStreetMap haritadan konum seçimi** (Proaslift sadece employee konumu takip ediyor, bina geo-konumu için harita seçici yok)
  - **Teknisyene Özel/Gizli Bilgiler:** Kapı/Giriş Şifresi + Erişim Notu — SADECE firma ekibi/teknisyen görür, müşteri portalında GÖRÜNMEZ (Proaslift'te yok, güvenlik açısından değerli bir ayrım)

### Asansörler (/elevators)
- Filtreler: Durum (Aktif/Bakımda/Tamirde/Arızalı/Pasif), Tip (Elektrikli/Hidrolik/Makine Dairesiz/Panoramik/Yük/Servis/Yürüyen Merdiven/Engelli Platformu/Diğer — Proaslift'te sadece yolcu/yuk/hasta/karma, çok daha dar), **TSE Etiket** (Yeşil/Mavi/Sarı/Kırmızı/Etiketsiz)
- TSE etiket renk kodu listede direkt görünüyor: Yeşil (vade>30 gün), Sarı (≤30 gün), Kırmızı (vade geçmiş) — **Proaslift'in ElevatorLabel sistemiyle kavramsal olarak aynı, burada Proaslift zaten güçlü.**
- Asansör oluşturmadan önce Bina zorunlu (Proaslift'le aynı model).

### Bölgeler (/bolgeler)
- Saha bölgesi tanımlama (örn. "Anadolu Yakası"), her birine sorumlu personel atanabiliyor, bağlı bina sayısı gösteriliyor. Rota Planlayıcı ile entegre. **Proaslift'te bölge/territory kavramı hiç yok.**

### Bakım Kayıtları (/maintenance)
- Kapsam filtresi: Firma Tümü / Bana Atanmış
- Durum: Planlandı/Devam Ediyor/Tamamlandı/İptal/Ertelendi/Beklemede (Proaslift: planli/atandi/baslandi/tamamlandi — daha az granüler)
- Tip: Periyodik/Düzeltici/Acil/Montaj/Muayene/Tamir/İyileştirme/Diğer (7 tip vs Proaslift'in 4 tipi: rutin_bakim/ariza_onarim/periyodik_kontrol/modernizasyon)
- Bölge + Teknisyen filtresi, **çoklu seçim ile toplu teknisyen atama ve toplu silme** (Proaslift'te yok)

### Rota Planlayıcı (/operations/rota-planlayici) — ⭐ ÖNEMLİ FARK
İnteraktif harita (Leaflet) üzerinde:
- "Günümün Rotasını Planla" — tarih+personel seçince o güne atanmış bakım/arızalar haritada otomatik diziliyor
- Filtre: Tümü / Bana Atanmış / Vade Geçen / Teknisyen
- **"En Yakın 5/10"** — başlangıç noktasından en yakın binaları otomatik seçme
- **"✨ Akıllı Sırala"** — seçili binaları optimum rota sırasına diziyor (muhtemelen nearest-neighbor algoritması)
- **"🚗 Yol Tarifi (Google Maps)"** — tek tıkla seçili rotayı Google Maps'te açıyor
- **Proaslift'te bu modül HİÇ YOK.** Sahada çalışan teknisyen sayısı arttıkça bu büyük bir verimlilik farkı yaratır.

### Aylık Toplu Bakım (/bakim-toplu) — ⭐ ÖNEMLİ FARK
Tüm asansörler için AYNI ANDA aylık bakım kaydı üretme sihirbazı:
1. Dönem seçimi (yıl/ay/başlangıç günü), resmi tatile denk gelirse ilk iş gününe kaydırma
2. Tarih dağılımı: "Birden fazla iş gününe yay" (örn. 20 asansör 5 güne ~4'er dağıtılır) veya "Hepsi aynı güne"
3. Teknisyen atama stratejisi: Bina varsayılanı / Hepsine aynı teknisyen / Round-robin dağıtım / Atama yapma
4. Hedef filtreleme: sadece bakım ücreti tanımlı asansörler / bölge / müşteri / bina bazlı
5. **"Önizle" ile önce ne olacağını görüp, sonra "Onayla ve Oluştur"** — hiçbir şey onay öncesi DB'ye yazılmıyor
- **Proaslift'te bu YOK.** Proaslift'te bakım kayıtları tek tek elle oluşturuluyor. Bu, çok sayıda asansörü olan firmalar için devasa bir zaman tasarrufu — ayın başında "tüm asansörlere bu ayın bakımını aç" diyip bitiriyorsun.

### Bakım Ücretleri (/bakim-ucretleri)
- **Asansör bazlı** sözleşme/ücret (Proaslift: bina bazlı `monthly_fee`). Yani bir binada 3 asansör varsa, asansorex 3 ayrı sözleşme/ücret tanımlayabiliyor, Proaslift tek bina ücreti üzerinden gidiyor. Çok asansörlü siteler için asansorex modeli daha doğru/esnek.
- Üstte "Faturalanabilir sözleşmelerden aylık toplam" özet göstergesi.

### Arıza Bildirimleri (/fault-reports)
- Canlı sayaçlar: Bugün Yeni, Bugün Çözülen, Açık, Bana Atanan, Sahipsiz, Çözülenler
- **"Hızlı Çöz" butonu** — tek tıkla hızlı çözüm akışı
- **Liste/Kanban görünüm seçeneği** (Proaslift'te sadece liste var)

### Asansör Siparişleri (/elevator-orders)
- Yeni asansör ÜRETİM/SATIŞ pipeline'ı: Taslak→Onay Bekliyor→Onaylandı→Üretimde→Sevkiyatta→Montaj→Test→Tamamlandı + ayrı Belge Durumu (Taslak/Gönderildi/Görüldü/Onaylandı/Reddedildi/Süresi Doldu)
- Bu, asansör ÜRETEN/SATAN firmalar için — Proaslift sadece bakım/servis firmalarını hedefliyor, bu modül muhtemelen kapsam dışı kalabilir (pazar segmenti farklı).

### Cariler (/cariler) — ⭐ ÖNEMLİ FARK (muhasebe mimarisi)
- **Tek "Cari" (hesap) kavramı altında**: Müşteri, Tedarikçi, Personel, Taşeron/Ekip, Gider Kalemi — hepsi birer "cari hesap", her birinin bakiyesi (alacak/borç) var
- Üstte "Toplam Alacaklarımız" / "Toplam Borçlarımız" özet
- **Proaslift'te bu unified "cari hesap" modeli YOK** — Proaslift'te Receivable (alacak), Payable (borç), AccountingEntry ayrı ayrı tablolar, ama "her varlığın kendi cari hesabı ve bakiyesi" kavramı yok. Bu, Türk MUB (muhasebe) yazılımlarının standart mimarisi — asansorex bunu doğru yapmış.

### Kasa & Banka (/kasalar)
- Kasa tipleri: Nakit Kasa/Banka Hesabı/Kredi Kartı/POS (Proaslift AccountType: kasa/banka/nakit/pos — hemen hemen aynı)
- **"⇄ Virman"** — kasalar arası transfer (örn. bankadan nakite) — **Proaslift'te yok**
- Her kasa için "Ekstre" (hesap özeti) linki

### Çek & Senet (/cek-senet)
- Tam çek/senet takip modülü: Yön (Gelen/Giden), Durum (Bekliyor/Tahsil Edildi/Ödendi/Karşılıksız/İade/Ciro Edildi), vadeye göre sıralama, "yakında vade" sayacı. **Proaslift'te hiç yok.** Çek ile çalışan (özellikle büyük/kurumsal müşterili) firmalar için önemli.

### Finansal Özet (/finance)
- Tek ekranda: Bu Ay Gelir/Gider/Net (kasa hareketlerinden, virman hariç), Kasa Toplam, Müşteri Alacak, Tedarikçi Borç, Aktif Sözleşme sayısı. Hızlı erişim linkleri.

### e-Fatura/e-Arşiv (/faturalar) — ⭐ BÜYÜK FARK
- Resmi e-Fatura/e-Arşiv kesimi sistem üzerinden (GİB entegrasyonu gerekiyor, muhtemelen Foriba/Logo gibi bir sağlayıcı üzerinden). Deneme hesaplarında kapalı (gerçek mali işlem içerdiği için). **Proaslift'te yok — ama bu, entegrasyon maliyeti yüksek, uzun vadeli bir yatırım, hemen yapılacak bir şey değil.**

### Sözleşmeler / Teklifler / Revizyon Teklifleri / ATF (Teklifler & Belgeler)
- Hepsi aynı pattern: Taslak→Gönderildi→Görüldü→Onaylandı/Reddedildi→Süresi Doldu belge durumu takibi (bazılarında ek durumlar). **Proaslift'in Quotation modülü kavramsal olarak aynı yapıda (public link ile onay/red) — burada Proaslift zaten güçlü, sadece "Revizyon Teklifi" ve "ATF" (yeni asansör talep formu) gibi alt tipleri yok.**

### Durum Tespit Raporu — DTR (/dtr) — ⭐ REGÜLASYON UYUMU
- TSE/G muayene sonrası zorunlu "durum tespit raporu" — Taslak/Tamamlandı/İmzalandı/Paylaşıldı/Onaylandı akışı. **Proaslift'te TSE muayene sonrası resmi rapor üretimi yok.**

### Kurtarma Formu (/form-belgeler?doc_type=rescue_form) — ⭐ REGÜLASYON UYUMU
- Asansörde mahsur kalma (kurtarma) olayı sonrası zorunlu doküman. **Proaslift'te yok.**

### Bakım Kontrol Şablonları (/settings/checklist-templates) — ⭐⭐⭐ EN ÖNEMLİ BULGU
- **8 asansör tipi için** (Elektrikli/Hidrolik/MRL/Panoramik/Yük/Servis/Yürüyen Merdiven/Engelli), her biri için TSE standartlarına uygun, ~50 maddelik, kategorize edilmiş (Kuyu Dibi, Kabin Üstü, Kabin İçi, Makine Dairesi, Elektrik/Kontrol) **yapılandırılmış bakım kontrol listesi**.
- Firma kendi özel maddelerini de ekleyebiliyor, sistem varsayılanının üzerine "firma şablonu" tanımlanabiliyor.
- Teknisyen "Tamamla" formunda bu checklist'i işaretleyerek bakımı tamamlıyor (muhtemelen).
- **Proaslift'te bakım tamamlama tamamen serbest metin (`work_description`) — hiçbir yapılandırılmış checklist yok.** Bu, profesyonellik algısı ve TSE denetimlerinde ibraz edilebilir kanıt açısından EN BÜYÜK fark. Bir teknisyenin gerçekten 50 maddeyi kontrol edip etmediğini kanıtlayan bir sistemle, "bir şeyler yaptım" diyen bir sistem arasındaki fark budur.

### Stok/Envanter (/inventory)
- SKU, satış fiyatı, **maliyet/kâr marjı**, **çoklu lokasyon** desteği. Proaslift'in Depo modülü daha basit (maliyet/kâr takibi yok, tek lokasyon).

### Personel (/users)
- Sadece 2 rol: Firma Yöneticisi / Teknisyen (Proaslift 3 rol: super_admin/company_admin/employee — Proaslift'in super_admin'i platform sahibi için, kıyaslanabilir değil, esasen ikisi de aynı ikili yapıda).

### Hakedişler (/hakedisler) — ⭐ FARK
- Teknisyene iş bazlı ek kazanç/prim (performansa dayalı ödeme) takibi. **Proaslift'te sadece sabit `salary` alanı var, prim/hakediş sistemi yok.**

### Devamsızlık (/devamsizlik)
- Personel izin/devamsızlık takibi (başlangıç/bitiş/tip/not). **Proaslift'te yok** (basit bir HR özelliği, düşük öncelik).

### Araç Takip (/araclar)
- Firma araçları: plaka, sürücü, muayene bitiş, sigorta bitiş, otomatik uyarılar. **Proaslift'te yok** (düşük-orta öncelik, filoya sahip firmalar için değerli).

### Personel Konum Takibi (/saha/personel-takip)
- Canlı harita + geçmiş, KVKK uyumlu (mesai saatleri içinde, teknisyen onayıyla, otomatik silinme). **Proaslift'te de var** (aktif bakım işi sırasında) — kavramsal olarak eşdeğer, asansorex'te mesai saatine bağlı daha genel bir takip.

### SMS Yönetimi (/sms, /sms/preferences) — ⭐⭐ ÖNEMLİ FARK
- Kredi bazlı SMS sistemi (1 SMS = 1 kredi), paket satın alma.
- **Bildirim Tercihleri matrisi:** ~15 olay tipi (arıza bildirimi/atama/çözüm, bakım hatırlatma/tamamlanma, tahsilat, teklif/sözleşme/ATF/DTR gönderimi, hoş geldin, şifre sıfırlama, OTP) × **3 kanal (SMS/E-posta/Push)**, her biri ayrı ayrı açık/kapalı, bazıları "kilitli" (kritik akış, kapatılamaz — örn. portal daveti, OTP).
- **Proaslift'te bildirim sistemi çok daha dar** — sadece bakım onay SMS'i var (bina birincil kişisine), yapılandırılabilir bir tercih matrisi yok, push notification altyapısı var ama admin tarafından event-bazlı açma/kapama yönetimi yok.

### Ayarlar (/settings)
- Firma logosu (otomatik WebP dönüşüm), **Kaşe/İmza görseli** (tekliflerde otomatik basılıyor), firma bilgileri (vergi no vb.), **Marka Renkleri** (müşteri portalı ve landing sayfasında kullanılıyor). **Proaslift'te firma logosu/kaşe/marka rengi özelleştirmesi yok.**

---

## Modül Haritası ve İş Akışı Bağlantıları (Özet)

**Çekirdek veri modeli zinciri:**
```
Müşteri (Cari) ──1:N──> Bina ──1:N──> Asansör ──1:N──> Bakım/Arıza
     │                     │                                │
     │                     └──> Bölge (Rota Planlayıcı)      │
     │                                                       │
     └──> Cari Hesap (bakiye) <──── Tahsilat/Fatura/Çek-Senet ┘
              │
              └──> Kasa/Banka (virman ile transfer)
```

**Ana iş akışları:**
1. **Satış öncesi:** Teklif oluştur → müşteriye gönder (public link, SMS/e-posta) → Görüldü/Onaylandı → Sözleşme'ye dönüştür → Bakım Ücreti (asansör bazlı) otomatik tanımlanır.
2. **Aylık bakım üretimi:** Ay başında "Toplu Bakım" sihirbazı → tüm/filtrelenmiş asansörler için önizle → onayla → bakım kayıtları + teknisyen ataması otomatik oluşur (Proaslift'te bu YOK, tek tek elle giriliyor).
3. **Saha günü:** Teknisyen "Rota Planlayıcı"da o güne atanmış işleri haritada görür → Akıllı Sırala → Google Maps'te yol tarifi → sahada bina detayında gizli kapı şifresi/erişim notunu görür → checklist ile bakımı tamamlar → müşteriye otomatik "bakım tamamlandı" bildirimi (SMS/e-posta/push tercihine göre).
4. **Arıza akışı:** Müşteri (portal/QR) veya firma arıza girer → teknisyen atanır (bildirim gider) → "Hızlı Çöz" veya normal akışla kapatılır → müşteriye bildirim.
5. **Tahsilat:** Cari üzerinden tahsilat alınır → Kasa'ya işlenir → Çek/Senet ise ayrıca vade takibine girer → müşteriye dekont SMS'i (opsiyonel).
6. **Bina/müşteri devri:** Bir bina başka bir müşteriye devredilirse, borç geçmişi (DVR-B/DVR-G hareket kodlarıyla) yeni müşteriye taşınabiliyor — cari bütünlüğü korunuyor.
7. **Regülasyon:** TSE Muayene Takibi → Durum Tespit Raporu (DTR) üretimi → müşteriye paylaşım. Kurtarma olayı olursa Kurtarma Formu dolduruluyor.

---

## Proaslift ile Karşılaştırma

### Proaslift'in ZATEN GÜÇLÜ olduğu / eşdeğer alanlar
| Alan | Not |
|---|---|
| Asansör TSE etiket sistemi (yeşil/sarı/kırmızı) | Proaslift'in ElevatorLabel sistemi kavramsal olarak aynı |
| Teklif (Quotation) yaşam döngüsü | Public link + görüldü/onaylandı/reddedildi akışı zaten var |
| Mobil uygulama (teknisyen) | Her ikisinde de var, Proaslift'te QR tarayıcı da var |
| Personel konum takibi | Aktif iş sırasında, KVKK'ya benzer yaklaşım |
| Bina→Alacak otomatik zinciri | Proaslift'te de var (bu oturumda kurduk) |
| Sistem sağlığı / hata izleme | **Proaslift'te var, asansorex'te bu tarz bir "biz görmüyoruz ama izliyoruz" paneli tespit edilmedi** — Proaslift burada muhtemelen önde |

### Proaslift'te EKSİK olan, asansorex'te bulunan öne çıkan farklar (önem sırasına göre)

| # | Özellik | Etki | Efor tahmini |
|---|---|---|---|
| 1 | **Yapılandırılmış bakım kontrol listesi** (8 tip × ~50 madde, kategorize) | ⭐⭐⭐ Çok yüksek — profesyonellik + denetim kanıtı | Orta (statik checklist şablonları + UI, DB şeması basit) |
| 2 | **Aylık Toplu Bakım üretme sihirbazı** (önizle→onayla, dağıtım+atama stratejileri) | ⭐⭐⭐ Çok yüksek — çok asansörlü firmalar için devasa zaman tasarrufu | Orta-Yüksek |
| 3 | **Rota Planlayıcı** (harita, en yakın N, akıllı sırala, Google Maps entegrasyonu) | ⭐⭐⭐ Yüksek — saha verimliliği | Yüksek (harita + rota optimizasyonu) |
| 4 | **Bildirim Tercihleri matrisi** (event × kanal, admin yapılandırılabilir) | ⭐⭐ Orta-Yüksek — profesyonellik algısı | Orta |
| 5 | **Unified Cari Hesap modeli** (müşteri/tedarikçi/personel/taşeron/gider hepsi tek hesap tipi, devir mekanizması) | ⭐⭐ Orta-Yüksek — muhasebe bütünlüğü, ama mevcut Receivable/Payable modelini yeniden yapılandırmak gerekir | Yüksek (mimari değişiklik) |
| 6 | **Müşteri Portalı** (müşteri kendi binasını/arızasını/faturasını görebiliyor) | ⭐⭐ Orta-Yüksek — rekabet farkı, satış argümanı | Yüksek (yeni bir auth/rol katmanı) |
| 7 | **Bina bazlı değil asansör bazlı bakım ücreti** | ⭐⭐ Orta — çok asansörlü siteler için daha doğru modelleme | Orta |
| 8 | **Çek & Senet takibi** | ⭐ Orta — kurumsal müşterisi çok olan firmalar için | Orta |
| 9 | **Firma özelleştirme** (logo/kaşe-imza otomatik teklif basımı/marka renkleri) | ⭐ Orta — profesyonel görünüm | Düşük-Orta |
| 10 | **Saha Bölgeleri (territory)** | ⭐ Orta — çok şubeli/büyük firmalar için | Düşük-Orta |
| 11 | **DTR + Kurtarma Formu** (regülasyon belgeleri) | ⭐ Orta — TSE denetimi için değerli ama dar kullanım | Orta |
| 12 | **e-Fatura/e-Arşiv entegrasyonu** | ⭐ Uzun vadede önemli ama PAHALI ve YAVAŞ (üçüncü parti sağlayıcı gerekir) | Çok Yüksek |
| 13 | Hakediş/prim sistemi, Araç Takip, Devamsızlık, Envanter maliyet/kâr, çoklu lokasyon | Düşük-Orta — HR/filo/stok detay özellikleri | Düşük-Orta (her biri ayrı ayrı) |
| 14 | Asansör Siparişleri (üretim/satış pipeline) | Muhtemelen kapsam dışı — Proaslift servis/bakım firmalarını hedefliyor, üretici/satıcı değil | — |

### Önerilen öncelik sırası (Proaslift roadmap için)
1. **Bakım kontrol listesi (checklist)** — en yüksek etki/efor oranı, hemen sonraki büyük özellik olmalı
2. **Toplu Bakım üretimi** — ikinci en yüksek etki
3. **Bildirim tercihleri matrisi** — mevcut bildirim altyapısının üzerine inşa edilebilir, nispeten ucuz
4. **Rota Planlayıcı** — daha büyük bir proje ama saha ekibi büyüdükçe kritikleşir
5. Geri kalanlar (Cari model, Müşteri Portalı, Çek-Senet, e-Fatura) — büyüme evresine göre zamanla değerlendirilmeli, şu an satışa hazırlanırken öncelik değil

