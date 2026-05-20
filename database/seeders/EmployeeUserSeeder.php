<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class EmployeeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // İlk firmayı al
        $company = Company::first();
        
        if (!$company) {
            $this->command->error('Önce CompanySeeder çalıştırılmalı!');
            return;
        }

        // Personel kullanıcıları oluştur (User model 'password' => 'hashed' cast kullanıyor, plain ver)
        $employees = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet.yilmaz@harmansah.com',
                'password' => 'password',
                'role' => 'employee',
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mehmet Demir',
                'email' => 'mehmet.demir@harmansah.com',
                'password' => 'password',
                'role' => 'employee',
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ayşe Kaya',
                'email' => 'ayse.kaya@harmansah.com',
                'password' => 'password',
                'role' => 'employee',
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($employees as $employee) {
            User::create($employee);
        }

        $this->command->info('Personel kullanıcıları oluşturuldu!');
        $this->command->info('Test hesapları:');
        $this->command->info('Email: ahmet.yilmaz@harmansah.com, Şifre: password');
        $this->command->info('Email: mehmet.demir@harmansah.com, Şifre: password');
        $this->command->info('Email: ayse.kaya@harmansah.com, Şifre: password');
    }
}
