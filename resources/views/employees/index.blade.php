@extends('layouts.app')

@section('title', 'Personel Yönetimi - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Personel Yönetimi</h1>
            <p class="text-gray-600 mt-1">Çalışan bilgileri ve personel yönetimi</p>
        </div>
        <a href="{{ route('employees.create') }}"
           class="mt-4 sm:mt-0 bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 transform hover:scale-105 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Yeni Personel Ekle
        </a>
    </div>

    <!-- Yeni Personel Giriş Bilgileri -->
    @if(session('new_employee_info'))
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-medium text-blue-800">Yeni Personel Giriş Bilgileri</h3>
                    <div class="mt-3 bg-white rounded-lg p-4 border border-blue-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <strong class="text-gray-700">Personel:</strong> {{ session('new_employee_info')['employee_name'] }}
                            </div>
                            <div>
                                <strong class="text-gray-700">Pozisyon:</strong> {{ session('new_employee_info')['position'] }}
                            </div>
                            <div>
                                <strong class="text-gray-700">Email:</strong>
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ session('new_employee_info')['email'] }}</code>
                            </div>
                            <div>
                                <strong class="text-gray-700">Geçici şifre:</strong>
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ session('new_employee_info')['password'] }}</code>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <strong>⚠️ Önemli:</strong> Personel, email adresi ve yukarıdaki geçici şifre ile sisteme giriş yapabilir. Bu bilgileri güvenli bir şekilde personele iletin. İlk girişten sonra şifresini değiştirmesini önerin.
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button onclick="this.parentElement.parentElement.parentElement.style.display='none'"
                                class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            Bu bildirimi kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{ session()->forget('new_employee_info') }}
    @endif

    <!-- Compact Filters -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-semibold text-gray-900">Personel Listesi</h2>
            <span class="text-sm text-gray-500">{{ $employees->total() }} kayıt</span>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Quick Search -->
            <div class="relative">
                <form method="GET" action="{{ route('employees.index') }}" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Personel ara..."
                           class="w-64 px-4 py-2 pl-10 text-sm border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-r-lg hover:bg-primary-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <button type="button" onclick="toggleEmployeeFilters()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span id="filterToggleText">Filtreleri Gizle</span>
            </button>
        </div>
    </div>

    <!-- Collapsible Filters -->
    <div id="employeeFilterPanel" class="bg-white rounded-xl p-4 shadow-md border border-gray-200 mb-6">
        <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Pozisyon</label>
                <select name="position" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="teknisyen" {{ request('position') == 'teknisyen' ? 'selected' : '' }}>Teknisyen</option>
                    <option value="usta" {{ request('position') == 'usta' ? 'selected' : '' }}>Usta</option>
                    <option value="muhendis" {{ request('position') == 'muhendis' ? 'selected' : '' }}>Mühendis</option>
                    <option value="yonetici" {{ request('position') == 'yonetici' ? 'selected' : '' }}>Yönetici</option>
                    <option value="muhasebe" {{ request('position') == 'muhasebe' ? 'selected' : '' }}>Muhasebe</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Durum</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Uygula
                </button>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Employees Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Personel</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">İletişim</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pozisyon</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Maaş</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">İşe Başlama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-primary-600 font-semibold text-sm">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                <div class="text-xs text-gray-500">{{ $employee->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($employee->position == 'yonetici') bg-purple-100 text-purple-800
                                    @elseif($employee->position == 'muhendis') bg-blue-100 text-blue-800
                                    @elseif($employee->position == 'usta') bg-green-100 text-green-800
                                    @elseif($employee->position == 'teknisyen') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $employee->position_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                ₺{{ number_format($employee->salary, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $employee->hire_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($employee->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Pasif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('employees.profile', $employee) }}"
                                       class="text-green-600 hover:text-green-800 transition-colors" title="Profil">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('employees.show', $employee) }}"
                                       class="text-blue-600 hover:text-blue-800 transition-colors" title="Görüntüle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}"
                                       class="text-yellow-600 hover:text-yellow-800 transition-colors" title="Düzenle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                          onsubmit="return confirm('Bu personeli silmek istediğinizden emin misiniz?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Sil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Henüz personel bulunmuyor</p>
                                    <p class="mt-2">İlk personeli eklemek için yukarıdaki butonu kullanın.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleEmployeeFilters() {
    const filterPanel = document.getElementById('employeeFilterPanel');
    const toggleText = document.getElementById('filterToggleText');
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
        toggleText.textContent = 'Filtreleri Gizle';
    } else {
        filterPanel.style.transition = 'all 0.3s ease';
        filterPanel.style.opacity = '0';
        filterPanel.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterPanel.classList.add('hidden');
            filterPanel.style.transition = '';
        }, 300);
        toggleText.textContent = 'Filtrele';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('position') || urlParams.has('status');
    const toggleText = document.getElementById('filterToggleText');

    if (hasFilters) {
        document.getElementById('employeeFilterPanel').classList.remove('hidden');
        toggleText.textContent = 'Filtreleri Gizle';
    } else {
        toggleText.textContent = 'Filtrele';
    }
});
</script>
@endsection
