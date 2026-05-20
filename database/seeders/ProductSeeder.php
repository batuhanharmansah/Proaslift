<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Yedek Parçalar
            [
                'name' => 'Asansör Kapı Sensörü',
                'code' => 'ASN-KS-001',
                'description' => 'Otomatik kapı açma/kapama sensörü, tüm asansör markalarına uyumlu',
                'category' => 'yedek_parca',
                'unit' => 'adet',
                'cost_price' => 450.00,
                'sale_price' => 650.00,
                'stock_quantity' => 15,
                'min_stock_level' => 5,
                'supplier' => 'Otis Yedek Parça A.Ş.',
                'is_active' => true,
                'notes' => 'En çok kullanılan parça'
            ],
            [
                'name' => 'Motor Karbon Fırçası',
                'code' => 'ASN-MF-002',
                'description' => '5HP asansör motoru için karbon fırça seti',
                'category' => 'yedek_parca',
                'unit' => 'takım',
                'cost_price' => 180.00,
                'sale_price' => 280.00,
                'stock_quantity' => 8,
                'min_stock_level' => 3,
                'supplier' => 'Schindler Türkiye',
                'is_active' => true,
                'notes' => '6 ayda bir değiştirilmeli'
            ],
            [
                'name' => 'Güvenlik Röle Kartı',
                'code' => 'ASN-GR-003',
                'description' => 'Acil durum güvenlik röle kontrol kartı',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 850.00,
                'sale_price' => 1200.00,
                'stock_quantity' => 3,
                'min_stock_level' => 2,
                'supplier' => 'Kone Elevator',
                'is_active' => true,
                'notes' => 'Kritik güvenlik parçası'
            ],
            [
                'name' => 'Asansör Halatı 8mm',
                'code' => 'ASN-HLT-004',
                'description' => 'Çelik halat 8mm çap, 100 metre rulo',
                'category' => 'mekanik',
                'unit' => 'metre',
                'cost_price' => 25.00,
                'sale_price' => 40.00,
                'stock_quantity' => 500,
                'min_stock_level' => 100,
                'supplier' => 'Çelik Halat San. Tic.',
                'is_active' => true,
                'notes' => 'Toplu alım yapılır'
            ],
            // Araç Gereçler
            [
                'name' => 'Dijital Multimetre',
                'code' => 'ARC-DMM-005',
                'description' => 'Profesyonel dijital multimetre, otomatik ölçüm',
                'category' => 'arac_gerec',
                'unit' => 'adet',
                'cost_price' => 320.00,
                'sale_price' => 480.00,
                'stock_quantity' => 6,
                'min_stock_level' => 2,
                'supplier' => 'Teknik Alet Mağazası',
                'is_active' => true,
                'notes' => 'Her teknisyende bulunmalı'
            ],
            [
                'name' => 'Asansör Test Cihazı',
                'code' => 'ARC-ATC-006',
                'description' => 'Asansör güvenlik sistemleri test cihazı',
                'category' => 'arac_gerec',
                'unit' => 'adet',
                'cost_price' => 2500.00,
                'sale_price' => 3500.00,
                'stock_quantity' => 2,
                'min_stock_level' => 1,
                'supplier' => 'Elevator Tech Solutions',
                'is_active' => true,
                'notes' => 'TSE kontrolü için gerekli'
            ],
            // Kimyasallar
            [
                'name' => 'Makine Yağı SAE 30',
                'code' => 'KIM-MY-007',
                'description' => 'Asansör motor yağı, sentetik',
                'category' => 'kimyasal',
                'unit' => 'litre',
                'cost_price' => 45.00,
                'sale_price' => 70.00,
                'stock_quantity' => 25,
                'min_stock_level' => 10,
                'supplier' => 'Shell Türkiye',
                'is_active' => true,
                'notes' => 'Aylık bakımlarda kullanılır'
            ],
            [
                'name' => 'Ray Gres Yağı',
                'code' => 'KIM-RG-008',
                'description' => 'Asansör rayları için özel gres yağı',
                'category' => 'kimyasal',
                'unit' => 'kg',
                'cost_price' => 35.00,
                'sale_price' => 55.00,
                'stock_quantity' => 12,
                'min_stock_level' => 5,
                'supplier' => 'Mobil Oil Türkiye',
                'is_active' => true,
                'notes' => 'Yüksek sıcaklığa dayanıklı'
            ],
            // Elektronik
            [
                'name' => 'LED Kabin Aydınlatması',
                'code' => 'ELK-LED-009',
                'description' => '12V LED panel, asansör kabini için',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 120.00,
                'sale_price' => 180.00,
                'stock_quantity' => 20,
                'min_stock_level' => 8,
                'supplier' => 'Philips Aydınlatma',
                'is_active' => true,
                'notes' => 'Enerji tasarruflu'
            ],
            [
                'name' => 'Acil Durum Telefonu',
                'code' => 'ELK-ADT-010',
                'description' => 'Asansör kabini acil durum telefonu',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 380.00,
                'sale_price' => 550.00,
                'stock_quantity' => 4,
                'min_stock_level' => 2,
                'supplier' => 'Emergency Systems Ltd.',
                'is_active' => true,
                'notes' => 'Yasal zorunluluk'
            ],
            // Stok Uyarısı Olanlar
            [
                'name' => 'Fren Balata Takımı',
                'code' => 'ASN-FB-011',
                'description' => 'Asansör motor fren balata seti',
                'category' => 'yedek_parca',
                'unit' => 'takım',
                'cost_price' => 200.00,
                'sale_price' => 320.00,
                'stock_quantity' => 2, // Az stok
                'min_stock_level' => 5,
                'supplier' => 'Brake Systems Co.',
                'is_active' => true,
                'notes' => 'Stok azaldı, sipariş verilmeli'
            ],
            [
                'name' => 'Emniyet Kilidi',
                'code' => 'ASN-EK-012',
                'description' => 'Asansör kapısı emniyet kilidi',
                'category' => 'mekanik',
                'unit' => 'adet',
                'cost_price' => 150.00,
                'sale_price' => 250.00,
                'stock_quantity' => 0, // Stokta yok
                'min_stock_level' => 3,
                'supplier' => 'Safety Lock Inc.',
                'is_active' => true,
                'notes' => 'Stok tükendi, acil sipariş gerekli'
            ]
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
