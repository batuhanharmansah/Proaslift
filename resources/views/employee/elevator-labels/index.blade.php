@extends('employee.layouts.app')

@section('title', 'Etiket Takibi')
@section('page-title', 'Etiket Takibi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🏷️ Asansör Etiket Takibi</h1>
            <p class="text-gray-600">Periyodik kontrol etiketleri ve süre takibi</p>
        </div>
        <a href="{{ route('employee.elevator-labels.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            ➕ Yeni Etiket
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Bina</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Etiket</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Kontrol Tarihi</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Son Tarih</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($buildings as $building)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $building->name }}</td>
                        <td class="px-4 py-3">
                            @if($building->activeLabel)
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($building->activeLabel->label_color === 'yesil') bg-green-100 text-green-800
                                    @elseif($building->activeLabel->label_color === 'mavi') bg-blue-100 text-blue-800
                                    @elseif($building->activeLabel->label_color === 'sari') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $building->activeLabel->label_color_text }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Etiket yok</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $building->activeLabel?->control_date?->format('d.m.Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $building->activeLabel?->next_control_date?->format('d.m.Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($building->activeLabel)
                                <a href="{{ route('employee.elevator-labels.show', $building->activeLabel) }}"
                                   class="text-primary-600 hover:text-primary-800 text-sm font-medium">Görüntüle</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $buildings->links() }}</div>
</div>
@endsection
