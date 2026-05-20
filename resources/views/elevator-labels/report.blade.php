@extends('layouts.app')

@section('title', 'Asansör Etiket Raporu')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📊 Asansör Etiket Raporu</h1>
                <p class="text-gray-600 mt-1">Detaylı analiz ve istatistikler</p>
                <p class="text-sm text-gray-500 mt-1">Rapor oluşturulma tarihi: {{ $reportData['generated_at']->format('d.m.Y H:i') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('elevator-labels.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    ← Geri Dön
                </a>
                <button onclick="window.print()"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    🖨️ Yazdır
                </button>
            </div>
        </div>
    </div>

    <!-- Filtre Bilgileri -->
    @if(array_filter($filters))
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8">
        <h3 class="text-sm font-medium text-blue-800 mb-2">Uygulanan Filtreler:</h3>
        <div class="flex flex-wrap gap-2">
            @if($filters['date_from'])
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    📅 Başlangıç: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d.m.Y') }}
                </span>
            @endif
            @if($filters['date_to'])
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    📅 Bitiş: {{ \Carbon\Carbon::parse($filters['date_to'])->format('d.m.Y') }}
                </span>
            @endif
            @if($filters['label_color'])
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    🏷️ Etiket: {{ ucfirst($filters['label_color']) }}
                </span>
            @endif
            @if($filters['status'])
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    📋 Durum: {{ ucfirst($filters['status']) }}
                </span>
            @endif
            @if($filters['risk_level'])
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    ⚠️ Risk: {{ ucfirst(str_replace('_', ' ', $filters['risk_level'])) }}
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Özet İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Toplam Bina</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $reportData['total_buildings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Normal Durum</p>
                    <p class="text-2xl font-bold text-green-600">{{ $reportData['risk_analysis']['normal'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Risk Durumu</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $reportData['risk_analysis']['due_soon_30'] + $reportData['risk_analysis']['due_soon_7'] + $reportData['risk_analysis']['due_soon_1'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Gecikmiş</p>
                    <p class="text-2xl font-bold text-red-600">{{ $reportData['risk_analysis']['overdue'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Etiket Dağılımı -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🏷️ Etiket Dağılımı</h3>
            <div class="space-y-4">
                @foreach(['yesil' => 'green', 'mavi' => 'blue', 'sari' => 'yellow', 'kirmizi' => 'red'] as $color => $colorClass)
                    @php
                        $count = $reportData['label_distribution'][$color] ?? 0;
                        $percentage = $reportData['total_buildings'] > 0 ? round(($count / $reportData['total_buildings']) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-{{ $colorClass }}-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $color }} Etiket</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $percentage }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $colorClass }}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Risk Analizi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Risk Analizi</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">Gecikmiş</span>
                    </div>
                    <span class="text-sm font-bold text-red-600">{{ $reportData['risk_analysis']['overdue'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">1 Gün İçinde</span>
                    </div>
                    <span class="text-sm font-bold text-orange-600">{{ $reportData['risk_analysis']['due_soon_1'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">7 Gün İçinde</span>
                    </div>
                    <span class="text-sm font-bold text-yellow-600">{{ $reportData['risk_analysis']['due_soon_7'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">30 Gün İçinde</span>
                    </div>
                    <span class="text-sm font-bold text-blue-600">{{ $reportData['risk_analysis']['due_soon_30'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">Mühürleme Adayı</span>
                    </div>
                    <span class="text-sm font-bold text-purple-600">{{ $reportData['risk_analysis']['sealing_candidates'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kontrol Tarihleri Analizi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Son Kontrol Tarihleri Analizi</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $reportData['control_date_analysis']['last_month'] }}</div>
                <div class="text-sm text-gray-600">Son 1 Ay</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $reportData['control_date_analysis']['last_3_months'] }}</div>
                <div class="text-sm text-gray-600">Son 3 Ay</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $reportData['control_date_analysis']['last_6_months'] }}</div>
                <div class="text-sm text-gray-600">Son 6 Ay</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $reportData['control_date_analysis']['older_than_6_months'] }}</div>
                <div class="text-sm text-gray-600">6 Aydan Eski</div>
            </div>
        </div>
    </div>

    <!-- Aylık Trend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Aylık Trend Analizi (Son 12 Ay)</h3>
        <div class="overflow-x-auto">
            <canvas id="monthlyTrendChart" width="800" height="300"></canvas>
        </div>
    </div>

    <!-- Detaylı Liste -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">📋 Detaylı Bina Listesi</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $buildings->count() }} bina bulundu</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bina Adı
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Asansör Kodu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Etiket Rengi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kontrol Tarihi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Son Tarih
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Risk Durumu
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($buildings as $building)
                        @if($building->activeLabel)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $building->name }}</div>
                                <div class="text-sm text-gray-500">{{ $building->address }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $building->elevator_code ?: 'Kod Yok' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($building->activeLabel->label_color === 'yesil') bg-green-100 text-green-800
                                    @elseif($building->activeLabel->label_color === 'mavi') bg-blue-100 text-blue-800
                                    @elseif($building->activeLabel->label_color === 'sari') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $building->activeLabel->label_color_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $building->activeLabel->control_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $building->activeLabel->next_control_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $remainingDays = $building->activeLabel->next_control_date->diffInDays(now(), false);
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($remainingDays < 0) bg-red-100 text-red-800
                                    @elseif($remainingDays <= 1) bg-orange-100 text-orange-800
                                    @elseif($remainingDays <= 7) bg-yellow-100 text-yellow-800
                                    @elseif($remainingDays <= 30) bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    @if($remainingDays < 0)
                                        {{ abs($remainingDays) }} gün geç
                                    @elseif($remainingDays <= 1)
                                        Kritik
                                    @elseif($remainingDays <= 7)
                                        Yaklaşıyor
                                    @elseif($remainingDays <= 30)
                                        Normal
                                    @else
                                        Uzun süre
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Filtrelere uygun bina bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aylık trend grafiği
    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
    const monthlyData = @json($reportData['monthly_trend']);

    const labels = Object.keys(monthlyData).map(key => monthlyData[key].month_name);
    const createdData = Object.keys(monthlyData).map(key => monthlyData[key].labels_created);
    const expiredData = Object.keys(monthlyData).map(key => monthlyData[key].labels_expired);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Oluşturulan Etiketler',
                data: createdData,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }, {
                label: 'Süresi Dolan Etiketler',
                data: expiredData,
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        font-size: 12px;
    }

    .bg-white {
        background: white !important;
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }

    .text-gray-900 {
        color: #111827 !important;
    }

    .text-gray-600 {
        color: #4b5563 !important;
    }

    .text-gray-500 {
        color: #6b7280 !important;
    }
}
</style>
@endpush
@endsection
