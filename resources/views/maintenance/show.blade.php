@extends('layouts.app')

@section('title', 'Bakım İşi Detayı - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $maintenance->maintenance_type_label }}</h1>
            <p class="text-gray-600 mt-1">{{ $maintenance->building?->name ?? 'Bina bilgisi bulunamadı' }} - {{ $maintenance->scheduled_date->format('d.m.Y') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('maintenance.edit', $maintenance) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Düzenle
            </a>
            <a href="{{ route('maintenance.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Geri Dön
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana Bilgiler -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Bakım İşi Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bakım Tipi</label>
                        <p class="text-gray-900">{{ $maintenance->maintenance_type_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $maintenance->status === 'tamamlandi' ? 'bg-green-100 text-green-800' :
                               ($maintenance->status === 'baslandi' ? 'bg-blue-100 text-blue-800' :
                               ($maintenance->status === 'atandi' ? 'bg-yellow-100 text-yellow-800' :
                               'bg-gray-100 text-gray-800')) }}">
                            {{ $maintenance->status_label }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Planlanan Tarih</label>
                        <p class="text-gray-900">{{ $maintenance->scheduled_date->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Öncelik</label>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $maintenance->priority === 'acil' ? 'bg-red-100 text-red-800' :
                               ($maintenance->priority === 'yuksek' ? 'bg-orange-100 text-orange-800' :
                               ($maintenance->priority === 'normal' ? 'bg-blue-100 text-blue-800' :
                               'bg-gray-100 text-gray-800')) }}">
                            {{ $maintenance->priority_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bina Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Bina Bilgileri</h2>
                @if($maintenance->building)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bina Adı</label>
                            <p class="text-gray-900">{{ $maintenance->building->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <p class="text-gray-900">{{ $maintenance->building->address }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">İlçe/Şehir</label>
                            <p class="text-gray-900">{{ $maintenance->building->district }}, {{ $maintenance->building->city }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asansör Bilgisi</label>
                            <p class="text-gray-900">{{ $maintenance->building->elevator_count }} adet {{ $maintenance->building->elevator_type_label }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Bina bilgisi bulunamadı</h3>
                            <p class="mt-1 text-sm text-gray-500">Bu bakım işi için bina bilgisi mevcut değil.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Açıklama -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Açıklama</h2>
                <p class="text-gray-900">{{ $maintenance->description }}</p>
            </div>

            <!-- Bakım Raporları -->
            @if($maintenance->maintenanceReports->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Bakım Raporları</h2>
                    <div class="space-y-4">
                        @foreach($maintenance->maintenanceReports as $report)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900">Rapor #{{ $report->id }}</h3>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $report->completion_status === 'tamamlandi' ? 'bg-green-100 text-green-800' :
                                           ($report->completion_status === 'kismi_tamamlandi' ? 'bg-yellow-100 text-yellow-800' :
                                           'bg-red-100 text-red-800') }}">
                                        {{ $report->completion_status_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $report->work_description }}</p>
                                <div class="text-xs text-gray-500">
                                    <span>Başlangıç: {{ $report->start_time ? \Carbon\Carbon::parse($report->start_time)->format('d.m.Y H:i') : 'Belirtilmemiş' }}</span>
                                    @if($report->end_time)
                                        <span class="ml-4">Bitiş: {{ \Carbon\Carbon::parse($report->end_time)->format('d.m.Y H:i') }}</span>
                                    @endif
                                </div>
                                @if($report->total_cost > 0)
                                    <div class="mt-2">
                                        <span class="text-sm font-semibold text-green-600">Toplam Maliyet: ₺{{ number_format($report->total_cost, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Yan Panel -->
        <div class="space-y-6">
            <!-- Atanan Personel -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Atanan Personel</h2>
                @if($maintenance->assignedEmployee)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900">{{ $maintenance->assignedEmployee->first_name }} {{ $maintenance->assignedEmployee->last_name }}</h3>
                        <p class="text-sm text-gray-600 mb-1">{{ $maintenance->assignedEmployee->position_label }}</p>
                        <p class="text-sm text-gray-600 mb-1">{{ $maintenance->assignedEmployee->email }}</p>
                        <p class="text-sm text-gray-600">{{ $maintenance->assignedEmployee->phone }}</p>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Henüz personel atanmamış.</p>
                @endif
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
                <div class="space-y-3">
                    @if($maintenance->maintenanceReport)
                        <div class="mb-3 p-3 rounded-lg border {{ ($maintenance->maintenanceReport->approval_status ?? '') === 'onay_bekliyor' ? 'bg-orange-50 border-orange-200' : 'bg-green-50 border-green-200' }}">
                            <p class="text-sm font-semibold text-gray-900">Bina yöneticisi onayı</p>
                            <p class="text-sm mt-1">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ ($maintenance->maintenanceReport->approval_status ?? '') === 'onay_bekliyor' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $maintenance->maintenanceReport->approval_status_label }}
                                </span>
                            </p>
                            @if($maintenance->maintenanceReport->approved_by_name)
                                <p class="text-xs text-gray-600 mt-2">Onaylayan: {{ $maintenance->maintenanceReport->approved_by_name }}</p>
                                @if($maintenance->maintenanceReport->approved_at)
                                    <p class="text-xs text-gray-500">{{ $maintenance->maintenanceReport->approved_at->format('d.m.Y H:i') }}</p>
                                @endif
                            @endif
                        </div>
                        @if(($maintenance->maintenanceReport->approval_status ?? '') === 'onay_bekliyor')
                            <form action="{{ route('maintenance.resend-approval-sms', $maintenance) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200">
                                    Onay SMS Tekrar Gönder
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('maintenance.report', $maintenance) }}"
                           class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            📋 Raporu Görüntüle
                        </a>
                    @elseif($maintenance->status == 'baslandi' || $maintenance->status == 'tamamlandi')
                        <div class="w-full bg-gray-300 text-gray-600 font-semibold py-3 px-4 rounded-xl flex items-center justify-center cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            ⏳ Rapor Henüz Oluşturulmadı
                        </div>
                    @endif
                    <a href="{{ route('maintenance.edit', $maintenance) }}"
                       class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Düzenle
                    </a>
                    @if($maintenance->assignedEmployee && $maintenance->status !== 'tamamlandi')
                        <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST" class="inline w-full"
                              onsubmit="return confirm('Bu bakım işini silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Sil
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- İş İstatistikleri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">İş İstatistikleri</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oluşturulma Tarihi</label>
                        <p class="text-gray-900">{{ $maintenance->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Son Güncelleme</label>
                        <p class="text-gray-900">{{ $maintenance->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
