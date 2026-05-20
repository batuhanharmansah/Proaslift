<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AccountType;
use App\Models\Company;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Kasa hesabı
            AccountType::create([
                'company_id' => $company->id,
                'name' => 'Ana Kasa',
                'type' => 'kasa',
                'initial_balance' => 5000.00,
                'current_balance' => 5000.00,
                'notes' => 'Ana kasa hesabı',
                'is_active' => true,
            ]);

            // Banka hesabı
            AccountType::create([
                'company_id' => $company->id,
                'name' => 'İş Bankası Hesabı',
                'type' => 'banka',
                'bank_name' => 'İş Bankası',
                'branch_name' => 'Kadıköy Şubesi',
                'account_number' => '1234567890',
                'initial_balance' => 15000.00,
                'current_balance' => 15000.00,
                'notes' => 'Ana banka hesabı',
                'is_active' => true,
            ]);

            // POS cihazı
            AccountType::create([
                'company_id' => $company->id,
                'name' => 'POS Cihazı',
                'type' => 'pos',
                'initial_balance' => 0.00,
                'current_balance' => 0.00,
                'notes' => 'Kredi kartı ödemeleri',
                'is_active' => true,
            ]);

            // Nakit hesabı
            AccountType::create([
                'company_id' => $company->id,
                'name' => 'Nakit Hesabı',
                'type' => 'nakit',
                'initial_balance' => 2000.00,
                'current_balance' => 2000.00,
                'notes' => 'Nakit ödemeler',
                'is_active' => true,
            ]);
        }
    }
}
