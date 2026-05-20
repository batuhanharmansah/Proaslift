<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Receivable;
use App\Models\Payable;
use App\Services\NotificationService;
use Carbon\Carbon;

/**
 * 🔔 Send Payment Reminders Command
 * Ödeme vadesi yaklaşan bildirimler
 */
class SendPaymentReminders extends Command
{
    protected $signature = 'notifications:payment-reminders';
    protected $description = 'Send payment reminders for upcoming due dates';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('🔔 Checking for payment reminders...');

        // 3 gün sonra vadesi dolacak alacaklar
        $threeDaysLater = Carbon::now('Europe/Istanbul')->addDays(3);
        
        $receivables = Receivable::where('due_date', $threeDaysLater->format('Y-m-d'))
            ->where('status', '!=', 'tamamlandi')
            ->with('building')
            ->get();

        $count = 0;
        foreach ($receivables as $receivable) {
            $this->notificationService->notifyPaymentDueSoon($receivable);
            $count++;
        }

        // Gecikmiş alacaklar
        $overdueReceivables = Receivable::where('due_date', '<', Carbon::now('Europe/Istanbul'))
            ->where('status', '!=', 'tamamlandi')
            ->whereRaw('DATE(due_date) = DATE(?)', [Carbon::yesterday('Europe/Istanbul')])
            ->with('building')
            ->get();

        foreach ($overdueReceivables as $receivable) {
            $this->notificationService->notifyPaymentOverdue($receivable);
            $count++;
        }

        // 3 gün sonra vadesi dolacak borçlar
        $payables = Payable::where('due_date', $threeDaysLater->format('Y-m-d'))
            ->where('status', '!=', 'tamamlandi')
            ->get();

        foreach ($payables as $payable) {
            $this->notificationService->notifyPayableDueSoon($payable);
            $count++;
        }

        $this->info("✅ {$count} payment reminder(s) sent");
        return 0;
    }
}
