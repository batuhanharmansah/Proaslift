<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = \App\Models\Company::first()->id ?? 1;
        
        $buildings = [
            [
                'company_id' => $companyId,
                'name' => 'Ataşehir Rezidans',
                'address' => 'Ataşehir Bulvarı No:123 Ataşehir/İstanbul',
                'district' => 'Ataşehir',
                'city' => 'İstanbul',
                'latitude' => 40.9833,
                'longitude' => 29.1167,
                'floor_count' => 15,
                'elevator_count' => 2,
                'elevator_type' => 'yolcu',
                'elevator_brand' => 'Otis',
                'elevator_model' => 'Gen2',
                'elevator_code' => 'ATR-001',
                'installation_year' => 2018,
                'contract_type' => 'bakim',
                'monthly_fee' => 3500.00,
                'contract_start_date' => '2023-12-31',
                'contract_end_date' => '2025-12-30',
                'status' => 'aktif',
                'operational_status' => 'aktif',
                'capacity_kg' => 1000,
                'capacity_person' => 13,
                'manufacturer' => 'Otis Elevator Company',
                'model' => 'Gen2-MRL',
                'serial_number' => 'OT-2018-ATR-001',
                'responsible_person' => 'Mehmet Yıldız',
                'responsible_phone' => '0532 111 22 33',
                'responsible_email' => 'yonetici@atasehirrezidans.com',
                'elevator_notes' => 'İki asansör, her ikisi de aktif. Aylık rutin bakım yapılıyor.',
                'notes' => '24 saat bakım hizmeti. Acil durumlarda 7/24 ulaşılabilir.'
            ],
            [
                'company_id' => $companyId,
                'name' => 'Kadıköy Plaza',
                'address' => 'Bağdat Caddesi No:456 Kadıköy/İstanbul',
                'district' => 'Kadıköy',
                'city' => 'İstanbul',
                'latitude' => 40.9900,
                'longitude' => 29.0200,
                'floor_count' => 12,
                'elevator_count' => 3,
                'elevator_type' => 'karma',
                'elevator_brand' => 'Schindler',
                'elevator_model' => '3300',
                'elevator_code' => 'KDP-001',
                'installation_year' => 2020,
                'contract_type' => 'bakim',
                'monthly_fee' => 4200.00,
                'contract_start_date' => '2024-05-31',
                'contract_end_date' => '2026-05-30',
                'status' => 'aktif',
                'operational_status' => 'aktif',
                'capacity_kg' => 1600,
                'capacity_person' => 21,
                'manufacturer' => 'Schindler Group',
                'model' => 'Schindler 3300',
                'serial_number' => 'SCH-2020-KDP-001',
                'responsible_person' => 'Ayşe Demir',
                'responsible_phone' => '0532 222 33 44',
                'responsible_email' => 'yonetici@kadikoyplaza.com',
                'elevator_notes' => 'Üç asansör, ticari kullanım. Yoğun trafik var.',
                'notes' => 'Ticari kullanım. Hafta içi yoğun, hafta sonu daha az kullanım.'
            ],
            [
                'company_id' => $companyId,
                'name' => 'Üsküdar Konutları',
                'address' => 'Çamlıca Mahallesi No:789 Üsküdar/İstanbul',
                'district' => 'Üsküdar',
                'city' => 'İstanbul',
                'latitude' => 41.0200,
                'longitude' => 29.0100,
                'floor_count' => 8,
                'elevator_count' => 1,
                'elevator_type' => 'yolcu',
                'elevator_brand' => 'Kone',
                'elevator_model' => 'MonoSpace',
                'elevator_code' => 'USK-001',
                'installation_year' => 2019,
                'contract_type' => 'bakim',
                'monthly_fee' => 2800.00,
                'contract_start_date' => '2024-03-01',
                'contract_end_date' => '2025-02-28',
                'status' => 'aktif',
                'operational_status' => 'aktif',
                'capacity_kg' => 630,
                'capacity_person' => 8,
                'manufacturer' => 'Kone Corporation',
                'model' => 'Kone MonoSpace',
                'serial_number' => 'KON-2019-USK-001',
                'responsible_person' => 'Ali Kaya',
                'responsible_phone' => '0532 333 44 55',
                'responsible_email' => 'yonetici@uskudarkonutlari.com',
                'elevator_notes' => 'Tek asansör, konut kullanımı. Düzenli bakım yapılıyor.',
                'notes' => 'Konut sitesi. Sakinler memnun, şikayet yok.'
            ],
            [
                'company_id' => $companyId,
                'name' => 'BOR BOTANİKA',
                'address' => 'Cığızoğlu Osman Efendi, 51700 Bor/Niğde',
                'district' => 'BOR',
                'city' => 'NİĞDE',
                'latitude' => 37.8200,
                'longitude' => 34.5500,
                'floor_count' => 10,
                'elevator_count' => 2,
                'elevator_type' => 'yolcu',
                'elevator_brand' => 'ASD',
                'elevator_model' => null,
                'elevator_code' => 'BOR-001',
                'installation_year' => 2021,
                'contract_type' => 'bakim',
                'monthly_fee' => 5000.00,
                'contract_start_date' => '2025-12-31',
                'contract_end_date' => '2026-12-31',
                'status' => 'aktif',
                'operational_status' => 'aktif',
                'capacity_kg' => 1000,
                'capacity_person' => 13,
                'manufacturer' => 'ASD Asansör',
                'model' => null,
                'serial_number' => 'ASD-2021-BOR-001',
                'responsible_person' => null,
                'responsible_phone' => null,
                'responsible_email' => null,
                'elevator_notes' => null,
                'notes' => null
            ],
            [
                'company_id' => $companyId,
                'name' => 'DENEME',
                'address' => 'DENEME',
                'district' => 'DENEME',
                'city' => 'İstanbul',
                'latitude' => null,
                'longitude' => null,
                'floor_count' => 23,
                'elevator_count' => 3,
                'elevator_type' => 'yolcu',
                'elevator_brand' => 'ASD',
                'elevator_model' => null,
                'elevator_code' => null,
                'installation_year' => null,
                'contract_type' => 'bakim',
                'monthly_fee' => 3500.00,
                'contract_start_date' => '2025-08-31',
                'contract_end_date' => '2026-10-06',
                'status' => 'aktif',
                'operational_status' => 'aktif',
                'capacity_kg' => null,
                'capacity_person' => null,
                'manufacturer' => null,
                'model' => null,
                'serial_number' => null,
                'responsible_person' => null,
                'responsible_phone' => null,
                'responsible_email' => null,
                'elevator_notes' => null,
                'notes' => null
            ],
        ];

        foreach ($buildings as $building) {
            $createdBuilding = \App\Models\Building::create($building);

            // Her bina için iletişim kişileri ekle
            $contacts = [];
            
            // Ana iletişim kişisi
            $contacts[] = [
                'building_id' => $createdBuilding->id,
                'company_id' => $companyId,
                'name' => $building['responsible_person'] ?? 'Site Yöneticisi',
                'title' => 'yonetici',
                'phone' => $building['responsible_phone'] ?? '0532 ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'email' => $building['responsible_email'] ?? 'yonetici@' . strtolower(str_replace(' ', '', $building['name'])) . '.com',
                'is_primary' => true,
                'is_active' => true,
                'notes' => 'Ana iletişim kişisi'
            ];
            
            // İkinci iletişim kişisi (bazı binalar için)
            if (in_array($createdBuilding->id, [1, 2])) {
                $contacts[] = [
                    'building_id' => $createdBuilding->id,
                    'company_id' => $companyId,
                    'name' => 'Yardımcı Yönetici',
                    'title' => 'yardimci_yonetici',
                    'phone' => '0533 ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'email' => 'yardimci@' . strtolower(str_replace(' ', '', $building['name'])) . '.com',
                    'is_primary' => false,
                    'is_active' => true,
                    'notes' => 'Yardımcı iletişim kişisi'
                ];
            }
            
            foreach ($contacts as $contact) {
                \App\Models\BuildingContact::create($contact);
            }
        }
    }
}
