@extends('layouts.app')

@section('title', 'Çek & Senet - Harmanşah Yazılım')

@section('content')
<div class="p-6" x-data="{ showForm: false }">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Çek & Senet</h1>
            <p class="text-gray-600 mt-1">Gelen/giden çek ve senetlerinizi vade takibiyle yönetin.</p>
        </div>
        <button @click="showForm = !showForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            + Yeni Çek/Senet
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-green-200">
            <p class="text-xs font-medium text-green-600 uppercase">Bekleyen Alacak</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($pendingReceivable, 2) }} ₺</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-red-200">
            <p class="text-xs font-medium text-red-600 uppercase">Bekleyen Borç</p>
            <p class="text-2xl font-bold text-red-700">{{ number_format($pendingPayable, 2) }} ₺</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-yellow-200">
            <p class="text-xs font-medium text-yellow-600 uppercase">Yakında Vade (15 gün)</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $dueSoonCount }} adet</p>
        </div>
    </div>

    <div x-show="showForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Yeni Çek/Senet</h2>
        <form method="POST" action="{{ route('checks.store') }}" class="grid grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tip</label>
                <select name="type" required class="w-full rounded-lg border-gray-300">
                    <option value="cek">Çek</option>
                    <option value="senet">Senet</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yön</label>
                <select name="direction" required class="w-full rounded-lg border-gray-300">
                    <option value="gelen">Gelen (bize)</option>
                    <option value="giden">Giden (bizden)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari (Ad/Ünvan) *</label>
                <input type="text" name="counterparty_name" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Seri No</label>
                <input type="text" name="serial_number" class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banka</label>
                <input type="text" name="bank_name" class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺) *</label>
                <input type="number" name="amount" step="0.01" min="0.01" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vade Tarihi *</label>
                <input type="date" name="due_date" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">İlgili Bina (opsiyonel)</label>
                <select name="building_id" class="w-full rounded-lg border-gray-300">
                    <option value="">— Seçilmedi —</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex gap-2">
        <a href="{{ route('checks.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('direction') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Tümü</a>
        <a href="{{ route('checks.index', ['direction' => 'gelen']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('direction') === 'gelen' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Gelen</a>
        <a href="{{ route('checks.index', ['direction' => 'giden']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('direction') === 'giden' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">Giden</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tip</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Yön</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Cari</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Seri No</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Tutar</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Vade</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($checks as $check)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $check->type === 'cek' ? 'Çek' : 'Senet' }}</td>
                        <td class="px-4 py-3">{{ $check->direction === 'gelen' ? 'Gelen' : 'Giden' }}</td>
                        <td class="px-4 py-3">{{ $check->counterparty_name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $check->serial_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($check->amount, 2) }} ₺</td>
                        <td class="px-4 py-3">{{ $check->due_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('checks.update-status', $check) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-300">
                                    @foreach(\App\Models\CompanyCheck::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $check->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('checks.destroy', $check) }}" onsubmit="return confirm('Silinsin mi?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $checks->links() }}</div>
</div>
@endsection
