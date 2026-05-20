@extends('employee.layouts.app')

@section('title', 'Bakım Raporu Oluştur')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bakım Raporu Oluştur</h1>
                <p class="text-gray-600 mt-1">{{ $maintenance->building->name }} - {{ $maintenance->maintenanceTypeLabel }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-xs font-medium rounded-full
                    @if($maintenance->priority === 'acil') bg-red-100 text-red-800
                    @elseif($maintenance->priority === 'yuksek') bg-orange-100 text-orange-800
                    @elseif($maintenance->priority === 'normal') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $maintenance->priorityLabel }}
                </span>
                <a href="{{ route('employee.maintenance.show', $maintenance) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    ← Geri Dön
                </a>
            </div>
        </div>
    </div>

    <!-- Bakım Bilgileri -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Bakım Bilgileri</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bina</label>
                <p class="text-sm text-gray-900">{{ $maintenance->building->name }}</p>
                <p class="text-xs text-gray-500">{{ $maintenance->building->address }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Planlanan Tarih</label>
                <p class="text-sm text-gray-900">{{ $maintenance->scheduled_date->format('d.m.Y') }}</p>
                @if($maintenance->scheduled_time)
                    <p class="text-xs text-gray-500">{{ $maintenance->scheduled_time }}</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bakım Türü</label>
                <p class="text-sm text-gray-900">{{ $maintenance->maintenanceTypeLabel }}</p>
            </div>
        </div>
        @if($maintenance->description)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                <p class="text-sm text-gray-600">{{ $maintenance->description }}</p>
            </div>
        @endif
    </div>

    <!-- Rapor Formu -->
    <form action="{{ route('employee.maintenance.store-report', $maintenance) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Çalışma Saatleri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Çalışma Saatleri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            İşe Başlama Zamanı *
                        </span>
                    </label>
                    <input type="datetime-local" id="start_time" name="start_time" required
                           value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('start_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            İş Bitirme Zamanı
                        </span>
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time"
                           value="{{ old('end_time') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('end_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">İş devam ediyorsa boş bırakabilirsiniz</p>
                </div>
            </div>
        </div>

        <!-- Yapılan İşler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Yapılan İşler</h3>
            <div>
                <label for="work_description" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        İş Açıklaması *
                    </span>
                </label>
                <textarea id="work_description" name="work_description" rows="5" required
                          placeholder="Yapılan işleri detaylı bir şekilde açıklayın..."
                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('work_description') }}</textarea>
                @error('work_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Kullanılan Ürünler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kullanılan Ürünler</h3>
            <div id="products-container">
                <div class="product-row grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ürün</label>
                        <select name="used_products[0][product_id]" class="product-select w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Ürün Seçin</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" data-unit="{{ $product->unit }}" data-category="{{ $product->category_label }}">
                                    {{ $product->name }} ({{ $product->code }}) - {{ $product->category_label }} - ₺{{ number_format($product->sale_price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Miktar</label>
                        <input type="number" name="used_products[0][quantity]" min="1" step="0.01"
                               class="quantity-input w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Birim Fiyat</label>
                        <input type="number" name="used_products[0][unit_price]" step="0.01" readonly
                               class="unit-price w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50"
                               placeholder="0.00">
                    </div>
                    <div>
                        <button type="button" class="remove-product px-4 py-3 text-red-600 hover:text-red-800 transition-colors" style="display: none;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" id="add-product" class="flex items-center px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Ürün Ekle
            </button>

            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Toplam Maliyet:</span>
                    <span id="total-cost" class="text-lg font-bold text-primary-600">₺0.00</span>
                </div>
            </div>
        </div>

        <!-- Sorunlar ve Öneriler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sorunlar ve Öneriler</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="problems_found" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Tespit Edilen Sorunlar
                        </span>
                    </label>
                    <textarea id="problems_found" name="problems_found" rows="4"
                              placeholder="Tespit ettiğiniz sorunları yazın..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('problems_found') }}</textarea>
                </div>

                <div>
                    <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Öneriler
                        </span>
                    </label>
                    <textarea id="recommendations" name="recommendations" rows="4"
                              placeholder="Gelecek için önerilerinizi yazın..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('recommendations') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Tamamlanma Durumu -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">İş Durumu</h3>
            <div>
                <label for="completion_status" class="block text-sm font-medium text-gray-700 mb-2">Tamamlanma Durumu *</label>
                <select id="completion_status" name="completion_status" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Durum Seçin</option>
                    <option value="tamamlandi" {{ old('completion_status') === 'tamamlandi' ? 'selected' : '' }}>✅ Tamamlandı</option>
                    <option value="kismi_tamamlandi" {{ old('completion_status') === 'kismi_tamamlandi' ? 'selected' : '' }}>⏳ Kısmi Tamamlandı</option>
                    <option value="ertelendi" {{ old('completion_status') === 'ertelendi' ? 'selected' : '' }}>⏸️ Ertelendi</option>
                </select>
                @error('completion_status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Müşteri Onayı -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Müşteri Onayı</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="customer_signature" value="1" {{ old('customer_signature') ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2">
                        <span class="ml-2 text-sm font-medium text-gray-700">Müşteri onayı alındı</span>
                    </label>
                </div>
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Onaylayan Kişi</label>
                    <input type="text" id="customer_name" name="customer_name"
                           value="{{ old('customer_name') }}"
                           placeholder="Onaylayan kişinin adı soyadı"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                </div>
            </div>
            <div class="mt-4">
                <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">Müşteri Notları</label>
                <textarea id="customer_notes" name="customer_notes" rows="3"
                          placeholder="Müşterinin belirttiği notlar..."
                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('customer_notes') }}</textarea>
            </div>
        </div>

        <!-- Fotoğraflar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Fotoğraflar</h3>
            <div>
                <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        İş Fotoğrafları
                    </span>
                </label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                <p class="text-xs text-gray-500 mt-1">Birden fazla fotoğraf seçebilirsiniz (JPG, PNG)</p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('employee.maintenance.show', $maintenance) }}"
               class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                İptal
            </a>
            <div class="flex space-x-3">
                <button type="submit" name="action" value="draft"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors">
                    📝 Taslak Kaydet
                </button>
                <button type="submit" name="action" value="complete"
                        class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all">
                    ✅ Raporu Tamamla
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 1;

    // Ürün ekleme
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const originalRow = container.querySelector('.product-row');
        const newRow = originalRow.cloneNode(true);

        // Index'leri güncelle
        newRow.querySelectorAll('select, input').forEach(element => {
            if (element.name) {
                element.name = element.name.replace('[0]', '[' + productIndex + ']');
            }
            element.value = '';
        });

        // Select içeriğini kopyala
        const originalSelect = originalRow.querySelector('.product-select');
        const newSelect = newRow.querySelector('.product-select');
        newSelect.innerHTML = originalSelect.innerHTML;

        // Remove butonunu göster
        newRow.querySelector('.remove-product').style.display = 'block';

        container.appendChild(newRow);
        productIndex++;

        updateRemoveButtons();
        setupProductRow(newRow);
    });

    // Ürün silme
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product')) {
            e.target.closest('.product-row').remove();
            updateRemoveButtons();
            calculateTotal();
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-product');
            if (rows.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    function setupProductRow(row) {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price');

        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.dataset.price || 0;
            unitPriceInput.value = price;
            calculateTotal();
        });

        quantityInput.addEventListener('input', calculateTotal);
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            total += quantity * unitPrice;
        });

        document.getElementById('total-cost').textContent = '₺' + total.toFixed(2);
    }

    // İlk satır için event listener'ları ekle
    setupProductRow(document.querySelector('.product-row'));
});
</script>
@endsection
