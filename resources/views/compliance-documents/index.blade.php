@extends('layouts.app')

@section('title', $typeLabel . ' - Harmanşah Yazılım')

@section('content')
<div class="p-6" x-data="{ showForm: false }">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $typeLabel }}</h1>
            <p class="text-gray-600 mt-1">
                @if($type === 'dtr')
                    TSE/G muayenesi sonrası durum tespit raporlarınızı buradan yönetin.
                @else
                    Asansörde mahsur kalma (kurtarma) olayları sonrası doldurulan formlar.
                @endif
            </p>
        </div>
        <button @click="showForm = !showForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            + Yeni {{ $typeLabel }}
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">{{ session('success') }}</div>
    @endif

    <div x-show="showForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Yeni {{ $typeLabel }}</h2>
        <form method="POST" action="{{ route('compliance-documents.store', $type) }}" class="grid grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bina *</label>
                <select name="building_id" required class="w-full rounded-lg border-gray-300">
                    <option value="">— Seçin —</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
                <input type="date" name="event_date" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $type === 'dtr' ? 'Muayene Eden' : 'Kurtarmayı Yapan Teknisyen' }}
                </label>
                <input type="text" name="inspector_or_technician_name" class="w-full rounded-lg border-gray-300">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama *</label>
                <textarea name="description" rows="4" required class="w-full rounded-lg border-gray-300"
                          placeholder="{{ $type === 'dtr' ? 'Tespit edilen eksiklikler / genel durum...' : 'Olayın gelişimi, mahsur kalma süresi, çözüm...' }}"></textarea>
            </div>
            <div class="col-span-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Kaydet</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tarih</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Bina</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $type === 'dtr' ? 'Muayene Eden' : 'Teknisyen' }}</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $doc->event_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-3">{{ $doc->building->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $doc->inspector_or_technician_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('compliance-documents.update-status', $doc) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-300">
                                    @foreach(\App\Models\ComplianceDocument::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $doc->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('compliance-documents.destroy', $doc) }}" onsubmit="return confirm('Silinsin mi?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 pb-3 text-xs text-gray-500">{{ $doc->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
</div>
@endsection
