<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin kullanıcı oluştur (User model 'password' => 'hashed' cast kullanıyor, plain ver)
        \App\Models\User::factory()->create([
            'name' => 'Yönetici',
            'email' => 'admin@harmansah.com',
            'password' => 'Admin123!',
        ]);

        // 25 sahte kullanıcı oluştur
        \App\Models\User::factory(25)->create();

        // Seeder'ları çalıştır
        $this->call([
            EmployeeSeeder::class,
            BuildingSeeder::class,
            MaintenanceSeeder::class,
            ProductSeeder::class,
            AccountingSeeder::class,
            StatsSeeder::class,
            ActivitiesSeeder::class,
            AdsSeeder::class,
            EmployeeUserSeeder::class,
            AccountTypeSeeder::class,
            FinancialTransactionSeeder::class,
        ]);
    }
}
