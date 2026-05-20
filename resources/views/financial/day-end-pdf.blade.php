<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gün Sonu Raporu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #3B82F6; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #1F2937; margin-bottom: 5px; }
        .header h2 { font-size: 14px; color: #3B82F6; }
        .header .info { font-size: 9px; color: #6B7280; margin-top: 8px; }
        .stats { display: table; width: 100%; margin-bottom: 20px; }
        .stat-box { display: table-cell; width: 33.33%; padding: 12px; text-align: center; border: 2px solid #E5E7EB; background-color: #F9FAFB; }
        .stat-box .label { font-size: 9px; color: #6B7280; margin-bottom: 5px; }
        .stat-box .value { font-size: 16px; font-weight: bold; }
        .stat-box.income .value { color: #059669; }
        .stat-box.expense .value { color: #DC2626; }
        .stat-box.net .value { color: #2563EB; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        thead { background-color: #F3F4F6; }
        th { padding: 8px 6px; text-align: left; font-size: 8px; font-weight: bold; color: #374151; border-bottom: 2px solid #D1D5DB; text-transform: uppercase; }
        td { padding: 6px; border-bottom: 1px solid #E5E7EB; font-size: 9px; }
        tbody tr:nth-child(even) { background-color: #F9FAFB; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 10px; left: 20px; right: 20px; text-align: center; font-size: 8px; color: #9CA3AF; border-top: 1px solid #E5E7EB; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company->name ?? 'Firma' }}</h1>
        <h2>GÜN SONU RAPORU</h2>
        <div class="info">{{ $dateFormatted }} | {{ $transactions->count() }} işlem</div>
    </div>

    <div class="stats">
        <div class="stat-box income">
            <div class="label">Gelir</div>
            <div class="value">₺{{ number_format($dayIncome, 2) }}</div>
        </div>
        <div class="stat-box expense">
            <div class="label">Gider</div>
            <div class="value">₺{{ number_format($dayExpense, 2) }}</div>
        </div>
        <div class="stat-box net">
            <div class="label">Net</div>
            <div class="value">₺{{ number_format($dayIncome - $dayExpense, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tarih/Saat</th>
                <th>Tür</th>
                <th>Açıklama</th>
                <th class="text-right">Tutar</th>
                <th>Hesap</th>
                <th>Bina</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->transaction_date ? $t->transaction_date->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') : ($t->created_at ? $t->created_at->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') : '—') }}</td>
                <td>{{ $t->type === 'gelir' ? 'Gelir' : ($t->type === 'gider' ? 'Gider' : 'Transfer') }}</td>
                <td>{{ Str::limit($t->description ?? '', 40) }}</td>
                <td class="text-right">₺{{ number_format($t->amount ?? 0, 2) }}</td>
                <td>{{ $t->accountType ? $t->accountType->name : '—' }}</td>
                <td>{{ $t->building ? $t->building->name : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; padding:20px;">Bu tarihte işlem bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ $company->name ?? '' }} — Gün Sonu {{ $selectedDate }} — {{ now('Europe/Istanbul')->format('d.m.Y H:i') }} tarihinde oluşturuldu.
    </div>
</body>
</html>
