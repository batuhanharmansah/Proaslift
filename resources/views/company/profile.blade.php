@extends('layouts.app')

@section('title', 'Firma Profili - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Firma Profili</h1>
            <p class="text-gray-600 mt-1">{{ $company->name }} - Firma bilgilerini yönetin</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana Bilgiler -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Firma Bilgileri</h2>
                <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Firma Logosu</label>
                            @if($company->logo_path)
                                <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-16 mb-2 rounded-lg border border-gray-200">
                            @endif
                            <input type="file" name="logo" accept="image/*" class="w-full text-sm">
                            <p class="text-xs text-gray-400 mt-1">Önerilen: kare, 512×512 px.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kaşe / İmza</label>
                            @if($company->stamp_path)
                                <img src="{{ Storage::url($company->stamp_path) }}" alt="Kaşe" class="h-16 mb-2 rounded-lg border border-gray-200">
                            @endif
                            <input type="file" name="stamp" accept="image/*" class="w-full text-sm">
                            <p class="text-xs text-gray-400 mt-1">Tekliflerde otomatik basılır. Önerilen: şeffaf PNG.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Birincil Marka Rengi</label>
                            <input type="color" name="brand_primary_color" value="{{ old('brand_primary_color', $company->brand_primary_color ?? '#2563eb') }}" class="w-full h-10 rounded-lg border border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">İkincil Marka Rengi</label>
                            <input type="color" name="brand_secondary_color" value="{{ old('brand_secondary_color', $company->brand_secondary_color ?? '#1d4ed8') }}" class="w-full h-10 rounded-lg border border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Firma Adı</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $company->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $company->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $company->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <textarea id="address" name="address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-1">Vergi Numarası</label>
                            <input type="text" id="tax_number" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tax_number') border-red-500 @enderror">
                            @error('tax_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                            <textarea id="notes" name="notes" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $company->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Bilgileri Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="space-y-6">
            <!-- Firma İstatistikleri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Firma İstatistikleri</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Planı</label>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($company->subscription_plan) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Durumu</label>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $company->subscription_status === 'active' ? 'bg-green-100 text-green-800' :
                               ($company->subscription_status === 'trial' ? 'bg-blue-100 text-blue-800' :
                               'bg-red-100 text-red-800') }}">
                            {{ $company->subscription_status === 'active' ? 'Aktif' :
                               ($company->subscription_status === 'trial' ? 'Deneme' : 'Pasif') }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aylık Ücret</label>
                        <p class="text-gray-900">₺{{ number_format($company->monthly_fee, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maksimum Bina</label>
                        <p class="text-gray-900">{{ $company->max_buildings }} adet</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maksimum Personel</label>
                        <p class="text-gray-900">{{ $company->max_employees }} kişi</p>
                    </div>
                </div>
            </div>

            <!-- Sistem Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Sistem Bilgileri</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oluşturulma Tarihi</label>
                        <p class="text-gray-900">{{ $company->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Son Güncelleme</label>
                        <p class="text-gray-900">{{ $company->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Başlangıcı</label>
                        <p class="text-gray-900">{{ $company->subscription_start ? \Carbon\Carbon::parse($company->subscription_start)->format('d.m.Y') : 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Bitişi</label>
                        <p class="text-gray-900">{{ $company->subscription_end ? \Carbon\Carbon::parse($company->subscription_end)->format('d.m.Y') : 'Belirtilmemiş' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

