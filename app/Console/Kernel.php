<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Asansör etiketleri günlük izleme (her gün 00:05'te)
        $schedule->command('elevator:monitor')
                 ->dailyAt('00:05')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Düzenli ödemeler (bina aylık ücretleri) için otomatik alacak oluşturma (her gün 00:15'te)
        $schedule->command('recurring-payments:process')
                 ->dailyAt('00:15')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Sistem sağlığı izleme tablosunun 30 günden eski kayıtlarını temizle (her gün 00:30'da)
        $schedule->command('system-events:prune')
                 ->dailyAt('00:30')
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
