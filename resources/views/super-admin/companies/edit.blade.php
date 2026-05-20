@extends('super-admin.layouts.app')

@section('title', $company->name . ' - Firma Düzenle')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                <span class="text-lg font-medium text-white">{{ substr($company->name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }} Düzenle</h1>
                <p class="text-gray-600">Firma bilgilerini ve abonelik ayarlarını güncelleyin</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('super-admin.companies.show', $company) }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Detayları Görüntüle
            </a>
            <a href="{{ route('super-admin.companies.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                ← Geri Dön
            </a>
        </div>
    </div>

    <form action="{{ route('super-admin.companies.update', $company) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Firma Bilgileri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Firma Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Firma Adı *
                        </span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $company->name) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            URL Slug *
                        </span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $company->slug) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    <p class="text-xs text-gray-500 mt-1">URL'de kullanılacak benzersiz tanımlayıcı (sadece harf, rakam ve tire)</p>
                    @error('slug')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            E-posta Adresi *
                        </span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $company->email) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Telefon
                        </span>
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $company->phone) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Adres
                    </span>
                </label>
                <textarea id="address" name="address" rows="3"
                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">{{ old('address', $company->address) }}</textarea>
                @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Abonelik Bilgileri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Abonelik Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="subscription_plan" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Abonelik Planı *
                        </span>
                    </label>
                    <select id="subscription_plan" name="subscription_plan" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Plan Seçin</option>
                        @foreach($subscriptionPlans as $plan)
                            <option value="{{ $plan->slug }}" data-price="{{ $plan->price }}"
                                    {{ old('subscription_plan', $company->subscription_plan) === $plan->slug ? 'selected' : '' }}>
                                {{ $plan->name }} - ₺{{ number_format($plan->price, 0) }}/ay
                            </option>
                        @endforeach
                    </select>
                    @error('subscription_plan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Aylık Ücret (₺) *
                        </span>
                    </label>
                    <input type="number" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $company->monthly_fee) }}" required
                           min="0" step="0.01"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('monthly_fee')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subscription_start" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Abonelik Başlangıç Tarihi *
                        </span>
                    </label>
                    <input type="date" id="subscription_start" name="subscription_start"
                           value="{{ old('subscription_start', $company->subscription_start?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('subscription_start')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subscription_end" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Abonelik Bitiş Tarihi *
                        </span>
                    </label>
                    <input type="date" id="subscription_end" name="subscription_end"
                           value="{{ old('subscription_end', $company->subscription_end?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    @error('subscription_end')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subscription_status" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Abonelik Durumu *
                        </span>
                    </label>
                    <select id="subscription_status" name="subscription_status" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Durum Seçin</option>
                        <option value="active" {{ old('subscription_status', $company->subscription_status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="trial" {{ old('subscription_status', $company->subscription_status) === 'trial' ? 'selected' : '' }}>Deneme</option>
                        <option value="expired" {{ old('subscription_status', $company->subscription_status) === 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
                        <option value="cancelled" {{ old('subscription_status', $company->subscription_status) === 'cancelled' ? 'selected' : '' }}>İptal Edilmiş</option>
                    </select>
                    @error('subscription_status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2">
                        <span class="ml-2 text-sm font-medium text-gray-700">Sistem aktif</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Kapalıysa firma sisteme giriş yapamaz</p>
                </div>
            </div>
        </div>

        <!-- Mevcut Kullanıcılar -->
        @if($company->users->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Mevcut Kullanıcılar ({{ $company->users->count() }})</h2>
                <div class="space-y-3">
                    @foreach($company->users as $user)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                @if($user->getCurrentRole() && $user->getCurrentRole()->role)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($user->getCurrentRole()->role->slug === 'company_admin') bg-purple-100 text-purple-800
                                        @elseif($user->getCurrentRole()->role->slug === 'employee') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($user->getCurrentRole()->role->slug === 'company_admin') Yönetici
                                        @elseif($user->getCurrentRole()->role->slug === 'employee') Personel
                                        @else {{ $user->getCurrentRole()->role->name ?? 'Bilinmiyor' }} @endif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Rol Yok
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $user->created_at->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800">Not</h4>
                            <p class="text-xs text-blue-700 mt-1">Kullanıcı bilgilerini değiştirmek için ayrı bir yönetim sayfası kullanılır. Bu sayfada sadece firma bilgileri düzenlenebilir.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="flex space-x-3">
                <a href="{{ route('super-admin.companies.show', $company) }}"
                   class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    İptal
                </a>
                @if($company->is_active)
                    <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Bu firmayı askıya almak istediğinizden emin misiniz?')"
                                class="px-6 py-3 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                            🚫 Askıya Al
                        </button>
                    </form>
                @else
                    <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Bu firmayı aktifleştirmek istediğinizden emin misiniz?')"
                                class="px-6 py-3 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors">
                            ✅ Aktifleştir
                        </button>
                    </form>
                @endif
            </div>
            <div class="flex space-x-3">
                <button type="submit" name="action" value="draft"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors">
                    📝 Taslak Kaydet
                </button>
                <button type="submit" name="action" value="update"
                        class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all">
                    ✅ Değişiklikleri Kaydet
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Abonelik planından fiyat doldur
    const planSelect = document.getElementById('subscription_plan');
    const feeInput = document.getElementById('monthly_fee');

    planSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.price;

        if (price && confirm('Aylık ücreti ' + price + ' ₺ olarak güncellemek istiyor musunuz?')) {
            feeInput.value = price;
        }
    });
});
</script>
@endsection
