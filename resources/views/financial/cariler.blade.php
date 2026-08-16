@extends('layouts.app')

@section('title', 'Cariler - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Cariler</h1>
            <p class="text-gray-600 mt-1">Müşteri (bina), tedarikçi/gider ve personel bakiyelerinin birleşik görünümü.</p>
        </div>
        <a href="{{ route('financial.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            Finansal Yönetime Dön
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-green-200">
            <p class="text-xs font-medium text-green-600 uppercase">Toplam Alacaklarımız</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($totalReceivable, 2) }} ₺</p>
            <p class="text-xs text-gray-400">Carilerin firmaya borcu (tahsil edilecek)</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-red-200">
            <p class="text-xs font-medium text-red-600 uppercase">Toplam Borçlarımız</p>
            <p class="text-2xl font-bold text-red-700">{{ number_format($totalPayable, 2) }} ₺</p>
            <p class="text-xs text-gray-400">Firmanın carilere borcu (ödenecek)</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex gap-2">
        <a href="{{ route('cariler.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Tümü</a>
        <a href="{{ route('cariler.index', ['type' => 'customer']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'customer' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Müşteri</a>
        <a href="{{ route('cariler.index', ['type' => 'supplier']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'supplier' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Tedarikçi</a>
        <a href="{{ route('cariler.index', ['type' => 'employee']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'employee' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Personel</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Ad / Ünvan</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tip</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Bakiye</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ $row['link'] }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ $row['name'] }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $row['type_label'] }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-medium">{{ number_format($row['balance'], 2) }} ₺</span>
                            <span class="block text-xs text-gray-400">{{ $row['balance_label'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
