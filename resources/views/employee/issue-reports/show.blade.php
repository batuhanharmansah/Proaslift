@extends('employee.layouts.app')

@section('title', "Arıza Bildirimi #{$issueReport->id}")
@section('page-title', "Arıza Bildirimi #{$issueReport->id}")

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Arıza Bildirimi #{{ $issueReport->id }}</h1>
            <p class="text-gray-600">{{ $issueReport->building->name ?? '-' }}</p>
        </div>
        <a href="{{ route('employee.issue-reports.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            ← Geri Dön
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Arıza Detayları</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Arıza Tipi</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->issue_type_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Öncelik</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 {{ $issueReport->priority_color }}">
                            {{ $issueReport->priority_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Durum</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 {{ $issueReport->status_color }}">
                            {{ $issueReport->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Bildiren Kişi</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->reported_by }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Bina Adresi</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->building->address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Bildirim Tarihi</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    @if($issueReport->location_details)
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Konum Detayı</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->location_details }}</p>
                    </div>
                    @endif
                </div>

                @if($issueReport->is_urgent)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg px-4 py-2">
                        <span class="text-sm font-medium text-red-700">⚠ Acil Durum olarak işaretlendi</span>
                    </div>
                @endif

                <div class="mt-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Açıklama</p>
                    <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $issueReport->description }}</p>
                </div>

                @if($issueReport->customer_notes)
                <div class="mt-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Müşteri Notları</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $issueReport->customer_notes }}</p>
                </div>
                @endif
            </div>

            @if($issueReport->contact_name || $issueReport->contact_phone)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">İletişim Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($issueReport->contact_name)
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Kişi</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->contact_name }}</p>
                    </div>
                    @endif
                    @if($issueReport->contact_phone)
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Telefon</p>
                        <a href="tel:{{ $issueReport->contact_phone }}" class="text-sm font-medium text-primary-600 hover:underline mt-1 block">
                            {{ $issueReport->contact_phone }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">İşlemler</h2>
                <div class="space-y-3">
                    @if($issueReport->status === 'ekip_atandi')
                    <form action="{{ route('employee.issue-reports.start-work', $issueReport) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-sm font-medium">
                            🚀 İşe Başla
                        </button>
                    </form>
                    @endif

                    @if($issueReport->status === 'calisma_basladi')
                    <form action="{{ route('employee.issue-reports.complete', $issueReport) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="flex items-center justify-center w-full px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition text-sm font-medium">
                            ✅ Tamamlandı İşaretle
                        </button>
                    </form>
                    @endif

                    @if($issueReport->status === 'tamamlandi')
                        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 text-center">
                            Bu arıza tamamlandı.
                        </div>
                    @endif
                </div>
            </div>

            @if($issueReport->maintenanceSchedule)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bağlı Bakım Planı</h2>
                <a href="{{ route('employee.maintenance.show', $issueReport->maintenanceSchedule) }}"
                   class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $issueReport->maintenanceSchedule->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $issueReport->maintenanceSchedule->scheduled_date }}</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
