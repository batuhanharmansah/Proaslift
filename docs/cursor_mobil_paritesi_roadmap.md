# Proaslift — Mobil Uygulamayı Web ile Paritye Getirme ve Bulunan Bug'ları Düzeltme

## Bağlam (Cursor'a proje tanıtımı)

Bu, "Proaslift" adlı asansör bakım/yönetim SaaS'ı. İki ayrı kod tabanı var:

- **Web (Laravel 10, Blade, MySQL):** `asansor-web/` — tam kapsamlı admin paneli, company_admin ve employee rolleri var. Route'lar `routes/web.php`'de.
- **Mobil (React Native + Expo, TypeScript):** `asansor-mobile/` — web'in bir alt kümesi. Ekranlar `src/screens/`, navigasyon `src/navigation/AppNavigator.tsx`, drawer menü `src/components/navigation/CustomSidebar.tsx`, API sabitleri `src/constants/index.ts`.
- **Mobil API (Laravel, Sanctum token auth):** `asansor-web/routes/mobile-api.php` — web'in `routes/web.php`'sinden TAMAMEN AYRI, mobile'a özel route dosyası. Mobil sadece buradaki endpoint'leri kullanabilir.

Kapsamlı bir manuel test turunda (hem web hem mobil, gerçek tarayıcı ve iOS Simulator üzerinden) şu bulgular netleşti. Aşağıdaki her madde ayrı bir çalışma birimi olarak ele alınmalı — birini bitirip diğerine geçmeden önce ilgilisi test edilmeli (mevcut test kullanıcısı: `admin@claude-asansor.com` / şifre kullanıcıda, MAMP local `127.0.0.1:8000`, mobil `.env.local`'daki `EXPO_PUBLIC_API_URL`'nin makinenin güncel LAN IP'sine işaret ettiğinden emin olun — DHCP ile değişebiliyor).

**Kesin kural: Hiçbir adımda git'e push YOK. Sadece local'de MAMP üzerinden test edip kullanıcıya rapor edin, push kullanıcının açık onayını bekler.**

---

## BÖLÜM A — Web Tarafı: Küçük Ama Gerçek Buglar (Hızlı, Düşük Risk)

### A1. Kontrol listesi (checklist) master verisinde mükerrer madde
**Belirti:** Rutin bakım raporu doldurma ekranında (hem web hem mobil detaylı form) "Makine Dairesi Kontrolü" bölümünde toplam 14 madde olması gerekirken 15 gösteriliyor / sayaç "15/14" gibi imkânsız bir oran veriyor. "Makina fren balata kontrolü" iki kez tanımlanmış.

**Yapılacak:**
1. Checklist master verisinin tutulduğu yeri bulun — `settings/checklist-items` sayfası (`resources/views/settings/checklist-items` civarı) ve/veya `App\Models\ChecklistItem` (veya benzeri model) + ilgili migration/seed dosyası.
2. `machine_room_checks` kategorisinde "Makina fren balata kontrolü" başlıklı iki satırı bulup mükerrer olanı silin (hangisinin orijinal/ hangisinin kopya olduğunu `created_at`'e göre belirleyin, daha eski olanı koruyun).
3. Bu değişikliği hem varsa seed dosyasında hem canlı DB'de (raw SQL migration dosyası olarak — Laravel `php artisan migrate` KULLANILMIYOR, hosting FTP-only, migration'lar elle SQL dosyası olarak `database/` altına yazılıyor) uygulayın.
4. Web'de bir bakım raporu açıp "15/14" yerine "14/14" gösterdiğini doğrulayın.

### A2. Bildirim sayaç tutarsızlığı: "Okunmamış" sayısı "Toplam"dan büyük çıkabiliyor
**Belirti:** Mobil Bildirimler ekranında "Tümü (20)" ile "Okunmamış (25)" aynı anda gösterildi — okunmamış sayısı toplamdan büyük olamaz, bu bir backend/count mantığı hatası.

**Yapılacak:**
1. `app/Http/Controllers/Api/` altında bildirim sayımını yapan controller'ı bulun (muhtemelen `NotificationController` — mobile-api.php'deki `notifications` prefix'ine bakın, `routes/mobile-api.php:204-220` civarı).
2. "Toplam" ve "Okunmamış" sayılarının AYNI sorgu/scope'tan (aynı `company_id`, aynı kullanıcıya ait bildirimler) geldiğini doğrulayın. Muhtemel kök neden: biri paginate edilmiş `count()`, diğeri paginate edilmemiş `count()`; ya da biri sadece "son N gün", diğeri tüm zamanlar; ya da bir join/where farkı.
3. Düzeltip hem web (varsa benzer bir bildirim sayacı) hem mobilde aynı mantığın kullanıldığını doğrulayın.

### A3. Bina detayında arıza tipi ham enum değeriyle gösteriliyor
**Belirti:** Web'de bir binanın detay sayfasında "Aktif Arızalar" bölümünde arıza tipi `kapı_arizasi` gibi ham (İngilizce/snake_case) değer görünüyor, "Kapı Arızası" gibi okunaklı Türkçe etiket yerine.

**Yapılacak:**
1. `resources/views/buildings/show.blade.php` içinde "Aktif Arızalar" bölümünü bulun.
2. `IssueReport` modelinde muhtemelen zaten bir `issue_type_label` accessor'ı vardır (diğer sayfalarda kullanılıyor olabilir — `IssueReportController` veya `resources/views/issue-reports/` altında arayın). Eğer varsa bina detay sayfasında o accessor kullanılmıyor demektir — `$issue->issue_type` yerine `$issue->issue_type_label` (veya eşdeğeri) kullanın.
3. Eğer accessor hiç yoksa, `App\Models\IssueReport`'a bir `getIssueTypeLabelAttribute()` ekleyin (diğer enum-to-Türkçe eşlemelerinin yapıldığı kalıbı takip edin — örn. `FinancialController::categoryLabel()` veya `position_label` gibi mevcut örnekler var, aynı deseni kullanın).

---

## BÖLÜM B — Mobil: Depo (Ürün/Stok) Modülüne Tam CRUD Eklenmesi

**Mevcut durum (kod ile doğrulandı):** `asansor-mobile/src/screens/depot/DepotScreen.tsx` sadece `apiClient.get(API_ENDPOINTS.MAINTENANCE_PRODUCTS)` çağırıyor — bu endpoint (`/api/mobile/maintenance/products`) bakım raporu formundaki "kullanılan ürün seç" dropdown'ı için var, gerçek bir Depo CRUD endpoint'i değil. Kartlar `TouchableOpacity` bile değil (`onPress` yok), header'da "+" yok. `routes/mobile-api.php`'de `products` veya `depot` prefix'i YOK — backend'de mobil için hiç Depo API'si yok.

Web tarafında referans: `app/Http/Controllers/ProductController.php` (tam CRUD: index/create/store/show/edit/update/destroy, ayrıca `bulkStockPage`/`bulkUpdateStock`).

**Yapılacak (sırayla):**

1. **Backend — mobil API endpoint'leri ekle** (`routes/mobile-api.php`, mevcut `buildings` grubunun deseniyle aynı: read-throttle + write-throttle ayrı gruplar):
   ```php
   Route::prefix('products')->group(function () {
       Route::middleware('throttle:120,1,mobile-products-read')->group(function () {
           Route::get('/', [App\Http\Controllers\Api\MobileProductController::class, 'index']);
           Route::get('/{id}', [App\Http\Controllers\Api\MobileProductController::class, 'show']);
       });
       Route::middleware('throttle:30,1,mobile-products-write')->group(function () {
           Route::post('/', [App\Http\Controllers\Api\MobileProductController::class, 'store']);
           Route::put('/{id}', [App\Http\Controllers\Api\MobileProductController::class, 'update']);
           Route::delete('/{id}', [App\Http\Controllers\Api\MobileProductController::class, 'destroy']);
       });
   });
   ```
2. **Backend — `App\Http\Controllers\Api\MobileProductController` oluştur.** Web'deki `ProductController`'ın validasyon kurallarını ve `company_id` scoping mantığını birebir kopyalayın (mobil API'de zaten kullanılan `auth()->user()->company_id` deseni — diğer mobil controller'lara bakın, örn. `Api\MobileBuildingController` varsa onu örnek alın). JSON response formatını mobil'in diğer endpoint'leriyle tutarlı tutun (`{success, data}` gibi bir zarf kullanılıyorsa aynısını kullanın — `MAINTENANCE_PRODUCTS` endpoint'inin response şeklini inceleyip taklit edin).
3. **Frontend — `src/constants/index.ts`'e endpoint sabitleri ekle:** `PRODUCTS: '/api/mobile/products'`, `PRODUCT_DETAIL: (id) => ...` gibi (mevcut `API_ENDPOINTS` deseniyle tutarlı).
4. **Frontend — `DepotScreen.tsx`'i güncelle:**
   - `renderItem`'daki `<View>`'ı `<TouchableOpacity onPress={() => navigation.navigate('ProductDetail', {productId: item.id})}>` yapın.
   - Header'a "+" (Ionicons `add`) butonu ekleyin → `navigation.navigate('ProductCreate')`.
   - Gerçek `PRODUCTS` endpoint'inden veri çekecek şekilde `fetchProducts`'ı güncelleyin.
5. **Frontend — yeni ekranlar oluştur** (`src/screens/depot/ProductDetailScreen.tsx`, `ProductCreateScreen.tsx`, `ProductEditScreen.tsx`), `BuildingCreateScreen.tsx`'i şablon olarak kullanın (validasyon + `Alert.alert` başarı/hata deseni aynı).
6. **`AppNavigator.tsx`'e yeni route'ları kaydet** (`ProductDetail`, `ProductCreate`, `ProductEdit`), `types/index.ts`'deki `RootStackParamList`'e ekleyin.
7. Test: Depo listesine gir → bir ürüne tıkla → detay açılmalı → "+" ile yeni ürün ekle → listeye düşmeli → web'de aynı ürünün göründüğünü doğrula (aynı DB).

---

## BÖLÜM C — Mobil: Menüde Hiç Olmayan Web Modülleri (Ürün Kararı Gerektirir)

Aşağıdaki modüller web'de tam çalışır durumda ama mobil drawer menüsünde (`CustomSidebar.tsx`) hiç yok, mobil API'de de (`routes/mobile-api.php`) hiç karşılığı yok:

- **Teklifler (Quotations)** — web: `routes/web.php` `teklifler` prefix'i, `QuotationController`
- **Raporlar** (genel raporlar sayfası — mobilin kendi `reports` prefix'i VAR ama bu farklı/dar kapsamlı, web'in `raporlar` sayfasıyla karıştırmayın)
- **Konum Takibi** (harita) — mobilde `location-map` API'si zaten var (`mobile-api.php:268`), ama drawer menüsünde ekran/giriş noktası yok — bu diğerlerinden ucuz olabilir, önce buna bakın
- **Rota Planlayıcı**
- **Toplu Bakım Oluştur** (sihirbaz)
- **Çek & Senet**
- **Hakediş & Araç Takip**
- **DTR (Durum Tespit Raporu)**
- **Kurtarma Formu**
- **Kullanım Kılavuzu**
- **Bakım Kontrol Listesi ayarları**

**Bu bölüm için Cursor'a görev vermeden önce KULLANICIYA SORULMASI GEREKEN KARAR:** Bunların hepsi mi mobile taşınacak, yoksa öncelik sırası var mı (örn. sahada en çok ihtiyaç duyulanlar: Konum Takibi, Toplu Bakım, Teklifler öncelikli; Çek&Senet/DTR/Kurtarma Formu ofis-admin işi olduğu için düşük öncelikli olabilir)? Bu netleşmeden Cursor'a topyekûn "hepsini ekle" görevi vermek büyük, kontrolsüz bir iş yükü yaratır.

**Her modül için Cursor'a verilecek görev şablonu (Konum Takibi örneği üzerinden):**
```
1. Web'deki karşılığını oku: resources/views/location-map/*.blade.php + ilgili controller.
2. Mobil API'de zaten var olan endpoint'i kullan: routes/mobile-api.php:268-274 (location-map prefix).
   Eksik endpoint varsa (örn. yazma/güncelleme) aynı dosyaya, BÖLÜM B'deki desenle ekle.
3. src/screens/location/ altında (TodayJobsScreen.tsx zaten var, örnek alınabilir) yeni bir
   LocationMapScreen.tsx oluştur — harita gösterimi için mevcut RN harita kütüphanesini
   kullan (package.json'da hangisi kurulu kontrol et — react-native-maps vb.).
4. CustomSidebar.tsx'teki menuItems dizisine yeni satır ekle (mevcut 10 öğenin deseniyle
   birebir aynı: id, title, icon, route, color; employee'den gizlenecekse
   ...(isEmployee ? [] : [{...}]) sarmalayıcısını kullan).
5. AppNavigator.tsx'e route kaydet, types/index.ts'e ekle.
6. iOS Simulator'de canlı test et: menüden aç, verinin web'dekiyle birebir eşleştiğini doğrula.
```
Diğer 9 modül için de birebir bu şablonu, ilgili web dosya yollarını değiştirerek tekrarlayın.

---

## BÖLÜM D — Mobil: Binalar Modülünde Eksik Düzenleme/Silme

**Mevcut durum:** `BuildingDetailScreen.tsx`'de hiç `apiClient.put`/`delete` çağrısı yok — sadece görüntüleme + arama/telefon butonları var. Oluşturma (`BuildingCreateScreen.tsx`) zaten çalışıyor.

**Yapılacak:**
1. `routes/mobile-api.php:102-108` civarında `mobile-buildings-write` grubuna `PUT /{id}` ve `DELETE /{id}` route'ları zaten var mı kontrol edin (muhtemelen sadece create var — yoksa ekleyin, backend `MobileBuildingController`'a `update`/`destroy` metodları ekleyin, web'deki `BuildingController@update`/`@destroy` validasyon kurallarını referans alın).
2. `BuildingEditScreen.tsx` oluşturun (yoksa) — `BuildingCreateScreen.tsx`'in düzenleme versiyonu, mevcut değerlerle formu doldurup `PUT` ile güncelleyin.
3. `BuildingDetailScreen.tsx`'e header'a düzenle (kalem ikonu) ve sil (çöp kutusu, `Alert.alert` onaylı) butonları ekleyin — `ReceivablesScreen.tsx:292-305`'teki silme onay deseni referans alınabilir.
4. `AppNavigator.tsx`'e `BuildingEdit` route'unu kaydedin.

---

## BÖLÜM E — Doğrulama Adımı (Her Bölüm İçin Zorunlu)

Her bölüm (A, B, C'nin her bir alt-modülü, D) tamamlandığında:
1. `php artisan route:list` ile yeni route'ların gerçekten kayıtlı olduğunu doğrula.
2. `php -l <değişen_dosya>` ile PHP syntax kontrolü yap.
3. Web tarafı değiştiyse tarayıcıda (MAMP `127.0.0.1:8000`), mobil tarafı değiştiyse iOS Simulator'de (`npx expo start` + Simulator) GERÇEKTEN AÇIP TIKLAYARAK test et — sadece kod okuyarak "olmalı" deme.
4. Aynı veriyi web ve mobilde yan yana karşılaştır (aynı MySQL DB'yi paylaşıyorlar, `harmansah_panel`).
5. Hiçbir adımda `git add`/`commit`/`push` YAPMA — sadece değişikliği bildir, push kullanıcı onayı bekler.

---

## Öncelik Sırası (Önerilen)

1. **A1, A2, A3** — küçük, hızlı, düşük riskli, hemen yapılabilir.
2. **D (Binalar düzenle/sil)** — orta boy, tek modül, backend+frontend net.
3. **B (Depo tam CRUD)** — orta-büyük, ama kapsamı net ve tek modül.
4. **C (eksik 10 modül)** — büyük iş, önce kullanıcıyla önceliklendirme konuşulmalı; konum takibi (API zaten var) en ucuz başlangıç noktası.
