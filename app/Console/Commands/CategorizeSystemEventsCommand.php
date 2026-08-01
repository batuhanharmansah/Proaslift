<?php

namespace App\Console\Commands;

use App\Models\SystemEvent;
use App\Support\SystemEventCategorizer;
use Illuminate\Console\Command;

/**
 * Bu özellik eklenmeden önce kaydedilmiş (category alanı boş) sistem olaylarını
 * geriye dönük olarak kategorilere ayırır. Yeni olaylar zaten SystemEvent::log()
 * içinde otomatik kategorize ediliyor — bu komut sadece eski kayıtlar içindir.
 */
class CategorizeSystemEventsCommand extends Command
{
    protected $signature = 'system-events:categorize';

    protected $description = 'category alanı boş olan eski sistem olaylarını geriye dönük olarak kategorize eder';

    public function handle(): int
    {
        $updated = 0;

        SystemEvent::whereNull('category')
            ->orWhere('category', '')
            ->chunkById(200, function ($events) use (&$updated) {
                foreach ($events as $event) {
                    $event->category = SystemEventCategorizer::categorize(
                        $event->type,
                        $event->source,
                        $event->message,
                        $event->stack_trace
                    );
                    $event->save();
                    $updated++;
                }
            });

        $this->info("{$updated} adet kayıt kategorize edildi.");

        return self::SUCCESS;
    }
}
