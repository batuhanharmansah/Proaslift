<?php
/**
 * 🔄 Basit Cache Temizleme Scripti
 * Laravel bootstrap olmadan direkt dosya sistemini kullanır
 * 
 * Kullanım: https://proaslift.com/clear-cache-simple.php?password=temizle2026
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Şifre kontrolü
$password = 'temizle2026';
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Cache Temizleme</title>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
            .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #333; }
            input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
            button { background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
            button:hover { background: #2563eb; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔄 Cache Temizleme</h1>
            <form method="GET">
                <input type="password" name="password" placeholder="Şifre: temizle2026" required>
                <button type="submit">Cache Temizle</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Cache dizinleri
$basePath = __DIR__ . '/../';
$cacheDirs = [
    'bootstrap/cache' => ['config.php', 'routes-v7.php', 'routes.php', 'services.php'],
    'storage/framework/cache' => [],
    'storage/framework/views' => [],
    'storage/framework/sessions' => [],
];

$results = [];
$errors = [];

// Cache dosyalarını temizle
foreach ($cacheDirs as $dir => $files) {
    $fullPath = $basePath . $dir;
    
    if (!is_dir($fullPath)) {
        $errors[] = "❌ Dizin bulunamadı: $dir";
        continue;
    }
    
    // Belirli dosyaları sil
    if (!empty($files)) {
        foreach ($files as $file) {
            $filePath = $fullPath . '/' . $file;
            if (file_exists($filePath)) {
                if (@unlink($filePath)) {
                    $results[] = "✅ Silindi: $dir/$file";
                } else {
                    $errors[] = "❌ Silinemedi: $dir/$file";
                }
            }
        }
    }
    
    // Dizindeki tüm dosyaları sil (cache data)
    if ($dir === 'storage/framework/cache' || $dir === 'storage/framework/views') {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                @unlink($file->getRealPath());
            } elseif ($file->isDir()) {
                @rmdir($file->getRealPath());
            }
        }
        $results[] = "✅ Temizlendi: $dir";
    }
}

// Sonuçları göster
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cache Temizleme Sonuçları</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { background: #d1fae5; border-left: 4px solid #10b981; padding: 12px; margin: 10px 0; border-radius: 4px; }
        .error { background: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin: 10px 0; border-radius: 4px; }
        .warning { background: #fef3c7; border: 2px solid #f59e0b; padding: 20px; border-radius: 4px; margin: 20px 0; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Cache Temizleme Sonuçları</h1>
        
        <?php if (!empty($results)): ?>
            <h2>✅ Başarılı İşlemler:</h2>
            <?php foreach ($results as $result): ?>
                <div class="success"><?php echo htmlspecialchars($result); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h2>❌ Hatalar:</h2>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="warning">
            <strong>⚠️ ÖNEMLİ:</strong><br>
            Cache temizlendi. <strong>Bu dosyayı hemen silin!</strong><br><br>
            Şimdi Laravel route cache'i yeniden oluşturmak için:<br>
            <code>php artisan route:cache</code> komutunu çalıştırmanız gerekebilir.
        </div>
        
        <p style="margin-top: 30px; color: #666;">
            <small>Zaman: <?php echo date('Y-m-d H:i:s'); ?></small>
        </p>
    </div>
</body>
</html>
