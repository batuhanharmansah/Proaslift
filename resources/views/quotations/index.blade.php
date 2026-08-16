@extends('layouts.app')

@section('title', 'Teklifler - Harmanşah Yazılım')

@section('content')
@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-800',
        'sent' => 'bg-blue-100 text-blue-800',
        'viewed' => 'bg-indigo-100 text-indigo-800',
        'accepted' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'expired' => 'bg-orange-100 text-orange-800',
        'converted' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
    ];
@endphp
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Teklifler</h1>
            <p class="text-gray-600 mt-1">Bakım, revizyon, montaj ve arıza tekliflerini yönetin</p>
        </div>
        <a href="{{ route('quotations.create') }}"
           class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path>
            </svg>
            Yeni Teklif
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('quotations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Teklif no, müşteri, bina ara..."
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Tüm Tipler</option>
                <option value="maintenance" @selected(request('type') === 'maintenance')>Bakım</option>
                <option value="modernization" @selected(request('type') === 'modernization')>Revizyon</option>
                <option value="installation" @selected(request('type') === 'installation')>Montaj</option>
                <option value="repair" @selected(request('type') === 'repair')>Arıza / Parça</option>
            </select>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Tüm Durumlar</option>
                @foreach(['draft' => 'Taslak', 'sent' => 'Gönderildi', 'viewed' => 'Görüntülendi', 'accepted' => 'Kabul Edildi', 'rejected' => 'Reddedildi', 'converted' => 'Alacağa Dönüştü', 'cancelled' => 'İptal'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg">Filtrele</button>
                <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Temizle</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teklif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri / Bina</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($quotations as $quotation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $quotation->quote_no }}</div>
                                <div class="text-sm text-gray-500">{{ $quotation->created_at->format('d.m.Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $quotation->customer_name }}</div>
                                <div class="text-sm text-gray-500">{{ $quotation->building?->name ?? 'Bina seçilmedi' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $quotation->type_label }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClasses[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $quotation->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                {{ $quotation->currency === 'TRY' ? '₺' : $quotation->currency }} {{ number_format($quotation->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('quotations.show', $quotation) }}" class="text-primary-600 hover:text-primary-800 font-medium">Görüntüle</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Henüz teklif oluşturulmamış.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($quotations->hasPages())
        <div class="mt-6">{{ $quotations->links() }}</div>
    @endif
</div>
@endsection
