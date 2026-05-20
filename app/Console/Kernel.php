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

        // Haftalık özet raporu (Pazartesi 08:00)
        $schedule->command('elevator:weekly-report')
                 ->weeklyOn(1, '08:00')
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
