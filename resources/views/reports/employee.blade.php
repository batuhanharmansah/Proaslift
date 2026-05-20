@extends('layouts.app')

@section('title', 'Personel Verimlilik Raporları - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Personel Verimlilik Raporları</h1>
        <p class="text-gray-600 mt-2">Personel performans analizi ve verimlilik raporları</p>
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
                   class="border-b-2 border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                    Personel Verimlilik
                </a>
                <a href="{{ route('reports.customer') }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 text-sm font-medium">
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
                <button type="button" onclick="exportReport('employee')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Export Et
                </button>
            </div>
        </form>
    </div>

    <!-- En İyi Performans Gösterenler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">En İyi Performans Gösterenler</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($topPerformers as $employee)
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-lg">{{ substr($employee->full_name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ $employee->full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $employee->position_label }}</p>
                        <p class="text-sm font-medium text-green-600">%{{ number_format($employee->completion_rate, 1) }} Tamamlanma</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pozisyon Bazlı Performans -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pozisyon Bazlı Performans</h3>
            <canvas id="positionPerformanceChart" width="400" height="200"></canvas>
        </div>

        <!-- Performans Dağılımı -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performans Dağılımı</h3>
            <canvas id="performanceDistributionChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Detaylı Personel Analizi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detaylı Personel Analizi</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pozisyon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam İş</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamamlanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamamlanma Oranı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zamanında Tamamlanma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performans</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employees as $employee)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $employee->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee->position_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->total_jobs }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->completed_jobs }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">%{{ number_format($employee->completion_rate, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">%{{ number_format($employee->on_time_rate, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($employee->completion_rate >= 90)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Mükemmel
                                </span>
                            @elseif($employee->completion_rate >= 75)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    İyi
                                </span>
                            @elseif($employee->completion_rate >= 50)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Orta
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Düşük
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pozisyon Bazlı Performans Grafiği
const positionCtx = document.getElementById('positionPerformanceChart').getContext('2d');
new Chart(positionCtx, {
    type: 'bar',
    data: {
        labels: @json($performanceByPosition->pluck('position')),
        datasets: [{
            label: 'Ortalama Tamamlanma Oranı (%)',
            data: @json($performanceByPosition->map(function($item) {
                return $item->total_jobs > 0 ? round(($item->completed_jobs / $item->total_jobs) * 100, 1) : 0;
            })),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
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
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// Performans Dağılımı Grafiği
const distributionCtx = document.getElementById('performanceDistributionChart').getContext('2d');
new Chart(distributionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Mükemmel (90%+)', 'İyi (75-89%)', 'Orta (50-74%)', 'Düşük (<50%)'],
        datasets: [{
            data: [
                {{ $employees->where('completion_rate', '>=', 90)->count() }},
                {{ $employees->whereBetween('completion_rate', [75, 89])->count() }},
                {{ $employees->whereBetween('completion_rate', [50, 74])->count() }},
                {{ $employees->where('completion_rate', '<', 50)->count() }}
            ],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
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
