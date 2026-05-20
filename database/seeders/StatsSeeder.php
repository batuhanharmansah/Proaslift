<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stats = [
            ['key' => 'total_users', 'value' => 1247],
            ['key' => 'daily_visits', 'value' => 342],
            ['key' => 'conversion_rate', 'value' => 73],
            ['key' => 'active_sessions', 'value' => 89],
        ];

        foreach ($stats as $stat) {
            \DB::table('stats')->insert([
                'key' => $stat['key'],
                'value' => $stat['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
