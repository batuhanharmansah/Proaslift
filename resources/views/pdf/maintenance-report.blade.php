<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakım Raporu - {{ $maintenance->building->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .header .subtitle {
            font-size: 16px;
            color: #666;
        }
        .info-section {
            margin-bottom: 25px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }
        .info-section h3 {
            margin: 0 0 15px 0;
            color: #2563eb;
            font-size: 16px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 8px 15px 8px 0;
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        .info-value {
            color: #111827;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section h3 {
            color: #2563eb;
            font-size: 16px;
            margin: 0 0 15px 0;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .cost-info {
            background: #ecfdf5;
            border: 1px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .cost-info h4 {
            margin: 0 0 10px 0;
            color: #047857;
        }
        .problems {
            background: #fef2f2;
            border: 1px solid #f87171;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .problems h4 {
            margin: 0 0 10px 0;
            color: #dc2626;
        }
        .recommendations {
            background: #f0f9ff;
            border: 1px solid #60a5fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .recommendations h4 {
            margin: 0 0 10px 0;
            color: #2563eb;
        }
        .customer-info {
            background: #fffbeb;
            border: 1px solid #fbbf24;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .customer-info h4 {
            margin: 0 0 10px 0;
            color: #d97706;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-completed {
            background: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .products-table th,
        .products-table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        .products-table th {
            background: #f3f4f6;
            font-weight: bold;
        }
        .products-table .total-row {
            background: #f9fafb;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📋 BAKIM RAPORU</h1>
        <div class="subtitle">{{ $maintenance->building->name }}</div>
        <div class="subtitle">{{ $maintenance->maintenanceTypeLabel }}</div>
    </div>

    <!-- İş Bilgileri -->
    <div class="info-section">
        <h3>🏢 İş Bilgileri</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Bina:</div>
                    <div class="info-value">{{ $maintenance->building->name }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Bakım Türü:</div>
                    <div class="info-value">{{ $maintenance->maintenanceTypeLabel }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Planlanan Tarih:</div>
                    <div class="info-value">{{ $maintenance->scheduled_date->format('d.m.Y') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Atanan Personel:</div>
                    <div class="info-value">{{ $maintenance->assignedEmployee->full_name ?? 'Atanmamış' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapor Detayları -->
    <div class="section">
        <h3>📊 Rapor Detayları</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Başlangıç Zamanı:</div>
                    <div class="info-value">{{ $maintenance->maintenanceReport->start_time ? \Carbon\Carbon::parse($maintenance->maintenanceReport->start_time)->format('d.m.Y H:i') : '-' }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Bitiş Zamanı:</div>
                    <div class="info-value">{{ $maintenance->maintenanceReport->end_time ? \Carbon\Carbon::parse($maintenance->maintenanceReport->end_time)->format('d.m.Y H:i') : '-' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Toplam Maliyet:</div>
                    <div class="info-value">₺{{ number_format($maintenance->maintenanceReport->total_cost, 2) }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Tamamlanma Durumu:</div>
                    <div class="info-value">
                        <span class="status-badge {{ $maintenance->maintenanceReport->completion_status === 'tamamlandi' ? 'status-completed' : 'status-pending' }}">
                            {{ $maintenance->maintenanceReport->completion_status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>🔧 Yapılan İşlemler</h3>
            <p>{{ $maintenance->maintenanceReport->work_description }}</p>
        </div>

        @if($maintenance->maintenanceReport->problems_found)
        <div class="problems">
            <h4>⚠️ Tespit Edilen Sorunlar</h4>
            <p>{{ $maintenance->maintenanceReport->problems_found }}</p>
        </div>
        @endif

        @if($maintenance->maintenanceReport->recommendations)
        <div class="recommendations">
            <h4>💡 Öneriler</h4>
            <p>{{ $maintenance->maintenanceReport->recommendations }}</p>
        </div>
        @endif
    </div>

    <!-- Kullanılan Ürünler -->
    @if($maintenance->maintenanceReport->used_products && count($maintenance->maintenanceReport->used_products) > 0)
    <div class="section">
        <h3>📦 Kullanılan Ürünler</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Ürün Bilgileri</th>
                    <th>Miktar</th>
                    <th>Birim Fiyat</th>
                    <th>Toplam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maintenance->maintenanceReport->used_products as $product)
                <tr>
                    <td>
                        <strong>{{ $product['product_name'] }}</strong>
                        @if(isset($product['product_code']))
                            <br><small>Marka: {{ $product['product_code'] }}</small>
                        @endif
                    </td>
                    <td>{{ $product['quantity'] }} {{ $product['unit'] }}</td>
                    <td>₺{{ number_format($product['unit_price'], 2) }}</td>
                    <td>₺{{ number_format($product['subtotal'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3"><strong>Toplam Ürün Maliyeti:</strong></td>
                    <td><strong>₺{{ number_format(collect($maintenance->maintenanceReport->used_products)->sum('subtotal'), 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Rutin Bakım Checklist -->
    @if($maintenance->maintenanceReport->routine_maintenance_checklist)
    <div class="section">
        <h3>📋 Rutin Bakım Kontrol Listesi</h3>
        @php
            $checklist = $maintenance->maintenanceReport->routine_maintenance_checklist;
        @endphp

        @if(isset($checklist['machine_room']))
        <div style="margin-bottom: 20px;">
            <h4 style="color: #2563eb; margin-bottom: 10px; font-size: 14px;">🏭 Makine Dairesi Kontrolü</h4>
            @foreach($checklist['machine_room'] as $item)
                <div style="margin-bottom: 5px; padding: 5px 0; {{ isset($item['has_error']) && $item['has_error'] ? 'background-color: #FEE2E2; padding: 8px; border-left: 4px solid #DC2626;' : '' }}">
                    <span style="color: {{ isset($item['has_error']) && $item['has_error'] ? '#dc2626' : ($item['checked'] ? '#059669' : '#dc2626') }}; font-weight: bold;">
                        {{ isset($item['has_error']) && $item['has_error'] ? '❌ HATALI' : ($item['checked'] ? '✅' : '❌') }}
                    </span>
                    <span style="margin-left: 8px;">{{ $item['title'] }}</span>
                    @if(!empty($item['notes']))
                        <div style="margin-left: 24px; margin-top: 5px; font-size: 11px; color: {{ isset($item['has_error']) && $item['has_error'] ? '#991B1B' : '#666' }}; font-style: italic; {{ isset($item['has_error']) && $item['has_error'] ? 'font-weight: bold;' : '' }}">
                            {{ isset($item['has_error']) && $item['has_error'] ? '⚠️ SORUN: ' : 'Not: ' }}{{ $item['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        @if(isset($checklist['floors']))
        <div style="margin-bottom: 20px;">
            <h4 style="color: #2563eb; margin-bottom: 10px; font-size: 14px;">🏢 Katlar</h4>
            @foreach($checklist['floors'] as $item)
                <div style="margin-bottom: 5px; padding: 5px 0; {{ isset($item['has_error']) && $item['has_error'] ? 'background-color: #FEE2E2; padding: 8px; border-left: 4px solid #DC2626;' : '' }}">
                    <span style="color: {{ isset($item['has_error']) && $item['has_error'] ? '#dc2626' : ($item['checked'] ? '#059669' : '#dc2626') }}; font-weight: bold;">
                        {{ isset($item['has_error']) && $item['has_error'] ? '❌ HATALI' : ($item['checked'] ? '✅' : '❌') }}
                    </span>
                    <span style="margin-left: 8px;">{{ $item['title'] }}</span>
                    @if(!empty($item['notes']))
                        <div style="margin-left: 24px; margin-top: 5px; font-size: 11px; color: {{ isset($item['has_error']) && $item['has_error'] ? '#991B1B' : '#666' }}; font-style: italic; {{ isset($item['has_error']) && $item['has_error'] ? 'font-weight: bold;' : '' }}">
                            {{ isset($item['has_error']) && $item['has_error'] ? '⚠️ SORUN: ' : 'Not: ' }}{{ $item['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        @if(isset($checklist['cabin_interior_top']))
        <div style="margin-bottom: 20px;">
            <h4 style="color: #2563eb; margin-bottom: 10px; font-size: 14px;">🚪 Kabin İç ve Kabin Üstü Kontrolü</h4>
            @foreach($checklist['cabin_interior_top'] as $item)
                <div style="margin-bottom: 5px; padding: 5px 0; {{ isset($item['has_error']) && $item['has_error'] ? 'background-color: #FEE2E2; padding: 8px; border-left: 4px solid #DC2626;' : '' }}">
                    <span style="color: {{ isset($item['has_error']) && $item['has_error'] ? '#dc2626' : ($item['checked'] ? '#059669' : '#dc2626') }}; font-weight: bold;">
                        {{ isset($item['has_error']) && $item['has_error'] ? '❌ HATALI' : ($item['checked'] ? '✅' : '❌') }}
                    </span>
                    <span style="margin-left: 8px;">{{ $item['title'] }}</span>
                    @if(!empty($item['notes']))
                        <div style="margin-left: 24px; margin-top: 5px; font-size: 11px; color: {{ isset($item['has_error']) && $item['has_error'] ? '#991B1B' : '#666' }}; font-style: italic; {{ isset($item['has_error']) && $item['has_error'] ? 'font-weight: bold;' : '' }}">
                            {{ isset($item['has_error']) && $item['has_error'] ? '⚠️ SORUN: ' : 'Not: ' }}{{ $item['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        @if(isset($checklist['shaft_interior']))
        <div style="margin-bottom: 20px;">
            <h4 style="color: #2563eb; margin-bottom: 10px; font-size: 14px;">🕳️ Kuyu İçi</h4>
            @foreach($checklist['shaft_interior'] as $item)
                <div style="margin-bottom: 5px; padding: 5px 0; {{ isset($item['has_error']) && $item['has_error'] ? 'background-color: #FEE2E2; padding: 8px; border-left: 4px solid #DC2626;' : '' }}">
                    <span style="color: {{ isset($item['has_error']) && $item['has_error'] ? '#dc2626' : ($item['checked'] ? '#059669' : '#dc2626') }}; font-weight: bold;">
                        {{ isset($item['has_error']) && $item['has_error'] ? '❌ HATALI' : ($item['checked'] ? '✅' : '❌') }}
                    </span>
                    <span style="margin-left: 8px;">{{ $item['title'] }}</span>
                    @if(!empty($item['notes']))
                        <div style="margin-left: 24px; margin-top: 5px; font-size: 11px; color: {{ isset($item['has_error']) && $item['has_error'] ? '#991B1B' : '#666' }}; font-style: italic; {{ isset($item['has_error']) && $item['has_error'] ? 'font-weight: bold;' : '' }}">
                            {{ isset($item['has_error']) && $item['has_error'] ? '⚠️ SORUN: ' : 'Not: ' }}{{ $item['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Hatalı/Sorunlu Maddeler Özeti -->
    @php
        $errorItems = [];
        if($maintenance->maintenanceReport->routine_maintenance_checklist) {
            $checklist = $maintenance->maintenanceReport->routine_maintenance_checklist;
            $sectionTitles = [
                'machine_room' => '🏭 Makine Dairesi',
                'floors' => '🏢 Katlar',
                'cabin_interior_top' => '🚪 Kabin İç ve Üstü',
                'shaft_interior' => '🕳️ Kuyu İçi'
            ];

            foreach($checklist as $sectionKey => $items) {
                foreach($items as $item) {
                    if(isset($item['has_error']) && $item['has_error']) {
                        $errorItems[] = [
                            'section' => $sectionTitles[$sectionKey] ?? $sectionKey,
                            'title' => $item['title'],
                            'notes' => $item['notes'] ?? ''
                        ];
                    }
                }
            }
        }
    @endphp

    @if(count($errorItems) > 0)
    <div class="problems" style="margin-top: 20px; page-break-inside: avoid;">
        <h3 style="color: #DC2626; border-bottom: 2px solid #DC2626; padding-bottom: 8px; margin-bottom: 15px;">
            ⚠️ Arızalı / Çalışmayan / Sorunlu Maddeler Özeti
        </h3>
        <div style="background-color: #FEE2E2; padding: 15px; border-radius: 8px; border: 2px solid #DC2626;">
            <p style="color: #991B1B; font-weight: bold; margin-bottom: 10px; font-size: 12px;">
                Toplam {{ count($errorItems) }} adet arızalı veya çalışmayan madde tespit edilmiştir:
            </p>
            @foreach($errorItems as $index => $error)
                <div style="margin-bottom: 12px; padding: 10px; background-color: white; border-left: 4px solid #DC2626; border-radius: 4px;">
                    <div style="margin-bottom: 5px;">
                        <span style="color: #DC2626; font-weight: bold; font-size: 11px;">{{ $loop->iteration }}.</span>
                        <span style="color: #6B7280; font-size: 10px; font-weight: bold;">[{{ $error['section'] }}]</span>
                    </div>
                    <div style="color: #1F2937; font-weight: bold; font-size: 11px; margin-bottom: 5px;">
                        {{ $error['title'] }}
                    </div>
                    @if(!empty($error['notes']))
                        <div style="color: #991B1B; font-size: 10px; font-style: italic; margin-left: 10px; padding: 5px; background-color: #FEF2F2; border-radius: 4px;">
                            ⚠️ <strong>Sorun Açıklaması:</strong> {{ $error['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <!-- Bina Yöneticisi Onayı -->
    @if($maintenance->maintenanceReport->approval_status)
    <div class="customer-info">
        <h4>Bina Yöneticisi Onayı</h4>
        <p><strong>Durum:</strong> {{ $maintenance->maintenanceReport->approval_status_label }}</p>
        @if($maintenance->maintenanceReport->approved_by_name)
            <p><strong>Onaylayan:</strong> {{ $maintenance->maintenanceReport->approved_by_name }}</p>
            @if($maintenance->maintenanceReport->approved_at)
                <p><strong>Onay Tarihi:</strong> {{ $maintenance->maintenanceReport->approved_at->format('d.m.Y H:i') }}</p>
            @endif
        @endif
    </div>
    @endif

    <!-- Müşteri Onayı -->
    @if($maintenance->maintenanceReport->customer_name || $maintenance->maintenanceReport->customer_signature)
    <div class="customer-info">
        <h4>✍️ Müşteri Onayı</h4>
        @if($maintenance->maintenanceReport->customer_name)
            <p><strong>Onaylayan Kişi:</strong> {{ $maintenance->maintenanceReport->customer_name }}</p>
        @endif
        <p><strong>Müşteri İmzası:</strong> {{ $maintenance->maintenanceReport->customer_signature ? '✅ Alındı' : '❌ Alınmadı' }}</p>
        @if($maintenance->maintenanceReport->customer_notes)
            <p><strong>Müşteri Notları:</strong> {{ $maintenance->maintenanceReport->customer_notes }}</p>
        @endif
    </div>
    @endif

    <!-- Rapor Bilgileri -->
    <div class="section">
        <h3>📋 Rapor Bilgileri</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Rapor Tarihi:</div>
                    <div class="info-value">{{ $maintenance->maintenanceReport->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Rapor Eden:</div>
                    <div class="info-value">{{ $maintenance->maintenanceReport->employee->full_name ?? 'Bilinmiyor' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Bu rapor {{ now()->format('d.m.Y H:i') }} tarihinde oluşturulmuştur.</p>
        <p>{{ $maintenance->building->company->name ?? 'Demo Asansör Firması' }}</p>
    </div>
</body>
</html>
