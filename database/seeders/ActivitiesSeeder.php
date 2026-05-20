<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            ['user_id' => 1, 'action' => 'Giriş yaptı', 'meta' => json_encode(['ip' => '192.168.1.1'])],
            ['user_id' => 2, 'action' => 'Profil güncelledi', 'meta' => json_encode(['fields' => ['name', 'email']])],
            ['user_id' => 3, 'action' => 'Rapor oluşturdu', 'meta' => json_encode(['report_type' => 'monthly'])],
            ['user_id' => 1, 'action' => 'Ayarları değiştirdi', 'meta' => json_encode(['setting' => 'notifications'])],
            ['user_id' => 4, 'action' => 'Dosya yükledi', 'meta' => json_encode(['file_name' => 'document.pdf'])],
            ['user_id' => 2, 'action' => 'Çıkış yaptı', 'meta' => json_encode(['session_duration' => '2h 15m'])],
            ['user_id' => 5, 'action' => 'Yeni kayıt oluşturdu', 'meta' => json_encode(['record_type' => 'customer'])],
            ['user_id' => 1, 'action' => 'Veri dışa aktardı', 'meta' => json_encode(['format' => 'excel'])],
        ];

        foreach ($activities as $activity) {
            \DB::table('activities')->insert([
                'user_id' => $activity['user_id'],
                'action' => $activity['action'],
                'meta' => $activity['meta'],
                'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                'updated_at' => now(),
            ]);
        }
    }
}
