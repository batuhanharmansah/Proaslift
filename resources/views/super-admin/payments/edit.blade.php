@extends('super-admin.layouts.app')

@section('title', 'Ödeme Düzenle - ' . $payment->company->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                <span class="text-lg font-medium text-white">{{ substr($payment->company->name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ödeme Düzenle</h1>
                <p class="text-gray-600">{{ $payment->company->name }} - ₺{{ number_format($payment->amount, 2) }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('super-admin.payments.show', $payment) }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Detayları Görüntüle
            </a>
            <a href="{{ route('super-admin.payments.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                ← Geri Dön
            </a>
        </div>
    </div>

    <form action="{{ route('super-admin.payments.update', $payment) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Firma Bilgileri (Sadece Görüntüleme) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Firma Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Firma</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ substr($payment->company->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->company->name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->company->email }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Detayları</label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-sm">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">Plan:</span>
                                <span class="font-medium">{{ $payment->company->subscription_plan ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Aylık Ücret:</span>
                                <span class="font-medium text-green-600">₺{{ number_format($payment->company->monthly_fee, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('super-admin.companies.show', $payment->company) }}"
                   class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                    Firma detaylarını görüntüle →
                </a>
            </div>
        </div>

        <!-- Ödeme Bilgileri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
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
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" required
                           min="0" step="0.01"
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
                    <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $payment->invoice_number) }}"
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
                           value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required
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
                           value="{{ old('due_date', $payment->due_date->format('Y-m-d')) }}" required
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
                        <option value="pending" {{ old('status', $payment->status) === 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="paid" {{ old('status', $payment->status) === 'paid' ? 'selected' : '' }}>Ödendi</option>
                        <option value="overdue" {{ old('status', $payment->status) === 'overdue' ? 'selected' : '' }}>Gecikmiş</option>
                        <option value="cancelled" {{ old('status', $payment->status) === 'cancelled' ? 'selected' : '' }}>İptal</option>
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
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('notes', $payment->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Durum Değişikliği Uyarıları -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Durum Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Mevcut Durum -->
                <div class="p-4 border rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Mevcut Durum</h4>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($payment->status === 'paid') bg-green-100 text-green-800
                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($payment->status === 'overdue') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $payment->statusLabel }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">
                        Son güncelleme: {{ $payment->updated_at->format('d.m.Y H:i') }}
                    </p>
                </div>

                <!-- Vade Durumu -->
                <div class="p-4 border rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Vade Durumu</h4>
                    @if($payment->due_date->isPast() && $payment->status !== 'paid')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            Vadesi Geçmiş
                        </span>
                        <p class="text-xs text-red-600 mt-1">{{ $payment->due_date->diffForHumans() }}</p>
                    @elseif($payment->due_date->diffInDays() <= 3 && $payment->status !== 'paid')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Vade Yaklaşıyor
                        </span>
                        <p class="text-xs text-yellow-600 mt-1">{{ $payment->due_date->diffInDays() }} gün kaldı</p>
                    @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            Vade İçinde
                        </span>
                        <p class="text-xs text-green-600 mt-1">{{ $payment->due_date->format('d.m.Y') }}</p>
                    @endif
                </div>

                <!-- Firma Durumu -->
                <div class="p-4 border rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Firma Durumu</h4>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        {{ $payment->company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $payment->company->is_active ? 'Aktif' : 'Askıda' }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $payment->company->subscription_plan ?? 'Plan Yok' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button type="button" onclick="markAsPaid()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ödendi İşaretle
                </button>

                <button type="button" onclick="markAsOverdue()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Gecikmiş İşaretle
                </button>

                <button type="button" onclick="setCurrentDate()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bugün Tarihi Ayarla
                </button>

                <button type="button" onclick="resetToCompanyFee()"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Firma Ücretini Ayarla
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="flex space-x-3">
                <a href="{{ route('super-admin.payments.show', $payment) }}"
                   class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    İptal
                </a>
                @if($payment->status === 'pending')
                    <form action="{{ route('super-admin.payments.mark-paid', $payment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Bu ödemeyi ödendi olarak işaretlemek istediğinizden emin misiniz?')"
                                class="px-6 py-3 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors">
                            ✅ Ödendi Olarak İşaretle
                        </button>
                    </form>
                @endif
            </div>
            <div class="flex space-x-3">
                <button type="submit" name="action" value="draft"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors">
                    📝 Taslak Kaydet
                </button>
                <button type="submit" name="action" value="update"
                        class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all">
                    ✅ Değişiklikleri Kaydet
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyFee = {{ $payment->company->monthly_fee }};

    window.markAsPaid = function() {
        document.getElementById('status').value = 'paid';
        document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
    };

    window.markAsOverdue = function() {
        document.getElementById('status').value = 'overdue';
    };

    window.setCurrentDate = function() {
        document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
    };

    window.resetToCompanyFee = function() {
        if (confirm('Ödeme tutarını firma aylık ücretine (₺' + companyFee.toFixed(2) + ') sıfırlamak istiyor musunuz?')) {
            document.getElementById('amount').value = companyFee;
        }
    };
});
</script>
@endsection
