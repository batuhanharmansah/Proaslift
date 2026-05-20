@extends('layouts.app')

@section('title', 'Borçlar - Finansal Yönetim')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">💳 Borçlar</h1>
                <p class="text-gray-600 mt-1">Ödenecek faturalar ve borç takibi</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="openModal('addPayableModal')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ➕ Yeni Borç
                </button>
                <a href="{{ route('financial.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ← Finansal Yönetim
                </a>
            </div>
        </div>
    </div>

    <!-- Kompakt İstatistik Kartları -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-blue-600 text-xs">📋</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Toplam Borç</p>
                <p class="text-lg font-bold text-blue-600 truncate">{{ $stats['total_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-red-600 text-xs font-bold">₺</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Toplam Tutar</p>
                <p class="text-sm font-bold text-red-600 truncate">₺{{ number_format($stats['total_amount']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-green-600 text-xs">✓</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Ödenen</p>
                <p class="text-sm font-bold text-green-600 truncate">₺{{ number_format($stats['total_paid']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-orange-600 text-xs">⏳</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Kalan</p>
                <p class="text-sm font-bold text-orange-600 truncate">₺{{ number_format($stats['total_remaining']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-red-600 text-xs">🚨</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Vadesi Geçen</p>
                <p class="text-lg font-bold text-red-600 truncate">{{ $stats['overdue_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-yellow-600 text-xs">📅</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bu Ay</p>
                <p class="text-sm font-bold text-yellow-600 truncate">₺{{ number_format($stats['due_this_month']/1000, 0) }}K</p>
            </div>
        </div>
    </div>

    <!-- Kompakt Filtreler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('financial.payables') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔍 Arama: başlık, tedarikçi..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
            </div>
            <div class="min-w-32">
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="beklemede" {{ request('status') === 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                    <option value="kismi_odendi" {{ request('status') === 'kismi_odendi' ? 'selected' : '' }}>Kısmi Ödendi</option>
                    <option value="tamamlandi" {{ request('status') === 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
            </div>
            <div class="min-w-28">
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                    <option value="">Kategoriler</option>
                    <option value="elektrik" {{ request('category') === 'elektrik' ? 'selected' : '' }}>Elektrik</option>
                    <option value="su" {{ request('category') === 'su' ? 'selected' : '' }}>Su</option>
                    <option value="dogalgaz" {{ request('category') === 'dogalgaz' ? 'selected' : '' }}>Doğalgaz</option>
                    <option value="maas" {{ request('category') === 'maas' ? 'selected' : '' }}>Maaş</option>
                    <option value="vergi" {{ request('category') === 'vergi' ? 'selected' : '' }}>Vergi</option>
                </select>
            </div>
            <div class="min-w-36">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
            </div>
            <div class="min-w-36">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
            </div>
            <div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium transition-colors text-sm">
                    🔍 Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Borçlar Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Borç Listesi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tedarikçi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Tutar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödenen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kalan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vade Tarihi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payables as $payable)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payable->title }}</div>
                                @if($payable->invoice_number)
                                    <div class="text-sm text-gray-500">Fatura: {{ $payable->invoice_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payable->category === 'elektrik') bg-yellow-100 text-yellow-800
                                    @elseif($payable->category === 'su') bg-blue-100 text-blue-800
                                    @elseif($payable->category === 'dogalgaz') bg-orange-100 text-orange-800
                                    @elseif($payable->category === 'maas') bg-green-100 text-green-800
                                    @elseif($payable->category === 'vergi') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $payable->category_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payable->supplier_name ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₺{{ number_format($payable->total_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600">₺{{ number_format($payable->paid_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-red-600">₺{{ number_format($payable->remaining_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payable->due_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}</div>
                                @if($payable->due_date < now('Europe/Istanbul') && $payable->status !== 'tamamlandi')
                                    <div class="text-xs text-red-600 font-medium">{{ $payable->due_date->setTimezone('Europe/Istanbul')->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payable->status === 'tamamlandi') bg-green-100 text-green-800
                                    @elseif($payable->status === 'kismi_odendi') bg-yellow-100 text-yellow-800
                                    @elseif($payable->status === 'beklemede') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $payable->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($payable->remaining_amount > 0)
                                    <button onclick="openPaymentModal('payable', {{ $payable->id }}, '{{ $payable->title }}', {{ $payable->remaining_amount }})"
                                            class="text-red-600 hover:text-red-900 mr-3">💳 Ödeme Yap</button>
                                @endif
                                <button onclick="editPayable({{ $payable->id }})" class="text-blue-600 hover:text-blue-900">✏️ Düzenle</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                Henüz borç kaydı bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payables->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payables->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Borç Ekleme Modal -->
<div id="addPayableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Yeni Borç Ekle</h3>
                <button onclick="closeModal('addPayableModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="addPayableForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık *</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Örn: Elektrik Faturası">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Toplam Tutar *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₺</span>
                            <input type="number" name="total_amount" step="0.01" min="0.01" required class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vade Tarihi *</label>
                        <input type="date" name="due_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Kategori seçiniz...</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fatura Numarası</label>
                        <input type="text" name="invoice_number" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Fatura numarası">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tedarikçi Adı</label>
                        <input type="text" name="supplier_name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Tedarikçi adı">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
                        <select name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="dusuk">Düşük</option>
                            <option value="orta" selected>Orta</option>
                            <option value="yuksek">Yüksek</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Borç ile ilgili detaylar..."></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal('addPayableModal')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                        💳 Borç Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ödeme Yapma Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ödeme Yap</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="paymentForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Borç</label>
                    <p class="text-gray-900 font-medium" id="paymentTitle"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tutarı *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₺</span>
                        <input type="number" id="paymentAmount" step="0.01" min="0.01" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hesap *</label>
                    <select id="paymentAccountId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @foreach($accounts ?? [] as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} (₺{{ number_format($account->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        💳 Ödeme Yap
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal fonksiyonları
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    const form = document.querySelector(`#${modalId} form`);
    if (form) form.reset();
}

// Ödeme modal fonksiyonları
let currentPaymentData = {};

function openPaymentModal(type, id, title, amount) {
    currentPaymentData = { type, id, title, amount };
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentTitle').textContent = title;
    document.getElementById('paymentAmount').value = amount;
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
    currentPaymentData = {};
}

// Form event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Borç ekleme formu
    const addPayableForm = document.getElementById('addPayableForm');
    if (addPayableForm) {
        addPayableForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

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
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                alert('Bir hata oluştu!');
            });
        });
    }

    // Ödeme yapma formu
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                payable_id: currentPaymentData.id,
                amount: document.getElementById('paymentAmount').value,
                account_id: document.getElementById('paymentAccountId').value,
                _token: document.querySelector('input[name="_token"]').value
            };

            fetch('{{ route("financial.make-payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ödeme başarıyla yapıldı!');
                    location.reload();
                } else {
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                alert('Bir hata oluştu!');
            });
        });
    }
});

function editPayable(id) {
    // Düzenleme fonksiyonu - gelecekte implement edilebilir
    alert('Düzenleme özelliği yakında eklenecek!');
}
</script>
@endsection
