<?php

namespace App\Exports;

use App\Models\AccountingEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DayEndExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected int $companyId;
    protected string $selectedDate;

    public function __construct(int $companyId, string $selectedDate)
    {
        $this->companyId = $companyId;
        $this->selectedDate = $selectedDate;
    }

    public function collection()
    {
        return AccountingEntry::where('company_id', $this->companyId)
            ->whereDate('transaction_date', $this->selectedDate)
            ->with(['accountType', 'building'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return ['Tarih/Saat', 'Tür', 'Açıklama', 'Tutar (₺)', 'Hesap', 'Bina'];
    }

    public function map($row): array
    {
        $dateStr = $row->transaction_date
            ? $row->transaction_date->setTimezone('Europe/Istanbul')->format('d.m.Y H:i')
            : ($row->created_at ? $row->created_at->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') : '—');
        $type = $row->type === 'gelir' ? 'Gelir' : ($row->type === 'gider' ? 'Gider' : 'Transfer');
        return [
            $dateStr,
            $type,
            $row->description ?? '',
            (float) ($row->amount ?? 0),
            $row->accountType ? $row->accountType->name : '—',
            $row->building ? $row->building->name : '—',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 12, 'C' => 40, 'D' => 14, 'E' => 20, 'F' => 20];
    }

    public function title(): string
    {
        return 'Gün Sonu ' . $this->selectedDate;
    }
}
