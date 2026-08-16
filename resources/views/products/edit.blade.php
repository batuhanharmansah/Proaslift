@extends('layouts.app')

@section('title', 'Ürün Düzenle - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ürün Düzenle</h1>
            <p class="text-gray-600 mt-1">{{ $product->name }}</p>
        </div>
        <a href="{{ route('products.show', $product) }}"
           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Temel Bilgiler -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Temel Bilgiler</h2>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Ürün Adı *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Ürün Markası *</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select id="category" name="category" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="yedek_parca" {{ old('category', $product->category) === 'yedek_parca' ? 'selected' : '' }}>Yedek Parça</option>
                        <option value="arac_gerec" {{ old('category', $product->category) === 'arac_gerec' ? 'selected' : '' }}>Araç Gereç</option>
                        <option value="kimyasal" {{ old('category', $product->category) === 'kimyasal' ? 'selected' : '' }}>Kimyasal</option>
                        <option value="elektronik" {{ old('category', $product->category) === 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                        <option value="mekanik" {{ old('category', $product->category) === 'mekanik' ? 'selected' : '' }}>Mekanik</option>
                    </select>
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Birim *</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" placeholder="adet, kg, lt, m" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                    <select id="is_active" name="is_active"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $product->is_active) ? '' : 'selected' }}>Pasif</option>
                    </select>
                </div>

                <!-- Fiyat Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 mt-8">Fiyat Bilgileri</h2>
                </div>

                <div>
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">Maliyet Fiyatı (₺) *</label>
                    <input type="number" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="0.01" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Satış Fiyatı (₺) *</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="0.01" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Stok Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 mt-8">Stok Bilgileri</h2>
                </div>

                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Mevcut Stok *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="min_stock_level" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stok Seviyesi *</label>
                    <input type="number" id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Tedarikçi Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 mt-8">Tedarikçi Bilgileri</h2>
                </div>

                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">Tedarikçi</label>
                    <input type="text" id="supplier" name="supplier" value="{{ old('supplier', $product->supplier) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Depo Lokasyonu</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $product->location) }}" placeholder="Raf A-3, Merkez Depo vb."
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Açıklama -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $product->notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('products.show', $product) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    İptal
                </a>
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
