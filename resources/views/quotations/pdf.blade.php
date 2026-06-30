<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->quote_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 16px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; margin: 0; }
        .muted { color: #6b7280; }
        .grid { width: 100%; margin-bottom: 18px; }
        .grid td { vertical-align: top; padding: 4px 0; }
        .section { margin-top: 18px; }
        .section-title { font-size: 15px; font-weight: bold; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 8px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th, table.items td { border: 1px solid #e5e7eb; padding: 8px; }
        table.items th { background: #f9fafb; text-align: left; }
        .right { text-align: right; }
        .totals { width: 45%; margin-left: auto; margin-top: 14px; border-collapse: collapse; }
        .totals td { padding: 6px; border-bottom: 1px solid #e5e7eb; }
        .grand { font-size: 16px; font-weight: bold; }
        .signature { margin-top: 40px; width: 100%; }
        .signature td { width: 50%; padding-top: 35px; text-align: center; border-top: 1px solid #9ca3af; }
        .pre { white-space: pre-line; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">{{ $quotation->quote_no }}</p>
        <p class="muted">{{ $quotation->type_label }} | Tarih: {{ $quotation->created_at->format('d.m.Y') }} | Geçerlilik: {{ $quotation->valid_until?->format('d.m.Y') }}</p>
    </div>

    <table class="grid">
        <tr>
            <td width="50%">
                <strong>Teklifi Hazırlayan</strong><br>
                {{ $quotation->company?->name ?? 'Firma' }}<br>
                {{ $quotation->company?->phone ?? '' }}<br>
                {{ $quotation->company?->email ?? '' }}
            </td>
            <td width="50%">
                <strong>Müşteri</strong><br>
                {{ $quotation->customer_name }}<br>
                {{ $quotation->customer_contact_name }}<br>
                {{ $quotation->customer_phone }} {{ $quotation->customer_email ? ' | ' . $quotation->customer_email : '' }}<br>
                {{ $quotation->customer_address }}
            </td>
        </tr>
    </table>

    @if($quotation->units->count() > 0)
        <div class="section">
            <div class="section-title">Asansör / Ünite Bilgileri</div>
            @foreach($quotation->units as $unit)
                <table class="grid">
                    <tr>
                        <td>Kod: <strong>{{ $unit->elevator_code ?: '-' }}</strong></td>
                        <td>Tip: <strong>{{ $unit->elevator_type ?: '-' }}</strong></td>
                        <td>Marka/Model: <strong>{{ trim(($unit->brand ?: '') . ' ' . ($unit->model ?: '')) ?: '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Durak: <strong>{{ $unit->stop_count ?: '-' }}</strong></td>
                        <td>Kapasite: <strong>{{ $unit->capacity_kg ?: '-' }} kg</strong></td>
                        <td>Etiket: <strong>{{ $unit->current_label_color ?: '-' }}</strong></td>
                    </tr>
                </table>
            @endforeach
        </div>
    @endif

    <div class="section">
        <div class="section-title">Kapsam</div>
        <div class="pre">{{ $quotation->scope_summary ?: '-' }}</div>
    </div>

    <div class="section">
        <div class="section-title">Fiyat Tablosu</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Açıklama</th>
                    <th class="right">Miktar</th>
                    <th class="right">Birim Fiyat</th>
                    <th class="right">KDV</th>
                    <th class="right">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="right">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                        <td class="right">{{ $quotation->currency }} {{ number_format($item->unit_price, 2) }}</td>
                        <td class="right">{{ $quotation->currency }} {{ number_format($item->vat_amount, 2) }}</td>
                        <td class="right">{{ $quotation->currency }} {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="totals">
            <tr><td>Ara Toplam</td><td class="right">{{ $quotation->currency }} {{ number_format($quotation->subtotal, 2) }}</td></tr>
            <tr><td>İndirim</td><td class="right">{{ $quotation->currency }} {{ number_format($quotation->discount_total, 2) }}</td></tr>
            <tr><td>KDV</td><td class="right">{{ $quotation->currency }} {{ number_format($quotation->vat_total, 2) }}</td></tr>
            <tr class="grand"><td>Genel Toplam</td><td class="right">{{ $quotation->currency }} {{ number_format($quotation->grand_total, 2) }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Dahil Olanlar</div>
        <div class="pre">{{ $quotation->scope_inclusions ?: '-' }}</div>
    </div>
    <div class="section">
        <div class="section-title">Hariç Olanlar</div>
        <div class="pre">{{ $quotation->scope_exclusions ?: '-' }}</div>
    </div>
    <div class="section">
        <div class="section-title">Ticari Şartlar</div>
        <div class="pre">{{ $quotation->terms ?: '-' }}</div>
    </div>

    <table class="signature">
        <tr>
            <td>Firma Yetkilisi</td>
            <td>Müşteri Yetkilisi</td>
        </tr>
    </table>
</body>
</html>
