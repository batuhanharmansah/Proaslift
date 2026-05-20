@extends('layouts.app')

@section('title', 'Yeni Bina Ekle - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Yeni Bina Ekle</h1>
            <p class="text-gray-600 mt-1">Bakım sözleşmesi yapılacak bina bilgilerini kaydedin</p>
        </div>
        <a href="{{ route('buildings.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <form method="POST" action="{{ route('buildings.store') }}" class="p-8" enctype="multipart/form-data">
            @csrf

            <!-- Bina Bilgileri -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Bina Bilgileri
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Bina Adı *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Örn: Ataşehir Rezidans">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">İlçe *</label>
                        <input type="text" name="district" id="district" value="{{ old('district') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('district') border-red-500 @enderror"
                               placeholder="Örn: Ataşehir">
                        @error('district')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">İl *</label>
                        <input type="text" name="city" id="city" value="{{ old('city', 'İstanbul') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="floor_count" class="block text-sm font-medium text-gray-700 mb-2">Kat Sayısı *</label>
                        <input type="number" name="floor_count" id="floor_count" value="{{ old('floor_count') }}" required min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('floor_count') border-red-500 @enderror">
                        @error('floor_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Tam Adres *
                        <span class="text-xs text-gray-500 ml-2">(Adres yazarken öneriler görünecektir)</span>
                    </label>
                    <div class="space-y-2">
                        <textarea name="address" id="address" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                  placeholder="Adres yazmaya başlayın...">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- COST-OPTIMIZED: Address Helper Buttons -->
                        <!-- Autocomplete is ON-DEMAND to avoid unnecessary API calls -->
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="btn-enable-autocomplete" 
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                🔍 Otomatik Öneri Aç (Ücretsiz)
                            </button>
                            <button type="button" id="btn-geocode-address" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Adresten Koordinat Al (Sadece Kayıt Sırasında)
                            </button>
                            <button type="button" id="btn-select-from-map" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Haritadan Seç (Ücretsiz)
                            </button>
                            <button type="button" id="btn-select-existing" 
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Kayıtlı Binadan Seç
                            </button>
                        </div>
                        
                        <!-- Koordinat Bilgileri (Gizli Inputlar) -->
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        
                        <!-- Koordinat Gösterimi -->
                        <div id="coordinates-display" class="text-sm text-gray-600 {{ old('latitude') && old('longitude') ? '' : 'hidden' }}">
                            <span class="font-medium">Koordinatlar:</span>
                            <span id="coordinates-text">
                                @if(old('latitude') && old('longitude'))
                                    {{ old('latitude') }}, {{ old('longitude') }}
                                    <a href="https://www.google.com/maps?q={{ old('latitude') }},{{ old('longitude') }}" target="_blank" class="text-blue-600 hover:underline ml-2">Haritada Göster</a>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Harita Seçim Modal -->
                <div id="map-selection-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900">Haritadan Konum Seçin</h3>
                            <button type="button" id="close-map-modal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1 relative">
                            <div id="address-selection-map" style="height: 500px; width: 100%;"></div>
                        </div>
                        <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" id="cancel-map-selection" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition">
                                İptal
                            </button>
                            <button type="button" id="confirm-map-selection" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                Bu Konumu Seç
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Kayıtlı Binalar Modal -->
                <div id="existing-buildings-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900">Kayıtlı Binalardan Seç</h3>
                            <button type="button" id="close-existing-modal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4">
                            <input type="text" id="search-existing-buildings" placeholder="Bina ara..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4">
                            <div id="existing-buildings-list" class="space-y-2">
                                <!-- Dinamik olarak doldurulacak -->
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200 flex justify-end">
                            <button type="button" id="cancel-existing-selection" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asansör Bilgileri -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8l4-4 4 4m-4-4v12"></path>
                    </svg>
                    Asansör Bilgileri
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="elevator_count" class="block text-sm font-medium text-gray-700 mb-2">Asansör Sayısı *</label>
                        <input type="number" name="elevator_count" id="elevator_count" value="{{ old('elevator_count', 1) }}" required min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('elevator_count') border-red-500 @enderror">
                        @error('elevator_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="elevator_type" class="block text-sm font-medium text-gray-700 mb-2">Asansör Türü *</label>
                        <select name="elevator_type" id="elevator_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('elevator_type') border-red-500 @enderror">
                            <option value="">Tür Seçin</option>
                            <option value="yolcu" {{ old('elevator_type') == 'yolcu' ? 'selected' : '' }}>Yolcu Asansörü</option>
                            <option value="yuk" {{ old('elevator_type') == 'yuk' ? 'selected' : '' }}>Yük Asansörü</option>
                            <option value="hasta" {{ old('elevator_type') == 'hasta' ? 'selected' : '' }}>Hasta Asansörü</option>
                            <option value="karma" {{ old('elevator_type') == 'karma' ? 'selected' : '' }}>Karma Asansör</option>
                        </select>
                        @error('elevator_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="elevator_brand" class="block text-sm font-medium text-gray-700 mb-2">Marka</label>
                        <input type="text" name="elevator_brand" id="elevator_brand" value="{{ old('elevator_brand') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Örn: Otis, Schindler, Kone">
                    </div>

                    <div>
                        <label for="installation_year" class="block text-sm font-medium text-gray-700 mb-2">Kurulum Yılı</label>
                        <input type="number" name="installation_year" id="installation_year" value="{{ old('installation_year') }}"
                               min="1990" max="{{ date('Y') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Sözleşme Bilgileri -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Sözleşme Bilgileri
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">Sözleşme Türü *</label>
                        <select name="contract_type" id="contract_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('contract_type') border-red-500 @enderror">
                            <option value="">Tür Seçin</option>
                            <option value="bakim" {{ old('contract_type') == 'bakim' ? 'selected' : '' }}>Bakım Sözleşmesi</option>
                            <option value="onarim" {{ old('contract_type') == 'onarim' ? 'selected' : '' }}>Onarım Sözleşmesi</option>
                            <option value="modernizasyon" {{ old('contract_type') == 'modernizasyon' ? 'selected' : '' }}>Modernizasyon</option>
                        </select>
                        @error('contract_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">Aylık Ücret (₺) *</label>
                        <input type="number" name="monthly_fee" id="monthly_fee" value="{{ old('monthly_fee') }}" required min="0" step="0.01"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('monthly_fee') border-red-500 @enderror"
                               placeholder="3500.00">
                        @error('monthly_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contract_start_date" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi *</label>
                        <input type="date" name="contract_start_date" id="contract_start_date" value="{{ old('contract_start_date') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('contract_start_date') border-red-500 @enderror">
                        @error('contract_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi *</label>
                        <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('contract_end_date') border-red-500 @enderror">
                        @error('contract_end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- İletişim Kişisi -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    İletişim Kişisi (Opsiyonel)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">İsim</label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Site yöneticisi adı">
                    </div>

                    <div>
                        <label for="contact_title" class="block text-sm font-medium text-gray-700 mb-2">Görevi</label>
                        <select name="contact_title" id="contact_title"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="yonetici" {{ old('contact_title') == 'yonetici' ? 'selected' : '' }}>Yönetici</option>
                            <option value="kapici" {{ old('contact_title') == 'kapici' ? 'selected' : '' }}>Kapıcı</option>
                            <option value="muhtar" {{ old('contact_title') == 'muhtar' ? 'selected' : '' }}>Muhtar</option>
                        </select>
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                        <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="0555 123 45 67">
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="yonetici@site.com">
                    </div>
                </div>
            </div>

            <!-- Belgeler (Opsiyonel) -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Belgeler (Opsiyonel)
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sözleşme Belgesi</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="contract_document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Dosya yükle</span>
                                        <input id="contract_document" name="contract_document" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                                    </label>
                                    <p class="pl-1">veya sürükle bırak</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max: 10MB)</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teknik Çizim</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="technical_drawing" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Dosya yükle</span>
                                        <input id="technical_drawing" name="technical_drawing" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png,.dwg">
                                    </label>
                                    <p class="pl-1">veya sürükle bırak</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG, DWG (Max: 10MB)</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diğer Belgeler</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="other_documents" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Dosya yükle</span>
                                        <input id="other_documents" name="other_documents[]" type="file" class="sr-only" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" multiple>
                                    </label>
                                    <p class="pl-1">veya sürükle bırak</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, XLS, JPG, PNG (Max: 10MB) - Birden fazla seçebilirsiniz</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asansör Etiket Bilgileri (İsteğe Bağlı) -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        🏷️ Etiket Bilgileri (İsteğe Bağlı)
                    </h3>
                    <label class="flex items-center">
                        <input type="checkbox" id="add_label_checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Etiket bilgisi ekle</span>
                    </label>
                </div>

                <div id="label_section" class="hidden space-y-6 p-6 bg-green-50 border border-green-200 rounded-lg">
                    <!-- Asansör Özellikleri -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Asansör Özellikleri</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="elevator_code" class="block text-sm font-medium text-gray-700 mb-2">Asansör Kodu</label>
                                <input type="text" name="elevator_code" id="elevator_code" value="{{ old('elevator_code') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: ASN-001">
                            </div>
                            <div>
                                <label for="capacity_kg" class="block text-sm font-medium text-gray-700 mb-2">Kapasite (kg)</label>
                                <input type="number" name="capacity_kg" id="capacity_kg" value="{{ old('capacity_kg') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: 630">
                            </div>
                            <div>
                                <label for="capacity_person" class="block text-sm font-medium text-gray-700 mb-2">Kapasite (kişi)</label>
                                <input type="number" name="capacity_person" id="capacity_person" value="{{ old('capacity_person') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: 8">
                            </div>
                            <div>
                                <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">Üretici</label>
                                <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: Otis, Schindler">
                            </div>
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                                <input type="text" name="model" id="model" value="{{ old('model') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: GeN2-MR">
                            </div>
                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Seri Numarası</label>
                                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: SN123456">
                            </div>
                        </div>
                    </div>

                    <!-- Sorumlu Kişi Bilgileri -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Sorumlu Kişi Bilgileri</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="responsible_person" class="block text-sm font-medium text-gray-700 mb-2">Sorumlu Kişi</label>
                                <input type="text" name="responsible_person" id="responsible_person" value="{{ old('responsible_person') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: Ahmet Yılmaz">
                            </div>
                            <div>
                                <label for="responsible_phone" class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                                <input type="tel" name="responsible_phone" id="responsible_phone" value="{{ old('responsible_phone') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: 0532 123 45 67">
                            </div>
                            <div>
                                <label for="responsible_email" class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                                <input type="email" name="responsible_email" id="responsible_email" value="{{ old('responsible_email') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Örn: yonetici@example.com">
                            </div>
                        </div>
                    </div>

                    <!-- İlk Etiket Bilgileri -->
                    <div class="border-t border-green-300 pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">İlk Etiket Bilgileri</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="label_color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Etiket Rengi *
                                </label>
                                <select name="label_color" id="label_color"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                                <input type="date" name="control_date" id="control_date"
                               value="{{ old('control_date', now('Europe/Istanbul')->format('Y-m-d')) }}"
                               max="{{ now('Europe/Istanbul')->format('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('control_date')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="label_description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kontrol Açıklaması
                                </label>
                                <textarea name="label_description" id="label_description" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                          placeholder="Kontrol sonuçları, tespit edilen durumlar, öneriler...">{{ old('label_description') }}</textarea>
                                @error('label_description')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Kontrol Eden Bilgileri -->
                    <div class="border-t border-green-300 pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Kontrol Eden Bilgileri</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="inspector_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Müfettiş Adı
                                </label>
                                <input type="text" name="inspector_name" id="inspector_name"
                                       value="{{ old('inspector_name') }}"
                                       placeholder="Kontrol müfettişi adı"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="inspector_company" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kontrol Firması
                                </label>
                                <input type="text" name="inspector_company" id="inspector_company"
                                       value="{{ old('inspector_company') }}"
                                       placeholder="Kontrol firması adı"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="inspector_license" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lisans Numarası
                                </label>
                                <input type="text" name="inspector_license" id="inspector_license"
                                       value="{{ old('inspector_license') }}"
                                       placeholder="Müfettiş lisans no"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Son Tarih Bilgisi -->
                    <div id="due_date_info" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">📅 Son Tarih Bilgisi</h4>
                        <p class="text-sm text-blue-800" id="due_date_text"></p>
                    </div>
                </div>
            </div>

            <!-- Notlar -->
            <div class="mb-8">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                <textarea name="notes" id="notes" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Ek bilgiler, özel notlar...">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('buildings.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200">
                    İptal
                </a>
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                    Bina Ekle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Sözleşme bitiş tarihini otomatik ayarla
document.getElementById('contract_start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDate = new Date(startDate);
    endDate.setFullYear(endDate.getFullYear() + 1); // 1 yıl ekle

    document.getElementById('contract_end_date').value = endDate.toISOString().split('T')[0];
});

// Etiket bölümünü göster/gizle
document.getElementById('add_label_checkbox').addEventListener('change', function() {
    const labelSection = document.getElementById('label_section');
    const labelInputs = labelSection.querySelectorAll('input, select, textarea');

    if (this.checked) {
        labelSection.classList.remove('hidden');
        // Etiket bilgileri zorunlu hale getir
        labelInputs.forEach(input => {
            if (input.name === 'label_color' || input.name === 'control_date') {
                input.setAttribute('required', 'required');
            }
        });
    } else {
        labelSection.classList.add('hidden');
        // Zorunluluk kaldır ve değerleri temizle
        labelInputs.forEach(input => {
            input.removeAttribute('required');
            if (input.type !== 'checkbox') {
                input.value = '';
            }
        });
        // Son tarih bilgisini gizle
        document.getElementById('due_date_info').classList.add('hidden');
    }
});

// Etiket rengi değiştiğinde son tarihi hesapla
document.getElementById('label_color').addEventListener('change', function() {
    updateDueDate();
});

document.getElementById('control_date').addEventListener('change', function() {
    updateDueDate();
});

function updateDueDate() {
    const labelColor = document.getElementById('label_color').value;
    const controlDate = document.getElementById('control_date').value;

    const followUpDays = {
        'yesil': 365,
        'mavi': 365,
        'sari': 120,
        'kirmizi': 60
    };

    if (labelColor && controlDate && followUpDays[labelColor]) {
        const dueDate = new Date(controlDate);
        dueDate.setDate(dueDate.getDate() + followUpDays[labelColor]);

        const dueDateInfo = document.getElementById('due_date_info');
        const dueDateText = document.getElementById('due_date_text');

        dueDateText.innerHTML = `
            📅 Son tarih: <strong>${dueDate.toLocaleDateString('tr-TR')}</strong>
            (${followUpDays[labelColor]} gün sonra)
        `;

        dueDateInfo.classList.remove('hidden');
    } else {
        document.getElementById('due_date_info').classList.add('hidden');
    }
}
</script>

@push('styles')
<!-- Leaflet CSS for map selection -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRC9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #address-selection-map {
        height: 500px;
        width: 100%;
        border-radius: 8px;
    }
    .pac-container {
        z-index: 9999 !important;
    }
    .building-card {
        cursor: pointer;
        transition: all 0.2s;
    }
    .building-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<!-- COST-OPTIMIZED: Google Maps API loaded ON-DEMAND, not on page load -->
<!-- Leaflet JS for map selection (FREE, no API key needed) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let autocomplete = null;
    let googleMapsLoaded = false;
    let selectionMap;
    let selectionMarker;
    let selectedCoordinates = null;
    
    const addressInput = document.getElementById('address');
    const GOOGLE_API_KEY = '{{ config('services.google.maps_api_key') }}';
    
    /**
     * COST-OPTIMIZED: Load Google Places API ONLY when user clicks "Enable Autocomplete"
     * This prevents unnecessary API calls on every keystroke
     */
    function loadGooglePlacesAPI() {
        if (googleMapsLoaded) {
            initAutocomplete();
            return Promise.resolve();
        }
        
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_API_KEY}&libraries=places&language=tr&region=tr&callback=initGoogleMapsCallback`;
            script.async = true;
            script.defer = true;
            
            window.initGoogleMapsCallback = function() {
                googleMapsLoaded = true;
                initAutocomplete();
                resolve();
            };
            
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    /**
     * Initialize Google Places Autocomplete (only after API is loaded)
     */
    function initAutocomplete() {
        if (!addressInput || typeof google === 'undefined') {
            console.warn('Google Maps API not available');
            return;
        }
        
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            componentRestrictions: { country: 'tr' },
            fields: ['formatted_address', 'geometry', 'address_components'],
            language: 'tr'
        });
        
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }
            
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            
            // Extract city and district
            let city = '';
            let district = '';
            
            place.address_components.forEach(component => {
                if (component.types.includes('administrative_area_level_1')) {
                    city = component.long_name;
                }
                if (component.types.includes('administrative_area_level_2') || 
                    component.types.includes('sublocality_level_1')) {
                    district = component.long_name;
                }
            });
            
            // Update form fields
            const cityInput = document.getElementById('city');
            if (cityInput && city) cityInput.value = city;
            
            const districtInput = document.getElementById('district');
            if (districtInput && district) districtInput.value = district;
            
            // Save coordinates (NO API CALL - coordinates come from Places selection)
            updateCoordinates(lat, lng);
        });
        
        // Show notification
        const btn = document.getElementById('btn-enable-autocomplete');
        if (btn) {
            btn.textContent = '✅ Autocomplete Aktif';
            btn.classList.remove('bg-gray-600');
            btn.classList.add('bg-green-600');
            btn.disabled = true;
        }
    }
    
    // Enable Autocomplete button (ON-DEMAND loading)
    document.getElementById('btn-enable-autocomplete')?.addEventListener('click', function() {
        this.disabled = true;
        this.textContent = '⏳ Yükleniyor...';
        
        loadGooglePlacesAPI()
            .then(() => {
                addressInput.focus();
            })
            .catch(() => {
                alert('❌ Google Maps API yüklenemedi. Lütfen daha sonra tekrar deneyin.');
                this.disabled = false;
                this.textContent = '🔍 Otomatik Öneri Aç';
            });
    });
    
    /**
     * Update coordinates in form (coordinates saved on form submit)
     */
    function updateCoordinates(lat, lng) {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        
        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;
        selectedCoordinates = { lat, lng };
        
        // Show coordinates preview
        const coordinatesDisplay = document.getElementById('coordinates-display');
        const coordinatesText = document.getElementById('coordinates-text');
        if (coordinatesDisplay && coordinatesText) {
            coordinatesDisplay.classList.remove('hidden');
            coordinatesText.innerHTML = `
                <span class="font-semibold">${lat.toFixed(7)}, ${lng.toFixed(7)}</span>
                <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="text-blue-600 hover:underline ml-2 text-xs">📍 Haritada Göster</a>
                <span class="text-xs text-gray-500 ml-2">(Form kaydedilirken veritabanına yazılacak)</span>
            `;
        }
    }
    
    /**
     * COST-OPTIMIZED: "Get Coordinates" button
     * This is OPTIONAL preview - geocoding will happen on form submit (backend)
     * Users can preview coordinates before saving, but it's not required
     */
    document.getElementById('btn-geocode-address')?.addEventListener('click', async function() {
        const address = addressInput.value.trim();
        if (!address) {
            alert('Lütfen bir adres girin. Koordinatlar form kaydedilirken otomatik alınacaktır.');
            return;
        }
        
        // Warn user that geocoding happens on save anyway
        if (!confirm('Koordinatlar form kaydedilirken otomatik alınacaktır.\n\nÖnizleme için devam edilsin mi?')) {
            return;
        }
        
        this.disabled = true;
        const originalText = this.innerHTML;
        this.innerHTML = '⏳ Önizleme alınıyor...';
        
        try {
            // OPTIONAL: Preview coordinates (will be cached, reused on save)
            const response = await fetch('/api/location-map/geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    address: address,
                    city: document.getElementById('city').value,
                    district: document.getElementById('district').value
                })
            });
            
            const result = await response.json();
            
            if (result.success && result.data.coordinates) {
                updateCoordinates(result.data.coordinates.lat, result.data.coordinates.lng);
                alert('✅ Önizleme: Koordinatlar alındı! (Form kaydedilirken tekrar alınmayacak, cache kullanılacak)');
            } else {
                alert('ℹ️ ' + (result.message || 'Koordinatlar form kaydedilirken otomatik alınacaktır.'));
            }
        } catch (error) {
            console.error('Preview geocoding error:', error);
            alert('ℹ️ Koordinatlar form kaydedilirken backend\'de otomatik alınacaktır.');
        } finally {
            this.disabled = false;
            this.innerHTML = originalText;
        }
    });
    
    // Haritadan seç butonu
    document.getElementById('btn-select-from-map')?.addEventListener('click', function() {
        const modal = document.getElementById('map-selection-modal');
        modal.classList.remove('hidden');
        
        // Haritayı başlat (eğer henüz başlatılmadıysa)
        if (!selectionMap) {
            setTimeout(() => {
                initSelectionMap();
            }, 100);
        }
    });
    
    // Harita seçim modalını başlat
    function initSelectionMap() {
        const mapContainer = document.getElementById('address-selection-map');
        if (!mapContainer || selectionMap) return;
        
        // Mevcut koordinatlar varsa onları kullan, yoksa Türkiye merkezi
        const currentLat = parseFloat(document.getElementById('latitude').value) || 39.9334;
        const currentLng = parseFloat(document.getElementById('longitude').value) || 32.8597;
        
        selectionMap = L.map('address-selection-map').setView([currentLat, currentLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(selectionMap);
        
        // Mevcut koordinat varsa marker ekle
        if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
            selectionMarker = L.marker([currentLat, currentLng], {
                draggable: true
            }).addTo(selectionMap);
            
            selectionMarker.on('dragend', function() {
                const pos = selectionMarker.getLatLng();
                selectedCoordinates = { lat: pos.lat, lng: pos.lng };
            });
            
            selectedCoordinates = { lat: currentLat, lng: currentLng };
        }
        
        // Haritaya tıklama
        selectionMap.on('click', function(e) {
            if (!selectionMarker) {
                selectionMarker = L.marker([e.latlng.lat, e.latlng.lng], {
                    draggable: true
                }).addTo(selectionMap);
                
                selectionMarker.on('dragend', function() {
                    const pos = selectionMarker.getLatLng();
                    selectedCoordinates = { lat: pos.lat, lng: pos.lng };
                });
            } else {
                selectionMarker.setLatLng([e.latlng.lat, e.latlng.lng]);
            }
            
            selectedCoordinates = { lat: e.latlng.lat, lng: e.latlng.lng };
        });
    }
    
    // Harita seçimini onayla
    document.getElementById('confirm-map-selection')?.addEventListener('click', function() {
        if (!selectedCoordinates) {
            alert('Lütfen haritada bir konum seçin');
            return;
        }
        
        updateCoordinates(selectedCoordinates.lat, selectedCoordinates.lng);
        document.getElementById('map-selection-modal').classList.add('hidden');
        alert('✅ Konum seçildi!');
    });
    
    // Harita modalını kapat
    document.getElementById('close-map-modal')?.addEventListener('click', function() {
        document.getElementById('map-selection-modal').classList.add('hidden');
    });
    
    document.getElementById('cancel-map-selection')?.addEventListener('click', function() {
        document.getElementById('map-selection-modal').classList.add('hidden');
    });
    
    // Kayıtlı binalar modalını aç
    document.getElementById('btn-select-existing')?.addEventListener('click', async function() {
        const modal = document.getElementById('existing-buildings-modal');
        modal.classList.remove('hidden');
        
        // Kayıtlı binaları yükle
        try {
            const response = await fetch('/api/location-map/data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            const result = await response.json();
            
            if (result.success) {
                const buildings = [...(result.data.buildings || []), ...(result.data.buildings_without_coordinates || [])];
                displayExistingBuildings(buildings);
            }
        } catch (error) {
            console.error('Buildings load error:', error);
        }
    });
    
    // Kayıtlı binaları göster
    function displayExistingBuildings(buildings) {
        const listContainer = document.getElementById('existing-buildings-list');
        listContainer.innerHTML = '';
        
        if (buildings.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Kayıtlı bina bulunamadı</p>';
            return;
        }
        
        buildings.forEach(building => {
            const card = document.createElement('div');
            card.className = 'building-card p-4 border border-gray-200 rounded-lg hover:border-blue-500';
            card.innerHTML = `
                <div class="font-semibold text-gray-900">${building.name}</div>
                <div class="text-sm text-gray-600 mt-1">${building.address || ''}</div>
                ${building.coordinates ? `<div class="text-xs text-gray-500 mt-1">📍 ${building.coordinates.lat}, ${building.coordinates.lng}</div>` : '<div class="text-xs text-red-500 mt-1">⚠️ Koordinat yok</div>'}
            `;
            
            card.addEventListener('click', function() {
                document.getElementById('address').value = building.address || '';
                if (building.city) document.getElementById('city').value = building.city;
                if (building.district) document.getElementById('district').value = building.district;
                if (building.coordinates) {
                    updateCoordinates(building.coordinates.lat, building.coordinates.lng);
                }
                document.getElementById('existing-buildings-modal').classList.add('hidden');
                alert('✅ Bina bilgileri kopyalandı!');
            });
            
            listContainer.appendChild(card);
        });
    }
    
    // Kayıtlı binalar arama
    document.getElementById('search-existing-buildings')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.building-card');
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? 'block' : 'none';
        });
    });
    
    // Kayıtlı binalar modalını kapat
    document.getElementById('close-existing-modal')?.addEventListener('click', function() {
        document.getElementById('existing-buildings-modal').classList.add('hidden');
    });
    
    document.getElementById('cancel-existing-selection')?.addEventListener('click', function() {
        document.getElementById('existing-buildings-modal').classList.add('hidden');
    });
});
</script>
@endpush
@endsection
