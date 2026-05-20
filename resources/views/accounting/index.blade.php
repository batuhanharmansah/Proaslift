@extends('layouts.app')

@section('title', 'Muhasebe - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Muhasebe</h1>
            <p class="text-gray-600 mt-1">Gelir-gider takibi ve finansal yönetim</p>
        </div>
        <a href="{{ route('accounting.create') }}"
           class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Yeni Kayıt Ekle
        </a>
    </div>

    <!-- Compact Stats Cards -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bu Ay Gelir</p>
                <p class="text-sm font-bold text-green-600 truncate">₺{{ number_format($stats['monthly_income'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bu Ay Gider</p>
                <p class="text-sm font-bold text-red-600 truncate">₺{{ number_format($stats['monthly_expense'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bu Ay KDV</p>
                <p class="text-sm font-bold text-blue-600 truncate">₺{{ number_format($stats['monthly_vat'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bekleyen Ödemeler</p>
                <p class="text-sm font-bold text-orange-600 truncate">{{ $stats['pending_payments'] }}</p>
            </div>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-semibold text-gray-900">Muhasebe Kayıtları</h2>
            <span class="text-sm text-gray-500">{{ $entries->total() }} kayıt</span>
        </div>

        <div class="flex items-center space-x-3">
            <button type="button" onclick="toggleAccountingFilters()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtrele
            </button>
        </div>
    </div>

    <!-- Collapsible Filters -->
    <div id="accountingFilterPanel" class="hidden bg-white rounded-xl p-4 shadow-md border border-gray-200 mb-6">
        <form method="GET" action="{{ route('accounting.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tür</label>
                <select name="type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="gelir" {{ request('type') == 'gelir' ? 'selected' : '' }}>Gelir</option>
                    <option value="gider" {{ request('type') == 'gider' ? 'selected' : '' }}>Gider</option>
                    <option value="maas" {{ request('type') == 'maas' ? 'selected' : '' }}>Maaş</option>
                    <option value="vergi" {{ request('type') == 'vergi' ? 'selected' : '' }}>Vergi</option>
                    <option value="sigorta" {{ request('type') == 'sigorta' ? 'selected' : '' }}>Sigorta</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Durum</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="beklemede" {{ request('status') == 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                    <option value="odendi" {{ request('status') == 'odendi' ? 'selected' : '' }}>Ödendi</option>
                    <option value="tahsil_edildi" {{ request('status') == 'tahsil_edildi' ? 'selected' : '' }}>Tahsil Edildi</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Başlangıç</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Bitiş</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Uygula
                </button>
                <a href="{{ route('accounting.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Temizle
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Accounting Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tür</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Açıklama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tutar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">KDV</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Toplam</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $entry->transaction_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $entry->type == 'gelir' ? 'bg-green-100 text-green-800' :
                                       ($entry->type == 'gider' ? 'bg-red-100 text-red-800' :
                                        ($entry->type == 'maas' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $entry->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $entry->category }}</div>
                                    <div class="text-gray-500">{{ Str::limit($entry->description, 50) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold
                                {{ $entry->type == 'gelir' ? 'text-green-600' : 'text-red-600' }}">
                                ₺{{ number_format($entry->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                ₺{{ number_format($entry->vat_amount, 2) }}
                                <div class="text-xs text-gray-400">(%{{ $entry->vat_rate }})</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold
                                {{ $entry->type == 'gelir' ? 'text-green-600' : 'text-red-600' }}">
                                ₺{{ number_format($entry->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $entry->status == 'odendi' || $entry->status == 'tahsil_edildi' ? 'bg-green-100 text-green-800' :
                                       ($entry->status == 'beklemede' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $entry->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('accounting.show', $entry) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('accounting.edit', $entry) }}" class="text-yellow-600 hover:text-yellow-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Henüz muhasebe kaydı bulunmuyor. İlk kaydı eklemek için yukarıdaki butonu kullanın.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="px-6 py-4 border-t">{{ $entries->links() }}</div>
        @endif
    </div>
</div>

<script>
function toggleAccountingFilters() {
    const filterPanel = document.getElementById('accountingFilterPanel');
    const isHidden = filterPanel.classList.contains('hidden');

    if (isHidden) {
        filterPanel.classList.remove('hidden');
        filterPanel.style.opacity = '0';
        filterPanel.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterPanel.style.transition = 'all 0.3s ease';
            filterPanel.style.opacity = '1';
            filterPanel.style.transform = 'translateY(0)';
        }, 10);
    } else {
        filterPanel.style.transition = 'all 0.3s ease';
        filterPanel.style.opacity = '0';
        filterPanel.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterPanel.classList.add('hidden');
            filterPanel.style.transition = '';
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('type') || urlParams.has('status') || urlParams.has('date_from') || urlParams.has('date_to');

    if (hasFilters) {
        document.getElementById('accountingFilterPanel').classList.remove('hidden');
    }
});
</script>
@endsection
