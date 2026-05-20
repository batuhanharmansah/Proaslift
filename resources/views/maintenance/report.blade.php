@extends('layouts.app')

@section('title', 'Bakım Raporu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">📋 Bakım Raporu</h1>
                <p class="text-blue-100 mt-2 text-lg">{{ $maintenance->building->name }} - {{ $maintenance->maintenanceTypeLabel }}</p>
                <div class="flex items-center mt-3 space-x-4">
                    <span class="px-3 py-1 bg-blue-500 rounded-full text-sm font-medium">
                        📅 {{ $maintenance->scheduled_date->format('d.m.Y') }}
                    </span>
                    <span class="px-3 py-1 bg-blue-500 rounded-full text-sm font-medium">
                        👤 {{ $maintenance->assignedEmployee->full_name ?? 'Atanmamış' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('maintenance.show', $maintenance) }}"
                   class="px-4 py-2 text-blue-800 bg-white rounded-lg hover:bg-blue-50 transition-colors font-medium">
                    ← Geri Dön
                </a>
                <a href="{{ route('maintenance.report.download', $maintenance) }}"
                   class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors font-medium">
                    📄 PDF İndir
                </a>
            </div>
        </div>
    </div>

    <!-- Rapor Detayları -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana Rapor -->
        <div class="lg:col-span-2 space-y-6">
            <!-- İş Bilgileri -->
            <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        🏢 İş Bilgileri
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 mb-1">🏢 Bina</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $maintenance->building->name }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 mb-1">🔧 Bakım Türü</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $maintenance->maintenanceTypeLabel }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 mb-1">📅 Planlanan Tarih</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $maintenance->scheduled_date->format('d.m.Y') }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 mb-1">👤 Atanan Personel</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $maintenance->assignedEmployee->full_name ?? 'Atanmamış' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rapor Detayları -->
            <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        📊 Rapor Detayları
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="text-sm font-medium text-green-600 mb-1">🕐 Başlangıç Zamanı</div>
                            <div class="text-lg font-semibold text-green-800">
                                {{ $maintenance->maintenanceReport->start_time ? \Carbon\Carbon::parse($maintenance->maintenanceReport->start_time)->format('d.m.Y H:i') : '-' }}
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="text-sm font-medium text-red-600 mb-1">🕐 Bitiş Zamanı</div>
                            <div class="text-lg font-semibold text-red-800">
                                {{ $maintenance->maintenanceReport->end_time ? \Carbon\Carbon::parse($maintenance->maintenanceReport->end_time)->format('d.m.Y H:i') : '-' }}
                            </div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-sm font-medium text-blue-600 mb-1">💰 Toplam Maliyet</div>
                            <div class="text-lg font-semibold text-blue-800">₺{{ number_format($maintenance->maintenanceReport->total_cost, 2) }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="text-sm font-medium text-purple-600 mb-1">✅ Tamamlanma Durumu</div>
                            <div class="text-lg font-semibold text-purple-800">
                                {{ $maintenance->maintenanceReport->completion_status_label }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="text-sm font-medium text-yellow-600 mb-2">🔧 Yapılan İşlemler</div>
                            <div class="text-gray-900">{{ $maintenance->maintenanceReport->work_description }}</div>
                        </div>

                        @if($maintenance->maintenanceReport->problems_found)
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <div class="text-sm font-medium text-red-600 mb-2">⚠️ Tespit Edilen Sorunlar</div>
                                <div class="text-gray-900">{{ $maintenance->maintenanceReport->problems_found }}</div>
                            </div>
                        @endif

                        @if($maintenance->maintenanceReport->recommendations)
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-600 mb-2">💡 Öneriler</div>
                                <div class="text-gray-900">{{ $maintenance->maintenanceReport->recommendations }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kullanılan Ürünler -->
            @if($maintenance->maintenanceReport->used_products && count($maintenance->maintenanceReport->used_products) > 0)
                <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            📦 Kullanılan Ürünler
                        </h3>
                    </div>
                    <div class="px-6 py-5">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Bilgileri</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanılan Miktar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Birim Fiyat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Tutar</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($maintenance->maintenanceReport->used_products as $product)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $product['product_name'] }}</div>
                                                    @if(isset($product['product_code']))
                                                        <div class="text-gray-500 text-xs">Marka: {{ $product['product_code'] }}</div>
                                                    @endif
                                                    @if(isset($product['product_category']))
                                                        <div class="text-gray-500 text-xs">Kategori: {{ $product['product_category'] == 'yedek_parca' ? 'Yedek Parça' :
                                                                                           ($product['product_category'] == 'arac_gerec' ? 'Araç Gereç' :
                                                                                           ($product['product_category'] == 'kimyasal' ? 'Kimyasal' :
                                                                                           ($product['product_category'] == 'elektronik' ? 'Elektronik' :
                                                                                           ($product['product_category'] == 'mekanik' ? 'Mekanik' : $product['product_category'])))) }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">{{ $product['quantity'] }}</div>
                                                <div class="text-gray-500 text-xs">{{ $product['unit'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">₺{{ number_format($product['unit_price'], 2) }}</div>
                                                <div class="text-gray-500 text-xs">birim başına</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-semibold text-green-600">₺{{ number_format($product['subtotal'], 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                            Toplam Ürün Maliyeti:
                                        </td>
                                        <td class="px-6 py-3 text-sm font-bold text-gray-900">
                                            ₺{{ number_format(collect($maintenance->maintenanceReport->used_products)->sum('subtotal'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Ürün Özeti -->
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-900 mb-3 flex items-center">📊 Ürün Kullanım Özeti</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="bg-white p-3 rounded-lg">
                                    <span class="text-blue-700 font-medium">📦 Toplam Ürün Çeşidi:</span>
                                    <span class="text-blue-900 ml-1 font-semibold">{{ count($maintenance->maintenanceReport->used_products) }} adet</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg">
                                    <span class="text-blue-700 font-medium">⚖️ Toplam Miktar:</span>
                                    <span class="text-blue-900 ml-1 font-semibold">{{ collect($maintenance->maintenanceReport->used_products)->sum('quantity') }} birim</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg">
                                    <span class="text-blue-700 font-medium">💰 Toplam Maliyet:</span>
                                    <span class="text-blue-900 ml-1 font-semibold">₺{{ number_format(collect($maintenance->maintenanceReport->used_products)->sum('subtotal'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Ürün Kullanılmadığında -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            📦 Kullanılan Ürünler
                        </h3>
                    </div>
                    <div class="px-6 py-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Ürün Kullanılmadı</h3>
                        <p class="text-gray-500">Bu bakım işinde herhangi bir ürün kullanılmamış.</p>
                    </div>
                </div>
            @endif

            <!-- Hatalı/Sorunlu Maddeler Özeti -->
            @if($maintenance->maintenanceReport->routine_maintenance_checklist)
                @php
                    $errorItems = [];
                    $checklist = $maintenance->maintenanceReport->routine_maintenance_checklist;
                    $sectionTitles = [
                        'machine_room' => '🏭 Makine Dairesi',
                        'floors' => '🏢 Katlar',
                        'cabin_interior_top' => '🚪 Kabin İç ve Üstü',
                        'shaft_interior' => '🕳️ Kuyu İçi'
                    ];

                    foreach($checklist as $sectionKey => $items) {
                        foreach($items as $item) {
                            if(isset($item['has_error']) && $item['has_error']) {
                                $errorItems[] = [
                                    'section' => $sectionTitles[$sectionKey] ?? $sectionKey,
                                    'title' => $item['title'],
                                    'notes' => $item['notes'] ?? ''
                                ];
                            }
                        }
                    }
                @endphp

                @if(count($errorItems) > 0)
                    <div class="bg-white shadow-lg rounded-xl border border-red-300">
                        <div class="px-6 py-5 border-b border-red-200 bg-red-50">
                            <h3 class="text-xl font-semibold text-red-800 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                ⚠️ Arızalı / Çalışmayan / Sorunlu Maddeler
                            </h3>
                            <p class="text-red-700 text-sm mt-1">Personel tarafından rapor oluşturulurken çarpı (Arızalı/Çalışmıyor) ile işaretlenen aksamlar.</p>
                        </div>
                        <div class="px-6 py-5">
                            <div class="bg-red-50 p-4 rounded-lg border-2 border-red-300 mb-4">
                                <p class="text-red-800 font-bold text-sm">
                                    Toplam <span class="text-xl">{{ count($errorItems) }}</span> adet arızalı veya çalışmayan madde tespit edilmiştir.
                                </p>
                            </div>

                            <div class="space-y-3">
                                @foreach($errorItems as $error)
                                    <div class="bg-white border-l-4 border-red-600 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                {{ $error['section'] }}
                                            </span>
                                            <span class="text-red-600 font-bold text-lg">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="text-gray-900 font-semibold mb-2">
                                            ❌ {{ $error['title'] }}
                                        </div>
                                        @if(!empty($error['notes']))
                                            <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                                <div class="text-xs font-medium text-red-600 mb-1">⚠️ Sorun Açıklaması:</div>
                                                <div class="text-sm text-red-900 font-medium">{{ $error['notes'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Bina Yöneticisi Onayı (SMS) -->
            @if($maintenance->maintenanceReport->approval_status)
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 mb-6">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-900">Bina Yöneticisi Onayı</h3>
                    </div>
                    <div class="px-6 py-5">
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $maintenance->maintenanceReport->approval_status === 'onay_bekliyor' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                            {{ $maintenance->maintenanceReport->approval_status_label }}
                        </span>
                        @if($maintenance->maintenanceReport->approved_by_name)
                            <p class="text-sm text-gray-600 mt-3">Onaylayan: {{ $maintenance->maintenanceReport->approved_by_name }}</p>
                            @if($maintenance->maintenanceReport->approved_at)
                                <p class="text-xs text-gray-500">{{ $maintenance->maintenanceReport->approved_at->format('d.m.Y H:i') }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <!-- Müşteri Onayı -->
            @if($maintenance->maintenanceReport->customer_name || $maintenance->maintenanceReport->customer_signature)
                <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            ✍️ Müşteri Onayı
                        </h3>
                    </div>
                    <div class="px-6 py-5">
                        @if($maintenance->maintenanceReport->customer_name)
                            <div class="mb-4 bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-600 mb-1">👤 Onaylayan Kişi</div>
                                <div class="text-lg font-semibold text-green-800">{{ $maintenance->maintenanceReport->customer_name }}</div>
                            </div>
                        @endif

                        <div class="mb-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-sm font-medium text-blue-600 mb-1">✍️ Müşteri İmzası</div>
                            <div class="text-lg font-semibold {{ $maintenance->maintenanceReport->customer_signature ? 'text-green-800' : 'text-red-800' }}">
                                {{ $maintenance->maintenanceReport->customer_signature ? '✅ Alındı' : '❌ Alınmadı' }}
                            </div>
                        </div>

                        @if($maintenance->maintenanceReport->customer_notes)
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <div class="text-sm font-medium text-yellow-600 mb-2">📝 Müşteri Notları</div>
                                <div class="text-gray-900">{{ $maintenance->maintenanceReport->customer_notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Rapor Bilgileri -->
            <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        📋 Rapor Bilgileri
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 mb-1">📅 Rapor Tarihi</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $maintenance->maintenanceReport->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 mb-1">👤 Rapor Eden</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $maintenance->maintenanceReport->employee->full_name ?? 'Bilinmiyor' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
