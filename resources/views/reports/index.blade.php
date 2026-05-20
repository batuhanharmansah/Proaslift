@extends('layouts.app')

@section('title', 'Raporlar - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Raporlar</h1>
        <p class="text-gray-600 mt-2">Detaylı analiz ve raporlar</p>
    </div>

    <!-- Sekmeler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
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
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 text-sm font-medium">
                    Müşteri Analizleri
                </a>
            </nav>
        </div>

        <div class="p-6">
            <!-- Hızlı İstatistikler -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-blue-100 text-sm font-medium">Bu Ay Gelir</p>
                            <p class="text-2xl font-bold">{{ number_format($monthlyIncome ?? 0, 0, ',', '.') }} ₺</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-red-100 text-sm font-medium">Bu Ay Gider</p>
                            <p class="text-2xl font-bold">{{ number_format($monthlyExpense ?? 0, 0, ',', '.') }} ₺</p>
                        </div>
                        <div class="w-12 h-12 bg-red-400 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-green-100 text-sm font-medium">Tamamlanan Bakım</p>
                            <p class="text-2xl font-bold">{{ $completedMaintenance ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-400 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-purple-100 text-sm font-medium">Aktif Müşteri</p>
                            <p class="text-2xl font-bold">{{ $activeBuildings ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-400 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rapor Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Finansal Raporlar -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Finansal Raporlar</h3>
                        <a href="{{ route('reports.financial') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Detayları Gör →
                        </a>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Gelir-gider analizi, aylık trendler ve kategori bazlı raporlar</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Gelir: {{ number_format($monthlyIncome ?? 0, 0, ',', '.') }} ₺
                    </div>
                </div>

                <!-- Bakım Performans -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Bakım Performans</h3>
                        <a href="{{ route('reports.maintenance') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Detayları Gör →
                        </a>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Bakım tamamlanma oranları, personel performansı ve trend analizleri</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tamamlanma: %{{ number_format($completionRate ?? 0, 1) }}
                    </div>
                </div>

                <!-- Personel Verimlilik -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Personel Verimlilik</h3>
                        <a href="{{ route('reports.employee') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Detayları Gör →
                        </a>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Personel performans analizi ve pozisyon bazlı raporlar</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Aktif Personel: {{ $totalEmployees ?? 0 }}
                    </div>
                </div>

                <!-- Müşteri Analizleri -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Müşteri Analizleri</h3>
                        <a href="{{ route('reports.customer') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Detayları Gör →
                        </a>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Bina analizleri, sözleşme durumları ve gelir dağılımları</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Aktif Binalar: {{ $activeBuildings ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport(type) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!csrfToken) {
        console.error('CSRF anahtarı bulunamadı');
        alert('Sayfa yüklenirken hata oluştu. Lütfen sayfayı yenileyin.');
        return;
    }

    const startDate = new Date().toISOString().split('T')[0].substring(0, 7) + '-01';
    const endDate = new Date().toISOString().split('T')[0];

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
            alert('Dışa aktarma sırasında hata oluştu: ' + (data.error || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Dışa aktarma hatası:', error);
        alert('Dışa aktarma sırasında hata oluştu');
    });
}
</script>
@endsection
