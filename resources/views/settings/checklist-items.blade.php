@extends('layouts.app')

@section('title', 'Bakım Kontrol Listesi Maddeleri - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bakım Kontrol Listesi — Özel Maddeler</h1>
            <p class="text-gray-600 mt-1">Standart 4 bölümlük rutin bakım kontrol listesine kendi firmanıza özel ek maddeler ekleyin. Bu maddeler hem web hem mobil bakım tamamlama formunda ilgili bölümün altında otomatik görünür.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            Panele Dön
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @foreach($sections as $sectionId => $sectionLabel)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $sectionLabel }}</h2>

            @if(isset($items[$sectionId]) && $items[$sectionId]->count() > 0)
                <ul class="divide-y divide-gray-100 mb-4">
                    @foreach($items[$sectionId] as $item)
                        <li class="flex items-center justify-between py-3">
                            <span class="text-gray-700">{{ $item->title }}</span>
                            <form method="POST" action="{{ route('settings.checklist-items.destroy', $item) }}"
                                  onsubmit="return confirm('Bu maddeyi silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-400 text-sm mb-4">Bu bölümde henüz özel madde yok.</p>
            @endif

            <form method="POST" action="{{ route('settings.checklist-items.store') }}" class="flex gap-2">
                @csrf
                <input type="hidden" name="section_id" value="{{ $sectionId }}">
                <input type="text" name="title" required maxlength="500"
                       placeholder="Yeni madde ekle (örn. Kapı fotoseli hassasiyet testi)"
                       class="flex-1 rounded-lg border-gray-300 text-sm">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 whitespace-nowrap">
                    + Ekle
                </button>
            </form>
        </div>
    @endforeach
</div>
@endsection
