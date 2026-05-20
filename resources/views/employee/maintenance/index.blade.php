@extends('employee.layouts.app')

@section('title', 'Bakım İşlerim')
@section('page-title', 'Bakım İşlerim')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Bakım İşlerim</h1>
            <p class="text-gray-600">Atandığım tüm bakım işleri ve durumları</p>
        </div>
    </div>

    <!-- Maintenance List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($maintenances as $maintenance)
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $maintenance->priority == 'acil' ? 'bg-red-100 text-red-600' :
                                       ($maintenance->priority == 'yuksek' ? 'bg-orange-100 text-orange-600' :
                                       ($maintenance->priority == 'normal' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600')) }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $maintenance->building->name }}</p>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $maintenance->priority == 'acil' ? 'bg-red-100 text-red-800' :
                                           ($maintenance->priority == 'yuksek' ? 'bg-orange-100 text-orange-800' :
                                           ($maintenance->priority == 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $maintenance->priority_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $maintenance->maintenance_type_label }}</p>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $maintenance->scheduled_date->setTimezone('Europe/Istanbul')->format('d.m.Y') }}
                                    @if($maintenance->scheduled_time)
                                        <span class="ml-1">{{ $maintenance->scheduled_time }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $maintenance->status == 'tamamlandi' ? 'bg-green-100 text-green-800' :
                                   ($maintenance->status == 'baslandi' ? 'bg-blue-100 text-blue-800' :
                                   ($maintenance->status == 'atandi' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $maintenance->status_label }}
                            </span>

                            <!-- Report Status -->
                            @if($maintenance->status == 'tamamlandi' && !$maintenance->maintenanceReport)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Rapor Bekliyor
                                </span>
                            @elseif($maintenance->maintenanceReport)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Rapor Tamamlandı
                                </span>
                            @endif

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('employee.maintenance.show', $maintenance) }}"
                                   class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                    Görüntüle
                                </a>

                                @if($maintenance->status == 'baslandi' && !$maintenance->maintenanceReport)
                                    <a href="{{ route('employee.maintenance.create-report', $maintenance) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        📝 Rapor Oluştur
                                    </a>
                                @endif

                                @if($maintenance->status == 'tamamlandi' && !$maintenance->maintenanceReport)
                                    <a href="{{ route('employee.maintenance.create-report', $maintenance) }}"
                                       class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                        ⚠️ Rapor Doldur
                                    </a>
                                @endif

                                @if($maintenance->maintenanceReport)
                                    <a href="{{ route('employee.maintenance.edit-report', $maintenance) }}"
                                       class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        ✏️ Raporu Düzenle
                                    </a>
                                @endif

                                <!-- Detaylı Bakım Formu -->
                                @if($maintenance->status == 'baslandi' || $maintenance->status == 'tamamlandi')
                                    @if(!$maintenance->detailedMaintenanceReport)
                                        <a href="{{ route('employee.maintenance.detailed-form', $maintenance) }}"
                                           class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                                            📋 Detaylı Form
                                        </a>
                                    @else
                                        <a href="{{ route('employee.maintenance.detailed-report', $maintenance) }}"
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            📊 Detaylı Rapor
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description Preview -->
                    @if($maintenance->description)
                        <div class="mt-3 ml-14">
                            <p class="text-sm text-gray-600">{{ Str::limit($maintenance->description, 100) }}</p>
                        </div>
                    @endif
                </li>
            @empty
                <li class="px-6 py-12">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Henüz iş atanmamış</h3>
                        <p class="mt-1 text-sm text-gray-500">Size atanmış bakım işi bulunmuyor.</p>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Pagination -->
    @if($maintenances->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $maintenances->links() }}
        </div>
    @endif
</div>
@endsection
