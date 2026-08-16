@extends('employee.layouts.app')

@section('title', 'Profilim')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
        <p class="text-gray-600 mt-1">Kişisel bilgilerinizi görüntüleyin ve güncelleyin</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Kişisel Bilgiler</h2>
        <form action="{{ route('employee.profile.update') }}" method="POST">
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
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $employee->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    Bilgileri Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
