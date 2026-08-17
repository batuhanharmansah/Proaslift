@extends('employee.layouts.app')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $product->name }}</h1>
            <p class="text-gray-600">{{ $product->code }} — {{ $product->category_label }}</p>
        </div>
        <a href="{{ route('employee.products.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            ← Geri Dön
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Stok Bilgileri</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Mevcut Stok</p>
                <p class="text-2xl font-bold mt-1
                    {{ $product->stock_status == 'in_stock' ? 'text-green-600' :
                       ($product->stock_status == 'low_stock' ? 'text-orange-600' : 'text-red-600') }}">
                    {{ $product->stock_quantity }} {{ $product->unit }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Minimum Stok Seviyesi</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $product->min_stock_level }} {{ $product->unit }}</p>
            </div>
            @if($product->location)
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Raf Konumu</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $product->location }}</p>
            </div>
            @endif
            @if($product->supplier)
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Tedarikçi</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $product->supplier }}</p>
            </div>
            @endif
        </div>

        @if($product->description)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Açıklama</p>
            <p class="text-sm text-gray-700 mt-1">{{ $product->description }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
