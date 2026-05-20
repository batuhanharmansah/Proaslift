@extends('layouts.app')

@section('title', 'Düzenli Ödemeler - Finansal Yönetim')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🔄 Düzenli Ödemeler</h1>
                <p class="text-gray-600 mt-1">Otomatik gelir ve gider işlemleri</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="openModal('addRecurringPaymentModal')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ➕ Yeni Düzenli Ödeme
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
                <p class="text-xs font-medium text-gray-500 truncate">Toplam</p>
                <p class="text-lg font-bold text-blue-600 truncate">{{ $stats['total_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-green-600 text-xs">✓</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Aktif</p>
                <p class="text-lg font-bold text-green-600 truncate">{{ $stats['active_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-emerald-600 text-xs">💰</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Toplam Tutar</p>
                <p class="text-sm font-bold text-emerald-600 truncate">₺{{ number_format($stats['total_amount']/1000, 0) }}K</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-yellow-600 text-xs">⏰</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">7 Gün İçinde</p>
                <p class="text-lg font-bold text-yellow-600 truncate">{{ $stats['next_payments'] }}</p>
            </div>
        </div>
    </div>

    <!-- Kompakt Filtreler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('financial.recurring-payments') }}" class="flex flex-wrap gap-3">
            <div class="min-w-28">
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <div class="min-w-24">
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    <option value="">Tür</option>
                    <option value="gelir" {{ request('type') === 'gelir' ? 'selected' : '' }}>Gelir</option>
                    <option value="gider" {{ request('type') === 'gider' ? 'selected' : '' }}>Gider</option>
                </select>
            </div>
            <div class="min-w-28">
                <select name="frequency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    <option value="">Sıklık</option>
                    <option value="gunluk" {{ request('frequency') === 'gunluk' ? 'selected' : '' }}>Günlük</option>
                    <option value="haftalik" {{ request('frequency') === 'haftalik' ? 'selected' : '' }}>Haftalık</option>
                    <option value="aylik" {{ request('frequency') === 'aylik' ? 'selected' : '' }}>Aylık</option>
                    <option value="uc_aylik" {{ request('frequency') === 'uc_aylik' ? 'selected' : '' }}>3 Aylık</option>
                    <option value="yillik" {{ request('frequency') === 'yillik' ? 'selected' : '' }}>Yıllık</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg font-medium transition-colors text-sm">
                    🔍 Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Düzenli Ödemeler Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Düzenli Ödeme Listesi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sıklık</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sonraki Ödeme</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hesap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recurringPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->title }}</div>
                                @if($payment->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($payment->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->type === 'gelir') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $payment->type === 'gelir' ? 'Gelir' : 'Gider' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium {{ $payment->type === 'gelir' ? 'text-green-600' : 'text-red-600' }}">
                                    ₺{{ number_format($payment->amount, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payment->frequency_label }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payment->next_payment_date ? $payment->next_payment_date->format('d.m.Y') : '-' }}</div>
                                @if($payment->next_payment_date && $payment->next_payment_date <= now()->addDays(3))
                                    <div class="text-xs text-orange-600 font-medium">Yakında!</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payment->account->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->is_active) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $payment->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($payment->is_active)
                                    <button onclick="toggleRecurringPayment({{ $payment->id }}, false)" class="text-orange-600 hover:text-orange-900 mr-3">⏸️ Durdur</button>
                                @else
                                    <button onclick="toggleRecurringPayment({{ $payment->id }}, true)" class="text-green-600 hover:text-green-900 mr-3">▶️ Başlat</button>
                                @endif
                                <button onclick="editRecurringPayment({{ $payment->id }})" class="text-blue-600 hover:text-blue-900">✏️ Düzenle</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Henüz düzenli ödeme kaydı bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($recurringPayments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $recurringPayments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Düzenli Ödeme Ekleme Modal -->
<div id="addRecurringPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Yeni Düzenli Ödeme Ekle</h3>
                <button onclick="closeModal('addRecurringPaymentModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="addRecurringPaymentForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık *</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Örn: Aylık Elektrik Faturası">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tutar *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₺</span>
                            <input type="number" name="amount" step="0.01" min="0.01" required class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="0.00">
                        </div>
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tür *</label>
                        <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500" id="recurring-type">
                            <option value="">Tür seçiniz...</option>
                            <option value="gelir">Gelir</option>
                            <option value="gider">Gider</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sıklık *</label>
                        <select name="frequency" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Sıklık seçiniz...</option>
                            <option value="gunluk">Günlük</option>
                            <option value="haftalik">Haftalık</option>
                            <option value="aylik">Aylık</option>
                            <option value="uc_aylik">3 Aylık</option>
                            <option value="alti_aylik">6 Aylık</option>
                            <option value="yillik">Yıllık</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
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
                            <option value="bina_geliri">Bina Geliri</option>
                            <option value="diger">Diğer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi *</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                        <input type="date" name="end_date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ayın Günü</label>
                        <input type="number" name="day_of_month" min="1" max="31" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hesap</label>
                        <select name="account_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Hesap seçiniz...</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="building-selection" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina *</label>
                        <select name="building_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Bina seçiniz...</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500" rows="3" placeholder="Düzenli ödeme ile ilgili detaylar..."></textarea>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal('addRecurringPaymentModal')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-medium">
                        🔄 Düzenli Ödeme Ekle
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

// Form event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Düzenli ödeme ekleme formu
    const addRecurringPaymentForm = document.getElementById('addRecurringPaymentForm');
    if (addRecurringPaymentForm) {
        addRecurringPaymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

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
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                alert('Bir hata oluştu!');
            });
        });
    }
});

// Tür seçimine göre bina seçimini göster/gizle
document.getElementById('recurring-type').addEventListener('change', function() {
    const buildingSelection = document.getElementById('building-selection');
    const buildingSelect = document.querySelector('select[name="building_id"]');

    if (this.value === 'gelir') {
        buildingSelection.style.display = 'block';
        buildingSelect.required = true;
    } else if (this.value === 'gider') {
        buildingSelection.style.display = 'none';
        buildingSelect.required = false;
        buildingSelect.value = ''; // Seçimi temizle
    } else {
        buildingSelection.style.display = 'none';
        buildingSelect.required = false;
        buildingSelect.value = '';
    }
});

function toggleRecurringPayment(id, activate) {
    const action = activate ? 'başlat' : 'durdur';
    if (confirm(`Bu düzenli ödemeyi ${action}mak istediğinizden emin misiniz?`)) {
        // Toggle fonksiyonu - backend'de implement edilmeli
        alert(`Düzenli ödeme ${action}ma özelliği yakında eklenecek!`);
    }
}

function editRecurringPayment(id) {
    // Düzenleme fonksiyonu - gelecekte implement edilebilir
    alert('Düzenleme özelliği yakında eklenecek!');
}
</script>
@endsection
