<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\FinancialTransaction;
use App\Models\Company;
use Carbon\Carbon;

class BuildingPaymentsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Tüm binaları al
        $buildings = Building::with('company')->get();

        foreach ($buildings as $building) {
            // Her bina için 2025 yılının bazı aylarına örnek ödemeler ekle
            $paidMonths = [1, 2, 3, 5, 6, 8, 9, 11]; // Bazı aylar ödendi, bazıları ödenmedi

            foreach ($paidMonths as $month) {
                // Bazen tam ödeme, bazen eksik ödeme
                $paymentAmount = $building->monthly_fee;
                if (rand(1, 10) > 8) { // %20 ihtimalle eksik ödeme
                    $paymentAmount = $building->monthly_fee * 0.8; // %80'i ödendi
                }

                // Rastgele bir gün seç (ayın 1-28 arası)
                $paymentDate = Carbon::create(2025, $month, rand(1, 28));

                // Company'nin ilk kullanıcısını bul
                $companyUser = $building->company->users()->first();

                FinancialTransaction::create([
                    'company_id' => $building->company_id,
                    'transaction_type' => 'gelir',
                    'category' => 'bina_geliri',
                    'description' => $building->name . ' - ' . $paymentDate->locale('tr')->translatedFormat('F Y') . ' bakım ücreti',
                    'amount' => $paymentAmount,
                    'vat_rate' => 20.00,
                    'vat_amount' => $paymentAmount * 0.20,
                    'total_amount' => $paymentAmount + ($paymentAmount * 0.20),
                    'transaction_date' => $paymentDate,
                    'transaction_time' => rand(9, 17) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
                    'building_id' => $building->id,
                    'payment_method' => ['banka_havalesi', 'nakit', 'pos'][rand(0, 2)],
                    'invoice_number' => 'INV-' . $building->id . '-' . $paymentDate->format('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT),
                    'status' => 'tamamlandi',
                    'created_by' => $companyUser ? $companyUser->id : 1
                ]);
            }
        }

        $this->command->info('Bina ödemeleri başarıyla eklendi!');
    }
}
