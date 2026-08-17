@extends('employee.layouts.app')

@section('title', 'Etiket Detayı')
@section('page-title', 'Etiket Detayı')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $elevatorLabel->building->name ?? '-' }}</h1>
            <p class="text-gray-600">{{ $elevatorLabel->building->address ?? '' }}</p>
        </div>
        <a href="{{ route('employee.elevator-labels.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            ← Geri Dön
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Etiket Bilgileri</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Etiket Rengi</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                    @if($elevatorLabel->label_color === 'yesil') bg-green-100 text-green-800
                    @elseif($elevatorLabel->label_color === 'mavi') bg-blue-100 text-blue-800
                    @elseif($elevatorLabel->label_color === 'sari') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ $elevatorLabel->label_color_text }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Durum</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $elevatorLabel->status_text }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Kontrol Tarihi</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $elevatorLabel->control_date->format('d.m.Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Sonraki Kontrol Tarihi</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $elevatorLabel->next_control_date->format('d.m.Y') }}</p>
            </div>
            @if($elevatorLabel->inspector_name)
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Müfettiş</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $elevatorLabel->inspector_name }}</p>
            </div>
            @endif
            @if($elevatorLabel->inspector_company)
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Kontrol Firması</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $elevatorLabel->inspector_company }}</p>
            </div>
            @endif
        </div>

        @if($elevatorLabel->description)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Açıklama</p>
            <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $elevatorLabel->description }}</p>
        </div>
        @endif
    </div>

    @if($labelHistory->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Bu Binanın Önceki Etiketleri</h2>
        <div class="space-y-2">
            @foreach($labelHistory as $history)
                <div class="flex items-center justify-between text-sm border-b border-gray-50 pb-2">
                    <span class="text-gray-700">{{ $history->control_date->format('d.m.Y') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($history->label_color === 'yesil') bg-green-100 text-green-800
                        @elseif($history->label_color === 'mavi') bg-blue-100 text-blue-800
                        @elseif($history->label_color === 'sari') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $history->label_color_text }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
