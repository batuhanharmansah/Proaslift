<?php

namespace App\Console\Commands;

use App\Models\SystemEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Sistem sağlığı izleme sayfası devreye girmeden ÖNCE oluşmuş hataları geriye dönük olarak
 * sisteme aktarır. İki kaynaktan okur:
 *   1) storage/logs/laravel*.log dosyaları (mevcut Laravel log kayıtları)
 *   2) failed_jobs tablosu (Laravel'in varsayılan başarısız kuyruk işi tablosu)
 *
 * Tek seferlik, elle çalıştırılan bir komuttur (cron'a eklenmez). Birden fazla kez
 * çalıştırılsa bile aynı kayıtları tekrar eklemez (idempotent).
 */
class ImportSystemEventsHistoryCommand extends Command
{
    protected $signature = 'system-events:import-history
        {--logs-only : Sadece log dosyalarından içe aktar}
        {--jobs-only : Sadece failed_jobs tablosundan içe aktar}';

    protected $description = 'Sistem sağlığı sayfası devreye girmeden önce oluşmuş eski hataları (log dosyaları + failed_jobs) geriye dönük olarak system_events tablosuna aktarır';

    public function handle(): int
    {
        $onlyLogs = $this->option('logs-only');
        $onlyJobs = $this->option('jobs-only');

        if (!$onlyJobs) {
            $this->importFromLogFiles();
        }

        if (!$onlyLogs) {
            $this->importFromFailedJobs();
        }

        return self::SUCCESS;
    }

    private function importFromLogFiles(): void
    {
        $files = glob(storage_path('logs/laravel*.log'));

        if (empty($files)) {
            $this->warn('storage/logs altında laravel*.log dosyası bulunamadı.');
            return;
        }

        // Aynı (tarih + mesaj) çiftini tekrar eklememek için mevcut kayıtları belleğe al.
        $existing = SystemEvent::where('type', 'exception_import')
            ->get(['created_at', 'message'])
            ->map(fn ($e) => $e->created_at->format('Y-m-d H:i:s') . '|' . $e->message)
            ->flip();

        $imported = 0;

        foreach ($files as $file) {
            $imported += $this->importSingleLogFile($file, $existing);
        }

        $this->info("Log dosyalarından {$imported} adet yeni kayıt aktarıldı.");
    }

    private function importSingleLogFile(string $file, \Illuminate\Support\Collection $existing): int
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error("Dosya açılamadı: {$file}");
            return 0;
        }

        $imported = 0;
        $currentEntry = null;

        while (($line = fgets($handle)) !== false) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.*)$/', $line, $m)) {
                if ($currentEntry) {
                    $imported += $this->flushLogEntry($currentEntry, $existing) ? 1 : 0;
                }

                $currentEntry = [
                    'timestamp' => $m[1],
                    'level' => strtoupper($m[2]),
                    'message' => trim($m[3]),
                    'stack' => '',
                ];
            } elseif ($currentEntry) {
                $currentEntry['stack'] .= $line;
            }
        }

        if ($currentEntry) {
            $imported += $this->flushLogEntry($currentEntry, $existing) ? 1 : 0;
        }

        fclose($handle);

        return $imported;
    }

    private function flushLogEntry(array $entry, \Illuminate\Support\Collection $existing): bool
    {
        // Sadece hata seviyesindeki (info/debug değil) kayıtları içe aktar — gürültüyü azaltır.
        $noisyLevels = ['DEBUG', 'INFO', 'NOTICE'];
        if (in_array($entry['level'], $noisyLevels, true)) {
            return false;
        }

        $key = $entry['timestamp'] . '|' . $entry['message'];
        if ($existing->has($key)) {
            return false;
        }

        $severity = in_array($entry['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)
            ? 'critical'
            : 'warning';

        SystemEvent::create([
            'source' => 'web',
            'type' => 'exception_import',
            'severity' => $severity,
            'message' => mb_substr($entry['message'], 0, 2000),
            'stack_trace' => trim($entry['stack']) ?: null,
            'context' => ['imported_from' => 'laravel_log', 'log_level' => $entry['level']],
            'created_at' => $entry['timestamp'],
        ]);

        $existing->put($key, true);

        return true;
    }

    private function importFromFailedJobs(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('failed_jobs')) {
            $this->warn('failed_jobs tablosu bulunamadı, atlanıyor.');
            return;
        }

        $existingIds = SystemEvent::where('type', 'queue_failed_import')
            ->get('context')
            ->pluck('context.failed_job_id')
            ->filter()
            ->all();

        $rows = DB::table('failed_jobs')
            ->when(!empty($existingIds), fn ($q) => $q->whereNotIn('id', $existingIds))
            ->get();

        foreach ($rows as $row) {
            $payload = json_decode($row->payload, true);
            $jobName = $payload['displayName'] ?? ($payload['job'] ?? 'unknown_job');

            SystemEvent::create([
                'source' => 'web',
                'type' => 'queue_failed_import',
                'severity' => 'critical',
                'message' => "Kuyruk işi başarısız: {$jobName}",
                'stack_trace' => mb_substr($row->exception, 0, 8000),
                'context' => [
                    'imported_from' => 'failed_jobs',
                    'failed_job_id' => $row->id,
                    'connection' => $row->connection,
                    'queue' => $row->queue,
                ],
                'created_at' => $row->failed_at,
            ]);
        }

        $this->info(count($rows) . ' adet failed_jobs kaydı aktarıldı.');
    }
}
