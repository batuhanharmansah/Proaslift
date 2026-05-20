<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceSchedule;
use App\Services\NotificationService;
use Carbon\Carbon;

/**
 * 🔔 Send Maintenance Reminders Command
 * Bakım 24 saat öncesi hatırlatma gönderimi
 */
class SendMaintenanceReminders extends Command
{
    protected $signature = 'notifications:maintenance-reminders';
    protected $description = 'Send maintenance reminders 24 hours before scheduled date';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('🔔 Checking for maintenance reminders...');

        $tomorrow = Carbon::tomorrow('Europe/Istanbul');
        
        // Yarın planlanan bakımları bul
        $maintenances = MaintenanceSchedule::where('scheduled_date', $tomorrow->format('Y-m-d'))
            ->whereIn('status', ['planli', 'atandi'])
            ->with(['building', 'assignedEmployee'])
            ->get();

        $count = 0;
        foreach ($maintenances as $maintenance) {
            $this->notificationService->notifyMaintenanceReminder($maintenance);
            $count++;
        }

        $this->info("✅ {$count} maintenance reminder(s) sent");
        return 0;
    }
}
