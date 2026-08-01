<?php

namespace App\Console\Commands;

use App\Models\SystemEvent;
use Illuminate\Console\Command;

class PruneSystemEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system-events:prune';

    /**
     * The console command description.
     */
    protected $description = 'Sistem sağlığı izleme tablosundaki (system_events) 30 günden eski kayıtları siler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = SystemEvent::where('created_at', '<', now()->subDays(30))->delete();

        $this->info("{$deleted} adet eski sistem olayı silindi.");

        return self::SUCCESS;
    }
}
