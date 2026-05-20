<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Temel Paket',
                'slug' => 'basic',
                'price' => 299.00,
                'max_buildings' => 5,
                'max_employees' => 3,
                'features' => [
                    'basic_dashboard',
                    'building_management',
                    'basic_maintenance',
                    'basic_reports',
                    'email_support'
                ],
                'description' => 'Küçük asansör firmaları için ideal başlangıç paketi',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Profesyonel Paket',
                'slug' => 'professional',
                'price' => 599.00,
                'max_buildings' => 15,
                'max_employees' => 10,
                'features' => [
                    'advanced_dashboard',
                    'building_management',
                    'full_maintenance',
                    'advanced_reports',
                    'product_catalog',
                    'accounting_module',
                    'sms_notifications',
                    'phone_support'
                ],
                'description' => 'Orta ölçekli firmalar için gelişmiş özellikler',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Kurumsal Paket',
                'slug' => 'enterprise',
                'price' => 999.00,
                'max_buildings' => -1, // Sınırsız
                'max_employees' => -1, // Sınırsız
                'features' => [
                    'full_dashboard',
                    'unlimited_buildings',
                    'unlimited_employees',
                    'full_maintenance',
                    'custom_reports',
                    'advanced_analytics',
                    'api_access',
                    'custom_integrations',
                    'priority_support',
                    'dedicated_account_manager'
                ],
                'description' => 'Büyük kurumsal firmalar için tam özellikli paket',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
