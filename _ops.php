<?php

/**
 * Geçici operasyon/teşhis scripti.
 * Sunucuda SSH olmadığı için cache temizleme, storage klasörü oluşturma ve
 * gerçek hata mesajını okuma amacıyla kullanılır.
 *
 * KULLANIM (token zorunlu):
 *   /_ops.php?key=TOKEN              -> teşhis (PHP sürümü, cache, storage, .env durumu)
 *   /_ops.php?key=TOKEN&do=fix       -> bootstrap/cache temizler, storage klasörlerini oluşturur, izin verir
 *   /_ops.php?key=TOKEN&do=log       -> storage/logs içindeki son hata kayıtlarını gösterir
 *
 * GÜVENLİK: İşi bitince bu dosya repodan silinip tekrar deploy edilecektir.
 */

declare(strict_types=1);

const OPS_TOKEN = '344d515c01e954f7450a23839ae92dd1';

header('Content-Type: text/plain; charset=utf-8');

$provided = isset($_GET['key']) ? (string) $_GET['key'] : '';
if (!hash_equals(OPS_TOKEN, $provided)) {
    http_response_code(404);
    echo "Not found.\n";
    exit;
}

$base = __DIR__;
$cacheDir = $base . '/bootstrap/cache';
$storageDir = $base . '/storage';
$action = isset($_GET['do']) ? (string) $_GET['do'] : 'diag';

function line(string $label, string $value = ''): void
{
    echo str_pad($label, 28) . $value . "\n";
}

echo "=== PROASLIFT OPS ===\n";
line('Zaman', date('c'));
line('PHP sürümü', PHP_VERSION);
line('Base dizin', $base);
line('Yazılabilir (base)?', is_writable($base) ? 'evet' : 'HAYIR');
echo "\n";

if ($action === 'fix') {
    echo "--- FIX: bootstrap/cache temizleniyor ---\n";
    $compiled = ['config.php', 'routes-v7.php', 'routes.php', 'events.php', 'packages.php', 'services.php'];
    foreach ($compiled as $file) {
        $path = $cacheDir . '/' . $file;
        if (is_file($path)) {
            echo (@unlink($path) ? '[silindi]  ' : '[HATA]     ') . $path . "\n";
        } else {
            echo "[yok]      $path\n";
        }
    }

    echo "\n--- FIX: storage klasörleri oluşturuluyor ---\n";
    $dirs = [
        $storageDir . '/logs',
        $storageDir . '/framework/cache/data',
        $storageDir . '/framework/sessions',
        $storageDir . '/framework/views',
        $storageDir . '/app/public',
        $cacheDir,
    ];
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            echo "[var]      $dir\n";
        } else {
            echo (@mkdir($dir, 0775, true) ? '[oluştu]   ' : '[HATA]     ') . $dir . "\n";
        }
        @chmod($dir, 0775);
    }

    echo "\n--- FIX tamam. Şimdi anasayfayı yenileyin. Hata sürerse ?do=log ile loglara bakın. ---\n";
    exit;
}

if ($action === 'log') {
    echo "--- storage/logs içeriği ---\n";
    $logDir = $storageDir . '/logs';
    if (!is_dir($logDir)) {
        echo "logs klasörü yok: $logDir\n";
        exit;
    }
    $files = glob($logDir . '/*.log') ?: [];
    if (!$files) {
        echo "Log dosyası bulunamadı.\n";
        exit;
    }
    foreach ($files as $file) {
        echo "\n##### $file (son 120 satır) #####\n";
        $lines = @file($file, FILE_IGNORE_NEW_LINES) ?: [];
        $tail = array_slice($lines, -120);
        echo implode("\n", $tail) . "\n";
    }
    exit;
}

echo "--- bootstrap/cache durumu ---\n";
if (is_dir($cacheDir)) {
    line('cache dizini yazılabilir?', is_writable($cacheDir) ? 'evet' : 'HAYIR');
    foreach (glob($cacheDir . '/*.php') ?: [] as $file) {
        $name = basename($file);
        $hasStalePath = false;
        $content = @file_get_contents($file);
        if ($content !== false && (strpos($content, 'vhosts') !== false || strpos($content, 'httpdocs') !== false)) {
            $hasStalePath = true;
        }
        echo "  - $name" . ($hasStalePath ? '   <<< BAYAT YOL İÇERİYOR (vhosts/httpdocs)' : '') . "\n";
    }
} else {
    echo "cache dizini yok.\n";
}

echo "\n--- storage durumu ---\n";
foreach (['logs', 'framework/cache', 'framework/sessions', 'framework/views'] as $sub) {
    $path = $storageDir . '/' . $sub;
    line("  $sub", (is_dir($path) ? 'var' : 'YOK') . (is_dir($path) ? (is_writable($path) ? ' / yazılabilir' : ' / YAZILAMAZ') : ''));
}

echo "\n--- .env durumu ---\n";
$envPath = $base . '/.env';
if (is_file($envPath)) {
    $env = @file_get_contents($envPath) ?: '';
    line('.env var mı?', 'evet');
    line('APP_KEY dolu mu?', preg_match('/^APP_KEY=base64:.+/m', $env) ? 'evet' : 'HAYIR (boş/eksik)');
    line('APP_DEBUG', preg_match('/^APP_DEBUG=(\S+)/m', $env, $m) ? $m[1] : '?');
    line('APP_ENV', preg_match('/^APP_ENV=(\S+)/m', $env, $m2) ? $m2[1] : '?');
    line('APP_URL', preg_match('/^APP_URL=(\S+)/m', $env, $m3) ? $m3[1] : '?');
    line('DB_DATABASE', preg_match('/^DB_DATABASE=(\S+)/m', $env, $m4) ? $m4[1] : '?');
} else {
    line('.env var mı?', 'HAYIR  <<< .env eksik! 500 sebebi bu olabilir.');
}

echo "\nİpucu: önce ?do=fix çalıştırın, sonra anasayfayı yenileyin. Hâlâ 500 ise ?do=log ile gerçek hatayı görün.\n";
