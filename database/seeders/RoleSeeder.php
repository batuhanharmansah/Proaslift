<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Sistem yöneticisi - Tüm firmaları yönetebilir',
                'permissions' => [
                    'manage_companies',
                    'manage_payments',
                    'manage_subscriptions',
                    'view_all_data',
                    'system_settings'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Firma Yöneticisi',
                'slug' => 'company_admin',
                'description' => 'Firma sahibi - Kendi firmasını yönetebilir',
                'permissions' => [
                    'manage_employees',
                    'manage_buildings',
                    'manage_maintenance',
                    'manage_products',
                    'manage_accounting',
                    'view_reports'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Personel',
                'slug' => 'employee',
                'description' => 'Firma personeli - Atandığı işleri yönetebilir',
                'permissions' => [
                    'view_assigned_tasks',
                    'create_reports',
                    'update_maintenance_status',
                    'view_products'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}
