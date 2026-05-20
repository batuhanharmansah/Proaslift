@extends('layouts.app')

@section('title', 'Muhasebe Kaydı Düzenle - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Muhasebe Kaydı Düzenle</h1>
                    <p class="text-gray-600 mt-1">{{ $accounting->category }} - {{ $accounting->description }}</p>
                </div>
                <a href="{{ route('accounting.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    ← Geri Dön
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('accounting.update', $accounting) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Temel Bilgiler -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                İşlem Türü *
                            </span>
                        </label>
                        <select id="type" name="type" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Tür Seçin</option>
                            <option value="gelir" {{ old('type', $accounting->type) === 'gelir' ? 'selected' : '' }}>💰 Gelir</option>
                            <option value="gider" {{ old('type', $accounting->type) === 'gider' ? 'selected' : '' }}>💸 Gider</option>
                            <option value="maas" {{ old('type', $accounting->type) === 'maas' ? 'selected' : '' }}>👥 Maaş</option>
                            <option value="vergi" {{ old('type', $accounting->type) === 'vergi' ? 'selected' : '' }}>🏛️ Vergi</option>
                            <option value="sigorta" {{ old('type', $accounting->type) === 'sigorta' ? 'selected' : '' }}>🛡️ Sigorta</option>
                        </select>
                        @error('type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Kategori *
                            </span>
                        </label>
                        <input type="text" id="category" name="category" required
                               value="{{ old('category', $accounting->category) }}"
                               placeholder="Örn: Bakım Hizmeti, Malzeme Alımı, Personel Maaşı"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @error('category')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Açıklama -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Açıklama *
                        </span>
                    </label>
                    <textarea id="description" name="description" rows="3" required
                              placeholder="İşlem detaylarını açıklayın..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('description', $accounting->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tutar ve KDV -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tutar (₺) *
                            </span>
                        </label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0" required
                               value="{{ old('amount', $accounting->amount) }}"
                               placeholder="0.00"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @error('amount')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                KDV Oranı (%)
                            </span>
                        </label>
                        <select id="vat_rate" name="vat_rate"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="0" {{ old('vat_rate', $accounting->vat_rate) == '0' ? 'selected' : '' }}>0% (KDV Yok)</option>
                            <option value="1" {{ old('vat_rate', $accounting->vat_rate) == '1' ? 'selected' : '' }}>1%</option>
                            <option value="8" {{ old('vat_rate', $accounting->vat_rate) == '8' ? 'selected' : '' }}>8%</option>
                            <option value="18" {{ old('vat_rate', $accounting->vat_rate) == '18' ? 'selected' : '' }}>18%</option>
                            <option value="20" {{ old('vat_rate', $accounting->vat_rate) == '20' ? 'selected' : '' }}>20%</option>
                        </select>
                        @error('vat_rate')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                İşlem Tarihi *
                            </span>
                        </label>
                        <input type="date" id="transaction_date" name="transaction_date" required
                               value="{{ old('transaction_date', $accounting->transaction_date->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @error('transaction_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Ödeme Yöntemi ve Durum -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Ödeme Yöntemi *
                            </span>
                        </label>
                        <select id="payment_method" name="payment_method" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Yöntem Seçin</option>
                            <option value="nakit" {{ old('payment_method', $accounting->payment_method) === 'nakit' ? 'selected' : '' }}>💵 Nakit</option>
                            <option value="banka_havalesi" {{ old('payment_method', $accounting->payment_method) === 'banka_havalesi' ? 'selected' : '' }}>🏦 Banka Havalesi</option>
                            <option value="kredi_karti" {{ old('payment_method', $accounting->payment_method) === 'kredi_karti' ? 'selected' : '' }}>💳 Kredi Kartı</option>
                            <option value="cek" {{ old('payment_method', $accounting->payment_method) === 'cek' ? 'selected' : '' }}>📄 Çek</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Durum *
                            </span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Durum Seçin</option>
                            <option value="beklemede" {{ old('status', $accounting->status) === 'beklemede' ? 'selected' : '' }}>⏳ Beklemede</option>
                            <option value="odendi" {{ old('status', $accounting->status) === 'odendi' ? 'selected' : '' }}>✅ Ödendi</option>
                            <option value="tahsil_edildi" {{ old('status', $accounting->status) === 'tahsil_edildi' ? 'selected' : '' }}>💰 Tahsil Edildi</option>
                            <option value="iptal" {{ old('status', $accounting->status) === 'iptal' ? 'selected' : '' }}>❌ İptal</option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Fatura Numarası -->
                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Fatura Numarası
                        </span>
                    </label>
                    <input type="text" id="invoice_number" name="invoice_number"
                           value="{{ old('invoice_number', $accounting->invoice_number) }}"
                           placeholder="Fatura numarası (opsiyonel)"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('invoice_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- İlişkili Kayıtlar -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                İlgili Bina
                            </span>
                        </label>
                        <select id="building_id" name="building_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Bina Seçin (Opsiyonel)</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}" {{ old('building_id', $accounting->building_id) == $building->id ? 'selected' : '' }}>
                                    {{ $building->name }} - {{ $building->address }}
                                </option>
                            @endforeach
                        </select>
                        @error('building_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                İlgili Personel
                            </span>
                        </label>
                        <select id="employee_id" name="employee_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Personel Seçin (Opsiyonel)</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id', $accounting->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->position_label }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notlar -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Notlar
                        </span>
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Ek notlar (opsiyonel)..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('notes', $accounting->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('accounting.index') }}"
                       class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        İptal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all">
                        💾 Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // KDV oranı değiştiğinde otomatik hesaplama
    const amountInput = document.getElementById('amount');
    const vatRateSelect = document.getElementById('vat_rate');

    function calculateVAT() {
        const amount = parseFloat(amountInput.value) || 0;
        const vatRate = parseFloat(vatRateSelect.value) || 0;
        const vatAmount = amount * (vatRate / 100);
        const totalAmount = amount + vatAmount;

        // Burada toplam tutarı gösterebilirsiniz
        console.log(`Tutar: ${amount}₺, KDV: ${vatAmount}₺, Toplam: ${totalAmount}₺`);
    }

    amountInput.addEventListener('input', calculateVAT);
    vatRateSelect.addEventListener('change', calculateVAT);
});
</script>
@endsection
