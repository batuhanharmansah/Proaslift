<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductStockExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::where('company_id', $this->companyId)
                     ->orderBy('name', 'asc')
                     ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Ürün Markası',
            'Ürün Adı',
            'Maliyet Fiyatı (₺)',
            'Kategori',
            'Birim',
            'Mevcut Stok',
            'Minimum Stok',
            'Stok Durumu',
            'Satış Fiyatı (₺)',
            'Kar Marjı (%)',
            'Tedarikçi',
            'Durum',
            'Notlar'
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        $categoryLabels = [
            'yedek_parca' => 'Yedek Parça',
            'arac_gerec' => 'Araç Gereç',
            'kimyasal' => 'Kimyasal',
            'elektronik' => 'Elektronik',
            'mekanik' => 'Mekanik'
        ];

        $stockStatusLabels = [
            'out_of_stock' => 'Stokta Yok',
            'low_stock' => 'Az Stok',
            'in_stock' => 'Stokta Var'
        ];

        return [
            $product->code,
            $product->name,
            number_format($product->cost_price, 2),
            $categoryLabels[$product->category] ?? $product->category,
            $product->unit,
            $product->stock_quantity,
            $product->min_stock_level,
            $stockStatusLabels[$product->stock_status] ?? 'Bilinmiyor',
            number_format($product->sale_price, 2),
            number_format($product->profit_margin, 2),
            $product->supplier ?? '-',
            $product->is_active ? 'Aktif' : 'Pasif',
            $product->notes ?? '-'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto-size row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Apply borders and alignment to all data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:M' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align price columns
        $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Color cost price (Maliyet) in red
        $sheet->getStyle('C2:C' . $highestRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'DC2626']
            ]
        ]);

        // Color sale price (Satış) in green
        $sheet->getStyle('I2:I' . $highestRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '10B981']
            ]
        ]);

        // Color code rows based on stock status
        for ($row = 2; $row <= $highestRow; $row++) {
            $stockStatus = $sheet->getCell('H' . $row)->getValue();

            $fillColor = null;
            if ($stockStatus === 'Stokta Var') {
                $fillColor = 'D1FAE5'; // Green
            } elseif ($stockStatus === 'Az Stok') {
                $fillColor = 'FED7AA'; // Orange
            } elseif ($stockStatus === 'Stokta Yok') {
                $fillColor = 'FEE2E2'; // Red
            }

            if ($fillColor) {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $fillColor]
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Ürün Markası
            'B' => 30,  // Ürün Adı
            'C' => 15,  // Maliyet Fiyatı
            'D' => 15,  // Kategori
            'E' => 10,  // Birim
            'F' => 12,  // Mevcut Stok
            'G' => 12,  // Minimum Stok
            'H' => 15,  // Stok Durumu
            'I' => 15,  // Satış Fiyatı
            'J' => 12,  // Kar Marjı
            'K' => 20,  // Tedarikçi
            'L' => 10,  // Durum
            'M' => 30,  // Notlar
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Ürün Stok Raporu';
    }
}

