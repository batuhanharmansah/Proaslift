<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\ElevatorLabel;
use Carbon\Carbon;

class ElevatorLabelSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // İlk önce building'lere elevator code ekleyelim
        $buildings = Building::all();

        foreach ($buildings as $index => $building) {
            $building->update([
                'elevator_code' => 'ASN-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'capacity_kg' => rand(400, 1000),
                'capacity_person' => rand(6, 13),
                'manufacturer' => ['Otis', 'Schindler', 'KONE', 'ThyssenKrupp'][rand(0, 3)],
                'model' => 'Model-' . rand(100, 999),
                'serial_number' => 'SN' . rand(100000, 999999),
                'responsible_person' => 'Yönetici ' . ($index + 1),
                'responsible_phone' => '0532' . rand(1000000, 9999999),
                'responsible_email' => 'yonetici' . ($index + 1) . '@example.com',
                'operational_status' => 'aktif',
            ]);
        }

        // Örnek etiket verileri
        $labelData = [
            // Yeşil etiketler (uygun)
            ['color' => 'yesil', 'days_ago' => 30, 'description' => 'Periyodik kontrol tamamlandı, herhangi bir sorun tespit edilmedi.'],
            ['color' => 'yesil', 'days_ago' => 60, 'description' => 'Tüm güvenlik sistemleri çalışır durumda.'],
            ['color' => 'yesil', 'days_ago' => 90, 'description' => 'Rutin bakım ve kontroller başarıyla tamamlandı.'],

            // Mavi etiketler (hafif kusur)
            ['color' => 'mavi', 'days_ago' => 45, 'description' => 'Kabin aydınlatmasında hafif sorun, acil değil.'],
            ['color' => 'mavi', 'days_ago' => 120, 'description' => 'Kapı ses sistemi ayarlanması gerekiyor.'],

            // Sarı etiketler (kusurlu - 120 gün takip)
            ['color' => 'sari', 'days_ago' => 20, 'description' => 'Fren balata değişimi gerekli, 120 gün içinde yapılmalı.'],
            ['color' => 'sari', 'days_ago' => 80, 'description' => 'Halat kontrolü ve gerginlik ayarı yapılmalı.'],
            ['color' => 'sari', 'days_ago' => 100, 'description' => 'Kapı güvenlik sensörleri kalibre edilmeli.'],

            // Kırmızı etiketler (güvensiz - 60 gün takip)
            ['color' => 'kirmizi', 'days_ago' => 10, 'description' => 'Acil durum freni arızalı, derhal onarılmalı.'],
            ['color' => 'kirmizi', 'days_ago' => 40, 'description' => 'Güvenlik devreleri çalışmıyor, kullanım riskli.'],
            ['color' => 'kirmizi', 'days_ago' => 70, 'description' => 'Kritik güvenlik sorunu - SÜRE DOLMUŞ!'],
        ];

        foreach ($labelData as $index => $data) {
            if ($index < $buildings->count()) {
                $building = $buildings[$index];
                $controlDate = Carbon::now()->subDays($data['days_ago']);

                ElevatorLabel::create([
                    'building_id' => $building->id,
                    'label_color' => $data['color'],
                    'control_date' => $controlDate,
                    'description' => $data['description'],
                    'inspector_name' => 'Kontrol Müh. ' . ($index + 1),
                    'inspector_company' => 'Kontrol A.Ş.',
                    'inspector_license' => 'KNT-' . rand(1000, 9999),
                    'source' => 'periyodik_kontrol',
                    'status' => 'aktif',
                ]);
            }
        }

        $this->command->info('Elevator label test verileri oluşturuldu!');
    }
}
