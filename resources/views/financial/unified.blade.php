@extends('layouts.app')

@section('title', 'Alacaklar, Borçlar ve Düzenli Ödemeler - Finansal Yönetim')

@section('content')
<div class="p-4 md:p-6 max-w-full overflow-x-hidden">
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Alacaklar, Borçlar ve Düzenli Ödemeler</h1>
                <p class="text-gray-600 mt-1">Tüm finansal işlemlerinizi tek sayfadan yönetin</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="button" onclick="document.getElementById('addFinancialItemModal').classList.remove('hidden')"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    ➕ Yeni İşlem
                </button>
                <a href="{{ route('financial.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                    ← Finansal Yönetim
                </a>
            </div>
        </div>
    </div>

    <!-- Özet kartları -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3">
            <p class="text-xs text-gray-500">Toplam Alacak</p>
            <p class="text-sm font-bold text-green-600">₺{{ number_format($stats['total_receivables'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3">
            <p class="text-xs text-gray-500">Toplam Borç</p>
            <p class="text-sm font-bold text-red-600">₺{{ number_format($stats['total_payables'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3">
            <p class="text-xs text-gray-500">Alacak Kayıt</p>
            <p class="text-sm font-bold text-gray-900">{{ $stats['receivables_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3">
            <p class="text-xs text-gray-500">Borç Kayıt</p>
            <p class="text-sm font-bold text-gray-900">{{ $stats['payables_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3">
            <p class="text-xs text-gray-500">Düzenli Ödeme</p>
            <p class="text-sm font-bold text-gray-900">{{ $stats['recurring_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3">
            <p class="text-xs text-gray-500">Aktif Düzenli</p>
            <p class="text-sm font-bold text-purple-600">{{ $stats['recurring_active'] }}</p>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ url('finansal/islemler') }}" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[120px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tür</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>Tümü</option>
                    <option value="alacak" {{ request('type') === 'alacak' ? 'selected' : '' }}>Alacak</option>
                    <option value="borc" {{ request('type') === 'borc' ? 'selected' : '' }}>Borç</option>
                    <option value="duzenli" {{ request('type') === 'duzenli' ? 'selected' : '' }}>Düzenli Ödeme</option>
                </select>
            </div>
            <div class="min-w-[140px] flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Başlık, bina..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Durum</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Tümü</option>
                    <option value="beklemede" {{ request('status') === 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                    <option value="kismi_odendi" {{ request('status') === 'kismi_odendi' ? 'selected' : '' }}>Kısmi</option>
                    <option value="tamamlandi" {{ request('status') === 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Düzenli)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif (Düzenli)</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Başlangıç</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Bitiş</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg text-sm font-medium hover:bg-primary-600">Filtrele</button>
        </form>
    </div>

    <!-- Birleşik liste -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tüm İşlemler</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlık</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kalan / Durum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bina / Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıklık</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paginator as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($item->row_type === 'alacak')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Alacak</span>
                                @elseif($item->row_type === 'borc')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Borç</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Düzenli</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">₺{{ number_format($item->amount, 0) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($item->date)
                                    {{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($item->remaining !== null)
                                    <span class="text-sm font-medium text-yellow-600">₺{{ number_format($item->remaining, 0) }}</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $item->status_label }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $item->building_name ?? $item->category ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $item->frequency ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-sm">
                                @if($item->row_type === 'alacak' && $item->remaining > 0)
                                    <button type="button" onclick="openPaymentModal('receivable', {{ $item->source_id }}, '{{ addslashes($item->title) }}', {{ $item->remaining }})" class="text-green-600 hover:text-green-800 mr-2">Tahsilat</button>
                                @endif
                                @if($item->row_type === 'borc' && $item->remaining > 0)
                                    <button type="button" onclick="openPaymentModal('payable', {{ $item->source_id }}, '{{ addslashes($item->title) }}', {{ $item->remaining }})" class="text-red-600 hover:text-red-800 mr-2">Ödeme</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Henüz kayıt yok. "Yeni İşlem" ile alacak, borç veya düzenli ödeme ekleyebilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($paginator->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $paginator->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@include('financial.partials.unified-modal', ['buildings' => $buildings, 'accounts' => $accounts])
@include('financial.partials.payment-modal', ['accounts' => $accounts])
@endsection

@push('scripts')
<script>
function openPaymentModal(type, id, title, amount) {
    window._paymentData = { type, id, title, amount };
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentTitle').textContent = title;
    document.getElementById('paymentAmount').value = amount;
    var titleEl = document.getElementById('paymentModalTitle');
    var btnEl = document.getElementById('paymentSubmitBtn');
    if (type === 'receivable') {
        if (titleEl) titleEl.textContent = 'Tahsilat Al';
        if (btnEl) btnEl.textContent = 'Tahsilat Al';
    } else {
        if (titleEl) titleEl.textContent = 'Ödeme Yap';
        if (btnEl) btnEl.textContent = 'Ödeme Yap';
    }
}
function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    window._paymentData = null;
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const d = window._paymentData;
            if (!d) return;
            const url = d.type === 'receivable' ? '{{ route("financial.receive-payment") }}' : '{{ route("financial.make-payment") }}';
            const body = d.type === 'receivable'
                ? { receivable_id: d.id, amount: document.getElementById('paymentAmount').value, account_id: document.getElementById('paymentAccountId').value, _token: '{{ csrf_token() }}' }
                : { payable_id: d.id, amount: document.getElementById('paymentAmount').value, account_id: document.getElementById('paymentAccountId').value, _token: '{{ csrf_token() }}' };
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(body)
            }).then(r => r.json()).then(data => {
                if (data.success) { alert('İşlem başarılı.'); location.reload(); }
                else { alert(data.message || 'Hata oluştu'); }
            }).catch(() => alert('Bir hata oluştu'));
        });
    }
});
</script>
@endpush
