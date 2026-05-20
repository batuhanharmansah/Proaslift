<?php
/**
 * Emergency Log Viewer
 * https://proaslift.com/check-logs.php?lines=100
 *
 * GÜVENLİK: Bu dosyayı kullandıktan sonra SİLİN!
 */

$basePath = dirname(__DIR__);
$logFile = $basePath . '/storage/logs/laravel.log';
$lines = isset($_GET['lines']) ? (int)$_GET['lines'] : 50;

if (!file_exists($logFile)) {
    die("Log file not found: $logFile");
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== Laravel Log (Son $lines satır) ===\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

// Son N satırı oku
$file = new SplFileObject($logFile, 'r');
$file->seek(PHP_INT_MAX);
$totalLines = $file->key();

$startLine = max(0, $totalLines - $lines);

$file->seek($startLine);
while (!$file->eof()) {
    echo $file->current();
    $file->next();
}

echo "\n========================================\n";
echo "✅ Log okundu! Bu dosyayı şimdi SİLİN!\n";

