@extends('layouts.app')

@section('title', 'Bina Detayı - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $building->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $building->district }}, {{ $building->city }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('buildings.edit', $building) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Düzenle
            </a>
            <a href="{{ route('buildings.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Geri Dön
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana Bilgiler -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Bina Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bina Adı</label>
                        <p class="text-gray-900">{{ $building->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                        <p class="text-gray-900">{{ $building->address }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                        <p class="text-gray-900">{{ $building->district }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
                        <p class="text-gray-900">{{ $building->city }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kat Sayısı</label>
                        <p class="text-gray-900">{{ $building->floor_count }} kat</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asansör Sayısı</label>
                        <p class="text-gray-900">{{ $building->elevator_count }} adet</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asansör Tipi</label>
                        <p class="text-gray-900">{{ $building->elevator_type_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $building->status === 'aktif' ? 'bg-green-100 text-green-800' :
                               ($building->status === 'beklemede' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $building->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sözleşme Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Sözleşme Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sözleşme Tipi</label>
                        <p class="text-gray-900">{{ $building->contract_type_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aylık Ücret</label>
                        <p class="text-green-600 font-semibold">₺{{ number_format($building->monthly_fee, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                        <p class="text-gray-900">{{ $building->contract_start_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                        <p class="text-gray-900">{{ $building->contract_end_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Aylık Ödemeler -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">2025 Yılı Aylık Ödemeler</h2>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $financialSummary['months_paid'] }}/12</span> ay ödendi
                    </div>
                </div>

                <!-- Aylık ödeme grid'i -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
                    @foreach($monthlyData as $month => $data)
                        <div class="border rounded-lg p-3 text-center cursor-pointer transition-all hover:shadow-md
                            {{ $data['status'] === 'paid' ? 'bg-green-50 border-green-200 hover:bg-green-100' : 'bg-red-50 border-red-200 hover:bg-red-100' }}"
                            onclick="openPaymentModal({{ $building->id }}, {{ now()->year }}, {{ $month }}, '{{ $data['month_name'] }}', {{ $data['expected'] }}, {{ $data['has_receipt'] ? 'true' : 'false' }})">
                            <div class="text-sm font-medium text-gray-900 mb-1">{{ $data['month_name'] }}</div>
                            <div class="text-xs text-gray-600 mb-2">{{ $month }}/2025</div>
                            @if($data['status'] === 'paid')
                                <div class="text-green-600 font-semibold text-sm">₺{{ number_format($data['amount'], 2) }}</div>
                                <div class="text-xs text-green-500">✓ Ödendi</div>
                            @else
                                <div class="text-red-600 font-semibold text-sm">₺{{ number_format($data['expected'], 2) }}</div>
                                <div class="text-xs text-red-500">✗ Ödenmedi</div>
                            @endif
                            @if($data['has_receipt'])
                                <div class="text-xs text-blue-500 mt-1">📄 Dekont var</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Finansal özet -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-green-600">₺{{ number_format($financialSummary['total_yearly'], 2) }}</div>
                            <div class="text-xs text-gray-600">Toplam Tahsilat</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">₺{{ number_format($financialSummary['expected_yearly'], 2) }}</div>
                            <div class="text-xs text-gray-600">Yıllık Hedef</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($financialSummary['collection_rate'], 1) }}%</div>
                            <div class="text-xs text-gray-600">Tahsilat Oranı</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ $financialSummary['months_unpaid'] }}</div>
                            <div class="text-xs text-gray-600">Ödenmemiş Ay</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Son Bakım İşleri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Son Bakım İşleri</h2>
                @if($building->maintenanceSchedules->count() > 0)
                    <div class="space-y-3">
                        @foreach($building->maintenanceSchedules as $maintenance)
                            <a href="{{ route('maintenance.show', $maintenance) }}"
                               class="block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 hover:text-blue-600">{{ $maintenance->maintenance_type_label }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $maintenance->description }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <p class="text-xs text-gray-500">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $maintenance->scheduled_date->format('d.m.Y') }}
                                            </p>
                                            @if($maintenance->assignedEmployee)
                                                <p class="text-xs text-blue-600">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $maintenance->assignedEmployee->first_name }} {{ $maintenance->assignedEmployee->last_name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $maintenance->status === 'tamamlandi' ? 'bg-green-100 text-green-800' :
                                               ($maintenance->status === 'baslandi' ? 'bg-blue-100 text-blue-800' :
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ $maintenance->status_label }}
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Henüz bakım işi planlanmamış.</p>
                @endif
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="space-y-6">
            <!-- İletişim Bilgileri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">İletişim Bilgileri</h2>
                @if($building->contacts->count() > 0)
                    <div class="space-y-4">
                        @foreach($building->contacts as $contact)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $contact->name }}</h3>
                                    @if($contact->is_primary)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Ana İletişim</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-1">{{ $contact->title_label }}</p>
                                @if($contact->phone)
                                    <p class="text-sm text-gray-600 mb-1">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $contact->phone }}
                                    </p>
                                @endif
                                @if($contact->email)
                                    <p class="text-sm text-gray-600">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $contact->email }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">İletişim bilgisi bulunmuyor.</p>
                @endif
            </div>

            <!-- Asansör Etiket Durumu -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">🏷️ Etiket Durumu</h2>
                    @if($building->activeLabel)
                        <a href="{{ route('elevator-labels.show', $building->activeLabel) }}"
                           class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Detay →
                        </a>
                    @endif
                </div>

                @if($building->activeLabel)
                    @php
                        $label = $building->activeLabel;
                        $remainingDays = $label->getRemainingDays();
                    @endphp

                    <!-- Aktif Etiket -->
                    <div class="p-4 rounded-lg border-2 mb-4
                        @if($label->label_color === 'yesil') border-green-200 bg-green-50
                        @elseif($label->label_color === 'mavi') border-blue-200 bg-blue-50
                        @elseif($label->label_color === 'sari') border-yellow-200 bg-yellow-50
                        @else border-red-200 bg-red-50 @endif">

                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded-full
                                    @if($label->label_color === 'yesil') bg-green-500
                                    @elseif($label->label_color === 'mavi') bg-blue-500
                                    @elseif($label->label_color === 'sari') bg-yellow-500
                                    @else bg-red-500 @endif"></div>
                                <span class="font-semibold text-sm
                                    @if($label->label_color === 'yesil') text-green-800
                                    @elseif($label->label_color === 'mavi') text-blue-800
                                    @elseif($label->label_color === 'sari') text-yellow-800
                                    @else text-red-800 @endif">
                                    {{ $label->label_color_text }}
                                </span>
                            </div>
                            <span class="text-xs font-medium
                                @if($remainingDays < 0) text-red-600
                                @elseif($remainingDays <= 7) text-orange-600
                                @elseif($remainingDays <= 30) text-yellow-600
                                @else text-green-600 @endif">
                                @if($remainingDays < 0)
                                    {{ abs($remainingDays) }} gün geç
                                @else
                                    {{ $remainingDays }} gün kala
                                @endif
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kontrol Tarihi:</span>
                                <span class="font-medium">{{ $label->control_date->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Son Tarih:</span>
                                <span class="font-medium">{{ $label->due_date->format('d.m.Y') }}</span>
                            </div>
                            @if($label->inspector_name)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kontrol Eden:</span>
                                <span class="font-medium">{{ $label->inspector_name }}</span>
                            </div>
                            @endif
                        </div>

                        @if($label->canBeSealed())
                        <div class="mt-3 p-2 bg-red-100 border border-red-300 rounded-lg">
                            <div class="flex items-center space-x-1">
                                <span class="text-red-600 text-xs">🔒</span>
                                <span class="text-red-800 text-xs font-medium">Mühürleme Adayı</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Etiket Geçmişi -->
                    @if($building->elevatorLabels->count() > 1)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Etiket Geçmişi</h3>
                        <div class="space-y-2">
                            @foreach($building->elevatorLabels->skip(1) as $historicalLabel)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full
                                        @if($historicalLabel->label_color === 'yesil') bg-green-500
                                        @elseif($historicalLabel->label_color === 'mavi') bg-blue-500
                                        @elseif($historicalLabel->label_color === 'sari') bg-yellow-500
                                        @else bg-red-500 @endif"></div>
                                    <span class="text-xs text-gray-700">{{ $historicalLabel->label_color_text }}</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $historicalLabel->control_date->format('d.m.Y') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Etiket İşlemleri -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('elevator-labels.create', ['building_id' => $building->id]) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors flex items-center justify-center">
                            ➕ Yeni Etiket Ekle
                        </a>
                    </div>

                @else
                    <!-- Etiket Yok -->
                    <div class="text-center py-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm mb-4">Henüz etiket kaydı bulunmuyor</p>
                        <a href="{{ route('elevator-labels.create', ['building_id' => $building->id]) }}"
                           class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            İlk Etiketi Oluştur
                        </a>
                    </div>
                @endif
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
                <div class="space-y-3">
                    <a href="{{ route('maintenance.create') }}?building_id={{ $building->id }}"
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Bakım Planla
                    </a>
                    <a href="#"
                       class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Rapor Oluştur
                    </a>
                </div>
            </div>

            <!-- Belge Yönetimi -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Belge Yönetimi</h2>
                    <button onclick="openDocumentModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors">
                        + Belge Ekle
                    </button>
                </div>

                @if($building->documents->count() > 0)
                    <div class="space-y-3">
                        @foreach($building->documents as $document)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-gray-900 text-sm">{{ $document->title }}</h3>
                                            <p class="text-xs text-gray-500">{{ $document->document_type_label }} • {{ $document->formatted_file_size }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('building.documents.download', $document) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        <button onclick="deleteDocument({{ $document->id }})"
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Henüz belge yüklenmemiş.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Belge Ekleme Modal -->
<div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Belge Ekle</h3>
                <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="documentForm" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="building_id" value="{{ $building->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Belge Başlığı</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" placeholder="Sözleşme belgesi">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Belge Türü</label>
                        <select name="document_type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="sozlesme">Sözleşme</option>
                            <option value="fatura">Fatura</option>
                            <option value="bakim_raporu">Bakım Raporu</option>
                            <option value="ariza_raporu">Arıza Raporu</option>
                            <option value="teknik_cizim">Teknik Çizim</option>
                            <option value="sertifika">Sertifika</option>
                            <option value="izin">İzin</option>
                            <option value="diger">Diğer</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50" rows="3" placeholder="Belge açıklaması"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dosya Seç</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Dosya yükle</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" required>
                                </label>
                                <p class="pl-1">veya sürükle bırak</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, DOC, XLS, JPG, PNG (Max: 10MB)</p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeDocumentModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors font-medium">
                        Yükle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDocumentModal() {
    document.getElementById('documentModal').classList.remove('hidden');
}

function closeDocumentModal() {
    document.getElementById('documentModal').classList.add('hidden');
    document.getElementById('documentForm').reset();
}

function deleteDocument(documentId) {
    if (confirm('Bu belgeyi silmek istediğinizden emin misiniz?')) {
        fetch(`/building-documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Belge silinirken hata oluştu!');
            }
        })
        .catch(error => {
            alert('Bir hata oluştu!');
        });
    }
}

// Belge formu submit
document.getElementById('documentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/building-documents', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Belge başarıyla yüklendi!');
            location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        alert('Belge yüklenirken hata oluştu!');
    });
});

// Aylık ödeme modal fonksiyonları
function openPaymentModal(buildingId, year, month, monthName, expectedAmount, hasReceipt) {
    document.getElementById('paymentModalTitle').textContent = `${monthName} ${year} - Ödeme Dekontu`;
    document.getElementById('paymentBuildingId').value = buildingId;
    document.getElementById('paymentYear').value = year;
    document.getElementById('paymentMonth').value = month;
    document.getElementById('paymentAmount').value = expectedAmount;

    // Modal içeriğini güncelle
    updatePaymentModalContent(buildingId, year, month, monthName, hasReceipt);

    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('receiptFile').value = '';
    document.getElementById('receiptsList').innerHTML = '';
}

function updatePaymentModalContent(buildingId, year, month, monthName, hasReceipt) {
    const uploadSection = document.getElementById('uploadSection');
    const viewSection = document.getElementById('viewSection');

    if (hasReceipt) {
        // Dekont var - görüntüleme seçeneği göster
        uploadSection.classList.add('hidden');
        viewSection.classList.remove('hidden');
        loadExistingReceipts(buildingId, year, month);
    } else {
        // Dekont yok - yükleme seçeneği göster
        uploadSection.classList.remove('hidden');
        viewSection.classList.add('hidden');
    }
}

function loadExistingReceipts(buildingId, year, month) {
    fetch(`/building-documents/${buildingId}/payment-status/${year}/${month}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.receipts.length > 0) {
                let receiptsHtml = '';
                data.receipts.forEach(receipt => {
                    receiptsHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2">
                            <div>
                                <div class="text-sm font-medium text-gray-900">${receipt.file_name}</div>
                                <div class="text-xs text-gray-500">
                                    Yükleme: ${new Date(receipt.created_at).toLocaleDateString('tr-TR')}
                                    ${receipt.payment_amount ? ' - ₺' + parseFloat(receipt.payment_amount).toLocaleString('tr-TR') : ''}
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="/building-documents/${receipt.id}/download"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    📄 Görüntüle
                                </a>
                                <button onclick="deleteReceipt(${receipt.id})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    🗑️ Sil
                                </button>
                            </div>
                        </div>
                    `;
                });
                document.getElementById('receiptsList').innerHTML = receiptsHtml;
            }
        })
        .catch(error => {
            console.error('Dekontlar yüklenirken hata:', error);
        });
}

function uploadReceipt() {
    const formData = new FormData();
    const fileInput = document.getElementById('receiptFile');
    const file = fileInput.files[0];

    if (!file) {
        alert('Lütfen bir dosya seçiniz.');
        return;
    }

    formData.append('building_id', document.getElementById('paymentBuildingId').value);
    formData.append('payment_year', document.getElementById('paymentYear').value);
    formData.append('payment_month', document.getElementById('paymentMonth').value);
    formData.append('payment_amount', document.getElementById('paymentAmount').value);
    formData.append('file', file);
    formData.append('description', document.getElementById('receiptDescription').value);

    fetch('/building-documents/payment-receipt', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Dekont başarıyla yüklendi!');
            closePaymentModal();
            location.reload(); // Sayfayı yenile
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        alert('Dekont yüklenirken hata oluştu!');
        console.error('Error:', error);
    });
}

function markAsPaid() {
    const buildingId = document.getElementById('paymentBuildingId').value;
    const year = document.getElementById('paymentYear').value;
    const month = document.getElementById('paymentMonth').value;
    const amount = document.getElementById('paymentAmount').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    console.log('CSRF Token:', csrfToken);
    console.log('Building ID:', buildingId);
    console.log('Year:', year);
    console.log('Month:', month);
    console.log('Amount:', amount);

    if (!confirm('Bu ayın ödemesini ödendi olarak işaretlemek istediğinizden emin misiniz?')) {
        return;
    }

    fetch('/building-documents/mark-as-paid', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            building_id: buildingId,
            payment_year: year,
            payment_month: month,
            payment_amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ödeme başarıyla ödendi olarak işaretlendi!');
            closePaymentModal();
            location.reload(); // Sayfayı yenile
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        alert('Ödeme işaretlenirken hata oluştu!');
        console.error('Error:', error);
    });
}

function deleteReceipt(documentId) {
    if (confirm('Bu dekontu silmek istediğinizden emin misiniz?')) {
        fetch(`/building-documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dekont başarıyla silindi!');
                closePaymentModal();
                location.reload();
            } else {
                alert('Dekont silinirken hata oluştu!');
            }
        })
        .catch(error => {
            alert('Bir hata oluştu!');
        });
    }
}

function showUploadForm() {
    document.getElementById('uploadSection').classList.remove('hidden');
    document.getElementById('viewSection').classList.add('hidden');
}
</script>

<!-- Aylık Ödeme Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="paymentModalTitle">Aylık Ödeme Dekontu</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Dekont Görüntüleme Bölümü -->
            <div id="viewSection" class="hidden">
                <div class="mb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Mevcut Dekontlar</h4>
                    <div id="receiptsList"></div>
                </div>
                <div class="flex space-x-3">
                    <button onclick="showUploadForm()"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                        ➕ Yeni Dekont Ekle
                    </button>
                    <button onclick="markAsPaid()"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                        ✅ Ödendi Olarak İşaretle
                    </button>
                </div>
                <div class="mt-2">
                    <button onclick="closePaymentModal()"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                        Kapat
                    </button>
                </div>
            </div>

            <!-- Dekont Yükleme Bölümü -->
            <div id="uploadSection">
                <div class="mb-4">
                    <label for="receiptFile" class="block text-sm font-medium text-gray-700 mb-2">
                        Dekont Dosyası *
                    </label>
                    <input type="file" id="receiptFile" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG formatları desteklenir (Max: 10MB)</p>
                </div>

                <div class="mb-4">
                    <label for="paymentAmount" class="block text-sm font-medium text-gray-700 mb-2">
                        Ödeme Tutarı *
                    </label>
                    <input type="number" id="paymentAmount" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="receiptDescription" class="block text-sm font-medium text-gray-700 mb-2">
                        Açıklama
                    </label>
                    <textarea id="receiptDescription" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Ödeme ile ilgili notlar..."></textarea>
                </div>

                <div class="flex space-x-3">
                    <button onclick="uploadReceipt()"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                        📤 Dekont Yükle
                    </button>
                    <button onclick="closePaymentModal()"
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                        İptal
                    </button>
                </div>
            </div>

            <!-- Hidden inputs -->
            <input type="hidden" id="paymentBuildingId">
            <input type="hidden" id="paymentYear">
            <input type="hidden" id="paymentMonth">
        </div>
    </div>
</div>

@endsection
