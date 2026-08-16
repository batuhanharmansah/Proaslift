@extends('layouts.app')

@section('title', 'Bildirim Tercihleri - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bildirim Tercihleri</h1>
            <p class="text-gray-600 mt-1">Hangi olayda hangi kanaldan bildirim gideceğini yönetin. Uygulama içi (zil) bildirimler her zaman açıktır, kapatılamaz. SMS kredi tüketir.</p>
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

    <form method="POST" action="{{ route('settings.notification-preferences.update') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Olay</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500 w-32">Push</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500 w-32">SMS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($events as $eventKey => $label)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ $label }}</td>
                            @foreach($channels as $channel)
                                <td class="px-4 py-3 text-center">
                                    <input type="hidden" name="enabled[{{ $eventKey }}][{{ $channel }}]" value="0">
                                    <input type="checkbox" name="enabled[{{ $eventKey }}][{{ $channel }}]" value="1"
                                           {{ $matrix[$eventKey][$channel] ? 'checked' : '' }}
                                           class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">
            Tercihleri Kaydet
        </button>
    </form>
</div>
@endsection
