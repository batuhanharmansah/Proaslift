# Cursor Değişiklikleri — Detaylı Test Planı

Bu doküman, `cursor_mobil_paritesi_roadmap.md`'deki A→D→B→C sırasıyla Cursor tarafından local'de uygulanan değişikliklerin **sayfa sayfa, adım adım** test planıdır. Her madde: nereye gidilecek, ne tıklanacak, ne beklenecek, hangi veriyle karşılaştırılacak şeklinde yazıldı.

**Ön koşullar:**
- MAMP çalışıyor, Laravel `127.0.0.1:8000`, MySQL `127.0.0.1:8889`, DB `harmansah_panel`.
- `asansor-web/database/remove_duplicate_checklist_brake_pad.sql` local DB'de ÇALIŞTIRILMIŞ olmalı (aşağıdaki A1 testinden önce şart).
- Mobil: `npx expo start` + iOS Simulator, `.env.local`'daki `EXPO_PUBLIC_API_URL` bu Mac'in güncel LAN IP'sine işaret ediyor olmalı (`ipconfig getifaddr en0` ile kontrol edin — DHCP ile değişmiş olabilir, değiştiyse `.env.local`'ı güncelleyip Expo'yu yeniden başlatın).
- Web admin: `admin@claude-asansor.com`. Mobil personel hesapları: Ali Demir / Ayşe Kaya / Mehmet Yılmaz (şifreleri sizde).
- Test firması "Claude Asansör" verisi: 27 bina, 3 personel, 15 ürün — sonuçları bunlarla karşılaştırın.

---

## BÖLÜM A — Bug Düzeltmeleri

### A1. Checklist mükerrer madde kaldırıldı mı?

**Web:**
1. `/settings/checklist-items` sayfasını aç.
2. "Makine Dairesi Kontrolü" kategorisinde madde listesini say. **Beklenen: 14 madde, "Makina fren balata kontrolü" tam olarak 1 kez geçiyor.**
3. Herhangi bir tamamlanmış rutin bakım işine gir (`/bakim-takibi`, tamamlanmış bir kayıt aç) → raporunda "Makine Dairesi Kontrolü: X/14" yazıyor mu, "X/15" değil. Eski (SQL çalıştırılmadan önce oluşturulmuş) kayıtlarda hâlâ 15 görünebilir — bu normal, SQL sadece master listeyi düzeltir, geçmiş kayıtları değiştirmez. **Yeni** bir rapor oluşturup orada 14 görmek asıl kanıt.
4. Yeni bir rutin bakım işi oluştur → raporunu doldurmaya başla → checklist'te "Makina fren balata kontrolü" satırının bir kez göründüğünü, hepsini işaretleyince "14/14" yazdığını doğrula.

**Mobil:**
1. iOS Simulator'de admin ile giriş yap → Bakım → planlı bir rutin bakım işine gir → "İşe Başla" → "Rapor Oluştur" (ya da "Detaylı Form" varsa onu da kontrol et).
2. "Makine Dairesi Kontrolü" bölümünde madde sayısını say, mükerrer olmadığını doğrula, tüm kutuları işaretleyip sayacın "14/14" gösterdiğini doğrula (önceki turda burada "15/14" görülmüştü).

### A2. Bildirim sayacı — okunmamış artık toplamı geçemiyor mu?

**Mobil (öncelikli, bug orada bulunmuştu):**
1. Admin ile giriş yap → sağ üstteki zil ikonuna dokun (Bildirimler ekranı).
2. Üstteki "Tümü (N)" ve "Okunmamış (M)" sayılarına bak. **Beklenen: M ≤ N her zaman.** Önceki turda "Tümü (20)" / "Okunmamış (25)" gibi imkânsız bir durum vardı — bu artık olmamalı.
3. Bir bildirime tıkla (okundu işaretlenir) → sayıları tekrar kontrol et: "Okunmamış" 1 azalmış, "Tümü" aynı kalmış olmalı.
4. "Tümünü Okundu İşaretle" butonuna bas → "Okunmamış (0)" olmalı, "Tümü" değişmemeli.
5. Drawer menüsündeki "Bildirimler" satırının yanındaki kırmızı badge sayısının, ekrandaki "Okunmamış" sayısıyla birebir eşleştiğini doğrula.

**Web (varsa benzer bir sayaç):** Dashboard'da veya bildirim ile ilgili bir widget varsa aynı tutarlılığı (okunmamış ≤ toplam) orada da kontrolden geçir.

### A3. Arıza tipi artık Türkçe mi geliyor?

1. Web'de `/binalar` → herhangi bir binaya gir (aktif arızası olan bir bina — örn. daha önce arıza kaydı oluşturulmuş binalardan biri, "Panorama Towers", "Akasya Konutları" vb.).
2. "Aktif Arızalar" bölümüne bak. **Beklenen: "Kapı Arızası", "Elektrik Arızası" gibi Türkçe etiket. Artık `kapı_arizasi`, `elektrik_arizasi` gibi ham kod GÖRÜNMEMELİ.**
3. Aynı binanın tüm arıza tiplerini (elektrik, mekanik, kapı, ses sistemi, acil durum, diğer) barındıran birkaç farklı bina/arıza kaydını kontrol ederek her enum değerinin doğru Türkçe karşılığa sahip olduğunu doğrula — eşleme eksikse o değer boş/null görünebilir, bunu da not et.
4. `/ariza-bildirimi` (Arıza Bildirimleri liste sayfası) ve arıza detay sayfasında da aynı etiketin (zaten önceden doğruydu ama karşılaştırma için) tutarlı olduğunu doğrula — bina detayındaki metin ile arıza listesindeki metin birebir aynı olmalı.

---

## BÖLÜM D — Mobil: Bina Düzenleme / Silme

1. Mobil admin → Binalar → herhangi bir binaya tıkla (detay sayfası).
2. Header'da kalem (düzenle) ve çöp kutusu (sil) ikonlarının göründüğünü doğrula.
3. **Düzenleme testi:** Kalem ikonuna tıkla → form mevcut değerlerle (isim, adres, ilçe, il, kat sayısı, asansör sayısı, aylık ücret vb.) dolu gelmeli. Bir alanı değiştir (örn. "Kat Sayısı"nı +1 yap) → kaydet.
   - Başarı mesajı görünmeli, detay sayfasına dönmeli, değişen değer (yeni kat sayısı) orada görünmeli.
   - Web'de aynı binayı aç (`/binalar/{id}`) → değişikliğin web'e de yansıdığını doğrula (aynı DB).
4. **Silme testi:** Farklı, ÖNEMSİZ bir test binası seç (gerçek veri içeren binaları silme — örn. daha önce hiç bakım/arıza kaydı bağlanmamış bir bina seçin, ya da özellikle bu test için "Test Silme Binası" adında yeni bir bina oluşturup onu silin). Çöp kutusuna tıkla → onay diyaloğu (`Alert.alert`) çıkmalı → onayla.
   - Bina listeden kaybolmalı, "27 Bina" sayısı 1 azalmalı (silme testi için özel oluşturduysanız 27'ye geri dönmeli).
   - Web'de de binanın gerçekten silindiğini (`/binalar` listesinde yok) doğrula.
   - **Dikkat:** Eğer binanın bağlı bakım/arıza/finansal kayıtları varsa silme ne oluyor kontrol et — sunucu hata mı veriyor, yoksa ilişkili kayıtlar da mı siliniyor, yoksa foreign key hatası mı alıyorsun? Bu davranışı gözlemleyip not et (silme constraint'i olmalı mı, ayrı bir karar konusu).

---

## BÖLÜM B — Mobil: Depo (Ürün) Tam CRUD

1. Mobil admin → Depo → **liste sayısını say, "15 ürün" (veya son eklenenlerle güncel sayı) yazdığını doğrula.**
2. **Kart tıklanabilirlik testi (önceki turda bu hiç çalışmıyordu):** Herhangi bir ürüne dokun → detay ekranı açılmalı (isim, marka/kod, kategori, stok miktarı, min. stok, tedarikçi, konum gibi alanlar görünmeli).
3. **Oluşturma testi:** Depo listesinde header'da "+" butonuna dokun → yeni ürün formu açılmalı. Test verisi gir (örn. isim: "Test Ürün Mobil", kod: benzersiz bir kod, kategori seç, birim, maliyet/satış fiyatı, stok miktarı, min. stok) → kaydet.
   - Başarı mesajı, listeye dönüş, yeni ürünün listede göründüğünü doğrula ("16 ürün" olmalı).
   - Web'de `/depo` sayfasında aynı ürünün göründüğünü doğrula.
4. **Düzenleme testi:** Az önce oluşturduğun test ürününe gir → düzenle → stok miktarını değiştir → kaydet → detayda güncel değerin göründüğünü doğrula → web'de de aynı değeri gördüğünü doğrula.
5. **Silme testi:** Aynı test ürününü sil (onay diyaloğuyla) → listeden kaybolmalı, "15 ürün"e geri dönmeli → web'de de silindiğini doğrula.
6. **Kod tekrarı/validasyon testi:** Var olan bir ürün kodunu (`URN-0001` gibi) yeni ürün formunda tekrar kullanmayı dene → backend'in "Ürün markası zaten kullanılıyor" gibi bir hata döndürdüğünü, formun kaydetmeyi reddettiğini doğrula (web'de `code` alanı `unique` idi, aynı kural mobilde de uygulanmalı).

---

## BÖLÜM C — Mobil: Yeni Eklenen 11 Menü Öğesi

Her modül için: **(a)** menüden erişilebiliyor mu, **(b)** liste/ana ekran doğru veri gösteriyor mu (web'deki sayılarla eşleşiyor mu), **(c)** detay/CRUD var mı çalışıyor mu, **(d)** employee hesabıyla girince (admin-only olması gerekenler için) gizli mi.

> Menüden test ederken tek tek tıklamak yerine, önce ekranın tamamının screenshot'ını al, satırların gerçek piksel/point konumunu görsel olarak tespit edip öyle dokun — önceki turda koordinat tahmini yüzünden çok zaman kaybedilmişti.

### C1. Teklifler
1. Menü → Teklifler → liste açılmalı, web'deki `/teklifler` ile aynı sayıda kayıt (test verisinde en az "TKL-20260813-0001" olmalı) görünmeli.
2. Bir teklife tıkla → detay (müşteri, kalemler, KDV, toplam, ₺ sembolü — web'de bu turda düzeltilmişti, mobilde de doğru mu kontrol et) doğru mu.
3. Yeni teklif oluşturma varsa dene; yoksa sadece görüntüleme olduğunu not et.
4. Employee hesabıyla gir → menüde Teklifler görünmemeli (admin-only ise).

### C2. Raporlar
1. Menü → Raporlar → web'deki `/raporlar` sayfasındaki özet rakamlarla (toplam bina, personel, bakım sayıları vb.) karşılaştır.
2. Varsa filtre/tarih aralığı seçeneklerini dene.

### C3. Konum Takibi
1. Menü → Konum Takibi → harita açılmalı, binalar harita üzerinde pin olarak görünmeli.
2. Bir pin'e dokunup bina bilgisinin açıldığını doğrula.
3. Web'deki `/konum-takibi` ile aynı bina sayısının/pinlerinin göründüğünü doğrula.

### C4. Rota Planlayıcı
1. Menü → Rota Planlayıcı → web'deki `/bakim/rota-planlayici` akışıyla karşılaştır (bina seçip rota oluşturma).
2. Bir rota oluşturmayı dene, sonucun mantıklı (binaların mesafeye göre sıralı) olduğunu doğrula.

### C5. Toplu Bakım Oluştur
1. Menü → Toplu Bakım → sihirbaz açılmalı (web'deki `/bakim/toplu` ile aynı adımlar: bina seçimi → bakım tipi → tarih aralığı → önizleme → oluştur).
2. Önizleme adımında kaç kayıt oluşturulacağını göster, "Oluştur"a bas, ardından Bakım listesinde bu yeni kayıtların göründüğünü doğrula.
3. **Dikkat:** Bu gerçek veri oluşturur — test sonrası oluşan kayıtları not edin, gerekirse temizlik için admin'e bildirin (silmeyin, sadece raporlayın).

### C6. Çek & Senet
1. Menü → Çek & Senet → liste web'deki `/cek-senet` ile eşleşmeli.
2. Yeni çek/senet ekleme, durum güncelleme (tahsil edildi/karşılıksız vb.) varsa test et.

### C7. Hakediş & Araç Takip
1. Menü → Hakediş & Araç → web'deki `/hakedis-arac-takip` ile karşılaştır.
2. Alt sekmeler varsa (hakediş, araç, devamsızlık vb.) her birini ayrı ayrı kontrol et.

### C8. DTR (Durum Tespit Raporu)
1. Menü → DTR → web'deki `/belgeler/dtr` akışıyla karşılaştır.
2. Yeni DTR oluşturma formunu dene, PDF/çıktı üretimi varsa (muhtemelen mobilde yok, sadece kayıt) onu not et.

### C9. Kurtarma Formu
1. Menü → Kurtarma Formu → web'deki `/belgeler/kurtarma` ile karşılaştır.
2. Aynı şekilde oluşturma formunu test et.

### C10. Bakım Kontrol Listesi Ayarları
1. Menü → Kontrol Listesi → mobilde bu ayarlar sayfası muhtemelen sadece admin's — A1'de düzelttiğimiz checklist master verisinin BURADAN da görüntülenip görüntülenmediğini kontrol et (mükerrer madde burada da tekrar görünmemeli).
2. Düzenleme izni varsa (madde ekleme/çıkarma), dikkatli test et — yanlışlıkla gerçek checklist'i bozmayın, test sonrası geri alın.

### C11. Kullanım Kılavuzu
1. Menü → Kılavuz → web'deki `/kullanim-kilavuzu` içeriğiyle karşılaştır, içerik doğru render oluyor mu (resim/link kopukluğu var mı).

---

## GENEL REGRESYON — Değişmemiş Olması Gereken Sayfalar

Menüye 11 yeni öğe eklendiği ve Depo/Binalar ekranları değiştiği için, aşağıdaki daha önce çalışan ekranların BOZULMADIĞINI da doğrulayın (menü sırası değişmiş olabilir, koordinat testleri artık farklı yerlere denk gelebilir — bu normal, ama fonksiyonellik bozulmamalı):

1. **Ana Sayfa (Dashboard):** 27 bina, gelir/gider özeti, bekleyen bakım sayısı önceki gibi doğru geliyor mu.
2. **Personel:** Liste + detay hâlâ çalışıyor mu (bu turda dokunulmadı ama menü sırası değiştiği için regresyon riski var).
3. **Bakım:** Liste, detay, rapor oluşturma akışı (bu oturumda daha önce tam test edilmişti) hâlâ bozulmadan çalışıyor mu.
4. **Arıza Bildirimi:** Atama→başla→tamamla zinciri hâlâ çalışıyor mu.
5. **Finansal Yönetim:** Alacaklar listesi, ödeme alma formu hâlâ açılıyor mu.
6. **Etiket Takibi:** Liste + detay + durum güncelleme butonları hâlâ çalışıyor mu.
7. **Firma Profili:** Düzenleme formu hâlâ çalışıyor mu.
8. **Employee (çalışan) girişi:** Ali Demir / Ayşe Kaya / Mehmet Yılmaz ile giriş yapıp, admin-only yeni modüllerin (Teklifler, Raporlar, Depo, Finansal, Etiket Takibi, Çek&Senet, Hakediş&Araç, DTR, Kurtarma, Kontrol Listesi, Firma Profili) çalışan menüsünde GÖRÜNMEDİĞİNİ doğrula — sadece Ana Sayfa, Bakım, Bildirimler, Konum Takibi (varsa), Kılavuz gibi employee'ye açık olanlar görünmeli.

---

## Bulgu Kayıt Formatı

Her test adımı için üç sonuçtan biri olmalı:
- ✅ **Beklendiği gibi çalışıyor**
- ❌ **Bug bulundu** — ekran görüntüsü + tekrar adımları + hangi dosya/route'un sorumlu olabileceği tahmini not edilsin
- ⚠️ **Belirsiz/karar gerektiriyor** — örn. silme constraint davranışı, boş state tasarımı gibi ürün kararı gereken konular

Test bitince tüm ❌ ve ⚠️ maddeleri tek bir özet listede toplanıp bana ya da Cursor'a geri verilecek — bu doküman sadece test ADIMLARINI tanımlıyor, sonuçları ayrı bir dosyada tutulmalı (örn. `docs/test_sonuclari_YYYYMMDD.md`).
