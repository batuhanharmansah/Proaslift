#!/bin/bash

echo "🚀 Laravel projesini Natro hosting için hazırlıyor..."

# Deployment klasörü oluştur
mkdir -p natro-deployment/public_html
mkdir -p natro-deployment/laravel

echo "📁 Dosyaları kopyalıyor..."

# Public klasörü içeriğini public_html'e kopyala
cp -r public/* natro-deployment/public_html/

# Laravel dosyalarını laravel klasörüne kopyala
cp -r app natro-deployment/laravel/
cp -r bootstrap natro-deployment/laravel/
cp -r config natro-deployment/laravel/
cp -r database natro-deployment/laravel/
cp -r resources natro-deployment/laravel/
cp -r routes natro-deployment/laravel/
cp -r storage natro-deployment/laravel/
cp -r vendor natro-deployment/laravel/
cp artisan natro-deployment/laravel/
cp composer.json natro-deployment/laravel/
cp composer.lock natro-deployment/laravel/

# .env template'i kopyala
cp env-natro-template.txt natro-deployment/laravel/.env

# Özel index.php'yi kopyala
cp natro-index.php natro-deployment/public_html/index.php

# Deployment guide'ı kopyala
cp natro-deployment-guide.md natro-deployment/

echo "🔧 İzinleri ayarlıyor..."
chmod -R 755 natro-deployment/laravel/storage
chmod -R 755 natro-deployment/laravel/bootstrap/cache

echo "✅ Natro deployment hazır!"
echo "📂 Dosyalar: natro-deployment/ klasöründe"
echo ""
echo "🔥 Sonraki adımlar:"
echo "1. natro-deployment/public_html/ içeriğini Natro'nun public_html/ klasörüne yükleyin"
echo "2. natro-deployment/laravel/ klasörünü Natro'da public_html/ dışına yükleyin"
echo "3. .env dosyasındaki veritabanı bilgilerini güncelleyin"
echo "4. APP_KEY generate edin: php artisan key:generate"
echo "5. Veritabanını import edin: harmansah_panel.sql"
echo ""
echo "📞 Test adresleri:"
echo "- Ana sayfa: https://yourdomain.com"
echo "- Admin: admin@harmansah.com / Admin123!"
echo "- Çalışan: ahmet.yilmaz@harmansah.com / password"
