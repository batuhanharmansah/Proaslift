@extends('layouts.app')

@section('title', 'Alacaklar - Finansal Yönetim')

@section('content')
<div class="p-4 md:p-6 max-w-full overflow-x-hidden">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">💰 Alacaklar</h1>
                <p class="text-gray-600 mt-1">Müşteri alacakları ve ödeme takibi</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button onclick="openModal('addReceivableModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ➕ Yeni Alacak
                </button>
                <a href="{{ route('financial.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ← Finansal Yönetim
                </a>
            </div>
        </div>
    </div>

    <!-- Kompakt İstatistik Kartları -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-2 min-w-0">
            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Toplam</p>
                <p class="text-sm font-bold text-blue-600 truncate">{{ $stats['total_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-2 min-w-0">
            <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-green-600 text-xs font-bold">₺</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Toplam</p>
                <p class="text-xs font-bold text-green-600 truncate">₺{{ number_format($stats['total_amount']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-2 min-w-0">
            <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-emerald-600 text-xs">✓</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Tahsil</p>
                <p class="text-xs font-bold text-emerald-600 truncate">₺{{ number_format($stats['total_received']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-2 min-w-0">
            <div class="w-6 h-6 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-yellow-600 text-xs">⏳</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Kalan</p>
                <p class="text-xs font-bold text-yellow-600 truncate">₺{{ number_format($stats['total_remaining']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-2 min-w-0">
            <div class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-red-600 text-xs">🚨</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Geçen</p>
                <p class="text-sm font-bold text-red-600 truncate">{{ $stats['overdue_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-2 min-w-0">
            <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-orange-600 text-xs">📅</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Bu Ay</p>
                <p class="text-xs font-bold text-orange-600 truncate">₺{{ number_format($stats['due_this_month']/1000, 0) }}K</p>
            </div>
        </div>
    </div>

    <!-- Kompakt Filtreler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('financial.receivables') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔍 Arama: başlık, bina..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="beklemede" {{ request('status') === 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                    <option value="kismi_odendi" {{ request('status') === 'kismi_odendi' ? 'selected' : '' }}>Kısmi Ödendi</option>
                    <option value="tamamlandi" {{ request('status') === 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       placeholder="Başlangıç"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
            </div>
            <div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors text-sm w-full">
                    🔍 Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Alacaklar Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Alacak Listesi</h3>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bina</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödenen</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kalan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vade</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($receivables as $receivable)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $receivable->title }}</div>
                                @if($receivable->description)
                                    <div class="text-xs text-gray-500">{{ Str::limit($receivable->description, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $receivable->building->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">₺{{ number_format($receivable->total_amount, 0) }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-green-600">₺{{ number_format($receivable->received_amount, 0) }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-yellow-600">₺{{ number_format($receivable->remaining_amount, 0) }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $receivable->due_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}</div>
                                @if($receivable->due_date < now('Europe/Istanbul') && $receivable->status !== 'tamamlandi')
                                    <div class="text-xs text-red-600 font-medium">{{ $receivable->due_date->setTimezone('Europe/Istanbul')->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($receivable->status === 'tamamlandi') bg-green-100 text-green-800
                                    @elseif($receivable->status === 'kismi_odendi') bg-yellow-100 text-yellow-800
                                    @elseif($receivable->status === 'beklemede') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $receivable->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-medium">
                                @if($receivable->remaining_amount > 0)
                                    <button onclick="openPaymentModal('receivable', {{ $receivable->id }}, '{{ $receivable->title }}', {{ $receivable->remaining_amount }})"
                                            class="text-green-600 hover:text-green-900 mr-2 text-xs">💰</button>
                                @endif
                                <button onclick="editReceivable({{ $receivable->id }})" class="text-blue-600 hover:text-blue-900 text-xs">✏️</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">
                                Henüz alacak kaydı bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            @forelse($receivables as $receivable)
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $receivable->title }}</h4>
                            <p class="text-xs text-gray-500">{{ $receivable->building->name ?? '-' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if($receivable->status === 'tamamlandi') bg-green-100 text-green-800
                            @elseif($receivable->status === 'kismi_odendi') bg-yellow-100 text-yellow-800
                            @elseif($receivable->status === 'beklemede') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $receivable->status_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-3 text-xs">
                        <div>
                            <p class="text-gray-500">Toplam</p>
                            <p class="font-medium">₺{{ number_format($receivable->total_amount, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Ödenen</p>
                            <p class="font-medium text-green-600">₺{{ number_format($receivable->received_amount, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Kalan</p>
                            <p class="font-medium text-yellow-600">₺{{ number_format($receivable->remaining_amount, 0) }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Vade: {{ $receivable->due_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}
                            @if($receivable->due_date < now('Europe/Istanbul') && $receivable->status !== 'tamamlandi')
                                <span class="text-red-600 font-medium ml-1">{{ $receivable->due_date->setTimezone('Europe/Istanbul')->diffForHumans() }}</span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if($receivable->remaining_amount > 0)
                                <button onclick="openPaymentModal('receivable', {{ $receivable->id }}, '{{ $receivable->title }}', {{ $receivable->remaining_amount }})"
                                        class="text-green-600 hover:text-green-900 text-xs">💰 Ödeme</button>
                            @endif
                            <button onclick="editReceivable({{ $receivable->id }})" class="text-blue-600 hover:text-blue-900 text-xs">✏️</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    Henüz alacak kaydı bulunmuyor.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($receivables->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $receivables->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Alacak Ekleme Modal -->
<div id="addReceivableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-4 md:p-8 border w-full max-w-4xl shadow-2xl rounded-xl bg-white m-4">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Yeni Alacak Ekle</h3>
                <button onclick="closeModal('addReceivableModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="addReceivableForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina *</label>
                        <select name="building_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Bina seçiniz...</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık *</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Örn: Ocak 2025 Bakım Ücreti">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Toplam Tutar *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₺</span>
                            <input type="number" name="total_amount" step="0.01" min="0.01" required class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vade Tarihi *</label>
                        <input type="date" name="due_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme Türü</label>
                        <select name="payment_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="tek_sefer">Tek Sefer</option>
                            <option value="taksitli">Taksitli</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
                        <select name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="dusuk">Düşük</option>
                            <option value="orta" selected>Orta</option>
                            <option value="yuksek">Yüksek</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" rows="3" placeholder="Alacak ile ilgili detaylar..."></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal('addReceivableModal')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-medium">
                        💰 Alacak Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ödeme Alma Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-4 border w-full max-w-lg shadow-lg rounded-md bg-white m-4">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ödeme Al</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="paymentForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alacak</label>
                    <p class="text-gray-900 font-medium" id="paymentTitle"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tutarı *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₺</span>
                        <input type="number" id="paymentAmount" step="0.01" min="0.01" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hesap *</label>
                    <select id="paymentAccountId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @foreach($accounts ?? [] as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} (₺{{ number_format($account->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        💰 Ödeme Al
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
    // Alacak ekleme formu
    const addReceivableForm = document.getElementById('addReceivableForm');
    if (addReceivableForm) {
        addReceivableForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

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
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                alert('Bir hata oluştu!');
            });
        });
    }

    // Ödeme alma formu
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                receivable_id: currentPaymentData.id,
                amount: document.getElementById('paymentAmount').value,
                account_id: document.getElementById('paymentAccountId').value,
                _token: document.querySelector('input[name="_token"]').value
            };

            fetch('{{ route("financial.receive-payment") }}', {
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
                    alert('Ödeme başarıyla alındı!');
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

function editReceivable(id) {
    // Düzenleme fonksiyonu - gelecekte implement edilebilir
    alert('Düzenleme özelliği yakında eklenecek!');
}
</script>
@endsection
