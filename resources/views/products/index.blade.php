@extends('layouts.app')

@section('title', 'Depo - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Depo</h1>
            <p class="text-gray-600 mt-1">Sahada kullanılan ürünler ve stok takibi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('products.bulk-stock') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Toplu Stok
            </a>
            <a href="{{ route('products.create') }}"
               class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-semibold text-gray-900">Depo</h2>
            <span class="text-sm text-gray-500">{{ $products->total() }} ürün</span>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Quick Search -->
            <div class="relative">
                <form method="GET" action="{{ route('products.index') }}" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Ürün ara..."
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

            <button type="button" onclick="toggleProductFilters()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtrele
            </button>
        </div>
    </div>

    <!-- Collapsible Filters -->
    <div id="productFilterPanel" class="hidden bg-white rounded-xl p-4 shadow-md border border-gray-200 mb-6">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="yedek_parca" {{ request('category') == 'yedek_parca' ? 'selected' : '' }}>Yedek Parça</option>
                    <option value="arac_gerec" {{ request('category') == 'arac_gerec' ? 'selected' : '' }}>Araç Gereç</option>
                    <option value="kimyasal" {{ request('category') == 'kimyasal' ? 'selected' : '' }}>Kimyasal</option>
                    <option value="elektronik" {{ request('category') == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                    <option value="mekanik" {{ request('category') == 'mekanik' ? 'selected' : '' }}>Mekanik</option>
                </select>
            </div>

            <div class="min-w-0 flex-1 max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Stok Durumu</label>
                <select name="stock_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Stokta Var</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Az Stok</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Stokta Yok</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Uygula
                </button>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
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

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                <!-- Product Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $product->code }}</p>
                    </div>
                    <div class="flex items-center space-x-1">
                        @if($product->is_active)
                            <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                        @else
                            <span class="w-3 h-3 bg-red-400 rounded-full"></span>
                        @endif
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $product->category == 'yedek_parca' ? 'bg-blue-100 text-blue-800' :
                           ($product->category == 'arac_gerec' ? 'bg-green-100 text-green-800' :
                            ($product->category == 'kimyasal' ? 'bg-yellow-100 text-yellow-800' :
                             ($product->category == 'elektronik' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                        {{ $product->category_label }}
                    </span>
                </div>

                <!-- Stock Status -->
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Stok:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $product->stock_status == 'in_stock' ? 'bg-green-100 text-green-800' :
                               ($product->stock_status == 'low_stock' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                            {{ $product->stock_quantity }} {{ $product->unit }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Min: {{ $product->min_stock_level }} {{ $product->unit }}
                    </div>
                </div>

                <!-- Pricing -->
                <div class="mb-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Maliyet:</span>
                        <span class="font-semibold text-red-600">₺{{ number_format($product->cost_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Satış:</span>
                        <span class="font-semibold text-green-600">₺{{ number_format($product->sale_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kar Marjı:</span>
                        <span class="font-semibold text-blue-600">%{{ number_format($product->profit_margin, 1) }}</span>
                    </div>
                </div>

                @if($product->supplier)
                    <div class="mb-4">
                        <div class="text-xs text-gray-500">Tedarikçi:</div>
                        <div class="text-sm font-medium text-gray-700">{{ $product->supplier }}</div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <a href="{{ route('products.show', $product) }}"
                           class="text-blue-600 hover:text-blue-800 transition-colors" title="Görüntüle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('products.edit', $product) }}"
                           class="text-yellow-600 hover:text-yellow-800 transition-colors" title="Düzenle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                          onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Sil">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="text-lg font-medium text-gray-500">Henüz ürün bulunmuyor</p>
                <p class="mt-2 text-gray-400">İlk ürünü eklemek için yukarıdaki butonu kullanın.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>

<script>
function toggleProductFilters() {
    const filterPanel = document.getElementById('productFilterPanel');
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
    const hasFilters = urlParams.has('category') || urlParams.has('stock_status');

    if (hasFilters) {
        document.getElementById('productFilterPanel').classList.remove('hidden');
    }
});
</script>
@endsection
