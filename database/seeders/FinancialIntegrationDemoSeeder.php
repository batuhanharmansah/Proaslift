<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Receivable;
use App\Models\FinancialTransaction;
use App\Models\RecurringPayment;
use App\Models\BuildingFinancialRecord;
use Carbon\Carbon;

class FinancialIntegrationDemoSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // İlk binayı al
        $building = Building::first();

        if (!$building) {
            $this->command->error('Önce bina oluşturun!');
            return;
        }

        $this->command->info("Demo için bina: {$building->name}");

        // 1. Bu bina için alacak kaydı oluştur (Ocak ayı)
        $januaryReceivable = Receivable::create([
            'company_id' => $building->company_id,
            'building_id' => $building->id,
            'title' => $building->name . ' - Ocak 2025 Bakım Ücreti',
            'description' => $building->name . ' binası Ocak 2025 dönemi bakım hizmeti ücreti',
            'total_amount' => $building->monthly_fee,
            'received_amount' => 0,
            'remaining_amount' => $building->monthly_fee,
            'due_date' => Carbon::create(2025, 1, 5),
            'status' => 'beklemede',
            'installment_amount' => $building->monthly_fee,
            'priority' => 'orta',
            'notes' => 'Demo için oluşturulan alacak kaydı',
            'created_by' => $building->company->users()->first()->id ?? 1
        ]);

        $this->command->info("✓ Ocak ayı alacak kaydı oluşturuldu: ₺{$building->monthly_fee}");

        // 2. Şimdi bu alacağı ödenmiş olarak işaretle (finansal işlem oluştur)
        $transaction = FinancialTransaction::create([
            'company_id' => $building->company_id,
            'transaction_type' => 'gelir',
            'category' => 'bina_geliri',
            'description' => $building->name . ' - Ocak 2025 Bakım Ücreti Ödemesi',
            'amount' => $building->monthly_fee,
            'vat_rate' => 20.00,
            'vat_amount' => $building->monthly_fee * 0.20,
            'total_amount' => $building->monthly_fee + ($building->monthly_fee * 0.20),
            'transaction_date' => Carbon::create(2025, 1, 5),
            'transaction_time' => '14:30:00',
            'building_id' => $building->id,
            'payment_method' => 'banka_havalesi',
            'invoice_number' => 'INV-DEMO-' . $building->id . '-2025-01',
            'status' => 'tamamlandi',
            'created_by' => $building->company->users()->first()->id ?? 1
        ]);

        // Alacak kaydını ödenmiş olarak güncelle
        $januaryReceivable->update([
            'received_amount' => $building->monthly_fee,
            'remaining_amount' => 0,
            'status' => 'tamamlandi'
        ]);

        $this->command->info("✓ Ocak ayı ödemesi alındı ve finansal işlem oluşturuldu");

        // 3. Şubat ayı için bekleyen alacak oluştur
        $februaryReceivable = Receivable::create([
            'company_id' => $building->company_id,
            'building_id' => $building->id,
            'title' => $building->name . ' - Şubat 2025 Bakım Ücreti',
            'description' => $building->name . ' binası Şubat 2025 dönemi bakım hizmeti ücreti',
            'total_amount' => $building->monthly_fee,
            'received_amount' => 0,
            'remaining_amount' => $building->monthly_fee,
            'due_date' => Carbon::create(2025, 2, 5),
            'status' => 'beklemede',
            'installment_amount' => $building->monthly_fee,
            'priority' => 'orta',
            'notes' => 'Demo için oluşturulan bekleyen alacak',
            'created_by' => $building->company->users()->first()->id ?? 1
        ]);

        $this->command->info("✓ Şubat ayı alacak kaydı oluşturuldu (beklemede): ₺{$building->monthly_fee}");

        // 4. Building Financial Record oluştur/güncelle
        $financialRecord = BuildingFinancialRecord::updateOrCreate(
            [
                'company_id' => $building->company_id,
                'building_id' => $building->id,
            ],
            [
                'contract_amount' => $building->monthly_fee * 12,
                'monthly_amount' => $building->monthly_fee,
                'total_received' => $building->monthly_fee,
                'total_remaining' => $building->monthly_fee * 11,
                'contract_start_date' => Carbon::create(2025, 1, 1),
                'contract_end_date' => Carbon::create(2025, 12, 31),
                'payment_frequency' => 'aylik',
                'status' => 'aktif'
            ]
        );

        $this->command->info("✓ Bina finansal kaydı oluşturuldu/güncellendi");

        // 5. Düzenli ödeme kaydı oluştur
        RecurringPayment::updateOrCreate(
            [
                'company_id' => $building->company_id,
                'building_id' => $building->id,
                'category' => 'bina_geliri'
            ],
            [
                'title' => $building->name . ' - Aylık Bakım Ücreti',
                'description' => $building->name . ' binası için aylık bakım hizmeti ücreti',
                'amount' => $building->monthly_fee,
                'type' => 'gelir',
                'frequency' => 'aylik',
                'start_date' => Carbon::create(2025, 1, 1),
                'end_date' => Carbon::create(2025, 12, 31),
                'day_of_month' => 5,
                'is_active' => true,
                'notes' => 'Demo için oluşturulan düzenli ödeme',
                'created_by' => $building->company->users()->first()->id ?? 1
            ]
        );

        $this->command->info("✓ Düzenli ödeme kaydı oluşturuldu");

        $this->command->info("");
        $this->command->info("🎉 Demo tamamlandı! Şimdi test edebilirsiniz:");
        $this->command->info("1. Bina detay sayfasında Ocak ayı 'ÖDENDİ' olarak görünecek");
        $this->command->info("2. Şubat ayı 'ÖDENMEDİ' olarak görünecek");
        $this->command->info("3. Finansal yönetim sayfasında Şubat alacağını görebilirsiniz");
        $this->command->info("4. Şubat alacağını ödeme aldığınızda bina sayfasında güncellenecek");
    }
}
