# 🌍 Geocoding (Koordinat Bulma) Kurulum Kılavuzu

Bu sistem, bina adreslerinden koordinat (enlem/boylam) bulmak için **Google Maps Geocoding API** veya **OpenStreetMap Nominatim API** kullanır.

## 🎯 Çözüm Seçenekleri

### 1. Google Maps API (Önerilen - Daha Doğru Sonuçlar)
- ✅ Çok daha doğru sonuçlar
- ✅ Daha iyi adres eşleştirme
- ✅ Türkiye adresleri için optimize edilmiş
- ⚠️ Ücretli (ama ilk $200/ay ücretsiz)
- 🔑 API key gerektirir

### 2. OpenStreetMap Nominatim (Ücretsiz)
- ✅ Tamamen ücretsiz
- ⚠️ Bazen yanlış sonuçlar verebilir
- ⚠️ Rate limiting var (saniyede 1 istek)
- ❌ API key gerektirmez

### 3. Manuel İşaretleme (En Doğru)
- ✅ Haritada tıklayarak manuel olarak koordinat ekleme
- ✅ %100 doğru sonuçlar
- ✅ İnternet bağlantısı gerektirmez

## 📝 Google Maps API Kurulumu

### Adım 1: API Key Oluşturma

1. [Google Cloud Console](https://console.cloud.google.com/) 'a gidin
2. Yeni bir proje oluşturun veya mevcut projeyi seçin
3. **APIs & Services > Library** bölümüne gidin
4. **Geocoding API**'yi arayın ve **Enable** edin
5. **APIs & Services > Credentials** bölümüne gidin
6. **Create Credentials > API Key** seçin
7. API key'i kopyalayın

### Adım 2: .env Dosyasına Ekleyin

`.env` dosyanıza şu satırları ekleyin:

```env
GOOGLE_MAPS_API_KEY=your_api_key_here
GOOGLE_GEOCODING_ENABLED=true
```

### Adım 3: API Key'i Kısıtlayın (Güvenlik İçin)

1. Google Cloud Console'da oluşturduğunuz API key'e tıklayın
2. **API restrictions** bölümünde:
   - **Restrict key** seçin
   - **Geocoding API** seçin
3. **Application restrictions** bölümünde:
   - **HTTP referrers** seçin
   - Web sitenizin domain'ini ekleyin (örn: `https://yourdomain.com/*`)

### Adım 4: Faturalandırma Ayarları

1. **Billing** bölümüne gidin
2. Faturalandırma hesabınızı bağlayın
3. **Budget alerts** oluşturun (önerilen: $50 limit)

> **Not:** Google Maps API'nin ilk $200'ü her ay ücretsizdir. Bu yaklaşık 40,000 geocoding isteği demektir.

## 🗺️ Manuel İşaretleme Kullanımı

Eğer otomatik geocoding yanlış sonuç verirse:

1. Harita sayfasına gidin (`/konum-takibi`)
2. Koordinatsız binalar listesinde **"🗺️ Haritada İşaretle"** butonuna tıklayın
3. Haritada doğru konumu tıklayın
4. Açılan popup'ta **"✅ Bu Konumu Kaydet"** butonuna tıklayın

## 🔄 Sistem Nasıl Çalışır?

1. **Bina oluşturma/güncelleme** sırasında:
   - Önce Google Maps API denenir (eğer etkinse)
   - Başarısız olursa OpenStreetMap denenir
   - Her iki servis de başarısız olursa koordinat eklenmez

2. **Harita sayfasında**:
   - Koordinatsız binalar için **"📍 Adresten Al"** butonu görünür
   - Bu buton otomatik geocoding'i tekrar dener
   - **"🗺️ Haritada İşaretle"** butonu ile manuel işaretleme yapılabilir

3. **Cache Sistemi**:
   - Başarılı geocoding sonuçları 24 saat cache'lenir
   - Aynı adres tekrar sorgulandığında API çağrısı yapılmaz

## 🎛️ Geocoding Servisi Ayarları

`config/services.php` dosyasında:

```php
'google' => [
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    'geocoding_enabled' => env('GOOGLE_GEOCODING_ENABLED', false),
],
```

## 💡 Öneriler

1. **Google Maps API kullanın**: Daha doğru sonuçlar için
2. **API key'i kısıtlayın**: Güvenlik için sadece gerekli API'lere izin verin
3. **Budget alert kurun**: Beklenmedik maliyetlerden korunmak için
4. **Manuel işaretleme kullanın**: Önemli binalar için en doğru yöntem
5. **Cache'i temizleyin**: Yanlış sonuçlar için: `php artisan cache:clear`

## 🐛 Sorun Giderme

### Google Maps API çalışmıyor
- API key'in doğru olduğundan emin olun
- Geocoding API'nin enable olduğunu kontrol edin
- Faturalandırma hesabının bağlı olduğunu kontrol edin
- `.env` dosyasında `GOOGLE_GEOCODING_ENABLED=true` olduğunu kontrol edin

### OpenStreetMap çok yavaş
- Normal: Saniyede 1 istek limiti var
- Rate limiting için sistem otomatik olarak bekler

### Koordinatlar yanlış
- Manuel işaretleme kullanın (en doğru yöntem)
- Google Maps API kullanmayı deneyin
- Adresi daha spesifik yazın (mahalle, sokak numarası)

## 📞 Destek

Sorun yaşarsanız:
1. `storage/logs/laravel.log` dosyasını kontrol edin
2. Browser console'da JavaScript hatalarını kontrol edin
3. API response'larını log'larda kontrol edin
