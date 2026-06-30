@extends('layouts.app')

@section('title', 'Yeni Teklif - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Yeni Teklif</h1>
            <p class="text-gray-600 mt-1">Bakım, revizyon, montaj veya arıza için teklif hazırlayın</p>
        </div>
        <a href="{{ route('quotations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl">Geri Dön</a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quotations.store') }}" class="space-y-6" id="quotationForm">
        @csrf

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Teklif Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teklif Tipi</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="maintenance" @selected(old('type') === 'maintenance')>Bakım Teklifi</option>
                        <option value="modernization" @selected(old('type') === 'modernization')>Revizyon / Modernizasyon</option>
                        <option value="repair" @selected(old('type') === 'repair')>Arıza / Parça</option>
                        <option value="installation" @selected(old('type') === 'installation')>Yeni Montaj</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bina</label>
                    <select name="building_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Bina seçmeden oluştur</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" @selected((string) old('building_id', $selectedBuilding?->id) === (string) $building->id)>
                                {{ $building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Geçerlilik Tarihi</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until', now('Europe/Istanbul')->addDays(15)->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Müşteri Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri / Bina Adı</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $selectedBuilding?->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yetkili Kişi</label>
                    <input type="text" name="customer_contact_name" value="{{ old('customer_contact_name', $selectedBuilding?->responsible_person) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $selectedBuilding?->responsible_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $selectedBuilding?->responsible_email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                    <textarea name="customer_address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('customer_address', $selectedBuilding?->address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Asansör / Ünite Bilgisi</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="unit[elevator_code]" value="{{ old('unit.elevator_code', $selectedBuilding?->elevator_code) }}" placeholder="Asansör kodu" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="text" name="unit[elevator_type]" value="{{ old('unit.elevator_type', $selectedBuilding?->elevator_type) }}" placeholder="Tip" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="text" name="unit[brand]" value="{{ old('unit.brand', $selectedBuilding?->elevator_brand) }}" placeholder="Marka" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="text" name="unit[model]" value="{{ old('unit.model', $selectedBuilding?->elevator_model) }}" placeholder="Model" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="number" name="unit[stop_count]" value="{{ old('unit.stop_count', $selectedBuilding?->floor_count) }}" placeholder="Durak/Kat" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="number" name="unit[capacity_kg]" value="{{ old('unit.capacity_kg') }}" placeholder="Kapasite kg" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="text" name="unit[speed]" value="{{ old('unit.speed') }}" placeholder="Hız" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="text" name="unit[current_label_color]" value="{{ old('unit.current_label_color', $selectedBuilding?->current_label_color) }}" placeholder="Etiket rengi" class="px-4 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Fiyat Kalemleri</h2>
                <button type="button" onclick="addQuotationItem()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Kalem Ekle</button>
            </div>
            <div id="quotationItems" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 quotation-item">
                    <select name="items[0][category]" class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="service">Hizmet</option>
                        <option value="material">Malzeme</option>
                        <option value="labor">İşçilik</option>
                        <option value="inspection">Kontrol</option>
                    </select>
                    <input type="text" name="items[0][description]" placeholder="Açıklama" class="md:col-span-4 px-3 py-2 border border-gray-300 rounded-lg" required>
                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" value="1" placeholder="Miktar" class="md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg" required>
                    <input type="text" name="items[0][unit]" value="adet" placeholder="Birim" class="md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg">
                    <input type="number" step="0.01" min="0" name="items[0][unit_price]" placeholder="Birim fiyat" class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg" required>
                    <input type="number" step="0.01" min="0" max="100" name="items[0][vat_rate]" value="20" placeholder="KDV" class="md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg">
                    <button type="button" onclick="removeQuotationItem(this)" class="md:col-span-1 px-3 py-2 bg-red-50 text-red-600 rounded-lg">Sil</button>
                    <input type="hidden" name="items[0][discount_rate]" value="0">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Kapsam ve Şartlar</h2>
            <input type="hidden" name="currency" value="TRY">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kısa Kapsam Özeti</label>
                    <textarea name="scope_summary" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('scope_summary') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dahil Olanlar</label>
                    <textarea name="scope_inclusions" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('scope_inclusions', "Periyodik kontrol, ayar ve raporlama.\nTeklif kapsamındaki işçilik ve malzemeler.") }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hariç Olanlar</label>
                    <textarea name="scope_exclusions" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('scope_exclusions', "Teklifte açıkça belirtilmeyen işler.\nResmi muayene kuruluşu ücretleri.\nKullanıcı hatası veya dış müdahale kaynaklı ek hasarlar.") }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticari Şartlar</label>
                    <textarea name="terms" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('terms', "Fiyatlara KDV dahildir/harictir bilgisi teklif üzerinde ayrıca belirtilir.\nTeklif geçerlilik tarihine kadar geçerlidir.\nİş başlangıcı ödeme mutabakatından sonra planlanır.") }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                    <textarea name="notes" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('quotations.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl">Vazgeç</a>
            <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl">Teklifi Oluştur</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let quotationItemIndex = 1;

function addQuotationItem() {
    const wrapper = document.getElementById('quotationItems');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-12 gap-3 quotation-item';
    row.innerHTML = `
        <select name="items[${quotationItemIndex}][category]" class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg">
            <option value="service">Hizmet</option>
            <option value="material">Malzeme</option>
            <option value="labor">İşçilik</option>
            <option value="inspection">Kontrol</option>
        </select>
        <input type="text" name="items[${quotationItemIndex}][description]" placeholder="Açıklama" class="md:col-span-4 px-3 py-2 border border-gray-300 rounded-lg" required>
        <input type="number" step="0.01" min="0.01" name="items[${quotationItemIndex}][quantity]" value="1" placeholder="Miktar" class="md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg" required>
        <input type="text" name="items[${quotationItemIndex}][unit]" value="adet" placeholder="Birim" class="md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg">
        <input type="number" step="0.01" min="0" name="items[${quotationItemIndex}][unit_price]" placeholder="Birim fiyat" class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg" required>
        <input type="number" step="0.01" min="0" max="100" name="items[${quotationItemIndex}][vat_rate]" value="20" placeholder="KDV" class="md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg">
        <button type="button" onclick="removeQuotationItem(this)" class="md:col-span-1 px-3 py-2 bg-red-50 text-red-600 rounded-lg">Sil</button>
        <input type="hidden" name="items[${quotationItemIndex}][discount_rate]" value="0">
    `;
    wrapper.appendChild(row);
    quotationItemIndex += 1;
}

function removeQuotationItem(button) {
    const rows = document.querySelectorAll('.quotation-item');
    if (rows.length > 1) {
        button.closest('.quotation-item').remove();
    }
}
</script>
@endpush
@endsection
