@extends('layouts.app')

@section('title', 'Ürün Detayı - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $product->code }} - {{ $product->category_label }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('products.edit', $product) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Düzenle
            </a>
            <a href="{{ route('products.index') }}"
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana Bilgiler -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ürün Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı</label>
                        <p class="text-gray-900">{{ $product->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Markası</label>
                        <p class="text-gray-900">{{ $product->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <p class="text-gray-900">{{ $product->category_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birim</label>
                        <p class="text-gray-900">{{ $product->unit }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tedarikçi</label>
                        <p class="text-gray-900">{{ $product->supplier ?: 'Belirtilmemiş' }}</p>
                    </div>
                </div>
            </div>

            <!-- Fiyat Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Fiyat Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maliyet Fiyatı</label>
                        <p class="text-red-600 font-semibold">₺{{ number_format($product->cost_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı</label>
                        <p class="text-green-600 font-semibold">₺{{ number_format($product->sale_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kar Marjı</label>
                        @php
                            $profit = $product->sale_price - $product->cost_price;
                            $profitMargin = $product->cost_price > 0 ? ($profit / $product->cost_price) * 100 : 0;
                        @endphp
                        <p class="text-blue-600 font-semibold">₺{{ number_format($profit, 2) }} (%{{ number_format($profitMargin, 1) }})</p>
                    </div>
                </div>
            </div>

            <!-- Stok Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Stok Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Stok</label>
                        <p class="text-2xl font-bold {{ $product->stock_quantity <= $product->min_stock_level ? 'text-red-600' : 'text-green-600' }}">
                            {{ $product->stock_quantity }} {{ $product->unit }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok Seviyesi</label>
                        <p class="text-gray-900">{{ $product->min_stock_level }} {{ $product->unit }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Durumu</label>
                        @if($product->stock_quantity <= 0)
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Stokta Yok</span>
                        @elseif($product->stock_quantity <= $product->min_stock_level)
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Düşük Stok</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Yeterli Stok</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Açıklama ve Notlar -->
            @if($product->description || $product->notes)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Açıklama ve Notlar</h2>
                    @if($product->description)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                            <p class="text-gray-900">{{ $product->description }}</p>
                        </div>
                    @endif
                    @if($product->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                            <p class="text-gray-900">{{ $product->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Yan Panel -->
        <div class="space-y-6">
            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
                <div class="space-y-3">
                    <a href="{{ route('products.edit', $product) }}"
                       class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Düzenle
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline w-full"
                          onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
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
                </div>
            </div>

            <!-- Ürün İstatistikleri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ürün İstatistikleri</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oluşturulma Tarihi</label>
                        <p class="text-gray-900">{{ $product->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Son Güncelleme</label>
                        <p class="text-gray-900">{{ $product->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
