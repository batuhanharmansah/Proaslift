<?php

namespace App\Console\Commands;

use App\Models\AccountingEntry;
use App\Models\AccountType;
use App\Models\Building;
use App\Models\BuildingApprovalToken;
use App\Models\BuildingFinancialRecord;
use App\Models\IssueReport;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceSchedule;
use App\Models\Receivable;
use App\Models\RecurringPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * load-tests/*.js script'lerinin oluşturduğu "LOADTEST_" önekli sahte binaları,
 * hesapları ve bunlara bağlı tüm türetilmiş kayıtları temizler:
 *   - Building (LOADTEST_ önekli) + BuildingFinancialRecord + RecurringPayment
 *     + Receivable + BuildingApprovalToken
 *   - Bu binalara bağlı IssueReport + MaintenanceSchedule + MaintenanceReport
 *   - Bu binalara bağlı AccountingEntry (finansal işlem kayıtları)
 *   - AccountType (LOADTEST_ önekli test hesapları)
 *
 * Sadece 'LOADTEST_' önekiyle başlayan kayıtları hedef alır — gerçek veriye dokunmaz.
 */
class CleanupLoadTestDataCommand extends Command
{
    protected $signature = 'load-test:cleanup {--dry-run : Sadece neyin silineceğini göster, silme}';

    protected $description = 'load-tests/ script\'lerinin oluşturduğu LOADTEST_ önekli sahte veriyi (binalar, hesaplar, finansal kayıtlar) siler';

    public function handle(): int
    {
        $buildingIds = Building::where('name', 'like', 'LOADTEST_%')->pluck('id', 'id');
        $accountIds = AccountType::where('name', 'like', 'LOADTEST_%')->pluck('id', 'id');

        if ($buildingIds->isEmpty() && $accountIds->isEmpty()) {
            $this->info('Temizlenecek yük testi verisi bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("{$buildingIds->count()} LOADTEST_ bina, {$accountIds->count()} LOADTEST_ hesap bulundu.");

        $maintenanceScheduleIds = MaintenanceSchedule::whereIn('building_id', $buildingIds)->pluck('id');

        if ($this->option('dry-run')) {
            $this->warn(sprintf(
                'Dry-run: %d receivable, %d recurring payment, %d financial record, %d issue report, %d maintenance schedule, %d maintenance report, %d accounting entry, %d approval token silinecekti. Gerçek silme yapılmadı.',
                Receivable::whereIn('building_id', $buildingIds)->count(),
                RecurringPayment::whereIn('building_id', $buildingIds)->count(),
                BuildingFinancialRecord::whereIn('building_id', $buildingIds)->count(),
                IssueReport::whereIn('building_id', $buildingIds)->count(),
                $maintenanceScheduleIds->count(),
                MaintenanceReport::whereIn('maintenance_schedule_id', $maintenanceScheduleIds)->count(),
                AccountingEntry::whereIn('building_id', $buildingIds)->orWhereIn('account_type_id', $accountIds)->count(),
                BuildingApprovalToken::whereIn('building_id', $buildingIds)->count(),
            ));
            return self::SUCCESS;
        }

        DB::transaction(function () use ($buildingIds, $accountIds, $maintenanceScheduleIds) {
            MaintenanceReport::whereIn('maintenance_schedule_id', $maintenanceScheduleIds)->delete();
            MaintenanceSchedule::whereIn('id', $maintenanceScheduleIds)->delete();
            IssueReport::whereIn('building_id', $buildingIds)->delete();
            BuildingApprovalToken::whereIn('building_id', $buildingIds)->delete();
            AccountingEntry::whereIn('building_id', $buildingIds)
                ->orWhereIn('account_type_id', $accountIds)
                ->delete();
            Receivable::whereIn('building_id', $buildingIds)->delete();
            RecurringPayment::whereIn('building_id', $buildingIds)->delete();
            BuildingFinancialRecord::whereIn('building_id', $buildingIds)->delete();
            Building::whereIn('id', $buildingIds)->delete();
            AccountType::whereIn('id', $accountIds)->delete();
        });

        $this->info('Yük testi verisi temizlendi.');

        return self::SUCCESS;
    }
}
