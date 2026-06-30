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
 *   /_ops.php?key=TOKEN&do=writeenv&dbpass=VERITABANI_SIFRESI[&force=1]
 *                                    -> sunucuda .env oluşturur (APP_KEY otomatik üretilir).
 *                                       Şifre sadece bu isteğin parametresinde gelir, git'e yazılmaz.
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

if ($action === 'writeenv') {
    echo "--- WRITEENV: .env oluşturuluyor ---\n";
    $envPath = $base . '/.env';

    if (is_file($envPath) && !isset($_GET['force'])) {
        echo "[atlandı] .env zaten var. Üzerine yazmak için &force=1 ekleyin.\n";
        exit;
    }

    $dbPass = isset($_GET['dbpass']) ? (string) $_GET['dbpass'] : '';
    if ($dbPass === '') {
        echo "[HATA] dbpass parametresi zorunlu. Örn: ?key=...&do=writeenv&dbpass=SIFRE\n";
        exit;
    }

    $dbHost = isset($_GET['dbhost']) ? (string) $_GET['dbhost'] : 'localhost';
    $dbPort = isset($_GET['dbport']) ? (string) $_GET['dbport'] : '3306';
    $dbName = isset($_GET['dbname']) ? (string) $_GET['dbname'] : 'u2759912_proaslift';
    $dbUser = isset($_GET['dbuser']) ? (string) $_GET['dbuser'] : 'u2759912_Batuhan';

    $appKey = 'base64:' . base64_encode(random_bytes(32));

    $envContent = <<<ENV
APP_NAME=Proaslift
APP_ENV=production
APP_KEY=$appKey
APP_DEBUG=false
APP_URL=https://proaslift.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=$dbHost
DB_PORT=$dbPort
DB_DATABASE=$dbName
DB_USERNAME=$dbUser
DB_PASSWORD="$dbPass"

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@proaslift.com"
MAIL_FROM_NAME="\${APP_NAME}"

GOOGLE_GEOCODING_ENABLED=false

ENV;

    $ok = @file_put_contents($envPath, $envContent);
    if ($ok === false) {
        echo "[HATA] .env yazılamadı: $envPath\n";
        exit;
    }
    @chmod($envPath, 0640);
    echo "[yazıldı] $envPath (" . $ok . " bayt)\n";
    echo "APP_KEY otomatik üretildi.\n";
    echo "DB_HOST=$dbHost DB_PORT=$dbPort DB_DATABASE=$dbName DB_USERNAME=$dbUser DB_PASSWORD=*** (gizli)\n";
    echo "\nNot: GOOGLE/MAIL gibi ek ayarları gerekiyorsa sonradan .env'e ekleyin.\n";
    echo "Şimdi ?do=fix çalıştırıp anasayfayı yenileyin.\n";
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
