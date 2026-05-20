<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialTransaction;
use App\Models\AccountType;
use App\Models\Building;
use App\Models\Company;

class FinancialTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $accounts = AccountType::where('company_id', $company->id)->get();
            $buildings = Building::where('company_id', $company->id)->get();

            if ($accounts->isEmpty() || $buildings->isEmpty()) {
                continue;
            }

            $kasa = $accounts->where('type', 'kasa')->first();
            $banka = $accounts->where('type', 'banka')->first();
            $pos = $accounts->where('type', 'pos')->first();
            $nakit = $accounts->where('type', 'nakit')->first();

            // Bina gelirleri
            foreach ($buildings->take(3) as $building) {
                FinancialTransaction::create([
                    'company_id' => $company->id,
                    'transaction_type' => 'gelir',
                    'category' => 'bina_geliri',
                    'description' => $building->name . ' - Ağustos 2025 bakım ücreti',
                    'amount' => rand(3000, 5000),
                    'vat_rate' => 20.00,
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'to_account_id' => $kasa->id,
                    'building_id' => $building->id,
                    'payment_method' => 'banka_havalesi',
                    'invoice_number' => 'FAT-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'status' => 'tamamlandi',
                    'created_by' => $company->admin_user_id ?? 1,
                ]);
            }

            // Dükkan giderleri
            FinancialTransaction::create([
                'company_id' => $company->id,
                'transaction_type' => 'gider',
                'category' => 'dukkan_gideri',
                'description' => 'Kira ödemesi - Ağustos 2025',
                'amount' => 8000.00,
                'vat_rate' => 0.00,
                'transaction_date' => now()->subDays(5),
                'from_account_id' => $banka->id,
                'payment_method' => 'banka_havalesi',
                'status' => 'tamamlandi',
                'created_by' => $company->admin_user_id ?? 1,
            ]);

            FinancialTransaction::create([
                'company_id' => $company->id,
                'transaction_type' => 'gider',
                'category' => 'dukkan_gideri',
                'description' => 'Elektrik faturası - Ağustos 2025',
                'amount' => 1200.00,
                'vat_rate' => 20.00,
                'transaction_date' => now()->subDays(3),
                'from_account_id' => $kasa->id,
                'payment_method' => 'nakit',
                'status' => 'tamamlandi',
                'created_by' => $company->admin_user_id ?? 1,
            ]);

            // POS gelirleri
            FinancialTransaction::create([
                'company_id' => $company->id,
                'transaction_type' => 'gelir',
                'category' => 'pos_geliri',
                'description' => 'POS cihazı geliri - Günlük toplam',
                'amount' => 4000.00,
                'vat_rate' => 20.00,
                'transaction_date' => now()->subDays(1),
                'to_account_id' => $pos->id,
                'payment_method' => 'pos',
                'status' => 'tamamlandi',
                'created_by' => $company->admin_user_id ?? 1,
            ]);

            // Nakit gelirleri
            FinancialTransaction::create([
                'company_id' => $company->id,
                'transaction_type' => 'gelir',
                'category' => 'nakit_geliri',
                'description' => 'Nakit ödeme geliri - Günlük toplam',
                'amount' => 3000.00,
                'vat_rate' => 20.00,
                'transaction_date' => now()->subDays(1),
                'to_account_id' => $nakit->id,
                'payment_method' => 'nakit',
                'status' => 'tamamlandi',
                'created_by' => $company->admin_user_id ?? 1,
            ]);

            // Transfer işlemleri
            FinancialTransaction::create([
                'company_id' => $company->id,
                'transaction_type' => 'transfer',
                'category' => 'banka_transfer',
                'description' => 'Kasadan banka hesabına transfer',
                'amount' => 2000.00,
                'vat_rate' => 0.00,
                'transaction_date' => now(),
                'from_account_id' => $kasa->id,
                'to_account_id' => $banka->id,
                'payment_method' => 'transfer',
                'status' => 'tamamlandi',
                'created_by' => $company->admin_user_id ?? 1,
            ]);
        }
    }
}
