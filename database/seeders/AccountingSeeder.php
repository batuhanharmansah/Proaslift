<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            // Gelirler
            [
                'type' => 'gelir',
                'category' => 'Bakım Sözleşmesi',
                'description' => 'Ataşehir Rezidans - Ağustos 2025 bakım ücreti',
                'amount' => 3500.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-01',
                'invoice_number' => 'FAT-2025-001',
                'building_id' => 1,
                'payment_method' => 'banka_havalesi',
                'status' => 'tahsil_edildi',
                'notes' => 'Aylık bakım sözleşmesi ödemesi'
            ],
            [
                'type' => 'gelir',
                'category' => 'Bakım Sözleşmesi',
                'description' => 'Kadıköy Plaza - Ağustos 2025 bakım ücreti',
                'amount' => 4200.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-01',
                'invoice_number' => 'FAT-2025-002',
                'building_id' => 2,
                'payment_method' => 'banka_havalesi',
                'status' => 'tahsil_edildi',
                'notes' => 'Aylık bakım sözleşmesi ödemesi'
            ],
            [
                'type' => 'gelir',
                'category' => 'Arıza Onarım',
                'description' => 'Kadıköy Plaza - Acil müdahale kapı sensörü onarımı',
                'amount' => 800.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-12',
                'invoice_number' => 'FAT-2025-003',
                'building_id' => 2,
                'payment_method' => 'nakit',
                'status' => 'tahsil_edildi',
                'notes' => 'Acil arıza onarım ücreti'
            ],
            [
                'type' => 'gelir',
                'category' => 'Bakım Sözleşmesi',
                'description' => 'Üsküdar Konutları - Ağustos 2025 bakım ücreti',
                'amount' => 2800.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-05',
                'invoice_number' => 'FAT-2025-004',
                'building_id' => 3,
                'payment_method' => 'banka_havalesi',
                'status' => 'beklemede',
                'notes' => 'Ödeme bekleniyor'
            ],

            // Maaşlar
            [
                'type' => 'maas',
                'category' => 'Personel Maaşı',
                'description' => 'Ahmet Yılmaz - Ağustos 2025 maaşı',
                'amount' => 18000.00,
                'vat_rate' => 0.00,
                'transaction_date' => '2025-08-01',
                'employee_id' => 1,
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Usta maaşı - SGK primleri dahil'
            ],
            [
                'type' => 'maas',
                'category' => 'Personel Maaşı',
                'description' => 'Mehmet Demir - Ağustos 2025 maaşı',
                'amount' => 15000.00,
                'vat_rate' => 0.00,
                'transaction_date' => '2025-08-01',
                'employee_id' => 2,
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Teknisyen maaşı - SGK primleri dahil'
            ],
            [
                'type' => 'maas',
                'category' => 'Personel Maaşı',
                'description' => 'Ayşe Kaya - Ağustos 2025 maaşı',
                'amount' => 25000.00,
                'vat_rate' => 0.00,
                'transaction_date' => '2025-08-01',
                'employee_id' => 3,
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Mühendis maaşı - SGK primleri dahil'
            ],

            // Giderler
            [
                'type' => 'gider',
                'category' => 'Yedek Parça',
                'description' => 'Asansör kapı sensörü ve motor fırçası alımı',
                'amount' => 1250.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-05',
                'invoice_number' => 'AL-2025-001',
                'payment_method' => 'kredi_karti',
                'status' => 'odendi',
                'notes' => 'Otis Yedek Parça A.Ş. - acil tedarik'
            ],
            [
                'type' => 'gider',
                'category' => 'Yakıt',
                'description' => 'Servis araçları yakıt gideri',
                'amount' => 2500.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-10',
                'payment_method' => 'kredi_karti',
                'status' => 'odendi',
                'notes' => 'Ağustos ayı yakıt giderleri'
            ],
            [
                'type' => 'gider',
                'category' => 'Ofis Giderleri',
                'description' => 'Ofis kira bedeli - Ağustos 2025',
                'amount' => 8000.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-01',
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Merkez ofis kira bedeli'
            ],
            [
                'type' => 'gider',
                'category' => 'Araç Gereç',
                'description' => 'Dijital multimetre ve test cihazı alımı',
                'amount' => 3200.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-08',
                'invoice_number' => 'AL-2025-002',
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Teknisyen ekipmanları'
            ],

            // Vergi ve Sigorta
            [
                'type' => 'vergi',
                'category' => 'KDV Beyanı',
                'description' => 'Temmuz 2025 KDV beyan ve ödeme',
                'amount' => 1850.00,
                'vat_rate' => 0.00,
                'transaction_date' => '2025-08-15',
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Aylık KDV beyanı'
            ],
            [
                'type' => 'sigorta',
                'category' => 'SGK Primleri',
                'description' => 'Personel SGK primleri - Ağustos 2025',
                'amount' => 12500.00,
                'vat_rate' => 0.00,
                'transaction_date' => '2025-08-20',
                'payment_method' => 'banka_havalesi',
                'status' => 'beklemede',
                'notes' => 'Tüm personel SGK primleri toplamı'
            ],
            [
                'type' => 'gider',
                'category' => 'Telefon-İnternet',
                'description' => 'Ofis telefon ve internet faturaları',
                'amount' => 450.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-08-12',
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Türk Telekom ve Superonline'
            ],

            // Geçmiş ay kayıtları (dashboard için)
            [
                'type' => 'gelir',
                'category' => 'Bakım Sözleşmesi',
                'description' => 'Temmuz 2025 bakım gelirleri toplamı',
                'amount' => 9800.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-07-01',
                'payment_method' => 'banka_havalesi',
                'status' => 'tahsil_edildi',
                'notes' => 'Temmuz ayı toplam bakım gelirleri'
            ],
            [
                'type' => 'gider',
                'category' => 'Genel Giderler',
                'description' => 'Temmuz 2025 genel giderler toplamı',
                'amount' => 6200.00,
                'vat_rate' => 20.00,
                'transaction_date' => '2025-07-15',
                'payment_method' => 'banka_havalesi',
                'status' => 'odendi',
                'notes' => 'Temmuz ayı toplam giderler'
            ]
        ];

        foreach ($entries as $entry) {
            \App\Models\AccountingEntry::create($entry);
        }
    }
}
