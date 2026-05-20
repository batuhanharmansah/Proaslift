<?php
/**
 * 🔄 Laravel Cache Temizleme Scripti
 *
 * Kullanım: Tarayıcıdan şu URL'yi açın:
 * https://proaslift.com/clear-cache.php
 *
 * ⚠️ GÜVENLİK: Bu dosyayı cache temizledikten sonra SİLİN veya şifre koruması ekleyin!
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Laravel bootstrap - hata kontrolü ile
try {
    $autoloadPath = __DIR__.'/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception("Autoload dosyası bulunamadı: $autoloadPath");
    }
    require $autoloadPath;

    $bootstrapPath = __DIR__.'/../bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        throw new Exception("Bootstrap dosyası bulunamadı: $bootstrapPath");
    }
    $app = require_once $bootstrapPath;

    // Laravel'i bootstrap et - facade'lerin çalışması için gerekli
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

} catch (Exception $e) {
    die("❌ Laravel bootstrap hatası: " . $e->getMessage() . "<br>Dosya: " . $e->getFile() . "<br>Satır: " . $e->getLine());
}

// Basit şifre koruması (opsiyonel - şifreyi değiştirin!)
$password = 'temizle2026'; // Bu şifreyi değiştirin!
$requirePassword = false; // false yaparsanız şifre istemez

// Şifre kontrolü
if ($requirePassword && (!isset($_GET['password']) || $_GET['password'] !== $password)) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Cache Temizleme</title>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                margin-bottom: 20px;
            }
            input {
                width: 100%;
                padding: 12px;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 16px;
            }
            button {
                background: #3b82f6;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                width: 100%;
            }
            button:hover {
                background: #2563eb;
            }
            .warning {
                background: #fef3c7;
                border: 1px solid #f59e0b;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
                color: #92400e;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔄 Cache Temizleme</h1>
            <div class="warning">
                ⚠️ <strong>Güvenlik:</strong> Şifre gerekli. Cache temizlendikten sonra bu dosyayı silin!
            </div>
            <form method="GET">
                <input type="password" name="password" placeholder="Şifre girin" required>
                <button type="submit">Cache Temizle</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Cache temizleme işlemleri
$results = [];
$errors = [];

try {
    // Route cache temizle
    Illuminate\Support\Facades\Artisan::call('route:clear');
    $results[] = ['status' => 'success', 'message' => '✅ Route cache temizlendi'];
} catch (Exception $e) {
    $errors[] = ['message' => '❌ Route cache temizlenemedi: ' . $e->getMessage()];
}

try {
    // Config cache temizle
    Illuminate\Support\Facades\Artisan::call('config:clear');
    $results[] = ['status' => 'success', 'message' => '✅ Config cache temizlendi'];
} catch (Exception $e) {
    $errors[] = ['message' => '❌ Config cache temizlenemedi: ' . $e->getMessage()];
}

try {
    // View cache temizle
    Illuminate\Support\Facades\Artisan::call('view:clear');
    $results[] = ['status' => 'success', 'message' => '✅ View cache temizlendi'];
} catch (Exception $e) {
    $errors[] = ['message' => '❌ View cache temizlenemedi: ' . $e->getMessage()];
}

try {
    // Application cache temizle
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    $results[] = ['status' => 'success', 'message' => '✅ Application cache temizlendi'];
} catch (Exception $e) {
    $errors[] = ['message' => '❌ Application cache temizlenemedi: ' . $e->getMessage()];
}

try {
    // Route cache yeniden oluştur
    Illuminate\Support\Facades\Artisan::call('route:cache');
    $results[] = ['status' => 'success', 'message' => '✅ Route cache yeniden oluşturuldu'];
} catch (Exception $e) {
    $errors[] = ['message' => '❌ Route cache oluşturulamadı: ' . $e->getMessage()];
}

try {
    // Config cache yeniden oluştur
    Illuminate\Support\Facades\Artisan::call('config:cache');
    $results[] = ['status' => 'success', 'message' => '✅ Config cache yeniden oluşturuldu'];
} catch (Exception $e) {
    $errors[] = ['message' => '❌ Config cache oluşturulamadı: ' . $e->getMessage()];
}

// Sonuçları göster
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cache Temizleme Sonuçları</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .error {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .warning {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
            color: #92400e;
        }
        .info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Cache Temizleme Sonuçları</h1>

        <?php if (!empty($results)): ?>
            <h2>✅ Başarılı İşlemler:</h2>
            <?php foreach ($results as $result): ?>
                <div class="success">
                    <?php echo htmlspecialchars($result['message']); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <h2>❌ Hatalar:</h2>
            <?php foreach ($errors as $error): ?>
                <div class="error">
                    <?php echo htmlspecialchars($error['message']); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="warning">
            <strong>⚠️ ÖNEMLİ GÜVENLİK UYARISI:</strong><br><br>
            Cache temizleme işlemi tamamlandı. <strong>Bu dosyayı hemen silin!</strong><br><br>
            Dosya yolu: <code>public/clear-cache.php</code><br><br>
            Bu dosya canlı sunucuda bırakılırsa güvenlik riski oluşturabilir.
        </div>

        <div class="info">
            <strong>ℹ️ Bilgi:</strong><br>
            • Route cache temizlendi ve yeniden oluşturuldu<br>
            • Config cache temizlendi ve yeniden oluşturuldu<br>
            • View cache temizlendi<br>
            • Application cache temizlendi<br><br>
            Artık API endpoint'leriniz güncel route'larla çalışmalı.
        </div>

        <p style="margin-top: 30px; color: #666;">
            <small>Zaman: <?php echo date('Y-m-d H:i:s'); ?></small>
        </p>
    </div>
</body>
</html>
