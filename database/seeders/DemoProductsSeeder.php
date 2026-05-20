<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Company;

class DemoProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Demo Asansör Firması'nı bul
        $company = Company::where('name', 'LIKE', '%Demo Asansör%')->first();

        if (!$company) {
            $this->command->error('Demo Asansör Firması bulunamadı!');
            return;
        }

        $this->command->info("Firma bulundu: {$company->name} (ID: {$company->id})");

        $products = [
            [
                'name' => 'Asansör Motoru 1000kg',
                'code' => 'ASN-MT-1000',
                'description' => '1000kg taşıma kapasiteli asansör motoru, enerji tasarruflu model',
                'category' => 'mekanik',
                'unit' => 'adet',
                'cost_price' => 35000.00,
                'sale_price' => 45000.00,
                'stock_quantity' => 5,
                'min_stock_level' => 2,
                'supplier' => 'Otis Türkiye',
                'is_active' => true,
            ],
            [
                'name' => 'Asansör Motoru 630kg',
                'code' => 'ASN-MT-630',
                'description' => '630kg taşıma kapasiteli asansör motoru',
                'category' => 'mekanik',
                'unit' => 'adet',
                'cost_price' => 27000.00,
                'sale_price' => 35000.00,
                'stock_quantity' => 3,
                'min_stock_level' => 1,
                'supplier' => 'Otis Türkiye',
                'is_active' => true,
            ],
            [
                'name' => 'Kabin Kapı Operatörü',
                'code' => 'ASN-KPO-01',
                'description' => 'Otomatik kabin kapı açma-kapama sistemi',
                'category' => 'mekanik',
                'unit' => 'adet',
                'cost_price' => 6500.00,
                'sale_price' => 8500.00,
                'stock_quantity' => 12,
                'min_stock_level' => 5,
                'supplier' => 'Fermator',
                'is_active' => true,
            ],
            [
                'name' => 'Kat Kapı Kilidi',
                'code' => 'ASN-KTK-01',
                'description' => 'Elektro-mekanik kat kapı güvenlik kilidi',
                'category' => 'mekanik',
                'unit' => 'adet',
                'cost_price' => 320.00,
                'sale_price' => 450.00,
                'stock_quantity' => 50,
                'min_stock_level' => 20,
                'supplier' => 'GMV',
                'is_active' => true,
            ],
            [
                'name' => 'Asansör Kabin Paneli',
                'code' => 'ASN-KBN-01',
                'description' => 'Paslanmaz çelik kabin kumanda paneli - LED göstergeli',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 1800.00,
                'sale_price' => 2500.00,
                'stock_quantity' => 8,
                'min_stock_level' => 3,
                'supplier' => 'Schneider Electric',
                'is_active' => true,
            ],
            [
                'name' => 'Limit Switch (Son Kat Kesicisi)',
                'code' => 'ASN-LMT-01',
                'description' => 'Son kat güvenlik kesicisi, çift kontak',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 250.00,
                'sale_price' => 350.00,
                'stock_quantity' => 25,
                'min_stock_level' => 10,
                'supplier' => 'Omron',
                'is_active' => true,
            ],
            [
                'name' => 'Encoder (Konum Sensörü)',
                'code' => 'ASN-ENC-01',
                'description' => 'Kabin konum tespit sensörü, 1024 pulse',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 1300.00,
                'sale_price' => 1800.00,
                'stock_quantity' => 6,
                'min_stock_level' => 3,
                'supplier' => 'Heidenhain',
                'is_active' => true,
            ],
            [
                'name' => 'Asansör Halatı 10mm',
                'code' => 'ASN-HLT-10',
                'description' => '10mm çelik asansör halatı, 6x19 örme',
                'category' => 'yedek_parca',
                'unit' => 'metre',
                'cost_price' => 65.00,
                'sale_price' => 85.00,
                'stock_quantity' => 500,
                'min_stock_level' => 200,
                'supplier' => 'Draka',
                'is_active' => true,
            ],
            [
                'name' => 'Asansör Halatı 8mm',
                'code' => 'ASN-HLT-08',
                'description' => '8mm çelik asansör halatı, 6x19 örme',
                'category' => 'yedek_parca',
                'unit' => 'metre',
                'cost_price' => 48.00,
                'sale_price' => 65.00,
                'stock_quantity' => 400,
                'min_stock_level' => 150,
                'supplier' => 'Draka',
                'is_active' => true,
            ],
            [
                'name' => 'Ray Yağı (Hidrolik)',
                'code' => 'ASN-YAG-01',
                'description' => 'Ray kaydırıcı ve hidrolik yağı, 5 litre',
                'category' => 'kimyasal',
                'unit' => 'litre',
                'cost_price' => 320.00,
                'sale_price' => 450.00,
                'stock_quantity' => 30,
                'min_stock_level' => 10,
                'supplier' => 'Shell',
                'is_active' => true,
            ],
            [
                'name' => 'Ray Temizleme Spreyi',
                'code' => 'ASN-TMZ-01',
                'description' => 'Ray temizleme ve koruma spreyi, 500ml',
                'category' => 'kimyasal',
                'unit' => 'adet',
                'cost_price' => 85.00,
                'sale_price' => 125.00,
                'stock_quantity' => 60,
                'min_stock_level' => 20,
                'supplier' => 'WD-40',
                'is_active' => true,
            ],
            [
                'name' => 'Kabin Aydınlatma LED Panel',
                'code' => 'ASN-LED-01',
                'description' => '60x60cm LED tavan paneli, 36W, beyaz ışık',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 600.00,
                'sale_price' => 850.00,
                'stock_quantity' => 20,
                'min_stock_level' => 8,
                'supplier' => 'Philips',
                'is_active' => true,
            ],
            [
                'name' => 'Acil Durum Alarm Butonu',
                'code' => 'ASN-ALM-01',
                'description' => 'Kabin içi acil durum alarm butonu, kablolu',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 190.00,
                'sale_price' => 280.00,
                'stock_quantity' => 35,
                'min_stock_level' => 15,
                'supplier' => 'Schneider Electric',
                'is_active' => true,
            ],
            [
                'name' => 'Counterweight (Karşı Ağırlık) Blokları',
                'code' => 'ASN-KAG-01',
                'description' => 'Döküm karşı ağırlık blokları, 10kg',
                'category' => 'yedek_parca',
                'unit' => 'adet',
                'cost_price' => 85.00,
                'sale_price' => 120.00,
                'stock_quantity' => 100,
                'min_stock_level' => 40,
                'supplier' => 'Asansör Metal',
                'is_active' => true,
            ],
            [
                'name' => 'Makaron (Ray Kaydırıcı)',
                'code' => 'ASN-MKR-01',
                'description' => 'Ray kaydırıcı plastik makaron, 80mm',
                'category' => 'yedek_parca',
                'unit' => 'adet',
                'cost_price' => 28.00,
                'sale_price' => 45.00,
                'stock_quantity' => 200,
                'min_stock_level' => 80,
                'supplier' => 'Plast Teknik',
                'is_active' => true,
            ],
            [
                'name' => 'Kabin Vantilasyon Fanı',
                'code' => 'ASN-VNT-01',
                'description' => 'Kabin havalandırma fanı, 220V, 50Hz',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 450.00,
                'sale_price' => 650.00,
                'stock_quantity' => 15,
                'min_stock_level' => 6,
                'supplier' => 'Vortice',
                'is_active' => true,
            ],
            [
                'name' => 'Hidrolik Piston Contası',
                'code' => 'ASN-CNT-01',
                'description' => 'Hidrolik silindir piston contası, NBR malzeme',
                'category' => 'yedek_parca',
                'unit' => 'adet',
                'cost_price' => 260.00,
                'sale_price' => 380.00,
                'stock_quantity' => 18,
                'min_stock_level' => 8,
                'supplier' => 'Freudenberg',
                'is_active' => true,
            ],
            [
                'name' => 'İnverter (Frekans Konvertörü)',
                'code' => 'ASN-INV-01',
                'description' => 'Asansör inverteri 7.5kW, vektör kontrollü',
                'category' => 'elektronik',
                'unit' => 'adet',
                'cost_price' => 9500.00,
                'sale_price' => 12500.00,
                'stock_quantity' => 4,
                'min_stock_level' => 2,
                'supplier' => 'Yaskawa',
                'is_active' => true,
            ],
            [
                'name' => 'Emniyet Ayakkabısı',
                'code' => 'ASN-EMN-01',
                'description' => 'Teknisyen emniyet ayakkabısı, S3 sınıfı, çelik burun',
                'category' => 'arac_gerec',
                'unit' => 'adet',
                'cost_price' => 380.00,
                'sale_price' => 550.00,
                'stock_quantity' => 25,
                'min_stock_level' => 10,
                'supplier' => 'Puma Safety',
                'is_active' => true,
            ],
            [
                'name' => 'Çok Fonksiyonlu Test Cihazı',
                'code' => 'ASN-TST-01',
                'description' => 'Asansör test cihazı - voltmetre, ampermetre, megger fonksiyonlu',
                'category' => 'arac_gerec',
                'unit' => 'adet',
                'cost_price' => 2600.00,
                'sale_price' => 3500.00,
                'stock_quantity' => 6,
                'min_stock_level' => 2,
                'supplier' => 'Fluke',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $productData['company_id'] = $company->id;

            // Aynı kod varsa güncelle, yoksa ekle
            Product::updateOrCreate(
                ['code' => $productData['code'], 'company_id' => $company->id],
                $productData
            );
        }

        $this->command->info('✅ 20 adet ürün başarıyla eklendi!');
    }
}

