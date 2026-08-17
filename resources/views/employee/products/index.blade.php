@extends('employee.layouts.app')

@section('title', 'Depo')
@section('page-title', 'Depo')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Depo</h1>
            <p class="text-gray-600">Stok durumunu görüntüleyin ({{ $products->total() }} ürün)</p>
        </div>
        <form method="GET" action="{{ route('employee.products.index') }}" class="flex">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Ürün ara..."
                   class="px-4 py-2 text-sm border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-r-lg hover:bg-primary-600 transition-colors text-sm font-medium">
                Ara
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $product->code }}</p>
                    </div>
                    <span class="w-3 h-3 rounded-full {{ $product->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                </div>

                <div class="mb-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $product->category == 'yedek_parca' ? 'bg-blue-100 text-blue-800' :
                           ($product->category == 'arac_gerec' ? 'bg-green-100 text-green-800' :
                            ($product->category == 'kimyasal' ? 'bg-yellow-100 text-yellow-800' :
                             ($product->category == 'elektronik' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                        {{ $product->category_label }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Stok:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $product->stock_status == 'in_stock' ? 'bg-green-100 text-green-800' :
                               ($product->stock_status == 'low_stock' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                            {{ $product->stock_quantity }} {{ $product->unit }}
                        </span>
                    </div>
                </div>

                @if($product->location)
                    <div class="text-xs text-gray-500 mb-2">Raf: {{ $product->location }}</div>
                @endif

                <div class="pt-3 border-t border-gray-100">
                    <a href="{{ route('employee.products.show', $product) }}"
                       class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        Detay Gör
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-lg font-medium text-gray-500">Depoda ürün bulunmuyor</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
