<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DashboardExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;
    protected $type;
    protected $headings;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
        $this->setHeadings();
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->headings as $heading) {
            $widths[] = 20;
        }
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    private function setHeadings()
    {
        switch ($this->type) {
            case 'maintenance':
                $this->headings = ['Tarih', 'Bina', 'Tür', 'Öncelik', 'Durum', 'Atanan Personel', 'Açıklama'];
                break;
            case 'financial':
                $this->headings = ['Tarih', 'Tür', 'Kategori', 'Açıklama', 'Tutar', 'Bina', 'Personel'];
                break;
            case 'employees':
                $this->headings = ['Ad Soyad', 'Pozisyon', 'Toplam İş', 'Tamamlanan İş', 'Tamamlanma Oranı', 'Maaş', 'İşe Başlama'];
                break;
            default:
                $this->headings = [];
        }
    }
}
