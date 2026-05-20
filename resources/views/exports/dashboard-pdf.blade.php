<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Raporu</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #4F46E5;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #4F46E5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard Raporu</h1>
        <p>Rapor Türü: {{ ucfirst($type) }}</p>
        <p>Oluşturulma Tarihi: {{ now()->format('d.m.Y H:i') }}</p>
        <p>Firma: {{ auth()->user()->company->name }}</p>
    </div>

    @if(isset($summary) && count($summary) > 0)
    <div class="summary">
        <h3>Özet Bilgiler</h3>
        @foreach($summary as $key => $value)
            <p><strong>{{ $key }}:</strong> {{ $value }}</p>
        @endforeach
    </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Bu rapor {{ auth()->user()->name }} tarafından {{ now()->format('d.m.Y H:i') }} tarihinde oluşturulmuştur.</p>
        <p>Asansör Yönetim Sistemi - Tüm hakları saklıdır.</p>
    </div>
</body>
</html>
