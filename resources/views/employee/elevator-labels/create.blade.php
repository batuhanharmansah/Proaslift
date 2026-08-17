@extends('employee.layouts.app')

@section('title', 'Yeni Etiket Oluştur')
@section('page-title', 'Yeni Etiket Oluştur')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🏷️ Yeni Etiket Oluştur</h1>
            <p class="text-gray-600">Periyodik kontrol sonucu etiket kaydı</p>
        </div>
        <a href="{{ route('employee.elevator-labels.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            ← Geri Dön
        </a>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('employee.elevator-labels.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Asansör Bilgileri</h3>
                <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">Asansör (Bina) *</label>
                <select name="building_id" id="building_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Asansör seçiniz...</option>
                    @foreach($buildings as $bldg)
                        <option value="{{ $bldg->id }}" {{ (request('building_id') == $bldg->id || (isset($building) && $building?->id == $bldg->id)) ? 'selected' : '' }}>
                            {{ $bldg->name }}
                            @if($bldg->elevator_code) - {{ $bldg->elevator_code }} @endif
                        </option>
                    @endforeach
                </select>
                @error('building_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="label_color" class="block text-sm font-medium text-gray-700 mb-2">Etiket Rengi *</label>
                        <select name="label_color" id="label_color" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Etiket rengi seçiniz...</option>
                            <option value="yesil" {{ old('label_color') === 'yesil' ? 'selected' : '' }}>🟢 Yeşil (Uygun)</option>
                            <option value="mavi" {{ old('label_color') === 'mavi' ? 'selected' : '' }}>🔵 Mavi (Hafif Kusur)</option>
                            <option value="sari" {{ old('label_color') === 'sari' ? 'selected' : '' }}>🟡 Sarı (Kusurlu)</option>
                            <option value="kirmizi" {{ old('label_color') === 'kirmizi' ? 'selected' : '' }}>🔴 Kırmızı (Güvensiz)</option>
                        </select>
                        @error('label_color')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="control_date" class="block text-sm font-medium text-gray-700 mb-2">Kontrol Tarihi *</label>
                        <input type="date" name="control_date" id="control_date" required
                               value="{{ old('control_date', now('Europe/Istanbul')->format('Y-m-d')) }}"
                               max="{{ now('Europe/Istanbul')->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('control_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-2">Kaynak *</label>
                        <select name="source" id="source" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Kaynak seçiniz...</option>
                            <option value="periyodik_kontrol" {{ old('source') === 'periyodik_kontrol' ? 'selected' : '' }}>Periyodik Kontrol</option>
                            <option value="takip_kontrol" {{ old('source') === 'takip_kontrol' ? 'selected' : '' }}>Takip Kontrol</option>
                            <option value="manuel" {{ old('source') === 'manuel' ? 'selected' : '' }}>Manuel Giriş</option>
                        </select>
                        @error('source')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Kontrol sonuçları, tespit edilen durumlar, öneriler..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Eden Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Müfettiş Adı</label>
                        <input type="text" name="inspector_name" value="{{ old('inspector_name') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kontrol Firması</label>
                        <input type="text" name="inspector_company" value="{{ old('inspector_company') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lisans Numarası</label>
                        <input type="text" name="inspector_license" value="{{ old('inspector_license') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    📝 Etiket Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
