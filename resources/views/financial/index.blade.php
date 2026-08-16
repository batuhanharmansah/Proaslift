@extends('layouts.app')

@section('title', 'Finansal Yönetim - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Finansal Yönetim</h1>
                <p class="text-gray-600 mt-1">Hesaplarınızı ve işlemlerinizi kolayca yönetin</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ url('finansal/islemler') }}" class="bg-primary-100 hover:bg-primary-200 text-gray-900 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    📋 Alacaklar, Borçlar ve Düzenli Ödemeler
                </a>
                <a href="{{ url('finansal/gun-sonu') }}" class="bg-amber-100 hover:bg-amber-200 text-amber-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    📅 Gün Sonu
                </a>
                <a href="{{ route('financial.history') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    📋 İşlem Geçmişi
                </a>
                <a href="{{ route('reports.financial') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    📊 Raporlar
                </a>
                <a href="{{ route('financial.kar-maliyet') }}" class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    📈 Kâr / Maliyet Raporu
                </a>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Toplam Bakiye</p>
                    <p class="text-2xl font-bold text-blue-600">₺{{ number_format($totalBalance, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Bu Ay Gelir</p>
                    <p class="text-2xl font-bold text-green-600">₺{{ number_format($monthlyIncome, 2) }}</p>
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
                    <p class="text-sm text-gray-500">Bu Ay Gider</p>
                    <p class="text-2xl font-bold text-red-600">₺{{ number_format($monthlyExpense, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Toplam Alacak</p>
                    <p class="text-2xl font-bold text-yellow-600">₺{{ number_format($totalReceivables, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Toplam Borç</p>
                    <p class="text-2xl font-bold text-orange-600">₺{{ number_format($totalPayables, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sol Kolon: Hesaplar -->
        <div class="space-y-6">
            <!-- Hesaplar Başlığı -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Hesaplar</h2>
                <button onclick="openAddAccountModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    + Yeni Hesap
                </button>
            </div>

            <!-- Hesaplar Listesi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
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
                            <button onclick="editBalance({{ $account->id }}, {{ $account->current_balance }})" class="text-xs text-blue-600 hover:text-blue-800">
                                Düzenle
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Hızlı İşlem -->
        <div class="space-y-6">
            <!-- Hızlı İşlem Başlığı -->
            <h2 class="text-xl font-semibold text-gray-900">Hızlı İşlem Ekle</h2>

            <!-- Hızlı İşlem Formu -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form id="quickTransactionForm" class="space-y-4">
                    @csrf

                    <!-- İşlem Türü -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">İşlem Türü</label>
                        <select id="transactionType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="gelir">Gelir</option>
                            <option value="gider">Gider</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="genel">Genel</option>
                            <option value="bakim_geliri">Bakım Geliri</option>
                            <option value="yedek_parca">Yedek Parça</option>
                            <option value="personel_maas">Personel Maaş</option>
                            <option value="kira">Kira</option>
                            <option value="elektrik">Elektrik</option>
                            <option value="su">Su</option>
                            <option value="yakıt">Yakıt</option>
                            <option value="vergi">Vergi</option>
                            <option value="sigorta">Sigorta</option>
                        </select>
                    </div>

                    <!-- Hesap Seçimi -->
                    <div id="accountSelection">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hesap</label>
                        <select id="accountId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hedef Hesap (Transfer için) -->
                    <div id="targetAccountSection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hedef Hesap</label>
                        <select id="targetAccountId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bina Seçimi (Gelir için) -->
                    <div id="buildingSection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina (Opsiyonel)</label>
                        <select id="buildingId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Bina seçmeyin</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tutar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tutar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">₺</span>
                            <input type="number" id="amount" step="0.01" min="0.01" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Açıklama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                        <input type="text" id="description" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="İşlem açıklaması">
                    </div>

                    <!-- Etiket (serbest, Kasa raporunda kırılım için) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Etiket (opsiyonel)</label>
                        <input type="text" id="tag" maxlength="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Yakıt, Kira, Malzeme...">
                    </div>

                    <!-- Gönder Butonu -->
                    <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        İşlemi Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Yaklaşan Vadeler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 mt-8">
        <!-- Yaklaşan Alacaklar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Yaklaşan Alacaklar</h2>
            <div class="space-y-4">
                @forelse($upcomingReceivables as $receivable)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 bg-yellow-100 text-yellow-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $receivable->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $receivable->building->name }} • {{ $receivable->due_date->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg text-yellow-600">{{ $receivable->formatted_remaining_amount }}</p>
                        <p class="text-sm text-gray-500">{{ $receivable->status_label }}</p>
                        @if($receivable->remaining_amount > 0)
                        <button onclick="openPaymentModal('receivable', {{ $receivable->id }}, '{{ $receivable->title }}', {{ $receivable->remaining_amount }})"
                                class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors">
                            Ödeme Al
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">Yaklaşan alacak bulunmuyor</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Yaklaşan Borçlar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Yaklaşan Borçlar</h2>
            <div class="space-y-4">
                @forelse($upcomingPayables as $payable)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 bg-orange-100 text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $payable->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $payable->category_label }} • {{ $payable->due_date->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg text-orange-600">{{ $payable->formatted_remaining_amount }}</p>
                        <p class="text-sm text-gray-500">{{ $payable->status_label }}</p>
                        @if($payable->remaining_amount > 0)
                        <button onclick="openPaymentModal('payable', {{ $payable->id }}, '{{ $payable->title }}', {{ $payable->remaining_amount }})"
                                class="mt-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors">
                            Ödeme Yap
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">Yaklaşan borç bulunmuyor</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Son İşlemler -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Son İşlemler</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hesap</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->transaction_date->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') }}
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
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ Str::limit($transaction->description, 50) }}
                                @if($transaction->building)
                                    <br><span class="text-xs text-gray-500">{{ $transaction->building->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium @if($transaction->type === 'gelir') text-green-600 @else text-red-600 @endif">
                                ₺{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($transaction->accountType)
                                    {{ $transaction->accountType->name }}
                                @else
                                    <span class="text-gray-400">Hesap belirtilmemiş</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Hesap Ekleme Modal -->
<div id="addAccountModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Yeni Hesap Ekle</h3>
            <form id="addAccountForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hesap Adı</label>
                    <input type="text" id="accountName" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Örn: Ana Kasa">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hesap Türü</label>
                    <select id="accountType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="kasa">Kasa</option>
                        <option value="banka">Banka</option>
                        <option value="nakit">Nakit</option>
                        <option value="pos">POS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Bakiyesi</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₺</span>
                        <input type="number" id="initialBalance" step="0.01" min="0" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="0.00">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeAddAccountModal()" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                        Hesap Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bakiye Düzenleme Modal -->
<div id="editBalanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bakiye Düzenle</h3>
            <form id="editBalanceForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Bakiye</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₺</span>
                        <input type="number" id="newBalance" step="0.01" min="0" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="0.00">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeEditBalanceModal()" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form alanlarını güncelleme
document.addEventListener('DOMContentLoaded', function() {
    const transactionType = document.getElementById('transactionType');
    if (transactionType) {
        transactionType.addEventListener('change', function() {
            const type = this.value;
            const accountSelection = document.getElementById('accountSelection');
            const targetAccountSection = document.getElementById('targetAccountSection');
            const buildingSection = document.getElementById('buildingSection');
            const descriptionField = document.getElementById('description');

            // Açıklama alanını temizle
            if (descriptionField) {
                descriptionField.value = '';
            }

            // Label'ları güncelle
            if (accountSelection) {
                const accountLabel = accountSelection.querySelector('label');
                if (type === 'gelir') {
                    accountLabel.textContent = 'Hedef Hesap';
                    if (targetAccountSection) targetAccountSection.classList.add('hidden');
                    if (buildingSection) buildingSection.classList.remove('hidden');
                } else if (type === 'gider') {
                    accountLabel.textContent = 'Kaynak Hesap';
                    if (targetAccountSection) targetAccountSection.classList.add('hidden');
                    if (buildingSection) buildingSection.classList.add('hidden');
                } else if (type === 'transfer') {
                    accountLabel.textContent = 'Kaynak Hesap';
                    if (targetAccountSection) targetAccountSection.classList.remove('hidden');
                    if (buildingSection) buildingSection.classList.add('hidden');
                }
            }
        });
    }
});

// Hızlı işlem formu
const quickTransactionForm = document.getElementById('quickTransactionForm');
if (quickTransactionForm) {
    quickTransactionForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            type: document.getElementById('transactionType')?.value || '',
            category: document.getElementById('category')?.value || 'genel',
            amount: document.getElementById('amount')?.value || '',
            description: document.getElementById('description')?.value || '',
            account_id: document.getElementById('accountId')?.value || '',
            target_account_id: document.getElementById('targetAccountId')?.value || '',
            building_id: document.getElementById('buildingId')?.value || '',
            tag: document.getElementById('tag')?.value || '',
            _token: document.querySelector('input[name="_token"]')?.value || ''
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Content-Type': 'application/json'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        fetch('{{ route("financial.quick-transaction") }}', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('İşlem başarıyla kaydedildi!');
                location.reload();
            } else {
                alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu!');
        });
    });
}

// Hesap ekleme modal
function openAddAccountModal() {
    document.getElementById('addAccountModal').classList.remove('hidden');
}

function closeAddAccountModal() {
    document.getElementById('addAccountModal').classList.add('hidden');
    document.getElementById('addAccountForm').reset();
}

const addAccountForm = document.getElementById('addAccountForm');
if (addAccountForm) {
    addAccountForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            name: document.getElementById('accountName')?.value || '',
            type: document.getElementById('accountType')?.value || '',
            initial_balance: document.getElementById('initialBalance')?.value || '',
            _token: document.querySelector('input[name="_token"]')?.value || ''
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Content-Type': 'application/json'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        fetch('{{ route("financial.add-account") }}', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Hesap başarıyla oluşturuldu!');
                location.reload();
            } else {
                alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu!');
        });
    });
}

// Bakiye düzenleme modal
let currentAccountId = null;

function editBalance(accountId, currentBalance) {
    currentAccountId = accountId;
    document.getElementById('newBalance').value = currentBalance;
    document.getElementById('editBalanceModal').classList.remove('hidden');
}

function closeEditBalanceModal() {
    document.getElementById('editBalanceModal').classList.add('hidden');
    document.getElementById('editBalanceForm').reset();
    currentAccountId = null;
}

const editBalanceForm = document.getElementById('editBalanceForm');
if (editBalanceForm) {
    editBalanceForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            account_id: currentAccountId,
            new_balance: document.getElementById('newBalance')?.value || '',
            _token: document.querySelector('input[name="_token"]')?.value || ''
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Content-Type': 'application/json'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        fetch('{{ route("financial.update-balance") }}', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Bakiye güncellendi!');
                location.reload();
            } else {
                alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu!');
        });
    });
}

// Modal fonksiyonları
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    // Form'ları temizle
    const form = document.querySelector(`#${modalId} form`);
    if (form) form.reset();
}

// Ödeme modal fonksiyonları
let currentPaymentData = {};

function openPaymentModal(type, id, title, amount) {
    console.log('🔓 Modal açılıyor:', { type, id, title, amount });
    currentPaymentData = { type, id, title, amount };
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentTitle').textContent = title;
    document.getElementById('paymentAmount').value = amount;
    document.getElementById('paymentType').textContent = type === 'receivable' ? 'Alacak Ödemesi' : 'Borç Ödemesi';

    // Buton metnini güncelle
    const paymentButton = document.getElementById('paymentButton');
    if (paymentButton) {
        paymentButton.textContent = type === 'receivable' ? 'Ödeme Al' : 'Ödeme Yap';
    }
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
    currentPaymentData = {};
}

// Form submit event listener'ları
document.addEventListener('DOMContentLoaded', function() {
    // Alacak formu
    const addReceivableForm = document.getElementById('addReceivableForm');
    if (addReceivableForm) {
        addReceivableForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            fetch('{{ route("financial.add-receivable") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Alacak başarıyla oluşturuldu!');
                    location.reload();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu!');
            });
        });
    }

    // Borç formu
    const addPayableForm = document.getElementById('addPayableForm');
    if (addPayableForm) {
        addPayableForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            fetch('{{ route("financial.add-payable") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Borç başarıyla oluşturuldu!');
                    location.reload();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu!');
            });
        });
    }

    // Düzenli ödeme formu
    const addRecurringPaymentForm = document.getElementById('addRecurringPaymentForm');
    if (addRecurringPaymentForm) {
        addRecurringPaymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            fetch('{{ route("financial.add-recurring-payment") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Düzenli ödeme başarıyla oluşturuldu!');
                    location.reload();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu!');
            });
        });
    }

    // Payment form event listener - DOMContentLoaded içinde
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        console.log('✅ paymentForm bulundu!');
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();


            const formData = {
                [currentPaymentData.type === 'receivable' ? 'receivable_id' : 'payable_id']: currentPaymentData.id,
                amount: document.getElementById('paymentAmount')?.value || '',
                account_id: document.getElementById('paymentAccountId')?.value || '',
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            };


            const url = currentPaymentData.type === 'receivable'
                ? '{{ route("financial.receive-payment") }}'
                : '{{ route("financial.make-payment") }}';


            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Content-Type': 'application/json'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            fetch(url, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(formData)
            })
            .then(response => {
                console.log('📡 Sunucu yanıtı:', response.status, response.statusText);
                return response.json();
            })
            .then(data => {

                if (data.success) {
                    alert('✅ Ödeme başarıyla kaydedildi!');
                    location.reload();
                } else {
                    alert('❌ Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('💥 JavaScript hatası:', error);
                alert('❌ Bir hata oluştu! Konsolu kontrol edin.');
            });
        });
    } else {
        console.error('❌ paymentForm bulunamadı!');
    }
});
</script>

<!-- Modal'lar -->
<!-- Alacak Ekleme Modal -->
<div id="addReceivableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Alacak Ekle</h3>
                <button onclick="closeModal('addReceivableModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="addReceivableForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina</label>
                        <select name="building_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="Ocak ayı bakım hizmeti">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Toplam Tutar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₺</span>
                            <input type="number" name="total_amount" step="0.01" min="0.01" required class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vade Tarihi</label>
                        <input type="date" name="due_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme Türü</label>
                        <select name="payment_type" id="paymentTypeSelect" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="tek_sefer">Tek Sefer</option>
                            <option value="taksitli">Taksitli</option>
                        </select>
                    </div>

                    <div id="installmentCountDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Taksit Sayısı</label>
                        <input type="number" name="installment_count" min="1" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
                        <select name="priority" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="dusuk">Düşük</option>
                            <option value="orta" selected>Orta</option>
                            <option value="yuksek">Yüksek</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" rows="3" placeholder="Açıklama"></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal('addReceivableModal')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition-colors font-medium">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Borç Ekleme Modal -->
<div id="addPayableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Borç Ekle</h3>
                <button onclick="closeModal('addPayableModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="addPayableForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="Elektrik faturası">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Toplam Tutar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₺</span>
                            <input type="number" name="total_amount" step="0.01" min="0.01" required class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vade Tarihi</label>
                        <input type="date" name="due_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="elektrik">Elektrik</option>
                            <option value="su">Su</option>
                            <option value="dogalgaz">Doğalgaz</option>
                            <option value="internet">İnternet</option>
                            <option value="telefon">Telefon</option>
                            <option value="maas">Maaş</option>
                            <option value="vergi">Vergi</option>
                            <option value="sigorta">Sigorta</option>
                            <option value="kira">Kira</option>
                            <option value="diger">Diğer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
                        <select name="priority" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="dusuk">Düşük</option>
                            <option value="orta" selected>Orta</option>
                            <option value="yuksek">Yüksek</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fatura Numarası</label>
                        <input type="text" name="invoice_number" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="Fatura no">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tedarikçi Adı</label>
                        <input type="text" name="supplier_name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="Tedarikçi adı">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" rows="4" placeholder="Açıklama"></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal('addPayableModal')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors font-medium">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Düzenli Ödeme Ekleme Modal -->
<div id="addRecurringPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Düzenli Ödeme Ekle</h3>
                <button onclick="closeModal('addRecurringPaymentModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="addRecurringPaymentForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="Aylık elektrik faturası">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tutar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₺</span>
                            <input type="number" name="amount" step="0.01" min="0.01" required class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tür</label>
                        <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="gelir">Gelir</option>
                            <option value="gider">Gider</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sıklık</label>
                        <select name="frequency" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="gunluk">Günlük</option>
                            <option value="haftalik">Haftalık</option>
                            <option value="aylik" selected>Aylık</option>
                            <option value="uc_aylik">3 Aylık</option>
                            <option value="alti_aylik">6 Aylık</option>
                            <option value="yillik">Yıllık</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="elektrik">Elektrik</option>
                            <option value="su">Su</option>
                            <option value="dogalgaz">Doğalgaz</option>
                            <option value="internet">İnternet</option>
                            <option value="telefon">Telefon</option>
                            <option value="maas">Maaş</option>
                            <option value="vergi">Vergi</option>
                            <option value="sigorta">Sigorta</option>
                            <option value="kira">Kira</option>
                            <option value="bina_geliri">Bina Geliri</option>
                            <option value="diger">Diğer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi (Opsiyonel)</label>
                        <input type="date" name="end_date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ayın Kaçıncı Günü</label>
                        <input type="number" name="day_of_month" min="1" max="31" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hesap</label>
                        <select name="account_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="">Hesap seçmeyin</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina (Gelir için)</label>
                        <select name="building_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="">Bina seçmeyin</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" rows="4" placeholder="Açıklama"></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal('addRecurringPaymentModal')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors font-medium">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ödeme Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="paymentType">Ödeme</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="paymentForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlık</label>
                    <p class="text-gray-900 font-medium" id="paymentTitle"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tutarı</label>
                    <input type="number" id="paymentAmount" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hesap</label>
                    <select id="paymentAccountId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        İptal
                    </button>
                    <button type="submit" id="paymentButton" class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        Ödeme Al
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Taksit sayısı alanını göster/gizle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeSelect = document.getElementById('paymentTypeSelect');
    if (paymentTypeSelect) {
        paymentTypeSelect.addEventListener('change', function() {
            const installmentDiv = document.getElementById('installmentCountDiv');
            if (installmentDiv) {
                if (this.value === 'taksitli') {
                    installmentDiv.classList.remove('hidden');
                } else {
                    installmentDiv.classList.add('hidden');
                }
            }
        });
    }
});
</script>

@endsection
