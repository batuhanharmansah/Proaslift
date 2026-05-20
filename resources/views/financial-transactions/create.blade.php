@extends('layouts.app')

@section('title', 'Yeni Finansal İşlem - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Yeni Finansal İşlem</h1>
                    <p class="text-gray-600 mt-1">Gelir, gider veya transfer işlemi ekleyin</p>
                </div>
                <a href="{{ route('financial-transactions.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    ← Geri Dön
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('financial-transactions.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Sol Kolon -->
                    <div class="space-y-6">
                        <!-- İşlem Türü -->
                        <div>
                            <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">İşlem Türü *</label>
                            <select name="transaction_type" id="transaction_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">İşlem türü seçin</option>
                                <option value="gelir" {{ old('transaction_type') == 'gelir' ? 'selected' : '' }}>Gelir</option>
                                <option value="gider" {{ old('transaction_type') == 'gider' ? 'selected' : '' }}>Gider</option>
                                <option value="transfer" {{ old('transaction_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            @error('transaction_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                            <select name="category" id="category" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Kategori seçin</option>
                                <optgroup label="Gelir Kategorileri" class="gelir-categories" style="display: none;">
                                    <option value="bakim_geliri" {{ old('category') == 'bakim_geliri' ? 'selected' : '' }}>Bakım Geliri</option>
                                    <option value="bina_geliri" {{ old('category') == 'bina_geliri' ? 'selected' : '' }}>Bina Geliri</option>
                                    <option value="pos_geliri" {{ old('category') == 'pos_geliri' ? 'selected' : '' }}>POS Geliri</option>
                                    <option value="nakit_geliri" {{ old('category') == 'nakit_geliri' ? 'selected' : '' }}>Nakit Geliri</option>
                                    <option value="diger" {{ old('category') == 'diger' ? 'selected' : '' }}>Diğer Gelir</option>
                                </optgroup>
                                <optgroup label="Gider Kategorileri" class="gider-categories" style="display: none;">
                                    <option value="dukkan_gideri" {{ old('category') == 'dukkan_gideri' ? 'selected' : '' }}>Dükkan Gideri</option>
                                    <option value="yedek_parca_gideri" {{ old('category') == 'yedek_parca_gideri' ? 'selected' : '' }}>Yedek Parça Gideri</option>
                                    <option value="personel_maasi" {{ old('category') == 'personel_maasi' ? 'selected' : '' }}>Personel Maaşı</option>
                                    <option value="vergi" {{ old('category') == 'vergi' ? 'selected' : '' }}>Vergi</option>
                                    <option value="sigorta" {{ old('category') == 'sigorta' ? 'selected' : '' }}>Sigorta</option>
                                    <option value="genel_gider" {{ old('category') == 'genel_gider' ? 'selected' : '' }}>Genel Gider</option>
                                </optgroup>
                                <optgroup label="Transfer Kategorileri" class="transfer-categories" style="display: none;">
                                    <option value="banka_transfer" {{ old('category') == 'banka_transfer' ? 'selected' : '' }}>Banka Transferi</option>
                                    <option value="kasa_giris" {{ old('category') == 'kasa_giris' ? 'selected' : '' }}>Kasa Girişi</option>
                                    <option value="kasa_cikis" {{ old('category') == 'kasa_cikis' ? 'selected' : '' }}>Kasa Çıkışı</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tutar -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Tutar *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₺</span>
                                <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                                       step="0.01" min="0.01" required
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0.00">
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- KDV Oranı -->
                        <div>
                            <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-2">KDV Oranı (%)</label>
                            <input type="number" name="vat_rate" id="vat_rate" value="{{ old('vat_rate', 20) }}"
                                   step="0.01" min="0" max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="20">
                            @error('vat_rate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tarih -->
                        <div>
                            <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">İşlem Tarihi *</label>
                            <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('transaction_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Saat -->
                        <div>
                            <label for="transaction_time" class="block text-sm font-medium text-gray-700 mb-2">İşlem Saati</label>
                            <input type="time" name="transaction_time" id="transaction_time" value="{{ old('transaction_time') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('transaction_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sağ Kolon -->
                    <div class="space-y-6">
                        <!-- Hesap Seçimi -->
                        <div id="account-selection">
                            <!-- Gelir için hedef hesap -->
                            <div id="to-account-section" class="hidden">
                                <label for="to_account_id" class="block text-sm font-medium text-gray-700 mb-2">Hedef Hesap *</label>
                                <select name="to_account_id" id="to_account_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Hedef hesap seçin</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} ({{ $account->type_label }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_account_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gider için kaynak hesap -->
                            <div id="from-account-section" class="hidden">
                                <label for="from_account_id" class="block text-sm font-medium text-gray-700 mb-2">Kaynak Hesap *</label>
                                <select name="from_account_id" id="from_account_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Kaynak hesap seçin</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} ({{ $account->type_label }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_account_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Transfer için her iki hesap -->
                            <div id="transfer-accounts-section" class="hidden space-y-4">
                                <div>
                                    <label for="from_account_id" class="block text-sm font-medium text-gray-700 mb-2">Kaynak Hesap *</label>
                                    <select name="from_account_id" id="from_account_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Kaynak hesap seçin</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }} ({{ $account->type_label }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="to_account_id" class="block text-sm font-medium text-gray-700 mb-2">Hedef Hesap *</label>
                                    <select name="to_account_id" id="to_account_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Hedef hesap seçin</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }} ({{ $account->type_label }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bina Seçimi (Sadece bina geliri için) -->
                        <div id="building-section" class="hidden">
                            <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">Bina</label>
                            <select name="building_id" id="building_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Bina seçin</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                        {{ $building->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('building_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Çalışan Seçimi -->
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Çalışan</label>
                            <select name="employee_id" id="employee_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Çalışan seçin</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ödeme Yöntemi -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Ödeme Yöntemi</label>
                            <select name="payment_method" id="payment_method"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Ödeme yöntemi seçin</option>
                                <option value="nakit" {{ old('payment_method') == 'nakit' ? 'selected' : '' }}>Nakit</option>
                                <option value="banka_havalesi" {{ old('payment_method') == 'banka_havalesi' ? 'selected' : '' }}>Banka Havalesi</option>
                                <option value="kredi_karti" {{ old('payment_method') == 'kredi_karti' ? 'selected' : '' }}>Kredi Kartı</option>
                                <option value="cek" {{ old('payment_method') == 'cek' ? 'selected' : '' }}>Çek</option>
                                <option value="pos" {{ old('payment_method') == 'pos' ? 'selected' : '' }}>POS</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            @error('payment_method')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fatura Numarası -->
                        <div>
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">Fatura Numarası</label>
                            <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="FAT-2025-001">
                            @error('invoice_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Makbuz Numarası -->
                        <div>
                            <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-2">Makbuz Numarası</label>
                            <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="MAK-2025-001">
                            @error('receipt_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Açıklama -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Açıklama *</label>
                    <textarea name="description" id="description" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="İşlem açıklaması...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notlar -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Ek notlar...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('financial-transactions.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        İptal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                        İşlemi Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const transactionType = document.getElementById('transaction_type');
    const category = document.getElementById('category');
    const toAccountSection = document.getElementById('to-account-section');
    const fromAccountSection = document.getElementById('from-account-section');
    const transferAccountsSection = document.getElementById('transfer-accounts-section');
    const buildingSection = document.getElementById('building-section');

    function updateFormFields() {
        const selectedType = transactionType.value;
        const selectedCategory = category.value;

        // Hesap seçimi güncelleme
        toAccountSection.classList.add('hidden');
        fromAccountSection.classList.add('hidden');
        transferAccountsSection.classList.add('hidden');

        if (selectedType === 'gelir') {
            toAccountSection.classList.remove('hidden');
        } else if (selectedType === 'gider') {
            fromAccountSection.classList.remove('hidden');
        } else if (selectedType === 'transfer') {
            transferAccountsSection.classList.remove('hidden');
        }

        // Kategori seçeneklerini güncelleme
        const gelirCategories = document.querySelectorAll('.gelir-categories option');
        const giderCategories = document.querySelectorAll('.gider-categories option');
        const transferCategories = document.querySelectorAll('.transfer-categories option');

        gelirCategories.forEach(opt => opt.style.display = 'none');
        giderCategories.forEach(opt => opt.style.display = 'none');
        transferCategories.forEach(opt => opt.style.display = 'none');

        if (selectedType === 'gelir') {
            gelirCategories.forEach(opt => opt.style.display = 'block');
            document.querySelector('.gelir-categories').style.display = 'block';
            document.querySelector('.gider-categories').style.display = 'none';
            document.querySelector('.transfer-categories').style.display = 'none';
        } else if (selectedType === 'gider') {
            giderCategories.forEach(opt => opt.style.display = 'block');
            document.querySelector('.gelir-categories').style.display = 'none';
            document.querySelector('.gider-categories').style.display = 'block';
            document.querySelector('.transfer-categories').style.display = 'none';
        } else if (selectedType === 'transfer') {
            transferCategories.forEach(opt => opt.style.display = 'block');
            document.querySelector('.gelir-categories').style.display = 'none';
            document.querySelector('.gider-categories').style.display = 'none';
            document.querySelector('.transfer-categories').style.display = 'block';
        }

        // Bina seçimi güncelleme
        if (selectedCategory === 'bina_geliri') {
            buildingSection.classList.remove('hidden');
        } else {
            buildingSection.classList.add('hidden');
        }
    }

    transactionType.addEventListener('change', updateFormFields);
    category.addEventListener('change', updateFormFields);

    // Sayfa yüklendiğinde kontrol et
    updateFormFields();
});
</script>
@endsection
