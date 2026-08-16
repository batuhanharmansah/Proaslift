<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $building->name }} - Müşteri Portalı</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php($brandColor = $building->company->brand_primary_color ?? '#2563eb')
<body class="bg-gray-100 font-sans">
    <div class="h-2" style="background-color: {{ $brandColor }};"></div>
    <div class="max-w-4xl mx-auto p-4 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                @if($building->company->logo_path)
                    <img src="{{ Storage::url($building->company->logo_path) }}" alt="Logo" class="h-10 w-10 rounded-lg object-cover border border-gray-200">
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $building->name }}</h1>
                    <p class="text-gray-500 text-sm">{{ $building->address }}, {{ $building->district }}/{{ $building->city }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('portal.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">Çıkış Yap</button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
        @endif

        <!-- Açık Arızalar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Açık Arızalar</h2>
            @forelse($issues as $issue)
                <div class="border-b border-gray-100 py-3 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $issue->description }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $issue->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full whitespace-nowrap">{{ $issue->status }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Açık arıza bulunmuyor.</p>
            @endforelse
        </div>

        <!-- Bakım Geçmişi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Bakım Geçmişi</h2>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="pb-2">Tarih</th>
                        <th class="pb-2">Tip</th>
                        <th class="pb-2">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $m)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-2">{{ $m->scheduled_date->format('d.m.Y') }}</td>
                            <td class="py-2">{{ $m->maintenance_type_label }}</td>
                            <td class="py-2">{{ $m->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Bakım kaydı bulunmuyor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Ödemeler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Ödeme Durumu</h2>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="pb-2">Açıklama</th>
                        <th class="pb-2">Vade</th>
                        <th class="pb-2 text-right">Tutar</th>
                        <th class="pb-2 text-right">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receivables as $r)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-2">{{ $r->title }}</td>
                            <td class="py-2">{{ $r->due_date->format('d.m.Y') }}</td>
                            <td class="py-2 text-right">{{ number_format($r->total_amount, 2) }} ₺</td>
                            <td class="py-2 text-right">
                                <span class="text-xs {{ $r->status === 'odendi' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} px-2 py-1 rounded-full">
                                    {{ $r->status === 'odendi' ? 'Ödendi' : 'Beklemede' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-400">Ödeme kaydı bulunmuyor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
