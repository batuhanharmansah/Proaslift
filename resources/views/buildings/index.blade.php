@extends('layouts.app')

@section('title', 'Bina Yönetimi - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bina Yönetimi</h1>
            <p class="text-gray-600 mt-1">Bakım yapılan binalar ve sözleşme bilgileri</p>
        </div>
        <a href="{{ route('buildings.create') }}"
           class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Yeni Bina Ekle
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- DEBUG: Bina sayısı ve kullanıcı bilgisi -->
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6">
        <strong>DEBUG:</strong>
        Toplam Bina: {{ $buildings->total() }} |
        Kullanıcı ID: {{ auth()->id() }} |
        Company ID: {{ auth()->user()->company_id ?? 'NULL' }} |
        Sayfa: {{ $buildings->currentPage() }} / {{ $buildings->lastPage() }}
    </div>

    <!-- Live Search Bar -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                       id="liveSearch"
                       placeholder="Bina adı, adres veya ilçe ile arama yapın (min. 2 karakter)..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" id="searchLoader" style="display: none;">
                    <svg class="animate-spin h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <button type="button"
                    id="clearSearch"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center"
                    style="display: none;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Temizle
            </button>
        </div>
        <div id="searchInfo" class="mt-4 text-sm text-gray-600" style="display: none;"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Bina</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Asansör</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Sözleşme</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Aylık Ücret</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($buildings as $building)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $building->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $building->district }}, {{ $building->city }}</div>
                                    @if($building->primaryContact)
                                        <div class="text-xs text-blue-600">{{ $building->primaryContact->name }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div>{{ $building->elevator_count }} adet</div>
                                    <div class="text-gray-500">{{ $building->elevator_type_label }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div>{{ $building->contract_type_label }}</div>
                                    <div class="text-gray-500">{{ $building->contract_end_date->format('d.m.Y') }} bitim</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                ₺{{ number_format($building->monthly_fee, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $building->status === 'aktif' ? 'bg-green-100 text-green-800' :
                                       ($building->status === 'beklemede' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $building->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('buildings.show', $building) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('buildings.edit', $building) }}" class="text-yellow-600 hover:text-yellow-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Henüz bina bulunmuyor. İlk binayı eklemek için yukarıdaki butonu kullanın.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($buildings->hasPages())
            <div class="px-6 py-4 border-t">{{ $buildings->links() }}</div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('liveSearch');
    const clearButton = document.getElementById('clearSearch');
    const searchLoader = document.getElementById('searchLoader');
    const searchInfo = document.getElementById('searchInfo');
    const tableContainer = document.querySelector('.bg-white.rounded-2xl.shadow-lg.border.border-gray-100.overflow-hidden');

    let searchTimeout;

    // Live search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Show/hide clear button
        if (searchTerm.length > 0) {
            clearButton.style.display = 'flex';
        } else {
            clearButton.style.display = 'none';
            searchInfo.style.display = 'none';
            // If search is cleared, immediately show all results
            performSearch('');
            return;
        }

        // Only search if minimum 2 characters entered
        if (searchTerm.length < 2) {
            return;
        }

        // Debounce search - wait 300ms after user stops typing (faster for better UX)
        searchTimeout = setTimeout(function() {
            performSearch(searchTerm);
        }, 300);
    });

    // Clear search functionality
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        clearButton.style.display = 'none';
        searchInfo.style.display = 'none';
        performSearch('');
    });

    // Perform search function
    function performSearch(searchTerm) {
        // Show loading indicator
        searchLoader.style.display = 'flex';

        // Build URL with search parameter
        const url = new URL(window.location.href);
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }

        // Perform AJAX request - only fetch table content for better performance
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ağ yanıtı başarısız.');
            }
            return response.text();
        })
        .then(html => {
            // Directly replace table content (since we're getting only the table from partial)
            tableContainer.innerHTML = html;

            // Update search info
            if (searchTerm) {
                const totalResults = tableContainer.querySelectorAll('tbody tr:not(.no-results)').length;
                searchInfo.innerHTML = `<strong>"${searchTerm}"</strong> için <strong>${totalResults}</strong> sonuç bulundu.`;
                searchInfo.style.display = 'block';
            } else {
                searchInfo.style.display = 'none';
            }

            // Hide loading indicator
            searchLoader.style.display = 'none';

            // Update URL without page reload
            window.history.replaceState({}, '', url.toString());
        })
        .catch(error => {
            console.error('Search error:', error);
            searchLoader.style.display = 'none';

            // Show error message
            searchInfo.innerHTML = 'Arama sırasında bir hata oluştu. Lütfen tekrar deneyin.';
            searchInfo.style.display = 'block';
            searchInfo.className = 'mt-4 text-sm text-red-600';
        });
    }
});
</script>
@endsection
