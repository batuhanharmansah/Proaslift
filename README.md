# Harmanşah Yazılım - Yönetim Paneli

Laravel 10 tabanlı kurumsal yönetim paneli projesi. Modern ve kullanıcı dostu arayüzü ile işletme yönetimini kolaylaştırır.

## 🚀 Özellikler

- **Modern Tasarım**: TailwindCSS ile responsive ve kurumsal tasarım
- **Güvenli Kimlik Doğrulama**: Laravel Breeze ile güvenli giriş sistemi
- **İnteraktif Dashboard**: Chart.js ile görsel raporlama
- **Türkçe Dil Desteği**: Tamamen Türkçeleştirilmiş arayüz
- **Mobil Uyumlu**: Tüm cihazlarda mükemmel görünüm

## 🛠 Teknoloji Stack

- **Backend**: PHP 8.2+, Laravel 10
- **Frontend**: Blade Templates, TailwindCSS, Alpine.js
- **Grafik**: Chart.js (CDN)
- **Veritabanı**: MySQL
- **Kimlik Doğrulama**: Laravel Breeze

## 📋 Gereksinimler

- PHP 8.2 veya üzeri
- Composer
- Node.js & NPM
- MySQL (MAMP önerilen)
- Git

## 🔧 Kurulum

### 1. Projeyi İndirin
```bash
git clone <repository-url>
cd asansor
```

### 2. Bağımlılıkları Yükleyin
```bash
# PHP bağımlılıkları
composer install

# Node.js bağımlılıkları
npm install
```

### 3. Ortam Ayarlarını Yapılandırın
```bash
# .env dosyasını oluşturun
cp .env.example .env

# Uygulama anahtarını oluşturun
php artisan key:generate
```

### 4. Veritabanı Ayarları (.env dosyasında)
MAMP kullanıyorsanız aşağıdaki ayarları kullanın:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=harmansah_panel
DB_USERNAME=root
DB_PASSWORD=root
```

### 5. Veritabanını Hazırlayın
```bash
# Veritabanını oluşturun (phpMyAdmin veya MySQL komut satırı ile)
# Database adı: harmansah_panel

# Migration'ları çalıştırın ve örnek verileri yükleyin
php artisan migrate --seed
```

### 6. Asset'leri Derleyin
```bash
# Geliştirme için
npm run dev

# Veya production için
npm run build
```

### 7. Sunucuyu Başlatın
```bash
php artisan serve
```

Uygulama `http://127.0.0.1:8000` adresinde çalışacaktır.

## 👤 Giriş Bilgileri

Kurulum sonrası aşağıdaki admin hesabı ile giriş yapabilirsiniz:

- **E-posta**: admin@harmansah.com
- **Şifre**: Admin123!

## 🗂 Proje Yapısı

```
├── app/
│   ├── Http/Controllers/
│   │   └── DashboardController.php      # Ana dashboard kontrolcüsü
│   └── Models/                          # Eloquent modelleri
├── database/
│   ├── migrations/                      # Veritabanı migration'ları
│   └── seeders/                        # Örnek veri seeder'ları
├── resources/
│   ├── views/
│   │   ├── auth/                       # Kimlik doğrulama sayfaları
│   │   ├── dashboard/                  # Dashboard sayfaları
│   │   ├── layouts/                    # Layout şablonları
│   │   ├── pages/                      # Diğer sayfalar
│   │   └── partials/                   # Tekrar kullanılabilir bileşenler
│   └── css/                            # Stil dosyaları
├── routes/
│   └── web.php                         # Web rotaları
└── lang/tr/                           # Türkçe dil dosyaları
```

## 📊 Dashboard Özellikleri

- **İstatistik Kartları**: Toplam kullanıcı, günlük ziyaret, dönüşüm oranı, aktif oturum
- **Grafik Görünümü**: Chart.js ile interaktif satış trendi grafiği
- **Son İşlemler**: Kullanıcı aktivitelerinin listesi
- **Banner Alanı**: Harmanşah Yazılım kurumsal banner'ı

## 🎨 Renk Paleti

- **Birincil**: #1A73E8 (Mavi)
- **Arka Plan**: #F9FAFB
- **Vurgu Renkleri**:
  - Altın: #F4B400
  - Yeşil: #34A853
- **Metin**:
  - Birincil: #111827
  - İkincil: #374151

## 📱 Responsive Tasarım

- **Desktop**: Tam özellikli sidebar ve navbar
- **Tablet**: Daraltılmış sidebar
- **Mobil**: Hamburger menü ile gizli/açılır sidebar

## 🔧 MAMP Ayarları

### phpMyAdmin Erişimi
- URL: `http://localhost:8888/phpMyAdmin`
- Kullanıcı: `root`
- Şifre: `root`
- Port: `8889`

### Veritabanı Oluşturma
1. phpMyAdmin'e giriş yapın
2. "Yeni" butonuna tıklayın
3. Veritabanı adı: `harmansah_panel`
4. Karakter seti: `utf8mb4_unicode_ci`
5. "Oluştur" butonuna tıklayın

## 🚀 Production Deployment

### 1. Asset'leri Optimize Edin
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Ortam Ayarları
```env
APP_ENV=production
APP_DEBUG=false
```

### 3. Dosya İzinleri
```bash
chmod -R 755 storage bootstrap/cache
```

## 🐛 Sorun Giderme

### Yaygın Sorunlar

1. **"No application encryption key has been specified"**
   ```bash
   php artisan key:generate
   ```

2. **Veritabanı bağlantı hatası**
   - MAMP'ın çalıştığından emin olun
   - .env dosyasındaki veritabanı ayarlarını kontrol edin

3. **CSS/JS dosyaları yüklenmiyor**
   ```bash
   npm run dev
   # veya
   npm run build
   ```

4. **Migration hataları**
   ```bash
   php artisan migrate:fresh --seed
   ```

## 📞 Destek

Bu proje **Harmanşah Yazılım** tarafından geliştirilmiştir.

- **Web**: [Harmanşah Yazılım](#)
- **E-posta**: info@harmansah.com
- **Telefon**: +90 (XXX) XXX XX XX

## 🧪 Test Notları

- PHPUnit test ortamı `sqlite` in-memory kullanır.
- Tarihsel migration'larda column change işlemleri bulunduğu için `doctrine/dbal` dev dependency olarak eklenmiştir.
- Kritik tenant izolasyonu regresyon testi:
  `php artisan test --filter=BuildingDocumentSecurityTest`

## 🏗 Mimari Not

- Mobil API için tek doğruluk kaynağı `routes/mobile-api.php` dosyasıdır.
- `routes/api.php` içinde yalnızca web/legacy API uçları tutulmalı; duplicate `/api/mobile/*` tanımları tekrar eklenmemelidir.
- Ayrıntılı mimari özet için `docs/architecture.md` dosyasına bakın.

## 📄 Lisans

© 2025 Harmanşah Yazılım – Tüm Hakları Saklıdır

---

**Not**: Bu proje Laravel 10 ve PHP 8.2+ gerektirir. Kurulum öncesi sistem gereksinimlerini kontrol edin.
