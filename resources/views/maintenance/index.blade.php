@extends('layouts.app')

@section('title', 'Bakım Takibi - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bakım Takibi</h1>
            <p class="text-gray-600 mt-1">Planlanan bakım işleri ve takip</p>
        </div>
        <a href="{{ route('maintenance.create') }}"
           class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Yeni Bakım Planla
        </a>
    </div>

    <!-- Compact Filters -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-semibold text-gray-900">Bakım Listesi</h2>
            <span class="text-sm text-gray-500">{{ $maintenances->total() }} kayıt</span>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Quick Filter Toggle -->
            <button type="button" onclick="toggleFilters()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtrele
            </button>
        </div>
    </div>

    <!-- Collapsible Filters -->
    <div id="filterPanel" class="hidden bg-white rounded-xl p-4 shadow-md border border-gray-200 mb-6">
        <form method="GET" action="{{ route('maintenance.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Durum</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="planli" {{ request('status') == 'planli' ? 'selected' : '' }}>Planlı</option>
                    <option value="atandi" {{ request('status') == 'atandi' ? 'selected' : '' }}>Atandı</option>
                    <option value="baslandi" {{ request('status') == 'baslandi' ? 'selected' : '' }}>Başlandı</option>
                    <option value="tamamlandi" {{ request('status') == 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Öncelik</label>
                <select name="priority" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="dusuk" {{ request('priority') == 'dusuk' ? 'selected' : '' }}>Düşük</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="yuksek" {{ request('priority') == 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                    <option value="acil" {{ request('priority') == 'acil' ? 'selected' : '' }}>Acil</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Onay</label>
                <select name="approval_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="onay_bekliyor" {{ request('approval_status') == 'onay_bekliyor' ? 'selected' : '' }}>Onay Bekleniyor</option>
                    <option value="onaylandi" {{ request('approval_status') == 'onaylandi' ? 'selected' : '' }}>Onaylandı</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Bina</label>
                <select name="building_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Uygula
                </button>
                <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
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

    <!-- Maintenance Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Bina</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Bakım Türü</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Personel</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Öncelik</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Onay</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($maintenances as $maintenance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $maintenance->building->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $maintenance->building->district }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $maintenance->maintenance_type == 'rutin_bakim' ? 'bg-blue-100 text-blue-800' :
                                       ($maintenance->maintenance_type == 'ariza_onarim' ? 'bg-red-100 text-red-800' :
                                        ($maintenance->maintenance_type == 'periyodik_kontrol' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800')) }}">
                                    {{ $maintenance->maintenance_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium">{{ $maintenance->scheduled_date->format('d.m.Y') }}</div>
                                    @if($maintenance->scheduled_time)
                                        <div class="text-gray-500">{{ $maintenance->scheduled_time }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($maintenance->assignedEmployee)
                                    <div class="text-sm">
                                        <div class="font-medium">{{ $maintenance->assignedEmployee->full_name }}</div>
                                        <div class="text-gray-500">{{ $maintenance->assignedEmployee->position_label }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Atanmadı</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $maintenance->priority == 'acil' ? 'bg-red-100 text-red-800' :
                                       ($maintenance->priority == 'yuksek' ? 'bg-orange-100 text-orange-800' :
                                        ($maintenance->priority == 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $maintenance->priority_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $maintenance->status == 'tamamlandi' ? 'bg-green-100 text-green-800' :
                                       ($maintenance->status == 'baslandi' ? 'bg-blue-100 text-blue-800' :
                                        ($maintenance->status == 'atandi' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $maintenance->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($maintenance->maintenanceReport)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ ($maintenance->maintenanceReport->approval_status ?? 'onaylandi') === 'onay_bekliyor' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $maintenance->maintenanceReport->approval_status_label ?? 'Onaylandı' }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('maintenance.show', $maintenance) }}" class="text-blue-600 hover:text-blue-800" title="Görüntüle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if($maintenance->maintenanceReport)
                                        <a href="{{ route('maintenance.report', $maintenance) }}" class="text-green-600 hover:text-green-800" title="Raporu Görüntüle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    @elseif($maintenance->status == 'baslandi' || $maintenance->status == 'tamamlandi')
                                        <span class="text-gray-400 cursor-not-allowed" title="Rapor henüz oluşturulmamış">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </span>
                                    @endif
                                    <a href="{{ route('maintenance.edit', $maintenance) }}" class="text-yellow-600 hover:text-yellow-800" title="Düzenle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Henüz bakım işi bulunmuyor. İlk bakımı planlamak için yukarıdaki butonu kullanın.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($maintenances->hasPages())
            <div class="px-6 py-4 border-t">{{ $maintenances->links() }}</div>
        @endif
    </div>
</div>

<script>
function toggleFilters() {
    const filterPanel = document.getElementById('filterPanel');
    const isHidden = filterPanel.classList.contains('hidden');

    if (isHidden) {
        filterPanel.classList.remove('hidden');
        // Smooth slide down animation
        filterPanel.style.opacity = '0';
        filterPanel.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterPanel.style.transition = 'all 0.3s ease';
            filterPanel.style.opacity = '1';
            filterPanel.style.transform = 'translateY(0)';
        }, 10);
    } else {
        // Smooth slide up animation
        filterPanel.style.transition = 'all 0.3s ease';
        filterPanel.style.opacity = '0';
        filterPanel.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterPanel.classList.add('hidden');
            filterPanel.style.transition = '';
        }, 300);
    }
}

// Auto-hide filters if no active filters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('status') || urlParams.has('priority') || urlParams.has('building_id');

    if (hasFilters) {
        document.getElementById('filterPanel').classList.remove('hidden');
    }
});
</script>
@endsection
