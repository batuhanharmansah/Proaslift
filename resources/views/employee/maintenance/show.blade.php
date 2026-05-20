@extends('employee.layouts.app')

@section('title', 'Bakım İşi Detayları')
@section('page-title', 'Bakım İşi Detayları')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('employee.maintenance.index') }}"
           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Job Details Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Bakım İşi Detayları</h3>
                        <div class="flex space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $maintenance->priority == 'acil' ? 'bg-red-100 text-red-800' :
                                   ($maintenance->priority == 'yuksek' ? 'bg-orange-100 text-orange-800' :
                                   ($maintenance->priority == 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $maintenance->priority_label }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $maintenance->status == 'tamamlandi' ? 'bg-green-100 text-green-800' :
                                   ($maintenance->status == 'baslandi' ? 'bg-blue-100 text-blue-800' :
                                   ($maintenance->status == 'atandi' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $maintenance->status_label }}
                            </span>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bina</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->building->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bakım Türü</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenance_type_label }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Planlanan Tarih</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $maintenance->scheduled_date->locale('tr')->isoFormat('D MMMM Y, dddd') }}
                                @if($maintenance->scheduled_time)
                                    <span class="text-gray-500">{{ $maintenance->scheduled_time }}</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tahmini Süre</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($maintenance->estimated_duration)
                                    {{ floor($maintenance->estimated_duration / 60) }} saat {{ $maintenance->estimated_duration % 60 }} dakika
                                @else
                                    Belirtilmemiş
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tahmini Maliyet</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($maintenance->estimated_cost)
                                    ₺{{ number_format($maintenance->estimated_cost, 2) }}
                                @else
                                    Belirtilmemiş
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Atanan Personel</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->assignedEmployee->full_name ?? 'Atanmamış' }}</dd>
                        </div>
                    </dl>

                    @if($maintenance->description)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Açıklama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->description }}</dd>
                        </div>
                    @endif

                    @if($maintenance->notes)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Notlar</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->notes }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Maintenance Report -->
            @if($maintenance->maintenanceReport)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Bakım Raporu</h3>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Başlangıç Saati</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenanceReport->start_time ? \Carbon\Carbon::parse($maintenance->maintenanceReport->start_time)->format('H:i') : '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bitiş Saati</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenanceReport->end_time ? \Carbon\Carbon::parse($maintenance->maintenanceReport->end_time)->format('H:i') : '-' }}</dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Yapılan İşlemler</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenanceReport->work_description }}</dd>
                            </div>

                            @if($maintenance->maintenanceReport->problems_found)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Bulunan Sorunlar</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenanceReport->problems_found }}</dd>
                                </div>
                            @endif

                            @if($maintenance->maintenanceReport->recommendations)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Öneriler</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenanceReport->recommendations }}</dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tamamlanma Durumu</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $maintenance->maintenanceReport->completion_status == 'tamamlandi' ? 'bg-green-100 text-green-800' :
                                           ($maintenance->maintenanceReport->completion_status == 'kismi_tamamlandi' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $maintenance->maintenanceReport->completion_status_label }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Toplam Maliyet</dt>
                                <dd class="mt-1 text-sm text-gray-900">₺{{ number_format($maintenance->maintenanceReport->total_cost, 2) }}</dd>
                            </div>

                            @if($maintenance->maintenanceReport->customer_name)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Müşteri Adı</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->maintenanceReport->customer_name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Müşteri İmzası</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $maintenance->maintenanceReport->customer_signature ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $maintenance->maintenanceReport->customer_signature ? 'Alındı' : 'Alınmadı' }}
                                        </span>
                                    </dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bina Yöneticisi Onayı</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($maintenance->maintenanceReport->approval_status ?? '') === 'onay_bekliyor' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $maintenance->maintenanceReport->approval_status_label ?? '—' }}
                                    </span>
                                    @if(($maintenance->maintenanceReport->approval_status ?? '') === 'onay_bekliyor')
                                        <p class="text-xs text-gray-500 mt-1">İş tamamlandı. Bina yöneticisi SMS ile onay verecek.</p>
                                    @elseif($maintenance->maintenanceReport->approved_by_name)
                                        <p class="text-xs text-gray-500 mt-1">{{ $maintenance->maintenanceReport->approved_by_name }} — {{ $maintenance->maintenanceReport->approved_at?->format('d.m.Y H:i') }}</p>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        <!-- Used Products -->
                        @if($maintenance->maintenanceReport->used_products && count($maintenance->maintenanceReport->used_products) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Kullanılan Ürünler</h4>
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Birim Fiyat</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($maintenance->maintenanceReport->used_products as $product)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product['product_name'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product['quantity'] }} {{ $product['unit'] ?? 'adet' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₺{{ number_format($product['unit_price'], 2) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₺{{ number_format($product['subtotal'] ?? ($product['quantity'] * $product['unit_price']), 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Edit Report Button -->
                        <div class="mt-6">
                            <a href="{{ route('employee.maintenance.edit-report', $maintenance) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Raporu Düzenle
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Building Details -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Bina Bilgileri</h3>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adres</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->building->address }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kat Sayısı</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->building->floor_count }} kat</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Asansör Sayısı</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->building->elevator_count }} adet</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Asansör Türü</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->building->elevator_type_label }}</dd>
                        </div>

                        @if($maintenance->building->elevator_brand)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Marka</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->building->elevator_brand }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Contact Information -->
            @if($maintenance->building->contacts && $maintenance->building->contacts->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">İletişim Bilgileri</h3>

                        @foreach($maintenance->building->contacts as $contact)
                            <div class="mb-4 last:mb-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $contact->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $contact->title }}</p>
                                    </div>
                                    @if($contact->is_primary)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ana İletişim
                                        </span>
                                    @endif
                                </div>
                                @if($contact->phone)
                                    <p class="text-sm text-gray-600 mt-1">📞 {{ $contact->phone }}</p>
                                @endif
                                @if($contact->email)
                                    <p class="text-sm text-gray-600">✉️ {{ $contact->email }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">İşlemler</h3>

                    <div class="space-y-3">
                        @if($maintenance->status == 'atandi' || $maintenance->status == 'planli')
                            <form method="POST" action="{{ route('employee.maintenance.start', $maintenance) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    🚀 İşe Başla
                                </button>
                            </form>
                        @endif

                        @if($maintenance->status == 'baslandi' && !$maintenance->maintenanceReport)
                            <a href="{{ route('employee.maintenance.create-report', $maintenance) }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                📝 Rapor Oluştur
                            </a>
                        @endif

                        @if($maintenance->status == 'tamamlandi' && !$maintenance->maintenanceReport)
                            <a href="{{ route('employee.maintenance.create-report', $maintenance) }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                ⚠️ Rapor Doldur
                            </a>
                        @endif

                        @if($maintenance->maintenanceReport)
                            <a href="{{ route('employee.maintenance.edit-report', $maintenance) }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                ✏️ Raporu Düzenle
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
