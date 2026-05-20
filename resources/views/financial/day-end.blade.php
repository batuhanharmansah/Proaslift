@extends('layouts.app')

@section('title', 'Gün Sonu - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gün Sonu</h1>
                <p class="text-gray-600 mt-1">Seçilen tarihteki hesap hareketleri</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ url('finansal/gun-sonu/excel?date=' . $selectedDate) }}" class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Excel İndir
                </a>
                <a href="{{ url('finansal/gun-sonu/pdf?date=' . $selectedDate) }}" class="bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    PDF İndir
                </a>
                <a href="{{ url('finansal') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ← Finansal Yönetim
                </a>
            </div>
        </div>
    </div>

    <!-- Tarih seçici -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ url('finansal/gun-sonu') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tarih seçin</label>
                <input type="date" name="date" id="date" value="{{ $selectedDate }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Göster
            </button>
        </form>
        <p class="text-sm text-gray-500 mt-2">Eski tarihleri seçerek geçmiş günlerin işlemlerini görebilirsiniz.</p>
    </div>

    <!-- Özet kartları -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Seçilen gün gelir</p>
                    <p class="text-2xl font-bold text-green-600">₺{{ number_format($dayIncome, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Seçilen gün gider</p>
                    <p class="text-2xl font-bold text-red-600">₺{{ number_format($dayExpense, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Net (gelir − gider)</p>
                    @php $net = $dayIncome - $dayExpense; @endphp
                    <p class="text-2xl font-bold {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }}">₺{{ number_format($net, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- İşlemler tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selectedDate)->locale('tr')->translatedFormat('d F Y') }} — İşlemler</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih / Saat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hesap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bina</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date ? $transaction->transaction_date->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') : ($transaction->created_at ? $transaction->created_at->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') : '—') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($transaction->type === 'gelir') bg-green-100 text-green-800
                                @elseif($transaction->type === 'gider') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                @if($transaction->type === 'gelir') Gelir
                                @elseif($transaction->type === 'gider') Gider
                                @else Transfer
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($transaction->description ?? '', 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium @if($transaction->type === 'gelir') text-green-600 @else text-red-600 @endif">
                            ₺{{ number_format($transaction->amount ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($transaction->accountType){{ $transaction->accountType->name }}@else<span class="text-gray-400">—</span>@endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($transaction->building){{ $transaction->building->name }}@else<span class="text-gray-400">—</span>@endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Bu tarihte işlem bulunamadı.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
