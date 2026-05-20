@extends('layouts.app')

@section('title', 'Arıza Bildirimi #{{ $issueReport->id }} - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Arıza Bildirimi #{{ $issueReport->id }}</h1>
            <p class="text-gray-600 mt-1">{{ $issueReport->building->name ?? '-' }}</p>
        </div>
        <a href="{{ route('issue-reports.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sol: Ana Bilgiler -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Arıza Detayları -->
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
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Bina</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $issueReport->building->name ?? '-' }}</p>
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
                @if($issueReport->requires_immediate_attention)
                    <div class="mt-2 bg-orange-50 border border-orange-200 rounded-lg px-4 py-2">
                        <span class="text-sm font-medium text-orange-700">⚡ Hemen müdahale gerekiyor</span>
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

            <!-- İletişim Bilgileri -->
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

            <!-- Bağlı Bakım Planı -->
            @if($issueReport->maintenanceSchedule)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bağlı Bakım Planı</h2>
                <a href="{{ route('maintenance.show', $issueReport->maintenanceSchedule) }}"
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

        <!-- Sağ: İşlemler -->
        <div class="space-y-6">

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">İşlemler</h2>
                <div class="space-y-3">

                    <a href="{{ route('issue-reports.edit', $issueReport) }}"
                       class="flex items-center w-full px-4 py-3 bg-yellow-50 text-yellow-700 rounded-xl hover:bg-yellow-100 transition text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Düzenle
                    </a>

                    @if(!$issueReport->maintenanceSchedule && in_array($issueReport->status, ['bildirildi', 'inceleniyor', 'ekip_atandi']))
                    <form action="{{ route('issue-reports.create-maintenance', $issueReport) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="flex items-center w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Bakım Planına Dönüştür
                        </button>
                    </form>
                    @endif

                    @if($issueReport->status !== 'tamamlandi' && $issueReport->status !== 'iptal_edildi')
                    <form action="{{ route('issue-reports.complete', $issueReport) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="flex items-center w-full px-4 py-3 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Tamamlandı İşaretle
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('issue-reports.destroy', $issueReport) }}" method="POST"
                          onsubmit="return confirm('Bu arıza bildirimini silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex items-center w-full px-4 py-3 bg-red-50 text-red-700 rounded-xl hover:bg-red-100 transition text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Sil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Personel Atama -->
            @if($issueReport->status !== 'tamamlandi')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Personel Atama</h2>

                @if($issueReport->assignedEmployee)
                <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                    <p class="text-xs text-gray-500">Atanan Personel</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">
                        {{ $issueReport->assignedEmployee->first_name }} {{ $issueReport->assignedEmployee->last_name }}
                    </p>
                    @if($issueReport->assigned_at)
                    <p class="text-xs text-gray-500 mt-1">{{ $issueReport->assigned_at->format('d.m.Y H:i') }}</p>
                    @endif
                </div>
                @endif

                <form action="{{ route('issue-reports.assign-employee', $issueReport) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <select name="employee_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Personel Seçin</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $issueReport->assigned_employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="datetime-local" name="estimated_completion_time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Tahmini tamamlanma">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition">
                            Personel Ata
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
