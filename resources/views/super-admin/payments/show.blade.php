@extends('super-admin.layouts.app')

@section('title', 'Ödeme Detayları - ' . $payment->company->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                <span class="text-lg font-medium text-white">{{ substr($payment->company->name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ödeme Detayları</h1>
                <p class="text-gray-600">{{ $payment->company->name }} - ₺{{ number_format($payment->amount, 2) }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                @if($payment->status === 'paid') bg-green-100 text-green-800
                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($payment->status === 'overdue') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $payment->statusLabel }}
            </span>
            <a href="{{ route('super-admin.payments.edit', $payment) }}"
               class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                Düzenle
            </a>
            <a href="{{ route('super-admin.payments.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                ← Geri Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ödeme Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ödeme Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tutarı</label>
                        <p class="text-2xl font-bold text-green-600">₺{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Durumu</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($payment->status === 'paid') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status === 'overdue') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $payment->statusLabel }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tarihi</label>
                        <p class="text-sm text-gray-900">{{ $payment->payment_date->format('d.m.Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->payment_date->format('l') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vade Tarihi</label>
                        <p class="text-sm text-gray-900">{{ $payment->due_date->format('d.m.Y') }}</p>
                        @if($payment->due_date->isPast() && $payment->status !== 'paid')
                            <p class="text-xs text-red-600 font-medium">{{ $payment->due_date->diffForHumans() }}</p>
                        @elseif($payment->due_date->diffInDays() <= 3 && $payment->status !== 'paid')
                            <p class="text-xs text-yellow-600 font-medium">{{ $payment->due_date->diffInDays() }} gün kaldı</p>
                        @else
                            <p class="text-xs text-gray-500">{{ $payment->due_date->format('l') }}</p>
                        @endif
                    </div>
                    @if($payment->invoice_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fatura Numarası</label>
                            <p class="text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">{{ $payment->invoice_number }}</p>
                        </div>
                    @endif
                </div>

                @if($payment->notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-900">{{ $payment->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Firma Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Firma Bilgileri</h2>
                    <a href="{{ route('super-admin.companies.show', $payment->company) }}"
                       class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                        Firma Detayları →
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Firma Adı</label>
                        <p class="text-sm text-gray-900">{{ $payment->company->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                        <p class="text-sm text-gray-900">
                            <a href="mailto:{{ $payment->company->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $payment->company->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Planı</label>
                        <p class="text-sm text-gray-900">{{ $payment->company->subscription_plan ?? 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aylık Ücret</label>
                        <p class="text-sm font-semibold text-green-600">₺{{ number_format($payment->company->monthly_fee, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sistem Durumu</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $payment->company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $payment->company->is_active ? 'Aktif' : 'Askıda' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Durumu</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($payment->company->subscription_status === 'active') bg-green-100 text-green-800
                            @elseif($payment->company->subscription_status === 'trial') bg-blue-100 text-blue-800
                            @elseif($payment->company->subscription_status === 'expired') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $payment->company->subscriptionStatusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ödeme Geçmişi -->
            @if($payment->company->payments->count() > 1)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Bu Firmaya Ait Diğer Ödemeler</h2>
                    <div class="space-y-3">
                        @foreach($payment->company->payments->where('id', '!=', $payment->id)->take(5) as $otherPayment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">₺{{ number_format($otherPayment->amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $otherPayment->payment_date->format('d.m.Y') }}</p>
                                    @if($otherPayment->invoice_number)
                                        <p class="text-xs text-gray-400">{{ $otherPayment->invoice_number }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($otherPayment->status === 'paid') bg-green-100 text-green-800
                                        @elseif($otherPayment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($otherPayment->status === 'overdue') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $otherPayment->statusLabel }}
                                    </span>
                                    <a href="{{ route('super-admin.payments.show', $otherPayment) }}"
                                       class="text-primary-600 hover:text-primary-800 text-xs">Detay</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($payment->company->payments->count() > 6)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('super-admin.payments.index', ['company_search' => $payment->company->name]) }}"
                               class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                Tüm Ödemeleri Görüntüle ({{ $payment->company->payments->count() - 1 }} daha) →
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı İşlemler</h3>
                <div class="space-y-3">
                    @if($payment->status === 'pending')
                        <form action="{{ route('super-admin.payments.mark-paid', $payment) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Bu ödemeyi ödendi olarak işaretlemek istediğinizden emin misiniz?')"
                                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ödendi Olarak İşaretle
                            </button>
                        </form>
                    @endif

                    @if($payment->status === 'pending' && $payment->due_date->isPast())
                        <form action="{{ route('super-admin.payments.mark-overdue', $payment) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Bu ödemeyi gecikmiş olarak işaretlemek istediğinizden emin misiniz?')"
                                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                Gecikmiş Olarak İşaretle
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('super-admin.payments.edit', $payment) }}"
                       class="flex items-center w-full px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Ödeme Bilgilerini Düzenle
                    </a>

                    <a href="{{ route('super-admin.companies.show', $payment->company) }}"
                       class="flex items-center w-full px-4 py-3 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-4 h-4 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Firma Detaylarını Görüntüle
                    </a>

                    <a href="{{ route('super-admin.payments.create', ['company_id' => $payment->company->id]) }}"
                       class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Bu Firma İçin Yeni Ödeme
                    </a>
                </div>
            </div>

            <!-- İstatistikler -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Firma İstatistikleri</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Toplam Ödenen:</span>
                        <span class="text-sm font-semibold text-green-600">
                            ₺{{ number_format($payment->company->payments->where('status', 'paid')->sum('amount'), 0) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Bekleyen Ödeme:</span>
                        <span class="text-sm font-semibold text-yellow-600">
                            ₺{{ number_format($payment->company->payments->where('status', 'pending')->sum('amount'), 0) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Gecikmiş Ödeme:</span>
                        <span class="text-sm font-semibold text-red-600">
                            ₺{{ number_format($payment->company->payments->where('status', 'overdue')->sum('amount'), 0) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-sm text-gray-600">Toplam Ödeme:</span>
                        <span class="text-sm font-bold text-gray-900">
                            ₺{{ number_format($payment->company->payments->sum('amount'), 0) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Ödeme Sayısı:</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $payment->company->payments->count() }} ödeme
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sistem Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sistem Bilgileri</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Oluşturulma:</span>
                        <span class="text-gray-900">{{ $payment->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Son Güncelleme:</span>
                        <span class="text-gray-900">{{ $payment->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ödeme ID:</span>
                        <span class="text-gray-900 font-mono">#{{ $payment->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
