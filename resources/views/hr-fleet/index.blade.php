@extends('layouts.app')

@section('title', 'Hakediş & Araç Takip - Harmanşah Yazılım')

@section('content')
<div class="p-6" x-data="{ tab: '{{ old('_active_tab', 'bonus') }}', showBonusForm: {{ $errors->any() && old('_active_tab') === 'bonus' ? 'true' : 'false' }}, showVehicleForm: {{ $errors->any() && old('_active_tab') === 'vehicle' ? 'true' : 'false' }}, showAbsenceForm: {{ $errors->any() && old('_active_tab') === 'absence' ? 'true' : 'false' }} }">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Hakediş & Araç Takip & Devamsızlık</h1>
        <p class="text-gray-600 mt-1">Personel prim/mesai ödemeleri, firma araçları ve devamsızlık kayıtlarını yönetin.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 mb-6 flex gap-2">
        <button @click="tab = 'bonus'" :class="tab === 'bonus' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">Hakedişler</button>
        <button @click="tab = 'vehicle'" :class="tab === 'vehicle' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">Araç Takip</button>
        <button @click="tab = 'absence'" :class="tab === 'absence' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">Devamsızlık</button>
    </div>

    <!-- HAKEDİŞ -->
    <div x-show="tab === 'bonus'" x-cloak>
        <div class="flex justify-end mb-4">
            <button @click="showBonusForm = !showBonusForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl text-sm">+ Yeni Hakediş</button>
        </div>
        <div x-show="showBonusForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="POST" action="{{ route('hr-fleet.bonus.store') }}" class="grid grid-cols-3 gap-4">
                @csrf
                <input type="hidden" name="_active_tab" value="bonus">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personel *</label>
                    <select name="employee_id" required class="w-full rounded-lg border-gray-300">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
                    <input type="date" name="bonus_date" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tip *</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300">
                        @foreach(\App\Models\EmployeeBonus::TYPES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="w-full rounded-lg border-gray-300">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <input type="text" name="description" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="col-span-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Kaydet</button>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Personel</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tarih</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tip</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Tutar</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Açıklama</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bonuses as $bonus)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $bonus->employee?->first_name }} {{ $bonus->employee?->last_name }}</td>
                            <td class="px-4 py-3">{{ $bonus->bonus_date->format('d.m.Y') }}</td>
                            <td class="px-4 py-3">{{ \App\Models\EmployeeBonus::TYPES[$bonus->type] ?? $bonus->type }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($bonus->amount, 2) }} ₺</td>
                            <td class="px-4 py-3 text-gray-500">{{ $bonus->description ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('hr-fleet.bonus.destroy', $bonus) }}" onsubmit="return confirm('Silinsin mi?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ARAÇ TAKİP -->
    <div x-show="tab === 'vehicle'" x-cloak>
        <div class="flex justify-end mb-4">
            <button @click="showVehicleForm = !showVehicleForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl text-sm">+ Yeni Araç</button>
        </div>
        <div x-show="showVehicleForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="POST" action="{{ route('hr-fleet.vehicle.store') }}" class="grid grid-cols-3 gap-4">
                @csrf
                <input type="hidden" name="_active_tab" value="vehicle">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plaka *</label>
                    <input type="text" name="plate" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marka/Model</label>
                    <input type="text" name="brand_model" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sürücü</label>
                    <select name="driver_employee_id" class="w-full rounded-lg border-gray-300">
                        <option value="">— Seçilmedi —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Muayene Tarihi</label>
                    <input type="date" name="inspection_due_date" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sigorta Bitiş</label>
                    <input type="date" name="insurance_due_date" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                </div>
                <div class="col-span-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Kaydet</button>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Plaka</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Marka/Model</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sürücü</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Muayene</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sigorta</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vehicles as $vehicle)
                        @php
                            $inspectionSoon = $vehicle->inspection_due_date && $vehicle->inspection_due_date->diffInDays(now(), false) > -15 && $vehicle->inspection_due_date->isFuture();
                            $insuranceSoon = $vehicle->insurance_due_date && $vehicle->insurance_due_date->diffInDays(now(), false) > -15 && $vehicle->insurance_due_date->isFuture();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $vehicle->plate }}</td>
                            <td class="px-4 py-3">{{ $vehicle->brand_model ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $vehicle->driver?->first_name }} {{ $vehicle->driver?->last_name ?? ($vehicle->driver ? '' : '—') }}</td>
                            <td class="px-4 py-3 {{ $inspectionSoon ? 'text-yellow-700 font-medium' : ($vehicle->inspection_due_date?->isPast() ? 'text-red-700 font-medium' : '') }}">{{ $vehicle->inspection_due_date?->format('d.m.Y') ?? '—' }}</td>
                            <td class="px-4 py-3 {{ $insuranceSoon ? 'text-yellow-700 font-medium' : ($vehicle->insurance_due_date?->isPast() ? 'text-red-700 font-medium' : '') }}">{{ $vehicle->insurance_due_date?->format('d.m.Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('hr-fleet.vehicle.destroy', $vehicle) }}" onsubmit="return confirm('Silinsin mi?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DEVAMSIZLIK -->
    <div x-show="tab === 'absence'" x-cloak>
        <div class="flex justify-end mb-4">
            <button @click="showAbsenceForm = !showAbsenceForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl text-sm">+ Yeni Devamsızlık</button>
        </div>
        <div x-show="showAbsenceForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="POST" action="{{ route('hr-fleet.absence.store') }}" class="grid grid-cols-3 gap-4">
                @csrf
                <input type="hidden" name="_active_tab" value="absence">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personel *</label>
                    <select name="employee_id" required class="w-full rounded-lg border-gray-300">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç *</label>
                    <input type="date" name="start_date" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş *</label>
                    <input type="date" name="end_date" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tip *</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300">
                        @foreach(\App\Models\EmployeeAbsence::TYPES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Not</label>
                    <input type="text" name="note" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="col-span-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Kaydet</button>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Personel</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Başlangıç</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Bitiş</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tip</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Not</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($absences as $absence)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $absence->employee?->first_name }} {{ $absence->employee?->last_name }}</td>
                            <td class="px-4 py-3">{{ $absence->start_date->format('d.m.Y') }}</td>
                            <td class="px-4 py-3">{{ $absence->end_date->format('d.m.Y') }}</td>
                            <td class="px-4 py-3">{{ \App\Models\EmployeeAbsence::TYPES[$absence->type] ?? $absence->type }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $absence->note ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('hr-fleet.absence.destroy', $absence) }}" onsubmit="return confirm('Silinsin mi?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
