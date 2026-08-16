@extends('layouts.app')

@section('title', $employee->full_name . ' - Personel Detayı')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
            <p class="text-gray-600 mt-1">{{ $employee->position_label }} - Personel Detayları</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('employees.edit', $employee) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Düzenle
            </a>
            <a href="{{ route('employees.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Geri Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Personal Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Kişisel Bilgiler
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ad Soyad</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $employee->full_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">E-posta</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <a href="mailto:{{ $employee->email }}" class="text-primary-600 hover:text-primary-800">
                                {{ $employee->email }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Telefon</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <a href="tel:{{ $employee->phone }}" class="text-primary-600 hover:text-primary-800">
                                {{ $employee->phone }}
                            </a>
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Adres</label>
                    <p class="text-gray-900">{{ $employee->address }}</p>
                </div>

                @if($employee->notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Notlar</label>
                        <p class="text-gray-900">{{ $employee->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Job Information & Status -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Durum</h3>
                <div class="text-center">
                    @if($employee->is_active)
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Aktif Çalışan
                        </span>
                    @else
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Pasif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Job Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                    İş Bilgileri
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Pozisyon</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($employee->position == 'yonetici') bg-purple-100 text-purple-800
                            @elseif($employee->position == 'muhendis') bg-blue-100 text-blue-800
                            @elseif($employee->position == 'usta') bg-green-100 text-green-800
                            @elseif($employee->position == 'teknisyen') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $employee->position_label }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Maaş</label>
                        <p class="text-2xl font-bold text-green-600">₺{{ number_format($employee->salary, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">İşe Başlama Tarihi</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $employee->hire_date->format('d.m.Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $employee->hire_date->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Çalışma Süresi</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $employee->hire_date->diff(now())->format('%y yıl, %m ay') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Hızlı İşlemler</h3>
                <div class="space-y-3">
                    <a href="{{ route('maintenance.index', ['employee_id' => $employee->id]) }}" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Bakım Raporları
                    </a>
                    <a href="{{ route('hr-fleet.index') }}" class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Maaş Ödemeleri (Hakediş)
                    </a>
                    <a href="{{ route('maintenance.create') }}" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0v8a2 2 0 002 2h4a2 2 0 002-2v-8"></path>
                        </svg>
                        İş Atama (Yeni Bakım)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
