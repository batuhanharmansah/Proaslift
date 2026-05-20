@extends('layouts.app')

@section('title', 'Asansör Etiket Takibi')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏷️ Asansör Etiket Takibi</h1>
                <p class="text-gray-600 mt-1">Periyodik kontrol etiketleri ve süre takibi</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('elevator-labels.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    ➕ Yeni Etiket
                </a>
                <a href="{{ route('elevator-labels.report') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    📊 Rapor Al
                </a>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <div class="w-6 h-6 bg-green-500 rounded-full"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Yeşil Etiket</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['label_distribution']['yesil'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <div class="w-6 h-6 bg-blue-500 rounded-full"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Mavi Etiket</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['label_distribution']['mavi'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <div class="w-6 h-6 bg-yellow-500 rounded-full"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Sarı Etiket</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['label_distribution']['sari'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <div class="w-6 h-6 bg-red-500 rounded-full"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Kırmızı Etiket</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['label_distribution']['kirmizi'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kritik Durumlar -->
    @if($stats['overdue'] > 0 || $stats['due_soon']['7_days'] > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Dikkat Gereken Durumlar</h3>
                <div class="mt-2 text-sm text-red-700">
                    @if($stats['overdue'] > 0)
                        <p>🚨 {{ $stats['overdue'] }} asansörün kontrolü gecikmiş</p>
                    @endif
                    @if($stats['due_soon']['7_days'] > 0)
                        <p>⚠️ {{ $stats['due_soon']['7_days'] }} asansörün kontrolü 7 gün içinde bitiyor</p>
                    @endif
                    @if($stats['sealing_candidates'] > 0)
                        <p>🔒 {{ $stats['sealing_candidates'] }} asansör mühürleme adayı</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Asansör Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Asansör Etiket Durumları</h3>
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
                            Kalan Gün
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
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
                                @php $remainingDays = $building->activeLabel->getRemainingDays(); @endphp
                                <span class="text-sm font-medium
                                    @if($remainingDays < 0) text-red-600
                                    @elseif($remainingDays <= 7) text-orange-600
                                    @elseif($remainingDays <= 30) text-yellow-600
                                    @else text-green-600 @endif">
                                    @if($remainingDays < 0)
                                        {{ abs($remainingDays) }} gün geç
                                    @else
                                        {{ $remainingDays }} gün
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($building->activeLabel->status === 'aktif') bg-green-100 text-green-800
                                    @elseif($building->activeLabel->status === 'tamamlandi') bg-gray-100 text-gray-800
                                    @elseif($building->activeLabel->status === 'muhurlendi') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $building->activeLabel->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('elevator-labels.show', $building->activeLabel) }}"
                                   class="text-blue-600 hover:text-blue-900">Detay</a>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Henüz etiket kaydı bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($buildings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $buildings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
