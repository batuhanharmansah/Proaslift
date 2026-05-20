<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = [
            [
                'location' => 'dashboard_top',
                'title' => 'Harmanşah Yazılım',
                'body' => 'Akıllı yazılım çözümleri ile işinizi dijitalleştirin',
                'image_url' => null,
            ],
            [
                'location' => 'login_bottom',
                'title' => 'Harmanşah Yazılım',
                'body' => 'Güvenilir teknoloji ortağınız',
                'image_url' => null,
            ],
        ];

        foreach ($ads as $ad) {
            \DB::table('ads')->insert([
                'location' => $ad['location'],
                'title' => $ad['title'],
                'body' => $ad['body'],
                'image_url' => $ad['image_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
