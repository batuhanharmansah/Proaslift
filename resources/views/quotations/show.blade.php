@extends('layouts.app')

@section('title', $quotation->quote_no . ' - Teklif Detayı')

@section('content')
@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-800',
        'sent' => 'bg-blue-100 text-blue-800',
        'viewed' => 'bg-indigo-100 text-indigo-800',
        'accepted' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'expired' => 'bg-orange-100 text-orange-800',
        'converted' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
    ];
@endphp
<div class="p-6">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $quotation->quote_no }}</h1>
            <p class="text-gray-600 mt-1">{{ $quotation->type_label }} - {{ $quotation->customer_name }}</p>
            <div class="mt-3">
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusClasses[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $quotation->status_label }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Listeye Dön</a>
            <a href="{{ route('quotations.pdf', $quotation) }}" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">PDF İndir</a>
            @if($quotation->status === 'draft')
                <form method="POST" action="{{ route('quotations.send', $quotation) }}">
                    @csrf
                    <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">Gönderildi İşaretle</button>
                </form>
            @endif
            @if(in_array($quotation->status, ['sent', 'viewed', 'accepted'], true) && !$quotation->converted_receivable_id)
                <form method="POST" action="{{ route('quotations.convert-to-receivable', $quotation) }}" onsubmit="return confirm('Bu teklif için alacak kaydı oluşturulsun mu?')">
                    @csrf
                    <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">Alacağa Dönüştür</button>
                </form>
            @endif
            @if(!in_array($quotation->status, ['accepted', 'converted', 'cancelled'], true))
                <form method="POST" action="{{ route('quotations.cancel', $quotation) }}" onsubmit="return confirm('Teklif iptal edilsin mi?')">
                    @csrf
                    <button class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg">İptal Et</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Müşteri ve Teklif Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Müşteri:</span> <span class="font-medium">{{ $quotation->customer_name }}</span></div>
                    <div><span class="text-gray-500">Yetkili:</span> <span class="font-medium">{{ $quotation->customer_contact_name ?: '-' }}</span></div>
                    <div><span class="text-gray-500">Telefon:</span> <span class="font-medium">{{ $quotation->customer_phone ?: '-' }}</span></div>
                    <div><span class="text-gray-500">E-posta:</span> <span class="font-medium">{{ $quotation->customer_email ?: '-' }}</span></div>
                    <div><span class="text-gray-500">Bina:</span> <span class="font-medium">{{ $quotation->building?->name ?: '-' }}</span></div>
                    <div><span class="text-gray-500">Geçerlilik:</span> <span class="font-medium">{{ $quotation->valid_until?->format('d.m.Y') }}</span></div>
                </div>
                @if($quotation->customer_address)
                    <div class="mt-4 text-sm"><span class="text-gray-500">Adres:</span> {{ $quotation->customer_address }}</div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Fiyat Kalemleri</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kalem</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">KDV</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($quotation->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $item->description }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->category_label }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                    <td class="px-4 py-3 text-right">{{ $quotation->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-right">%{{ number_format($item->vat_rate, 0) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $quotation->currency }} {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-sm space-y-2 text-sm">
                        <div class="flex justify-between"><span>Ara Toplam</span><span>{{ $quotation->currency }} {{ number_format($quotation->subtotal, 2) }}</span></div>
                        <div class="flex justify-between"><span>İndirim</span><span>{{ $quotation->currency }} {{ number_format($quotation->discount_total, 2) }}</span></div>
                        <div class="flex justify-between"><span>KDV</span><span>{{ $quotation->currency }} {{ number_format($quotation->vat_total, 2) }}</span></div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2"><span>Genel Toplam</span><span>{{ $quotation->currency }} {{ number_format($quotation->grand_total, 2) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Kapsam ve Şartlar</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Dahil Olanlar</h3>
                        <div class="whitespace-pre-line text-gray-700">{{ $quotation->scope_inclusions ?: '-' }}</div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Hariç Olanlar</h3>
                        <div class="whitespace-pre-line text-gray-700">{{ $quotation->scope_exclusions ?: '-' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="font-semibold text-gray-900 mb-2">Ticari Şartlar</h3>
                        <div class="whitespace-pre-line text-gray-700">{{ $quotation->terms ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Müşteri Linki</h2>
                <input readonly value="{{ $quotation->publicUrl() }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                <p class="text-xs text-gray-500 mt-2">Bu link müşterinin teklifi görüntüleyip kabul/reddetmesi içindir.</p>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Zaman Çizgisi</h2>
                <div class="space-y-2 text-sm">
                    <div>Oluşturuldu: {{ $quotation->created_at->format('d.m.Y H:i') }}</div>
                    <div>Gönderildi: {{ $quotation->sent_at?->format('d.m.Y H:i') ?: '-' }}</div>
                    <div>Görüntülendi: {{ $quotation->viewed_at?->format('d.m.Y H:i') ?: '-' }}</div>
                    <div>Kabul: {{ $quotation->accepted_at?->format('d.m.Y H:i') ?: '-' }}</div>
                    <div>Red: {{ $quotation->rejected_at?->format('d.m.Y H:i') ?: '-' }}</div>
                    <div>Dönüşüm: {{ $quotation->converted_at?->format('d.m.Y H:i') ?: '-' }}</div>
                </div>
            </div>

            @if($quotation->convertedReceivable)
                <div class="bg-emerald-50 rounded-xl border border-emerald-200 p-6">
                    <h2 class="text-lg font-semibold text-emerald-900 mb-2">Alacak Kaydı Oluştu</h2>
                    <p class="text-sm text-emerald-800">Alacak ID: {{ $quotation->convertedReceivable->id }}</p>
                </div>
            @endif

            @if($quotation->acceptances->count() > 0)
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Müşteri Yanıtları</h2>
                    <div class="space-y-3">
                        @foreach($quotation->acceptances as $acceptance)
                            <div class="border border-gray-200 rounded-lg p-3 text-sm">
                                <div class="font-semibold">{{ $acceptance->action === 'accepted' ? 'Kabul' : 'Red' }} - {{ $acceptance->accepted_by_name }}</div>
                                <div class="text-gray-500">{{ $acceptance->accepted_at?->format('d.m.Y H:i') }}</div>
                                @if($acceptance->customer_note)
                                    <div class="mt-2 text-gray-700">{{ $acceptance->customer_note }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
