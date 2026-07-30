<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quotation->quote_no }} - Teklif Onayı</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto p-4 md:p-8">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 md:p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
            @endif

            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 border-b border-gray-200 pb-6 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $quotation->quote_no }}</h1>
                    <p class="text-gray-600 mt-1">{{ $quotation->type_label }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $quotation->company?->name }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Geçerlilik</div>
                    <div class="font-semibold">{{ $quotation->valid_until?->format('d.m.Y') }}</div>
                    <div class="text-2xl font-bold text-primary-600 mt-3">{{ $quotation->currency }} {{ number_format($quotation->grand_total, 2) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                <div><span class="text-gray-500">Müşteri:</span> <span class="font-medium">{{ $quotation->customer_name }}</span></div>
                <div><span class="text-gray-500">Bina:</span> <span class="font-medium">{{ $quotation->building?->name ?: '-' }}</span></div>
                <div class="md:col-span-2"><span class="text-gray-500">Kapsam:</span> {{ $quotation->scope_summary ?: '-' }}</div>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quotation->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ $quotation->currency }} {{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">Dahil Olanlar</h2>
                    <div class="text-sm text-gray-700 whitespace-pre-line">{{ $quotation->scope_inclusions ?: '-' }}</div>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">Hariç Olanlar</h2>
                    <div class="text-sm text-gray-700 whitespace-pre-line">{{ $quotation->scope_exclusions ?: '-' }}</div>
                </div>
                <div class="md:col-span-2">
                    <h2 class="font-semibold text-gray-900 mb-2">Ticari Şartlar</h2>
                    <div class="text-sm text-gray-700 whitespace-pre-line">{{ $quotation->terms ?: '-' }}</div>
                </div>
            </div>

            @if($quotation->status === 'draft')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center font-semibold text-gray-700">
                    Bu teklif henüz hazırlanma aşamasında. Onaylamak için lütfen size gönderilen güncel bağlantıyı bekleyin.
                </div>
            @elseif(in_array($quotation->status, ['accepted', 'rejected', 'expired', 'converted', 'cancelled'], true))
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center font-semibold text-gray-700">
                    Bu teklifin güncel durumu: {{ $quotation->status_label }}
                </div>
            @else
                <form method="POST" action="{{ route('quotations.public.respond', $quotation->public_token) }}" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    @csrf
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Teklif Yanıtı</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                        <input type="text" name="accepted_by_name" placeholder="Ad Soyad" class="px-4 py-2 border border-gray-300 rounded-lg" required>
                        <input type="text" name="accepted_by_phone" placeholder="Telefon" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <input type="email" name="accepted_by_email" placeholder="E-posta" class="px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <textarea name="customer_note" rows="3" placeholder="Notunuz" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4"></textarea>
                    <div class="flex flex-col md:flex-row gap-3 justify-end">
                        <button name="action" value="rejected" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg">Reddet</button>
                        <button name="action" value="accepted" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg">Kabul Et</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</body>
</html>
