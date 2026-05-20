<?php
/**
 * OPcache Reset
 * https://proaslift.com/reset-opcache.php
 *
 * GÜVENLİK: Bu dosyayı kullandıktan sonra SİLİN!
 */

header('Content-Type: application/json');

$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'opcache' => [],
    'session' => []
];

// OPcache sıfırla
if (function_exists('opcache_reset')) {
    opcache_reset();
    $result['opcache']['status'] = '✅ OPcache temizlendi';
    $result['opcache']['enabled'] = true;
} else {
    $result['opcache']['status'] = '⚠️ OPcache kullanılamıyor';
    $result['opcache']['enabled'] = false;
}

// Realpath cache temizle
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    $result['realpath_cache'] = '✅ Realpath cache temizlendi';
}

// APCu varsa temizle
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    $result['apcu'] = '✅ APCu cache temizlendi';
}

echo json_encode($result, JSON_PRETTY_PRINT);
echo "\n\n✅ Tüm cache'ler temizlendi!\n";
echo "⚠️ Bu dosyayı şimdi SİLİN: public/reset-opcache.php\n";

