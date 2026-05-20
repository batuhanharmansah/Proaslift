@extends('super-admin.layouts.app')

@section('title', 'Yeni Ödeme Kaydı')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Yeni Ödeme Kaydı</h1>
            <p class="text-gray-600 mt-1">Firma için yeni ödeme kaydı oluşturun</p>
        </div>
        <a href="{{ route('super-admin.payments.index') }}"
           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            ← Geri Dön
        </a>
    </div>

    <form action="{{ route('super-admin.payments.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Firma Seçimi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Firma Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Firma Seçin *
                        </span>
                    </label>
                    <select id="company_id" name="company_id" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Firma Seçin</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" data-fee="{{ $company->monthly_fee }}" data-plan="{{ $company->subscription_plan }}"
                                    {{ old('company_id', request('company_id')) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }} - ₺{{ number_format($company->monthly_fee, 0) }}/ay
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="company-info" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Firma Detayları</label>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">Plan:</span>
                                <span id="company-plan" class="font-medium">-</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">Aylık Ücret:</span>
                                <span id="company-fee" class="font-medium text-green-600">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Son Ödeme:</span>
                                <span id="company-last-payment" class="font-medium">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ödeme Bilgileri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ödeme Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Ödeme Tutarı (₺) *
                        </span>
                    </label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required
                           min="0" step="0.01"
                           placeholder="299.00"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Fatura Numarası
                        </span>
                    </label>
                    <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}"
                           placeholder="2025/001"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('invoice_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Ödeme Tarihi *
                        </span>
                    </label>
                    <input type="date" id="payment_date" name="payment_date"
                           value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('payment_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Vade Tarihi *
                        </span>
                    </label>
                    <input type="date" id="due_date" name="due_date"
                           value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ödeme Durumu *
                        </span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Durum Seçin</option>
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Ödendi</option>
                        <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>Gecikmiş</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>İptal</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h10a2 2 0 002-2V8m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2"/>
                            </svg>
                            Notlar
                        </span>
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Ödeme ile ilgili notlar..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Hızlı Seçenekler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;" >
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Hızlı Seçenekler</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button type="button" onclick="setCurrentMonth()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bu Ay İçin Ayarla
                </button>

                <button type="button" onclick="setNextMonth()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Gelecek Ay İçin Ayarla
                </button>

                <button type="button" onclick="markAsPaid()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ödendi Olarak İşaretle
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200" style="background: gainsboro !important;"  >
            <a href="{{ route('super-admin.payments.index') }}"
               class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                İptal
            </a>
            <div class="flex space-x-3">
                <button type="submit" name="action" value="draft"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors">
                    📝 Taslak Kaydet
                </button>
                <button type="submit" name="action" value="create"
                        class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all">
                    ✅ Ödeme Kaydı Oluştur
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company_id');
    const companyInfo = document.getElementById('company-info');
    const amountInput = document.getElementById('amount');

    // Firma seçildiğinde bilgileri göster
    companySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            const fee = selectedOption.dataset.fee;
            const plan = selectedOption.dataset.plan;

            document.getElementById('company-plan').textContent = plan || 'Belirtilmemiş';
            document.getElementById('company-fee').textContent = '₺' + parseFloat(fee).toLocaleString('tr-TR', {minimumFractionDigits: 2});
            document.getElementById('company-last-payment').textContent = 'Yükleniyor...';

            // Aylık ücreti otomatik doldur
            if (!amountInput.value || amountInput.dataset.manual !== 'true') {
                amountInput.value = fee;
            }

            companyInfo.classList.remove('hidden');

            // Son ödeme bilgisini AJAX ile getir (isteğe bağlı)
            fetchLastPayment(selectedOption.value);
        } else {
            companyInfo.classList.add('hidden');
        }
    });

    // Miktar manuel değiştirildiğinde işaretle
    amountInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Sayfa yüklendiğinde seçili firma varsa bilgileri göster
    if (companySelect.value) {
        companySelect.dispatchEvent(new Event('change'));
    }
});

function fetchLastPayment(companyId) {
    // Bu fonksiyon AJAX ile son ödeme bilgisini getirebilir
    // Şimdilik basit bir placeholder
    setTimeout(() => {
        document.getElementById('company-last-payment').textContent = 'Veri yok';
    }, 500);
}

function setCurrentMonth() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

    document.getElementById('payment_date').value = firstDay.toISOString().split('T')[0];
    document.getElementById('due_date').value = lastDay.toISOString().split('T')[0];

    // Fatura numarasını otomatik oluştur
    const invoiceNumber = now.getFullYear() + '/' + String(now.getMonth() + 1).padStart(2, '0') + '/' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    document.getElementById('invoice_number').value = invoiceNumber;
}

function setNextMonth() {
    const now = new Date();
    const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
    const lastDayNextMonth = new Date(now.getFullYear(), now.getMonth() + 2, 0);

    document.getElementById('payment_date').value = nextMonth.toISOString().split('T')[0];
    document.getElementById('due_date').value = lastDayNextMonth.toISOString().split('T')[0];

    // Fatura numarasını otomatik oluştur
    const invoiceNumber = nextMonth.getFullYear() + '/' + String(nextMonth.getMonth() + 1).padStart(2, '0') + '/' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    document.getElementById('invoice_number').value = invoiceNumber;
}

function markAsPaid() {
    document.getElementById('status').value = 'paid';
    document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
}
</script>
@endsection
