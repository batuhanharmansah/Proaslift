@extends('employee.layouts.app')

@section('title', $typeLabel)
@section('page-title', $typeLabel)

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $typeLabel }}</h1>
            <p class="text-gray-600">
                @if($type === 'dtr')
                    Sahada yaptığınız muayene sonrası durum tespit raporu doldurun.
                @else
                    Asansörde mahsur kalma (kurtarma) olayı sonrası bu formu doldurun.
                @endif
            </p>
        </div>
        <button @click="showForm = !showForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl transition duration-200 text-sm">
            + Yeni {{ $typeLabel }}
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">{{ session('success') }}</div>
    @endif

    <div x-show="showForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Yeni {{ $typeLabel }}</h2>
        <form method="POST" action="{{ route('employee.compliance-documents.store', $type) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama *</label>
                <textarea name="description" rows="4" required class="w-full rounded-lg border-gray-300"
                          placeholder="{{ $type === 'dtr' ? 'Tespit edilen eksiklikler / genel durum...' : 'Olayın gelişimi, mahsur kalma süresi, çözüm...' }}"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 text-sm">Kaydet</button>
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $doc->event_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-3">{{ $doc->building->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $doc->inspector_or_technician_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium">{{ \App\Models\ComplianceDocument::STATUSES[$doc->status] ?? $doc->status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 pb-3 text-xs text-gray-500">{{ $doc->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $documents->links() }}</div>
</div>
@endsection
