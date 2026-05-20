@extends('layouts.app')

@section('title', 'Muhasebe Kaydı Detayı - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Muhasebe Kaydı Detayı</h1>
                    <p class="text-gray-600 mt-1">{{ $accounting->category }} - {{ $accounting->description }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('accounting.edit', $accounting) }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                        ✏️ Düzenle
                    </a>
                    <a href="{{ route('accounting.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        ← Geri Dön
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Details Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Temel Bilgiler</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">İşlem Türü</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $accounting->type === 'gelir' ? 'bg-green-100 text-green-800' :
                                           ($accounting->type === 'gider' ? 'bg-red-100 text-red-800' :
                                           ($accounting->type === 'maas' ? 'bg-blue-100 text-blue-800' :
                                           ($accounting->type === 'vergi' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        @switch($accounting->type)
                                            @case('gelir')
                                                💰 Gelir
                                                @break
                                            @case('gider')
                                                💸 Gider
                                                @break
                                            @case('maas')
                                                👥 Maaş
                                                @break
                                            @case('vergi')
                                                🏛️ Vergi
                                                @break
                                            @case('sigorta')
                                                🛡️ Sigorta
                                                @break
                                            @default
                                                {{ $accounting->type }}
                                        @endswitch
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accounting->category }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Açıklama</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accounting->description }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">İşlem Tarihi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accounting->transaction_date->format('d.m.Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $accounting->status === 'odendi' ? 'bg-green-100 text-green-800' :
                                           ($accounting->status === 'tahsil_edildi' ? 'bg-blue-100 text-blue-800' :
                                           ($accounting->status === 'beklemede' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                        @switch($accounting->status)
                                            @case('beklemede')
                                                ⏳ Beklemede
                                                @break
                                            @case('odendi')
                                                ✅ Ödendi
                                                @break
                                            @case('tahsil_edildi')
                                                💰 Tahsil Edildi
                                                @break
                                            @case('iptal')
                                                ❌ İptal
                                                @break
                                            @default
                                                {{ $accounting->status }}
                                        @endswitch
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Finansal Bilgiler</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tutar</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">₺{{ number_format($accounting->amount, 2) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">KDV Oranı</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accounting->vat_rate }}%</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">KDV Tutarı</dt>
                                <dd class="mt-1 text-sm text-gray-900">₺{{ number_format($accounting->vat_amount, 2) }}</dd>
                            </div>

                            <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Toplam Tutar</dt>
                                <dd class="mt-1 text-2xl font-bold text-primary-600">₺{{ number_format($accounting->total_amount, 2) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ödeme Yöntemi</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @switch($accounting->payment_method)
                                        @case('nakit')
                                            💵 Nakit
                                            @break
                                        @case('banka_havalesi')
                                            🏦 Banka Havalesi
                                            @break
                                        @case('kredi_karti')
                                            💳 Kredi Kartı
                                            @break
                                        @case('cek')
                                            📄 Çek
                                            @break
                                        @default
                                            {{ $accounting->payment_method }}
                                    @endswitch
                                </dd>
                            </div>

                            @if($accounting->invoice_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fatura Numarası</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $accounting->invoice_number }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                @if($accounting->notes)
                    <!-- Notes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Notlar</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-900">{{ $accounting->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Related Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">İlişkili Bilgiler</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($accounting->building)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">İlgili Bina</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <div class="font-medium">{{ $accounting->building->name }}</div>
                                    <div class="text-gray-600">{{ $accounting->building->address }}</div>
                                </dd>
                            </div>
                        @endif

                        @if($accounting->employee)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">İlgili Personel</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <div class="font-medium">{{ $accounting->employee->full_name }}</div>
                                    <div class="text-gray-600">{{ $accounting->employee->position_label }}</div>
                                </dd>
                            </div>
                        @endif

                        @if($accounting->maintenanceReport)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">İlgili Bakım Raporu</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <div class="font-medium">Bakım #{{ $accounting->maintenanceReport->id }}</div>
                                    <div class="text-gray-600">{{ $accounting->maintenanceReport->work_description }}</div>
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Sistem Bilgileri</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kayıt ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $accounting->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Oluşturulma Tarihi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $accounting->created_at->format('d.m.Y H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Son Güncelleme</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $accounting->updated_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">İşlemler</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('accounting.edit', $accounting) }}"
                           class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Düzenle
                        </a>

                        <form action="{{ route('accounting.destroy', $accounting) }}" method="POST" class="w-full"
                              onsubmit="return confirm('Bu muhasebe kaydını silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
