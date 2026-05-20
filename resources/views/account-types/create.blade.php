@extends('layouts.app')

@section('title', 'Yeni Hesap Ekle - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Yeni Hesap Ekle</h1>
                    <p class="text-gray-600 mt-1">Kasa, banka hesabı veya diğer finansal hesaplarınızı oluşturun</p>
                </div>
                <a href="{{ route('account-types.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    ← Geri Dön
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('account-types.store') }}" method="POST" class="p-6">
                @csrf

                <!-- Hesap Türü -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Hesap Türü *</label>
                    <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Hesap türü seçin</option>
                        <option value="kasa" {{ old('type') == 'kasa' ? 'selected' : '' }}>Kasa</option>
                        <option value="banka" {{ old('type') == 'banka' ? 'selected' : '' }}>Banka Hesabı</option>
                        <option value="nakit" {{ old('type') == 'nakit' ? 'selected' : '' }}>Nakit</option>
                        <option value="pos" {{ old('type') == 'pos' ? 'selected' : '' }}>POS Cihazı</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hesap Adı -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Hesap Adı *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Örn: Ana Kasa, İş Bankası Hesabı, POS Cihazı">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Başlangıç Bakiyesi -->
                <div class="mb-6">
                    <label for="initial_balance" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Bakiyesi *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₺</span>
                        <input type="number" name="initial_balance" id="initial_balance" value="{{ old('initial_balance', 0) }}"
                               step="0.01" min="0" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="0.00">
                    </div>
                    @error('initial_balance')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Banka Bilgileri (Sadece banka hesabı seçildiğinde görünür) -->
                <div id="bank-fields" class="hidden space-y-6">
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Banka Bilgileri</h3>

                        <!-- Banka Adı -->
                        <div class="mb-4">
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">Banka Adı</label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Örn: İş Bankası, Garanti BBVA">
                            @error('bank_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Şube Adı -->
                        <div class="mb-4">
                            <label for="branch_name" class="block text-sm font-medium text-gray-700 mb-2">Şube Adı</label>
                            <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Örn: Kadıköy Şubesi">
                            @error('branch_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hesap Numarası -->
                        <div class="mb-4">
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">Hesap Numarası</label>
                            <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono"
                                   placeholder="1234567890">
                            @error('account_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notlar -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Hesap hakkında ek bilgiler...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('account-types.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        İptal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                        Hesap Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const bankFields = document.getElementById('bank-fields');
    const bankNameInput = document.getElementById('bank_name');
    const branchNameInput = document.getElementById('branch_name');
    const accountNumberInput = document.getElementById('account_number');

    function toggleBankFields() {
        if (typeSelect.value === 'banka') {
            bankFields.classList.remove('hidden');
            // Banka alanlarını zorunlu yap
            bankNameInput.required = true;
            branchNameInput.required = true;
            accountNumberInput.required = true;
        } else {
            bankFields.classList.add('hidden');
            // Banka alanlarını opsiyonel yap
            bankNameInput.required = false;
            branchNameInput.required = false;
            accountNumberInput.required = false;
        }
    }

    typeSelect.addEventListener('change', toggleBankFields);

    // Sayfa yüklendiğinde kontrol et
    toggleBankFields();
});
</script>
@endsection
