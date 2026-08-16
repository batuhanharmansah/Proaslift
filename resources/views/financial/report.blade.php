@extends('layouts.app')

@section('title', 'Finansal Raporlar - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Finansal Raporlar</h1>
                <p class="text-gray-600 mt-1">Aylık gelir-gider analizi ve finansal durum</p>
            </div>
            <a href="{{ route('financial.index') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                ← Ana Sayfaya Dön
            </a>
        </div>
    </div>

    <!-- Aylık Karşılaştırma -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Bu Ay -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Bu Ay ({{ now()->translatedFormat('F Y') }})</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Toplam Gelir</p>
                        <p class="text-2xl font-bold text-green-600">₺{{ number_format($monthlyStats['current']['income'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Toplam Gider</p>
                        <p class="text-2xl font-bold text-red-600">₺{{ number_format($monthlyStats['current']['expense'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Net Kar/Zarar</p>
                        <p class="text-2xl font-bold @if($monthlyStats['current']['profit'] >= 0) text-blue-600 @else text-red-600 @endif">
                            ₺{{ number_format($monthlyStats['current']['profit'], 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geçen Ay -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Geçen Ay ({{ now()->subMonth()->translatedFormat('F Y') }})</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Toplam Gelir</p>
                        <p class="text-2xl font-bold text-green-600">₺{{ number_format($monthlyStats['last']['income'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Toplam Gider</p>
                        <p class="text-2xl font-bold text-red-600">₺{{ number_format($monthlyStats['last']['expense'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Net Kar/Zarar</p>
                        <p class="text-2xl font-bold @if($monthlyStats['last']['profit'] >= 0) text-blue-600 @else text-red-600 @endif">
                            ₺{{ number_format($monthlyStats['last']['profit'], 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Hesap Bakiyeleri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Hesap Bakiyeleri</h2>
            <div class="space-y-4">
                @foreach($accounts as $account)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                            @if($account->type === 'kasa') bg-green-100 text-green-600
                            @elseif($account->type === 'banka') bg-blue-100 text-blue-600
                            @elseif($account->type === 'pos') bg-purple-100 text-purple-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($account->type === 'kasa')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            @elseif($account->type === 'banka')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            @elseif($account->type === 'pos')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $account->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $account->type_label }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg @if($account->current_balance >= 0) text-green-600 @else text-red-600 @endif">
                            ₺{{ number_format($account->current_balance, 2) }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Kategori Bazlı Analiz -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Bu Ay Kategori Analizi</h2>
            <div class="space-y-4">
                @php
                    $incomeCategories = $categoryStats->where('transaction_type', 'gelir');
                    $expenseCategories = $categoryStats->where('transaction_type', 'gider');
                @endphp

                @if($incomeCategories->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-green-700 mb-3">Gelir Kategorileri</h3>
                    <div class="space-y-2">
                        @foreach($incomeCategories as $category)
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">{{ $category->category_label }}</span>
                            <span class="text-sm font-bold text-green-600">₺{{ number_format($category->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($expenseCategories->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-red-700 mb-3">Gider Kategorileri</h3>
                    <div class="space-y-2">
                        @foreach($expenseCategories as $category)
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">{{ $category->category_label }}</span>
                            <span class="text-sm font-bold text-red-600">₺{{ number_format($category->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($categoryStats->count() == 0)
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-500">Bu ay henüz işlem bulunmuyor</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Etiket Bazlı Analiz -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Bu Ay Etiket Analizi</h2>
            <p class="text-sm text-gray-500 mb-6">İşlem eklerken girilen serbest etiketlere göre kırılım (Yakıt, Kira, Malzeme vb.)</p>
            <div class="space-y-4">
                @php
                    $incomeTags = $tagStats->where('transaction_type', 'gelir');
                    $expenseTags = $tagStats->where('transaction_type', 'gider');
                @endphp

                @if($incomeTags->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-green-700 mb-3">Gelir Etiketleri</h3>
                    <div class="space-y-2">
                        @foreach($incomeTags as $tagRow)
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">{{ $tagRow->tag }}</span>
                            <span class="text-sm font-bold text-green-600">₺{{ number_format($tagRow->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($expenseTags->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-red-700 mb-3">Gider Etiketleri</h3>
                    <div class="space-y-2">
                        @foreach($expenseTags as $tagRow)
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">{{ $tagRow->tag }}</span>
                            <span class="text-sm font-bold text-red-600">₺{{ number_format($tagRow->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($tagStats->count() == 0)
                <div class="text-center py-8">
                    <p class="text-gray-500">Bu ay etiketli işlem bulunmuyor. Hızlı işlem eklerken "Etiket" alanını doldurarak kırılım oluşturabilirsiniz.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
