@extends('layouts.app')

@section('title', 'Müşteri Analizleri - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Analizleri</h1>
        <p class="text-gray-600 mt-2">Bina analizleri ve müşteri performans raporları</p>
    </div>

    <!-- Sekmeler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('reports.financial') }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 text-sm font-medium">
                    Finansal Raporlar
                </a>
                <a href="{{ route('reports.maintenance') }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 text-sm font-medium">
                    Bakım Performans
                </a>
                <a href="{{ route('reports.employee') }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 text-sm font-medium">
                    Personel Verimlilik
                </a>
                <a href="{{ route('reports.customer') }}"
                   class="border-b-2 border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                    Müşteri Analizleri
                </a>
            </nav>
        </div>
    </div>

    <!-- Tarih Filtreleri -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Filtrele
                </button>
                <button type="button" onclick="exportReport('customer')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Export Et
                </button>
            </div>
        </form>
    </div>

    <!-- Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Toplam Bina</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalBuildings }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Aktif Sözleşmeler</p>
                    <p class="text-2xl font-bold text-green-600">{{ $activeContracts }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Toplam Gelir</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($totalRevenue, 0, ',', '.') }} ₺</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Ortalama Gelir</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($averageRevenue, 0, ',', '.') }} ₺</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gelir Dağılımı -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bina Bazlı Gelir Dağılımı</h3>
            <canvas id="revenueDistributionChart" width="400" height="200"></canvas>
        </div>

        <!-- Arıza Türü Analizi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Arıza Türü Analizi</h3>
            <canvas id="issueTypeChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Bina Performans Analizi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bina Performans Analizi</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bina</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Bakım</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamamlanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bakım Oranı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Gelir</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($buildingPerformance as $building)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $building->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($building->status === 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $building->total_maintenance }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $building->completed_maintenance }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">%{{ number_format($building->maintenance_rate, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($building->total_revenue, 0, ',', '.') }} ₺</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- En Çok Gelir Getiren Binalar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">En Çok Gelir Getiren Binalar</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($topRevenueBuildings as $building)
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-lg">{{ substr($building->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ $building->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $building->address }}</p>
                        <p class="text-sm font-medium text-purple-600">{{ number_format($building->total_revenue, 0, ',', '.') }} ₺</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gelir Dağılımı Grafiği
const revenueCtx = document.getElementById('revenueDistributionChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: @json($revenueByBuilding->pluck('name')),
        datasets: [{
            label: 'Gelir (₺)',
            data: @json($revenueByBuilding->pluck('total_revenue')),
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Arıza Türü Analizi Grafiği
const issueCtx = document.getElementById('issueTypeChart').getContext('2d');
new Chart(issueCtx, {
    type: 'doughnut',
    data: {
        labels: @json($issueAnalysis->pluck('issue_type_label')),
        datasets: [{
            data: @json($issueAnalysis->pluck('count')),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(14, 165, 233, 0.8)',
                'rgba(34, 197, 94, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const urgentCount = @json($issueAnalysis->pluck('urgent_count', 'issue_type_label'));
                        const urgent = urgentCount[context.label] || 0;
                        return context.label + ': ' + context.parsed + ' adet (Acil: ' + urgent + ')';
                    }
                }
            }
        }
    }
});

function exportReport(type) {
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!startDateInput || !endDateInput || !csrfToken) {
        console.error('Gerekli öğeler bulunamadı');
        alert('Sayfa yüklenirken hata oluştu. Lütfen sayfayı yenileyin.');
        return;
    }

    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    fetch(`/raporlar/export?type=${type}&start_date=${startDate}&end_date=${endDate}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // JSON dosyasını indir
            const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = data.filename;
            a.click();
            window.URL.revokeObjectURL(url);
        } else {
            alert('Export sırasında hata oluştu: ' + (data.error || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Dışa aktarma hatası:', error);
        alert('Export sırasında hata oluştu');
    });
}
</script>
@endsection
