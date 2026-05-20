@extends('layouts.app')

@section('title', 'Çalışan Profili - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
            <p class="text-gray-600 mt-1">{{ $employee->position_label }} - Çalışan bilgilerini yönetin</p>
        </div>
        <div class="flex space-x-3">
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

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana Bilgiler -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Çalışan Bilgileri</h2>
                <form action="{{ route('employees.profile.update', $employee) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Ad</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Soyad</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Pozisyon</label>
                            <select id="position" name="position"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('position') border-red-500 @enderror">
                                <option value="teknisyen" {{ old('position', $employee->position) == 'teknisyen' ? 'selected' : '' }}>Teknisyen</option>
                                <option value="usta" {{ old('position', $employee->position) == 'usta' ? 'selected' : '' }}>Usta</option>
                                <option value="muhendis" {{ old('position', $employee->position) == 'muhendis' ? 'selected' : '' }}>Mühendis</option>
                                <option value="yonetici" {{ old('position', $employee->position) == 'yonetici' ? 'selected' : '' }}>Yönetici</option>
                                <option value="muhasebe" {{ old('position', $employee->position) == 'muhasebe' ? 'selected' : '' }}>Muhasebe</option>
                            </select>
                            @error('position')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Maaş</label>
                            <input type="number" step="0.01" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('salary') border-red-500 @enderror">
                            @error('salary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">İşe Başlama Tarihi</label>
                            <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <textarea id="address" name="address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                            <textarea id="notes" name="notes" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $employee->notes) }}</textarea>
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
            <!-- Çalışan Durumu -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Çalışan Durumu</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $employee->is_active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pozisyon</label>
                        <p class="text-gray-900">{{ $employee->position_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maaş</label>
                        <p class="text-gray-900 font-semibold">₺{{ number_format($employee->salary, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- İş Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">İş Bilgileri</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İşe Başlama Tarihi</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Çalışma Süresi</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($employee->hire_date)->diffForHumans() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oluşturulma Tarihi</label>
                        <p class="text-gray-900">{{ $employee->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Son Güncelleme</label>
                        <p class="text-gray-900">{{ $employee->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

