@extends('layouts.app')

@section('title', 'Yeni Etiket Oluştur')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏷️ Yeni Etiket Oluştur</h1>
                <p class="text-gray-600 mt-1">Periyodik kontrol sonucu etiket kaydı</p>
            </div>
            <a href="{{ route('elevator-labels.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                ← Geri Dön
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('elevator-labels.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Asansör Seçimi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Asansör Bilgileri</h3>

                <div class="space-y-4">
                    <div>
                        <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Asansör (Bina) *
                        </label>
                        <select name="building_id" id="building_id" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Asansör seçiniz...</option>
                            @foreach($buildings as $bldg)
                                <option value="{{ $bldg->id }}" {{ (request('building_id') == $bldg->id || (isset($building) && $building->id == $bldg->id)) ? 'selected' : '' }}>
                                    {{ $bldg->name }}
                                    @if($bldg->elevator_code) - {{ $bldg->elevator_code }} @endif
                                    @if($bldg->activeLabel) (Aktif: {{ $bldg->activeLabel->label_color_text }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('building_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Kontrol Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Bilgileri</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="label_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Etiket Rengi *
                        </label>
                        <select name="label_color" id="label_color" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Etiket rengi seçiniz...</option>
                            <option value="yesil" {{ old('label_color') === 'yesil' ? 'selected' : '' }}>
                                🟢 Yeşil (Uygun) - 365 gün takip
                            </option>
                            <option value="mavi" {{ old('label_color') === 'mavi' ? 'selected' : '' }}>
                                🔵 Mavi (Hafif Kusur) - 365 gün takip
                            </option>
                            <option value="sari" {{ old('label_color') === 'sari' ? 'selected' : '' }}>
                                🟡 Sarı (Kusurlu) - 120 gün takip
                            </option>
                            <option value="kirmizi" {{ old('label_color') === 'kirmizi' ? 'selected' : '' }}>
                                🔴 Kırmızı (Güvensiz) - 60 gün takip
                            </option>
                        </select>
                        @error('label_color')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="control_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Kontrol Tarihi *
                        </label>
                        <input type="date" name="control_date" id="control_date" required
                               value="{{ old('control_date', now('Europe/Istanbul')->format('Y-m-d')) }}"
                               max="{{ now('Europe/Istanbul')->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('control_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-2">
                            Kaynak *
                        </label>
                        <select name="source" id="source" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Kaynak seçiniz...</option>
                            <option value="periyodik_kontrol" {{ old('source') === 'periyodik_kontrol' ? 'selected' : '' }}>
                                Periyodik Kontrol
                            </option>
                            <option value="takip_kontrol" {{ old('source') === 'takip_kontrol' ? 'selected' : '' }}>
                                Takip Kontrol
                            </option>
                            <option value="manuel" {{ old('source') === 'manuel' ? 'selected' : '' }}>
                                Manuel Giriş
                            </option>
                        </select>
                        @error('source')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Açıklama
                    </label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Kontrol sonuçları, tespit edilen durumlar, öneriler..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kontrol Eden Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Eden Bilgileri</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="inspector_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Müfettiş Adı
                        </label>
                        <input type="text" name="inspector_name" id="inspector_name"
                               value="{{ old('inspector_name') }}"
                               placeholder="Kontrol müfettişi adı"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('inspector_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="inspector_company" class="block text-sm font-medium text-gray-700 mb-2">
                            Kontrol Firması
                        </label>
                        <input type="text" name="inspector_company" id="inspector_company"
                               value="{{ old('inspector_company') }}"
                               placeholder="Kontrol firması adı"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('inspector_company')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="inspector_license" class="block text-sm font-medium text-gray-700 mb-2">
                            Lisans Numarası
                        </label>
                        <input type="text" name="inspector_license" id="inspector_license"
                               value="{{ old('inspector_license') }}"
                               placeholder="Müfettiş lisans no"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('inspector_license')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Takip Süresi Bilgisi -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">📅 Takip Süreleri</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-blue-800">Yeşil: 365 gün</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-blue-800">Mavi: 365 gün</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-blue-800">Sarı: 120 gün</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-blue-800">Kırmızı: 60 gün</span>
                    </div>
                </div>
                <p class="text-blue-700 text-sm mt-3">
                    💡 Son tarih otomatik olarak kontrol tarihi + takip süresi şeklinde hesaplanacaktır.
                </p>
            </div>

            <!-- Submit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">
                            Yeni etiket oluşturulduğunda, varsa önceki aktif etiket otomatik olarak "tamamlandı" durumuna geçecektir.
                        </p>
                    </div>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        📝 Etiket Oluştur
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Etiket rengi değiştiğinde takip süresini göster
document.getElementById('label_color').addEventListener('change', function() {
    const selectedColor = this.value;
    const followUpDays = {
        'yesil': 365,
        'mavi': 365,
        'sari': 120,
        'kirmizi': 60
    };

    if (selectedColor && followUpDays[selectedColor]) {
        const controlDate = document.getElementById('control_date').value;
        if (controlDate) {
            const dueDate = new Date(controlDate);
            dueDate.setDate(dueDate.getDate() + followUpDays[selectedColor]);

            // Bilgi mesajı göster
            const infoDiv = document.getElementById('due-date-info');
            if (infoDiv) {
                infoDiv.remove();
            }

            const newInfo = document.createElement('div');
            newInfo.id = 'due-date-info';
            newInfo.className = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg';
            newInfo.innerHTML = `
                <p class="text-sm text-blue-800">
                    📅 Son tarih: <strong>${dueDate.toLocaleDateString('tr-TR')}</strong>
                    (${followUpDays[selectedColor]} gün sonra)
                </p>
            `;

            this.parentElement.appendChild(newInfo);
        }
    }
});

// Kontrol tarihi değiştiğinde de güncelle
document.getElementById('control_date').addEventListener('change', function() {
    const event = new Event('change');
    document.getElementById('label_color').dispatchEvent(event);
});
</script>
@endsection
