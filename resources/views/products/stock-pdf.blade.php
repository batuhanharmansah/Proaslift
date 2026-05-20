<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Stok Raporu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            color: #3B82F6;
            margin-bottom: 10px;
        }

        .header .info {
            font-size: 10px;
            color: #6B7280;
        }

        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 2px solid #E5E7EB;
            background-color: #F9FAFB;
        }

        .stat-box .label {
            font-size: 9px;
            color: #6B7280;
            margin-bottom: 5px;
        }

        .stat-box .value {
            font-size: 18px;
            font-weight: bold;
        }

        .stat-box.total .value { color: #3B82F6; }
        .stat-box.in-stock .value { color: #10B981; }
        .stat-box.low-stock .value { color: #F59E0B; }
        .stat-box.out-stock .value { color: #EF4444; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #F3F4F6;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #D1D5DB;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        tbody tr:hover {
            background-color: #F3F4F6;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }

        .badge-blue { background-color: #DBEAFE; color: #1E40AF; }
        .badge-green { background-color: #D1FAE5; color: #065F46; }
        .badge-yellow { background-color: #FEF3C7; color: #92400E; }
        .badge-purple { background-color: #E9D5FF; color: #6B21A8; }
        .badge-gray { background-color: #F3F4F6; color: #374151; }

        .badge-stock-ok { background-color: #D1FAE5; color: #065F46; }
        .badge-stock-low { background-color: #FED7AA; color: #9A3412; }
        .badge-stock-out { background-color: #FEE2E2; color: #991B1B; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', monospace; }

        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company->name }}</h1>
        <h2>ÜRÜN STOK RAPORU</h2>
        <div class="info">
            Rapor Tarihi: {{ $date }} | Toplam {{ $products->count() }} Ürün
        </div>
    </div>

    <div class="stats">
        <div class="stat-box total">
            <div class="label">Toplam Ürün</div>
            <div class="value">{{ $products->count() }}</div>
        </div>
        <div class="stat-box in-stock">
            <div class="label">Stokta Var</div>
            <div class="value">{{ $products->where('stock_status', 'in_stock')->count() }}</div>
        </div>
        <div class="stat-box low-stock">
            <div class="label">Az Stok</div>
            <div class="value">{{ $products->where('stock_status', 'low_stock')->count() }}</div>
        </div>
        <div class="stat-box out-stock">
            <div class="label">Stokta Yok</div>
            <div class="value">{{ $products->where('stock_status', 'out_of_stock')->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Marka</th>
                <th style="width: 20%;">Ürün Adı</th>
                <th style="width: 10%;" class="text-right">Maliyet</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 10%;" class="text-center">Durum</th>
                <th style="width: 10%;" class="text-center">Stok</th>
                <th style="width: 8%;" class="text-center">Min</th>
                <th style="width: 10%;" class="text-right">Satış</th>
                <th style="width: 10%;">Tedarikçi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td class="font-mono">{{ $product->code }}</td>
                <td class="font-bold">{{ $product->name }}</td>
                <td class="text-right">
                    <span style="color: #DC2626; font-weight: bold;">₺{{ number_format($product->cost_price, 2) }}</span>
                </td>
                <td>
                    <span class="badge
                        {{ $product->category == 'yedek_parca' ? 'badge-blue' :
                           ($product->category == 'arac_gerec' ? 'badge-green' :
                            ($product->category == 'kimyasal' ? 'badge-yellow' :
                             ($product->category == 'elektronik' ? 'badge-purple' : 'badge-gray'))) }}">
                        {{ $product->category_label }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge
                        {{ $product->stock_status == 'in_stock' ? 'badge-stock-ok' :
                           ($product->stock_status == 'low_stock' ? 'badge-stock-low' : 'badge-stock-out') }}">
                        {{ $product->stock_status_label }}
                    </span>
                </td>
                <td class="text-center font-bold">
                    {{ $product->stock_quantity }} {{ $product->unit }}
                </td>
                <td class="text-center">
                    {{ $product->min_stock_level }} {{ $product->unit }}
                </td>
                <td class="text-right">
                    <span style="color: #10B981; font-weight: bold;">₺{{ number_format($product->sale_price, 2) }}</span>
                </td>
                <td>{{ $product->supplier ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $company->name }} - Ürün Stok Raporu | Sayfa 1 | {{ $date }}</p>
        <p>Bu rapor Harmanşah Yazılım tarafından oluşturulmuştur.</p>
    </div>
</body>
</html>

