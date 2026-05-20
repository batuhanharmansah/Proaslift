@extends('employee.layouts.app')

@section('title', 'Personel Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Personel Çalışma Paneli</h1>
        <p class="text-gray-600 mt-1">{{ now('Europe/Istanbul')->locale('tr')->isoFormat('D MMMM Y, dddd') }} - Günlük iş durumu</p>
    </div>

    <!-- Compact Quick Stats -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bugünkü İşler</p>
                <p class="text-sm font-bold text-blue-600 truncate">{{ $stats['today_tasks_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bu Ay Tamamlanan</p>
                <p class="text-sm font-bold text-green-600 truncate">{{ $stats['completed_this_month'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bekleyen Raporlar</p>
                <p class="text-sm font-bold text-orange-600 truncate">{{ $stats['pending_reports_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Ort. Tamamlanma</p>
                <p class="text-sm font-bold text-purple-600 truncate">{{ round($stats['average_completion_time']) }} dk</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Bugünkü İşlerim (Ana Panel) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Bugünkü İşlerim</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ now('Europe/Istanbul')->format('d.m.Y') }} - Atanan bakım işleri</p>
                    </div>
                    <a href="{{ route('employee.maintenance.index') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Tüm İşlerim
                    </a>
                </div>
            </div>
            <div class="p-6">
                @forelse($todayTasks as $task)
                    <div class="flex items-center justify-between p-4 mb-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                {{ $task->priority == 'acil' ? 'bg-red-100 text-red-600' :
                                   ($task->priority == 'yuksek' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $task->building->name }}</div>
                                <div class="text-sm text-gray-600">{{ $task->maintenance_type_label }}</div>
                                <div class="text-xs text-gray-500">{{ $task->scheduled_time ?? 'Saat belirtilmemiş' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $task->status == 'baslandi' ? 'bg-blue-100 text-blue-800' :
                                       ($task->status == 'atandi' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $task->status_label }}
                                </span>
                            </div>
                            @if($task->status == 'atandi')
                                <a href="{{ route('employee.maintenance.create-report', $task->id) }}" class="mt-2 inline-block text-xs text-primary-600 hover:text-primary-800">
                                    İşe Başla →
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Bugün iş yok!</h3>
                        <p class="text-gray-600">Bugün size atanan bakım işi bulunmuyor.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sağ Panel -->
        <div class="space-y-6">
            <!-- Bu Hafta İşlerim -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Bu Hafta İşlerim</h3>
                <div class="space-y-3">
                    @forelse($thisWeekTasks->take(5) as $task)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold text-xs">{{ $task->scheduled_date->setTimezone('Europe/Istanbul')->format('d') }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm">{{ $task->building->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $task->maintenance_type_label }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold {{ $task->status == 'tamamlandi' ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ $task->status_label }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">Bu hafta iş yok</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Bekleyen Raporlar -->
            @if($pendingReports->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-yellow-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Bekleyen Raporlar
                </h3>
                <div class="space-y-3">
                    @foreach($pendingReports->take(3) as $report)
                        <div class="bg-white p-3 rounded-lg border border-yellow-200">
                            <div class="font-medium text-yellow-800">{{ $report->building->name }}</div>
                            <div class="text-sm text-yellow-600">{{ $report->maintenance_type_label }}</div>
                            <div class="text-xs text-yellow-500 mt-1">{{ $report->scheduled_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}</div>
                            <a href="{{ route('employee.maintenance.create-report', $report->id) }}" class="mt-2 inline-block text-xs text-yellow-700 hover:text-yellow-900">
                                Rapor Oluştur →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Son Kullandığım Ürünler -->
            @if($recentProducts->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Son Kullanılan Ürünler
                </h3>
                <div class="space-y-2">
                    @foreach($recentProducts->take(4) as $product)
                        <div class="flex items-center justify-between text-sm">
                            <div class="text-blue-800">{{ $product->name }}</div>
                            <div class="text-blue-600 font-medium">
                                {{ $product->stock_quantity }} {{ $product->unit }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Alt Panel: Performans Grafiği ve Tamamlanan İşler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Performans Grafiği -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Son 7 Gün Performansım</h3>
            <div class="h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Son Tamamlanan İşler -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Son Tamamlanan İşler</h3>
            <div class="space-y-4">
                @forelse($completedTasks->take(5) as $task)
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <div class="text-sm text-green-600 font-medium">{{ $task->maintenanceSchedule->building->name }}</div>
                            <div class="text-xs text-green-500">{{ $task->created_at->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500">Henüz tamamlanan iş yok</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hızlı Eylemler -->
    <div class="mt-8 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold mb-2">Hızlı İşlemler</h3>
                <p class="text-green-100">Sık kullanılan işlemlere hızlı erişim</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('employee.maintenance.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl font-semibold transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    İşlerim
                </a>
                <a href="{{ route('employee.maintenance.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl font-semibold transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Rapor Oluştur
                </a>
                <a href="{{ route('profile.edit') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl font-semibold transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profilim
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Chart.js'i lazy loading ile yükle
    await window.loadChartJS();

    const ctx = document.getElementById('performanceChart').getContext('2d');
    const chartData = @json($performanceChart);

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection
