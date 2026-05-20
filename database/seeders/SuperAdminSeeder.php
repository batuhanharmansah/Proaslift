<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin rolünü al
        $superAdminRole = Role::where('slug', 'super_admin')->first();

        if (!$superAdminRole) {
            $this->command->error('Super Admin rolü bulunamadı! Önce RoleSeeder çalıştırın.');
            return;
        }

        // Super Admin kullanıcısı oluştur (User model 'password' => 'hashed' cast kullanıyor, plain ver)
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@harmansah.com'],
            [
                'name' => 'Harmanşah Yazılım',
                'email' => 'superadmin@harmansah.com',
                'password' => 'SuperAdmin123!',
                'company_id' => null, // Super admin'in firma bağlantısı yok
                'email_verified_at' => now(),
            ]
        );

        // Super Admin rolünü ata
        $superAdmin->assignRole('super_admin');

        // Demo firma oluştur (mevcut verileri bu firmaya bağla)
        $demoCompany = Company::updateOrCreate(
            ['slug' => 'demo-asansor-firmasi'],
            [
                'name' => 'Demo Asansör Firması',
                'slug' => 'demo-asansor-firmasi',
                'email' => 'demo@asansorfirmasi.com',
                'phone' => '0212 555 0123',
                'address' => 'Demo Mahallesi, Demo Sokak No:1, İstanbul',
                'subscription_plan' => 'professional',
                'subscription_status' => 'active',
                'subscription_start' => now(),
                'subscription_end' => now()->addYear(),
                'monthly_fee' => 599.00,
                'max_buildings' => 15,
                'max_employees' => 10,
                'is_active' => true,
                'notes' => 'Demo amaçlı oluşturulan firma',
            ]
        );

        // Mevcut admin kullanıcısını demo firmasına bağla
        $companyAdmin = User::where('email', 'admin@harmansah.com')->first();
        if ($companyAdmin) {
            $companyAdmin->update(['company_id' => $demoCompany->id]);
            $companyAdmin->assignRole('company_admin', $demoCompany->id);
        }

        $this->command->info('Super Admin oluşturuldu:');
        $this->command->info('Email: superadmin@harmansah.com');
        $this->command->info('Şifre: SuperAdmin123!');
        $this->command->info('');
        $this->command->info('Demo firma oluşturuldu: ' . $demoCompany->name);
    }
}
