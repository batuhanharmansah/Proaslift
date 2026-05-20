@extends('layouts.app')

@section('title', 'Yeni Arıza Bildirimi - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Yeni Arıza Bildirimi</h1>
            <p class="text-gray-600 mt-1">Asansör arızasını kayıt altına alın</p>
        </div>
        <a href="{{ route('issue-reports.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form action="{{ route('issue-reports.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Temel Bilgiler -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Arıza Bilgileri</h2>
                </div>

                <div>
                    <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">Bina *</label>
                    <select id="building_id" name="building_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }} - {{ $building->district }}, {{ $building->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('building_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="issue_type" class="block text-sm font-medium text-gray-700 mb-2">Arıza Tipi *</label>
                    <select id="issue_type" name="issue_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="elektrik_arizasi" {{ old('issue_type') === 'elektrik_arizasi' ? 'selected' : '' }}>Elektrik Arızası</option>
                        <option value="mekanik_ariza" {{ old('issue_type') === 'mekanik_ariza' ? 'selected' : '' }}>Mekanik Arıza</option>
                        <option value="kapı_arizasi" {{ old('issue_type') === 'kapı_arizasi' ? 'selected' : '' }}>Kapı Arızası</option>
                        <option value="ses_sistemi" {{ old('issue_type') === 'ses_sistemi' ? 'selected' : '' }}>Ses Sistemi</option>
                        <option value="acil_durum" {{ old('issue_type') === 'acil_durum' ? 'selected' : '' }}>Acil Durum</option>
                        <option value="diger" {{ old('issue_type') === 'diger' ? 'selected' : '' }}>Diğer</option>
                    </select>
                    @error('issue_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Öncelik *</label>
                    <select id="priority" name="priority" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="dusuk" {{ old('priority') === 'dusuk' ? 'selected' : '' }}>Düşük</option>
                        <option value="orta" {{ old('priority') === 'orta' ? 'selected' : '' }}>Orta</option>
                        <option value="yuksek" {{ old('priority') === 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                        <option value="acil" {{ old('priority') === 'acil' ? 'selected' : '' }}>Acil</option>
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="reported_by" class="block text-sm font-medium text-gray-700 mb-2">Bildiren Kişi *</label>
                    <input type="text" id="reported_by" name="reported_by" value="{{ old('reported_by') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Arızayı bildiren kişinin adı">
                    @error('reported_by') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Arıza Açıklaması *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Arızanın detaylı açıklaması (en az 10 karakter)...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="location_details" class="block text-sm font-medium text-gray-700 mb-2">Konum Detayı</label>
                    <input type="text" id="location_details" name="location_details" value="{{ old('location_details') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Örn: 3. kat sol asansör">
                    @error('location_details') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Acil Durum -->
                <div class="md:col-span-2 flex items-center space-x-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="is_urgent" value="0">
                        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }}
                               class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Acil Durum</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="requires_immediate_attention" value="0">
                        <input type="checkbox" name="requires_immediate_attention" value="1" {{ old('requires_immediate_attention') ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Hemen Müdahale Gerekiyor</span>
                    </label>
                </div>

                <!-- İletişim Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 mt-4">İletişim Bilgileri <span class="text-sm font-normal text-gray-500">(Opsiyonel)</span></h2>
                </div>

                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">İletişim Kişisi</label>
                    <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="İletişim kurulacak kişinin adı">
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">İletişim Telefonu</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="0532 123 45 67">
                </div>

                <div class="md:col-span-2">
                    <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">Müşteri Notları</label>
                    <textarea id="customer_notes" name="customer_notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Müşterinin ek notları...">{{ old('customer_notes') }}</textarea>
                </div>

            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('issue-reports.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    İptal
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    Arıza Bildir
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
