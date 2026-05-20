@extends('layouts.app')

@section('title', 'Hesaplar - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hesaplar</h1>
            <p class="text-gray-600 mt-1">Kasa, banka hesapları ve finansal hesaplarınızı yönetin</p>
        </div>
        <a href="{{ route('account-types.create') }}"
           class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Yeni Hesap Ekle
        </a>
    </div>

    <!-- Toplam Bakiye Kartı -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">Toplam Bakiye</h2>
                <p class="text-3xl font-bold">₺{{ number_format($totalBalance, 2) }}</p>
            </div>
            <div class="text-right">
                <p class="text-blue-100">Aktif Hesaplar</p>
                <p class="text-2xl font-bold">{{ $accounts->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Hesaplar Listesi -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($accounts as $account)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
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
                <div class="dropdown">
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('account-types.edit', $account) }}" class="dropdown-item">Düzenle</a>
                        @if($account->current_balance == 0)
                        <form action="{{ route('account-types.destroy', $account) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-red-600" onclick="return confirm('Bu hesabı silmek istediğinizden emin misiniz?')">Sil</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Güncel Bakiye:</span>
                    <span class="font-semibold text-lg @if($account->current_balance >= 0) text-green-600 @else text-red-600 @endif">
                        {{ $account->formatted_current_balance }}
                    </span>
                </div>

                @if($account->type === 'banka' && $account->bank_name)
                <div class="text-sm text-gray-500">
                    <p>{{ $account->bank_name }}</p>
                    @if($account->branch_name)
                        <p>{{ $account->branch_name }}</p>
                    @endif
                    @if($account->account_number)
                        <p class="font-mono">{{ $account->account_number }}</p>
                    @endif
                </div>
                @endif

                @if($account->notes)
                <div class="text-sm text-gray-500 border-t pt-2">
                    <p>{{ Str::limit($account->notes, 50) }}</p>
                </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex space-x-2">
                    <a href="{{ route('financial-transactions.create', ['account_id' => $account->id]) }}"
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium py-2 px-3 rounded-lg text-center transition-colors">
                        İşlem Ekle
                    </a>
                    <a href="{{ route('financial-transactions.index', ['account_id' => $account->id]) }}"
                       class="flex-1 bg-primary-50 hover:bg-primary-100 text-primary-700 text-sm font-medium py-2 px-3 rounded-lg text-center transition-colors">
                        Geçmiş
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($accounts->isEmpty())
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz hesap oluşturulmamış</h3>
        <p class="text-gray-500 mb-6">İlk hesabınızı oluşturarak başlayın</p>
        <a href="{{ route('account-types.create') }}" class="bg-primary-500 hover:bg-primary-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            İlk Hesabı Oluştur
        </a>
    </div>
    @endif
</div>

<style>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 120px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    z-index: 1000;
    border: 1px solid #e5e7eb;
}

.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-item {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #374151;
    font-size: 14px;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f3f4f6;
}

.dropdown-item:first-child {
    border-radius: 8px 8px 0 0;
}

.dropdown-item:last-child {
    border-radius: 0 0 8px 8px;
}
</style>
@endsection
