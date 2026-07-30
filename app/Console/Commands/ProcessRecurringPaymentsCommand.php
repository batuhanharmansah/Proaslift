<?php

namespace App\Console\Commands;

use App\Services\BuildingFinancialService;
use Illuminate\Console\Command;

class ProcessRecurringPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'recurring-payments:process';

    /**
     * The console command description.
     */
    protected $description = 'Vadesi gelen düzenli ödemeler (bina aylık ücretleri) için otomatik alacak (Receivable) kaydı oluşturur';

    /**
     * Execute the console command.
     */
    public function handle(BuildingFinancialService $service)
    {
        $created = $service->processDueRecurringPayments();

        $this->info("{$created} adet yeni alacak kaydı oluşturuldu.");

        return self::SUCCESS;
    }
}
