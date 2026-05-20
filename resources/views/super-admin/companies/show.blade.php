@extends('super-admin.layouts.app')

@section('title', $company->name . ' - Firma Detayları')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-600 to-blue-800 flex items-center justify-center shadow-lg">
                            <span class="text-xl font-bold text-white">{{ substr($company->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $company->name }}</h1>
                            <p class="text-lg text-gray-600">{{ $company->email }}</p>
                            <p class="text-sm text-gray-500">{{ $company->phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($company->is_active)
                            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                ✅ Aktif Firma
                            </span>
                        @else
                            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                ❌ Askıda
                            </span>
                        @endif
                        <a href="{{ route('super-admin.companies.edit', $company) }}"
                           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            ✏️ Düzenle
                        </a>
                        <a href="{{ route('super-admin.companies.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors border border-gray-300">
                            ← Geri Dön
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Kullanıcılar</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Binalar</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_buildings'] ?? 0 }}</p>
                </div>
            </div>
        </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Personeller</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_employees'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Aylık Ücret</p>
                            <p class="text-3xl font-bold text-gray-900">₺{{ number_format($company->monthly_fee, 0) }}</p>
                        </div>
                    </div>
                </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Firma Bilgileri -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Genel Bilgiler -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Firma Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Firma Adı</label>
                        <p class="text-sm text-gray-900">{{ $company->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                        <p class="text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">{{ $company->slug }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                        <p class="text-sm text-gray-900">
                            <a href="mailto:{{ $company->email }}" class="text-blue-600 hover:text-blue-800">{{ $company->email }}</a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                        <p class="text-sm text-gray-900">
                            @if($company->phone)
                                <a href="tel:{{ $company->phone }}" class="text-blue-600 hover:text-blue-800">{{ $company->phone }}</a>
                            @else
                                <span class="text-gray-400">Belirtilmemiş</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if($company->address)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                        <p class="text-sm text-gray-900">{{ $company->address }}</p>
                    </div>
                @endif
            </div>

            <!-- Abonelik Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Abonelik Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                        <p class="text-sm text-gray-900">{{ $company->subscription_plan ?? 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aylık Ücret</label>
                        <p class="text-sm text-gray-900 font-semibold text-green-600">₺{{ number_format($company->monthly_fee, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                        <p class="text-sm text-gray-900">{{ $company->subscription_start?->format('d.m.Y') ?? 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                        <p class="text-sm text-gray-900">
                            {{ $company->subscription_end?->format('d.m.Y') ?? 'Belirtilmemiş' }}
                            @if($company->subscription_end && $company->subscription_end->isPast())
                                <span class="ml-2 text-red-600 text-xs">(Süresi Dolmuş)</span>
                            @elseif($company->subscription_end && $company->subscription_end->diffInDays() <= 30)
                                <span class="ml-2 text-yellow-600 text-xs">({{ $company->subscription_end->diffInDays() }} gün kaldı)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Durumu</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($company->subscription_status === 'active') bg-green-100 text-green-800
                            @elseif($company->subscription_status === 'trial') bg-blue-100 text-blue-800
                            @elseif($company->subscription_status === 'expired') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $company->subscriptionStatusLabel }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sistem Durumu</label>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $company->is_active ? 'Aktif' : 'Askıda' }}
                            </span>
                            @if($company->is_active)
                                <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Bu firmayı askıya almak istediğinizden emin misiniz?')"
                                            class="text-xs text-red-600 hover:text-red-800 underline">Askıya Al</button>
                                </form>
                            @else
                                <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Bu firmayı aktifleştirmek istediğinizden emin misiniz?')"
                                            class="text-xs text-green-600 hover:text-green-800 underline">Aktifleştir</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kullanıcılar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Kullanıcılar ({{ $company->users->count() }})</h2>
                @if($company->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($company->users as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
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
                @else
                    <p class="text-gray-500 text-sm">Henüz kullanıcı bulunmuyor.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Son Ödemeler -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Son Ödemeler</h3>
                <p class="text-gray-500 text-sm">Ödeme sistemi henüz aktif değil.</p>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı İşlemler</h3>
                <div class="space-y-3">
                    <a href="{{ route('super-admin.payments.create', ['company_id' => $company->id]) }}"
                       class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Ödeme Kaydı Ekle
                    </a>

                    <a href="{{ route('super-admin.companies.edit', $company) }}"
                       class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Firma Bilgilerini Düzenle
                    </a>

                    @if($company->is_active)
                        <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Bu firmayı askıya almak istediğinizden emin misiniz?')"
                                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                                Firmayı Askıya Al
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Bu firmayı aktifleştirmek istediğinizden emin misiniz?')"
                                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Firmayı Aktifleştir
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Sistem Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sistem Bilgileri</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kayıt Tarihi:</span>
                        <span class="text-gray-900">{{ $company->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Son Güncelleme:</span>
                        <span class="text-gray-900">{{ $company->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Toplam Ödeme:</span>
                        <span class="text-gray-900 font-semibold">₺0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bekleyen Ödeme:</span>
                        <span class="text-gray-900 font-semibold">₺0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
