@extends('layouts.app')

@section('title', 'Bakım İşi Düzenle - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bakım İşi Düzenle</h1>
            <p class="text-gray-600 mt-1">{{ $maintenance->maintenance_type_label }} - {{ $maintenance->building->name }}</p>
        </div>
        <a href="{{ route('maintenance.show', $maintenance) }}"
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
        <form action="{{ route('maintenance.update', $maintenance) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Temel Bilgiler -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Temel Bilgiler</h2>
                </div>

                <div>
                    <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">Bina *</label>
                    <select id="building_id" name="building_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id', $maintenance->building_id) == $building->id ? 'selected' : '' }}>
                                {{ $building->name }} - {{ $building->district }}, {{ $building->city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="maintenance_type" class="block text-sm font-medium text-gray-700 mb-2">Bakım Tipi *</label>
                    <select id="maintenance_type" name="maintenance_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="rutin_bakim" {{ old('maintenance_type', $maintenance->maintenance_type) === 'rutin_bakim' ? 'selected' : '' }}>Rutin Bakım</option>
                        <option value="ariza_onarim" {{ old('maintenance_type', $maintenance->maintenance_type) === 'ariza_onarim' ? 'selected' : '' }}>Arıza Onarım</option>
                        <option value="periyodik_kontrol" {{ old('maintenance_type', $maintenance->maintenance_type) === 'periyodik_kontrol' ? 'selected' : '' }}>Periyodik Kontrol</option>
                        <option value="modernizasyon" {{ old('maintenance_type', $maintenance->maintenance_type) === 'modernizasyon' ? 'selected' : '' }}>Modernizasyon</option>
                    </select>
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Planlanan Tarih *</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', $maintenance->scheduled_date ? $maintenance->scheduled_date->format('Y-m-d') : '') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Çalışanın Gideceği Saat</label>
                    <input type="time" id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time', $maintenance->scheduled_time ? \Carbon\Carbon::parse($maintenance->scheduled_time)->format('H:i') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Örn: 09:00">
                    <p class="mt-1 text-xs text-gray-500">Çalışanın binaya varacağı tahmini saat</p>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Öncelik *</label>
                    <select id="priority" name="priority" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="dusuk" {{ old('priority', $maintenance->priority) === 'dusuk' ? 'selected' : '' }}>Düşük</option>
                        <option value="normal" {{ old('priority', $maintenance->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="yuksek" {{ old('priority', $maintenance->priority) === 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                        <option value="acil" {{ old('priority', $maintenance->priority) === 'acil' ? 'selected' : '' }}>Acil</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Durum *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz</option>
                        <option value="planli" {{ old('status', $maintenance->status) === 'planli' ? 'selected' : '' }}>Planlı</option>
                        <option value="atandi" {{ old('status', $maintenance->status) === 'atandi' ? 'selected' : '' }}>Atandı</option>
                        <option value="baslandi" {{ old('status', $maintenance->status) === 'baslandi' ? 'selected' : '' }}>Başlandı</option>
                        <option value="tamamlandi" {{ old('status', $maintenance->status) === 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                        <option value="ertelendi" {{ old('status', $maintenance->status) === 'ertelendi' ? 'selected' : '' }}>Ertelendi</option>
                        <option value="iptal" {{ old('status', $maintenance->status) === 'iptal' ? 'selected' : '' }}>İptal</option>
                    </select>
                </div>

                <!-- Personel Atama -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 mt-8">Personel Atama</h2>
                </div>

                <div class="md:col-span-2">
                    <label for="assigned_employee_id" class="block text-sm font-medium text-gray-700 mb-2">Atanacak Personel</label>
                    <select id="assigned_employee_id" name="assigned_employee_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seçiniz (Opsiyonel)</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_employee_id', $maintenance->assigned_employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->first_name }} {{ $employee->last_name }} - {{ $employee->position_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Açıklama -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Açıklama *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Yapılacak işlerin detaylı açıklaması...">{{ old('description', $maintenance->description) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('maintenance.show', $maintenance) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    İptal
                </a>
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
