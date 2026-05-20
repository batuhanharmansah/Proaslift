@extends('layouts.app')

@section('title', 'Toplu Stok Güncelleme - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Toplu Stok Güncelleme</h1>
            </div>
            <p class="text-gray-600 mt-1">Ürün stok miktarlarını toplu olarak güncelleyin ve raporlayın</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('products.bulk-stock.export-excel') }}"
               class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Excel İndir
            </a>
            <a href="{{ route('products.bulk-stock.export-pdf') }}"
               class="bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF İndir
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Info Card -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-2">Toplu Stok Güncelleme</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Aşağıdaki tabloda tüm ürünlerinizin mevcut stok durumlarını görebilirsiniz</li>
                    <li>Stok miktarlarını değiştirdikten sonra "Kaydet" butonuna tıklayarak güncelleyin</li>
                    <li>Excel veya PDF formatında rapor alabilirsiniz</li>
                    <li>Renkli etiketler stok durumunu gösterir: 🟢 Yeterli, 🟠 Az Stok, 🔴 Stokta Yok</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Toplam Ürün</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $products->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stokta Var</p>
                    <p class="text-2xl font-bold text-green-600">{{ $products->where('stock_status', 'in_stock')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Az Stok</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $products->where('stock_status', 'low_stock')->count() }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stokta Yok</p>
                    <p class="text-2xl font-bold text-red-600">{{ $products->where('stock_status', 'out_of_stock')->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Stock Form -->
    <form method="POST" action="{{ route('products.bulk-update-stock') }}" class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        @csrf

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ürün Markası</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ürün Adı</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Maliyet (₺)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Mevcut Stok</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Min. Stok</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Yeni Stok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tedarikçi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm text-gray-900">{{ $product->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="font-semibold text-red-600">₺{{ number_format($product->cost_price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $product->category == 'yedek_parca' ? 'bg-blue-100 text-blue-800' :
                                   ($product->category == 'arac_gerec' ? 'bg-green-100 text-green-800' :
                                    ($product->category == 'kimyasal' ? 'bg-yellow-100 text-yellow-800' :
                                     ($product->category == 'elektronik' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ $product->category_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $product->stock_status == 'in_stock' ? 'bg-green-100 text-green-800' :
                                   ($product->stock_status == 'low_stock' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                {{ $product->stock_status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="font-semibold text-gray-900">{{ $product->stock_quantity }}</span>
                            <span class="text-sm text-gray-500 ml-1">{{ $product->unit }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm text-gray-700">{{ $product->min_stock_level }}</span>
                            <span class="text-xs text-gray-500 ml-1">{{ $product->unit }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <input type="number"
                                       name="stocks[{{ $product->id }}]"
                                       id="stock_{{ $product->id }}"
                                       value="{{ $product->stock_quantity }}"
                                       min="0"
                                       step="1"
                                       class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center font-semibold text-gray-900"
                                       onchange="highlightChanged(this, {{ $product->stock_quantity }})">
                                <span class="text-sm text-gray-600">{{ $product->unit }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $product->supplier ?? '-' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Form Footer -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-600">
                <span class="font-semibold">{{ $products->count() }}</span> ürün listeleniyor
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}"
                   class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    İptal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Değişiklikleri Kaydet
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function highlightChanged(input, originalValue) {
    const newValue = parseInt(input.value);
    const parent = input.closest('tr');

    if (newValue !== originalValue) {
        parent.classList.add('bg-yellow-50');
        input.classList.add('border-yellow-500', 'ring-2', 'ring-yellow-200');
    } else {
        parent.classList.remove('bg-yellow-50');
        input.classList.remove('border-yellow-500', 'ring-2', 'ring-yellow-200');
    }
}
</script>
@endsection

