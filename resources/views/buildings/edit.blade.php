@extends('layouts.app')

@section('title', 'Bina Düzenle - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bina Düzenle</h1>
            <p class="text-gray-600 mt-1">{{ $building->name }}</p>
        </div>
        <a href="{{ route('buildings.show', $building) }}"
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
        <form action="{{ route('buildings.update', $building) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bina Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Bina Bilgileri</h2>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Bina Adı *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $building->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="district" class="block text-sm font-medium text-gray-700 mb-2">İlçe *</label>
                    <input type="text" id="district" name="district" value="{{ old('district', $building->district) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Şehir *</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $building->city) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adres *
                        <span class="text-xs text-gray-500 ml-2">(Adres yazarken öneriler görünecektir)</span>
                    </label>
                    <div class="space-y-2">
                        <textarea id="address" name="address" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Adres yazmaya başlayın...">{{ old('address', $building->address) }}</textarea>
                        
                        <!-- Adres Yardımcı Butonları -->
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
                                Koordinat Önizle (Sadece Güncelleme Sırasında)
                            </button>
                            <button type="button" id="btn-select-from-map" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Haritadan Seç
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
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $building->latitude) }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $building->longitude) }}">
                        
                        <!-- Koordinat Gösterimi -->
                        <div id="coordinates-display" class="text-sm text-gray-600 {{ $building->latitude && $building->longitude ? '' : 'hidden' }}">
                            <span class="font-medium">Koordinatlar:</span>
                            <span id="coordinates-text">
                                @if($building->latitude && $building->longitude)
                                    {{ $building->latitude }}, {{ $building->longitude }}
                                    <a href="https://www.google.com/maps?q={{ $building->latitude }},{{ $building->longitude }}" target="_blank" class="text-blue-600 hover:underline ml-2">Haritada Göster</a>
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

                <div>
                    <label for="floor_count" class="block text-sm font-medium text-gray-700 mb-2">Kat Sayısı *</label>
                    <input type="number" id="floor_count" name="floor_count" value="{{ old('floor_count', $building->floor_count) }}" min="1" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="elevator_count" class="block text-sm font-medium text-gray-700 mb-2">Asansör Sayısı *</label>
                    <input type="number" id="elevator_count" name="elevator_count" value="{{ old('elevator_count', $building->elevator_count) }}" min="1" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="elevator_type" class="block text-sm font-medium text-gray-700 mb-2">Asansör Tipi *</label>
                    <select id="elevator_type" name="elevator_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="yolcu" {{ old('elevator_type', $building->elevator_type) === 'yolcu' ? 'selected' : '' }}>Yolcu Asansörü</option>
                        <option value="yuk" {{ old('elevator_type', $building->elevator_type) === 'yuk' ? 'selected' : '' }}>Yük Asansörü</option>
                        <option value="hasta" {{ old('elevator_type', $building->elevator_type) === 'hasta' ? 'selected' : '' }}>Hasta Asansörü</option>
                        <option value="karma" {{ old('elevator_type', $building->elevator_type) === 'karma' ? 'selected' : '' }}>Karma Asansör</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Durum *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="aktif" {{ old('status', $building->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="beklemede" {{ old('status', $building->status) === 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                        <option value="pasif" {{ old('status', $building->status) === 'pasif' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>

                <!-- Sözleşme Bilgileri -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 mt-8">Sözleşme Bilgileri</h2>
                </div>

                <div>
                    <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">Sözleşme Tipi *</label>
                    <select id="contract_type" name="contract_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="bakim" {{ old('contract_type', $building->contract_type) === 'bakim' ? 'selected' : '' }}>Bakım Sözleşmesi</option>
                        <option value="onarim" {{ old('contract_type', $building->contract_type) === 'onarim' ? 'selected' : '' }}>Onarım Sözleşmesi</option>
                        <option value="modernizasyon" {{ old('contract_type', $building->contract_type) === 'modernizasyon' ? 'selected' : '' }}>Modernizasyon Sözleşmesi</option>
                    </select>
                </div>

                <div>
                    <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">Aylık Ücret (₺) *</label>
                    <input type="number" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $building->monthly_fee) }}" min="0" step="0.01" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="fee_per_elevator" class="block text-sm font-medium text-gray-700 mb-2">Asansör Başına Ücret (₺) — opsiyonel</label>
                    <input type="number" id="fee_per_elevator" name="fee_per_elevator" value="{{ old('fee_per_elevator', $building->fee_per_elevator) }}" min="0" step="0.01"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           oninput="document.getElementById('monthly_fee').value = (parseFloat(this.value || 0) * parseInt(document.getElementById('elevator_count').value || 1)).toFixed(2)">
                    <p class="text-xs text-gray-400 mt-1">Doldurulursa, "Aylık Ücret" otomatik olarak (asansör sayısı × bu tutar) hesaplanır. Birden fazla asansörü olan binalar için.</p>
                </div>

                <div>
                    <label for="default_employee_id" class="block text-sm font-medium text-gray-700 mb-2">Varsayılan Teknisyen</label>
                    <select id="default_employee_id" name="default_employee_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">— Seçilmedi —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('default_employee_id', $building->default_employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Toplu bakım oluştururken bu bina için otomatik atanır.</p>
                </div>

                <div>
                    <label for="contract_start_date" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi *</label>
                    <input type="date" id="contract_start_date" name="contract_start_date" value="{{ old('contract_start_date', $building->contract_start_date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi *</label>
                    <input type="date" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date', $building->contract_end_date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('buildings.show', $building) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    İptal
                </a>
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    Güncelle
                </button>
            </div>
        </form>

        <!-- Müşteri Portalı Erişimi -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Müşteri Portalı Erişimi</h2>
            <p class="text-sm text-gray-500 mb-4">Bina yöneticisi/sahibi kendi bakım geçmişini, açık arızalarını ve ödeme durumunu görebileceği ayrı bir panele giriş yapabilir.</p>

            @if($portalAccount)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-sm text-green-800">
                    Portal erişimi aktif. Giriş telefonu: <strong>{{ $portalAccount->phone }}</strong>
                    @if($portalAccount->last_login_at)
                        · Son giriş: {{ $portalAccount->last_login_at->format('d.m.Y H:i') }}
                    @endif
                </div>
                <form method="POST" action="{{ route('buildings.portal.disable', $building) }}" class="inline"
                      onsubmit="return confirm('Portal erişimini kaldırmak istediğinize emin misiniz?');">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Portal Erişimini Kaldır</button>
                </form>
            @endif

            <form method="POST" action="{{ route('buildings.portal.enable', $building) }}" class="grid grid-cols-2 gap-4 mt-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon (Giriş için)</label>
                    <input type="text" name="portal_phone" placeholder="05XX XXX XX XX" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şifre (en az 8 karakter)</label>
                    <input type="password" name="portal_password" minlength="8" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="col-span-2">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                        {{ $portalAccount ? 'Şifreyi/Telefonu Güncelle' : 'Portal Erişimi Oluştur' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
     */
    function loadGooglePlacesAPI() {
        if (googleMapsLoaded) {
            initAutocomplete();
            return Promise.resolve();
        }
        
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_API_KEY}&libraries=places&language=tr&region=tr&callback=initGoogleMapsEditCallback`;
            script.async = true;
            script.defer = true;
            
            window.initGoogleMapsEditCallback = function() {
                googleMapsLoaded = true;
                initAutocomplete();
                resolve();
            };
            
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    /**
     * Initialize Google Places Autocomplete
     */
    function initAutocomplete() {
        if (!addressInput || typeof google === 'undefined') {
            return;
        }
        
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            componentRestrictions: { country: 'tr' },
            fields: ['formatted_address', 'geometry', 'address_components'],
            language: 'tr'
        });
        
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;
            
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            
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
            
            if (city) {
                const cityInput = document.getElementById('city');
                if (cityInput) cityInput.value = city;
            }
            if (district) {
                const districtInput = document.getElementById('district');
                if (districtInput) districtInput.value = district;
            }
            
            updateCoordinates(lat, lng);
        });
        
        const btn = document.getElementById('btn-enable-autocomplete');
        if (btn) {
            btn.textContent = '✅ Autocomplete Aktif';
            btn.classList.remove('bg-gray-600');
            btn.classList.add('bg-green-600');
            btn.disabled = true;
        }
    }
    
    // Enable Autocomplete button (ON-DEMAND)
    document.getElementById('btn-enable-autocomplete')?.addEventListener('click', function() {
        this.disabled = true;
        this.textContent = '⏳ Yükleniyor...';
        
        loadGooglePlacesAPI()
            .then(() => addressInput.focus())
            .catch(() => {
                alert('❌ Google Maps API yüklenemedi.');
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
                <span class="text-xs text-gray-500 ml-2">(Form güncellenirken veritabanına yazılacak)</span>
            `;
        }
    }
    
    // Adresten koordinat al butonu
    document.getElementById('btn-geocode-address')?.addEventListener('click', async function() {
        const address = addressInput.value.trim();
        if (!address) {
            alert('Lütfen bir adres girin');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '⏳ Koordinat alınıyor...';
        
        // OPTIONAL: Preview coordinates (geocoding happens on form submit anyway)
        if (!confirm('Koordinatlar form güncellenirken otomatik alınacaktır.\n\nÖnizleme için devam edilsin mi?')) {
            return;
        }
        
        try {
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
                alert('✅ Önizleme: Koordinatlar alındı! (Form güncellenirken cache kullanılacak)');
            } else {
                alert('ℹ️ ' + (result.message || 'Koordinatlar form güncellenirken otomatik alınacaktır.'));
            }
        } catch (error) {
            console.error('Preview geocoding error:', error);
            alert('ℹ️ Koordinatlar form güncellenirken backend\'de otomatik alınacaktır.');
        } finally {
            this.disabled = false;
            const originalText = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>Koordinat Önizle (Sadece Güncelleme Sırasında)';
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
                displayExistingBuildings(buildings.filter(b => b.id !== {{ $building->id }}));
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
                if (building.coordinates) {
                    document.getElementById('address').value = building.address || '';
                    if (building.city) document.getElementById('city').value = building.city;
                    if (building.district) document.getElementById('district').value = building.district;
                    updateCoordinates(building.coordinates.lat, building.coordinates.lng);
                    document.getElementById('existing-buildings-modal').classList.add('hidden');
                    alert('✅ Bina bilgileri kopyalandı!');
                } else {
                    alert('Bu binanın koordinat bilgisi yok');
                }
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
