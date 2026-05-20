@extends('layouts.app')

@section('title', 'Arıza Bildirimi Düzenle #{{ $issueReport->id }} - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Arıza Bildirimi Düzenle</h1>
            <p class="text-gray-600 mt-1">#{{ $issueReport->id }} — {{ $issueReport->building->name ?? '' }}</p>
        </div>
        <a href="{{ route('issue-reports.show', $issueReport) }}"
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
        <form action="{{ route('issue-reports.update', $issueReport) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Arıza Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-1">Arıza Bilgileri</h2>
                    <p class="text-sm text-gray-500 mb-4">Arıza kaydını güncelleyin</p>
                    <div class="border-t border-gray-100"></div>
                </div>

                <div>
                    <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Bina <span class="text-red-500">*</span>
                    </label>
                    <select id="building_id" name="building_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="">Seçiniz</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}"
                                {{ old('building_id', $issueReport->building_id) == $building->id ? 'selected' : '' }}>
                                {{ $building->name }} — {{ $building->district }}, {{ $building->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('building_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="issue_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Arıza Tipi <span class="text-red-500">*</span>
                    </label>
                    <select id="issue_type" name="issue_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="">Seçiniz</option>
                        <option value="elektrik_arizasi" {{ old('issue_type', $issueReport->issue_type) === 'elektrik_arizasi' ? 'selected' : '' }}>Elektrik Arızası</option>
                        <option value="mekanik_ariza"   {{ old('issue_type', $issueReport->issue_type) === 'mekanik_ariza'   ? 'selected' : '' }}>Mekanik Arıza</option>
                        <option value="kapı_arizasi"    {{ old('issue_type', $issueReport->issue_type) === 'kapı_arizasi'    ? 'selected' : '' }}>Kapı Arızası</option>
                        <option value="ses_sistemi"     {{ old('issue_type', $issueReport->issue_type) === 'ses_sistemi'     ? 'selected' : '' }}>Ses Sistemi</option>
                        <option value="acil_durum"      {{ old('issue_type', $issueReport->issue_type) === 'acil_durum'      ? 'selected' : '' }}>Acil Durum</option>
                        <option value="diger"           {{ old('issue_type', $issueReport->issue_type) === 'diger'           ? 'selected' : '' }}>Diğer</option>
                    </select>
                    @error('issue_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Öncelik <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="">Seçiniz</option>
                        <option value="dusuk"  {{ old('priority', $issueReport->priority) === 'dusuk'  ? 'selected' : '' }}>Düşük</option>
                        <option value="orta"   {{ old('priority', $issueReport->priority) === 'orta'   ? 'selected' : '' }}>Orta</option>
                        <option value="yuksek" {{ old('priority', $issueReport->priority) === 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                        <option value="acil"   {{ old('priority', $issueReport->priority) === 'acil'   ? 'selected' : '' }}>Acil</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reported_by" class="block text-sm font-medium text-gray-700 mb-2">
                        Bildiren Kişi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="reported_by" name="reported_by"
                           value="{{ old('reported_by', $issueReport->reported_by) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Arızayı bildiren kişinin adı">
                    @error('reported_by')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Arıza Açıklaması <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="5" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Arızanın detaylı açıklaması (en az 10 karakter)...">{{ old('description', $issueReport->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="location_details" class="block text-sm font-medium text-gray-700 mb-2">Konum Detayı</label>
                    <input type="text" id="location_details" name="location_details"
                           value="{{ old('location_details', $issueReport->location_details) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Örn: 3. kat sol asansör">
                    @error('location_details')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acil Durum Seçenekleri -->
                <div class="md:col-span-2">
                    <div class="flex flex-wrap items-center gap-6 p-4 bg-red-50 rounded-xl border border-red-100">
                        <label class="flex items-center cursor-pointer">
                            <input type="hidden" name="is_urgent" value="0">
                            <input type="checkbox" name="is_urgent" value="1"
                                   {{ old('is_urgent', $issueReport->is_urgent) ? 'checked' : '' }}
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">⚠ Acil Durum</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="hidden" name="requires_immediate_attention" value="0">
                            <input type="checkbox" name="requires_immediate_attention" value="1"
                                   {{ old('requires_immediate_attention', $issueReport->requires_immediate_attention) ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">⚡ Hemen Müdahale Gerekiyor</span>
                        </label>
                    </div>
                </div>

                <!-- İletişim Bilgileri -->
                <div class="md:col-span-2 mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-1">İletişim Bilgileri</h2>
                    <p class="text-sm text-gray-500 mb-4">Opsiyonel — arıza ile ilgili iletişim kurulacak kişi</p>
                    <div class="border-t border-gray-100"></div>
                </div>

                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">İletişim Kişisi</label>
                    <input type="text" id="contact_name" name="contact_name"
                           value="{{ old('contact_name', $issueReport->contact_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ad Soyad">
                    @error('contact_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" id="contact_phone" name="contact_phone"
                           value="{{ old('contact_phone', $issueReport->contact_phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="0532 123 45 67">
                    @error('contact_phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">Müşteri Notları</label>
                    <textarea id="customer_notes" name="customer_notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Müşterinin ek notları...">{{ old('customer_notes', $issueReport->customer_notes) }}</textarea>
                    @error('customer_notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Form Altı -->
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <div class="text-xs text-gray-400">
                    Son güncelleme: {{ $issueReport->updated_at->format('d.m.Y H:i') }}
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('issue-reports.show', $issueReport) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                        İptal
                    </a>
                    <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
