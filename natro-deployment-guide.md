# Laravel Natro Hosting Deployment Guide

## 📁 Dosya Yapısı (Natro için)

### Natro'da şu yapıyı oluşturun:
```
public_html/          (Bu Natro'nun root klasörü)
├── index.php        (Laravel'in public/index.php dosyası)
├── .htaccess         (Laravel'in public/.htaccess dosyası)
├── css/              (public/css klasörü)
├── js/               (public/js klasörü)
├── images/           (public/images klasörü)
└── build/            (public/build klasörü)

laravel/              (Bu klasörü public_html dışında oluşturun)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── composer.lock
```

## 🔧 Adım Adım Kurulum:

### 1. Dosyaları Yükleyin
- `public` klasörü içindeki tüm dosyaları `public_html/` içine kopyalayın
- Diğer tüm dosyaları `laravel/` klasörü içine koyun

### 2. index.php Dosyasını Düzenleyin
`public_html/index.php` dosyasında path'leri düzeltin:

```php
<?php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

### 3. .env Dosyasını Oluşturun
```env
APP_NAME="Asansör Otomasyon"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 4. Storage Klasörü İzinleri
Storage klasörüne yazma izni verin:
```bash
chmod -R 755 laravel/storage
chmod -R 755 laravel/bootstrap/cache
```

### 5. .htaccess Dosyası
`public_html/.htaccess` dosyasını kontrol edin:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 🗄️ Veritabanı Kurulumu:

### 1. phpMyAdmin'den:
- Yeni veritabanı oluşturun
- `harmansah_panel.sql` dosyasını import edin
- Kullanıcı izinlerini ayarlayın

### 2. Laravel Komutları (SSH varsa):
```bash
cd laravel/
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔐 Güvenlik:
- `.env` dosyasının web'den erişilemez olduğundan emin olun
- `storage/` ve `bootstrap/cache/` klasörlerine yazma izni verin
- APP_DEBUG=false olarak ayarlayın

## 📞 Test:
1. Ana sayfaya gidin: `https://yourdomain.com`
2. Login sayfasını test edin: `https://yourdomain.com/login`
3. Admin paneli: `admin@harmansah.com` / `Admin123!`

## ⚠️ Önemli Notlar:
- Composer paketlerini local'de yükleyip vendor klasörünü upload edin
- APP_KEY'i mutlaka generate edin
- Dosya izinlerini kontrol edin
- Error log'larını takip edin
