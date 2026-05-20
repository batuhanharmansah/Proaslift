@extends('employee.layouts.app')

@section('title', 'Rutin Bakım Raporu Oluştur')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🔧 Rutin Bakım Raporu Oluştur</h1>
                <p class="text-gray-600 mt-1">{{ $maintenance->building->name }} - {{ $maintenance->maintenanceTypeLabel }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                    <span class="text-sm font-medium">Tamamlanma: <span id="completion-percentage">0%</span></span>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full
                    @if($maintenance->priority === 'acil') bg-red-100 text-red-800
                    @elseif($maintenance->priority === 'yuksek') bg-orange-100 text-orange-800
                    @elseif($maintenance->priority === 'normal') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $maintenance->priorityLabel }}
                </span>
                <a href="{{ route('employee.maintenance.show', $maintenance) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    ← Geri Dön
                </a>
            </div>
        </div>
        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Rapor Formu -->
    <form id="routine-report-form" action="{{ route('employee.maintenance.store-report', $maintenance) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Rutin Bakım Kontrol Listesi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-8">
                <h2 class="text-xl font-bold text-green-600">Bakımda Kontrol Edilen Aksamlar</h2>
                <p class="text-gray-600 mt-2">Lütfen her kontrolü gerçekleştirdikten sonra işaretleyin</p>
            </div>

            <!-- Makine Dairesi Kontrolü -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-3">⚙️</span>
                        Makine Dairesi Kontrolü
                    </h3>
                    <span class="section-progress px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">0/14</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-section="machine_room">
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][carrying_ropes]" id="carrying_ropes">
                        <label for="carrying_ropes" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Taşıyıcı halatların boy ve tel erimesi kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][regulator_ropes]" id="regulator_ropes">
                        <label for="regulator_ropes" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Regülatör halatları boy ve tel erimesi kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_switch_level]" id="machine_switch_level">
                        <label for="machine_switch_level" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina şalter ve seviye kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_brake_pad]" id="machine_brake_pad">
                        <label for="machine_brake_pad" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina fren balata kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_bearing_bushing]" id="machine_bearing_bushing">
                        <label for="machine_bearing_bushing" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina yatak ve rulman kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][motor_coupling_adjustment]" id="motor_coupling_adjustment">
                        <label for="motor_coupling_adjustment" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Motor kaplin, şase, saplama ve kasnak ayarının kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_drive_oil_levels]" id="machine_drive_oil_levels">
                        <label for="machine_drive_oil_levels" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina tahrik grubu yağ seviyeleri
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_brake_coil]" id="machine_brake_coil">
                        <label for="machine_brake_coil" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina fren bobini kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_brake_pad_2]" id="machine_brake_pad_2">
                        <label for="machine_brake_pad_2" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina fren balata kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_panel_fuse_contactor]" id="machine_panel_fuse_contactor">
                        <label for="machine_panel_fuse_contactor" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina panosu sigorta ve kontaktör kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][control_panel_fuse_contactor]" id="control_panel_fuse_contactor">
                        <label for="control_panel_fuse_contactor" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kumanda panosu sigorta ve kontaktör kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][electrical_panel_30ma]" id="electrical_panel_30ma">
                        <label for="electrical_panel_30ma" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Elektrik panosu 30mA kaçak akım rölesi kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_room_grounding]" id="machine_room_grounding">
                        <label for="machine_room_grounding" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina dairesi topraklama ölçümü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[machine_room][machine_room_cleaning]" id="machine_room_cleaning">
                        <label for="machine_room_cleaning" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Makina dairesi temizliği
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Katlar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-3">🏢</span>
                        Katlar
                    </h3>
                    <span class="section-progress px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">0/7</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-section="floors">
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][floor_door_lock_safety]" id="floor_door_lock_safety">
                        <label for="floor_door_lock_safety" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kat kapı kilidi emniyet devreleri (130-140) kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][door_automatic_device]" id="door_automatic_device">
                        <label for="door_automatic_device" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kapı otomatik cihazı görsel kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][door_spring_pulley]" id="door_spring_pulley">
                        <label for="door_spring_pulley" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kapı yaylı makara kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][door_shock_absorber]" id="door_shock_absorber">
                        <label for="door_shock_absorber" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kapı amortisör ayarlaması kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][door_spring_rope_wheel]" id="door_spring_rope_wheel">
                        <label for="door_spring_rope_wheel" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kapı yaylı-halat-tekeri kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][door_locks_cleaning]" id="door_locks_cleaning">
                        <label for="door_locks_cleaning" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kapı kilitleri temizliği
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[floors][door_hinges]" id="door_hinges">
                        <label for="door_hinges" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kapı menteşeleri kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kabini İç ve Kabin Üstü Kontrolü -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-3">🚪</span>
                        Kabini İç ve Kabin Üstü Kontrolü
                    </h3>
                    <span class="section-progress px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">0/15</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-section="cabin_interior_top">
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][safety_circuits_120]" id="safety_circuits_120">
                        <label for="safety_circuits_120" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Emniyet devreleri (120) kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][cabin_door_lock_safety_140]" id="cabin_door_lock_safety_140">
                        <label for="cabin_door_lock_safety_140" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kabin kapı kilidi emniyet devreleri (140) kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][floor_selector_clamp]" id="floor_selector_clamp">
                        <label for="floor_selector_clamp" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kat seçici klemens, somun, kopyala kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][regulator_rope_connection]" id="regulator_rope_connection">
                        <label for="regulator_rope_connection" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Regülatör halat bağlantısı kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][revision_movement]" id="revision_movement">
                        <label for="revision_movement" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Revizyon hareket kontrolü
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][level_switch]" id="level_switch">
                        <label for="level_switch" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Seviye şalteri kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][limit_switch_817_818]" id="limit_switch_817_818">
                        <label for="limit_switch_817_818" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            817-818 sınır kesici kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][emergency_lighting_alarm]" id="emergency_lighting_alarm">
                        <label for="emergency_lighting_alarm" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Acil aydınlatma, alarm, diafon kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][floor_selector_cabin_cancel]" id="floor_selector_cabin_cancel">
                        <label for="floor_selector_cabin_cancel" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kat seçici kabini iptal acil stop butonu kontrol (120)
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][level_alignments]" id="level_alignments">
                        <label for="level_alignments" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Seviye hizaları kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][tables_warning_signs]" id="tables_warning_signs">
                        <label for="tables_warning_signs" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Tablolar ve uyarı levhaları kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][floor_buttons]" id="floor_buttons">
                        <label for="floor_buttons" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kat butonları kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][elevator_interior_lighting]" id="elevator_interior_lighting">
                        <label for="elevator_interior_lighting" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Asansör içi aydınlatma kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][cabin_top_mechanical_brake]" id="cabin_top_mechanical_brake">
                        <label for="cabin_top_mechanical_brake" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kabin üstü mekanik fren çakılar kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[cabin_interior_top][overload_control]" id="overload_control">
                        <label for="overload_control" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Aşırı yük kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kuyu İçi -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-3">🕳️</span>
                        Kuyu İçi
                    </h3>
                    <span class="section-progress px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">0/8</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-section="shaft_interior">
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][counterweight_rope_chassis]" id="counterweight_rope_chassis">
                        <label for="counterweight_rope_chassis" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Karşı ağırlık halat şasesi, somun, kopyala kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][counterweight_guide_bolt]" id="counterweight_guide_bolt">
                        <label for="counterweight_guide_bolt" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Karşı ağırlık paten ve civata kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][main_rail_weight_rail_oiling]" id="main_rail_weight_rail_oiling">
                        <label for="main_rail_weight_rail_oiling" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Anaray ve ağırlık rayı yağlanması kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][shaft_bottom_regulator_pulley]" id="shaft_bottom_regulator_pulley">
                        <label for="shaft_bottom_regulator_pulley" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kuyu dibi regülatör kasnağı kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][shaft_bottom_regulator_cleaning]" id="shaft_bottom_regulator_cleaning">
                        <label for="shaft_bottom_regulator_cleaning" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kuyu dibi regülatör kasnağı temizliği
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][shaft_bottom_buffer]" id="shaft_bottom_buffer">
                        <label for="shaft_bottom_buffer" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kuyu dibi tampon kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][shaft_bottom_safety_circuits]" id="shaft_bottom_safety_circuits">
                        <label for="shaft_bottom_safety_circuits" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kuyu dibi emniyet devreleri (120) kontrol
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="checklist-item flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500"
                               name="checklist[shaft_interior][shaft_bottom_cleaning]" id="shaft_bottom_cleaning">
                        <label for="shaft_bottom_cleaning" class="flex-1 text-sm text-gray-700 cursor-pointer">
                            Kuyu dibi temizliği
                        </label>
                        <button type="button" class="error-btn text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full p-1 focus:outline-none transition-colors ml-auto flex-shrink-0" onclick="toggleError(this)" title="Hatalı/Arızalı İşaretle">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Çalışma Saatleri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Çalışma Saatleri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        İşe Başlama Zamanı *
                    </label>
                    <input type="datetime-local" id="start_time" name="start_time" required
                           value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        İş Bitiş Zamanı
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time"
                           value="{{ old('end_time') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Genel Notlar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Genel Notlar ve Öneriler</h3>
            <textarea name="recommendations" rows="4" placeholder="Genel notlar, öneriler ve tespit edilen durumlar..."
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('recommendations') }}</textarea>
        </div>

        <!-- Tamamlanma Durumu -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tamamlanma Durumu</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="completion_status" value="tamamlandi" checked
                           class="h-4 w-4 text-green-600 focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">Tamamlandı</span>
                </label>
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="completion_status" value="kismi_tamamlandi"
                           class="h-4 w-4 text-green-600 focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">Kısmi Tamamlandı</span>
                </label>
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="completion_status" value="ertelendi"
                           class="h-4 w-4 text-green-600 focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">Ertelendi</span>
                </label>
            </div>
        </div>

        <!-- Müşteri Bilgileri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Müşteri Bilgileri</h3>
            <div class="space-y-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Müşteri Adı
                    </label>
                    <input type="text" id="customer_name" name="customer_name"
                           value="{{ old('customer_name') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Müşteri Notları
                    </label>
                    <textarea id="customer_notes" name="customer_notes" rows="3"
                              placeholder="Müşteri ile ilgili notlar..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('customer_notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tüm kontrolleri tamamladıktan sonra raporu kaydedin.</p>
                </div>
                <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    📤 Rutin Bakım Raporunu Kaydet
                </button>
            </div>
        </div>

        <!-- Hidden field for checklist data -->
        <input type="hidden" name="routine_maintenance_checklist" id="checklist-data">
        <input type="hidden" name="completion_percentage" id="completion-percentage-field">
    </form>
</div>

<style>
.checklist-item input[type="checkbox"]:checked + label {
    text-decoration: line-through;
    color: #6b7280;
}

.checklist-item input[type="checkbox"]:checked {
    background-color: #059669;
    border-color: #059669;
}

.note-container {
    margin-top: 0.75rem;
    padding-left: 2rem;
    padding-right: 3rem;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.item-error {
    border-width: 2px !important;
}

.error-btn {
    flex-shrink: 0;
    font-weight: bold;
    z-index: 10;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: rgba(254, 226, 226, 0.5);
    border: 2px solid #DC2626;
}

.error-btn:hover {
    background-color: #FEE2E2 !important;
    border-color: #991B1B !important;
}

.error-btn svg {
    filter: drop-shadow(0 1px 2px rgba(220, 38, 38, 0.5));
    stroke-width: 1.5px;
}

.checklist-item input[type="checkbox"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Rutin bakım formu yüklendi');

    const errorButtons = document.querySelectorAll('.error-btn');
    console.log('❌ Kırmızı çarpı sayısı:', errorButtons.length);

    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const progressBar = document.getElementById('progress-bar');
    const completionPercentage = document.getElementById('completion-percentage');
    const completionPercentageField = document.getElementById('completion-percentage-field');
    const checklistDataField = document.getElementById('checklist-data');

    const sectionCounts = {
        'machine_room': 14,
        'floors': 7,
        'cabin_interior_top': 15,
        'shaft_interior': 8
    };

    // Error status için hidden input oluştur (açıklama alanı kaldırıldı)
    checkboxes.forEach(checkbox => {
        const checklistItem = checkbox.closest('.checklist-item');
        const checkboxName = checkbox.name;

        const errorInput = document.createElement('input');
        errorInput.type = 'hidden';
        errorInput.name = `errors${checkboxName.replace('checklist', '')}`;
        errorInput.value = '0';
        errorInput.className = 'error-status';
        checklistItem.appendChild(errorInput);
    });

    // toggleError fonksiyonu - çarpı butonları için (açıklama alanı yok, sadece işaretleme)
    window.toggleError = function(errorBtn) {
        const checklistItem = errorBtn.closest('.checklist-item');
        const checkbox = checklistItem.querySelector('input[type="checkbox"]');
        const errorInput = checklistItem.querySelector('.error-status');

        const isError = checklistItem.classList.contains('item-error');

        if (isError) {
            checklistItem.classList.remove('item-error', 'bg-red-50', 'border-red-300');
            checklistItem.classList.add('hover:bg-gray-50');
            errorInput.value = '0';
            checkbox.disabled = false;
            errorBtn.classList.remove('bg-red-100');
        } else {
            checklistItem.classList.add('item-error', 'bg-red-50', 'border-red-300');
            checklistItem.classList.remove('hover:bg-gray-50');
            errorInput.value = '1';
            checkbox.checked = false;
            checkbox.disabled = true;
            errorBtn.classList.add('bg-red-100');
        }

        updateProgress();
        updateChecklistData();
    };

    // Checkbox change event (hata modundaysa işaretlemeye izin verme)
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checklistItem = this.closest('.checklist-item');
            if (checklistItem.classList.contains('item-error')) {
                this.checked = false;
                return;
            }
        });
    });

    function updateProgress() {
        const totalItems = checkboxes.length;
        const checkedItems = document.querySelectorAll('input[type="checkbox"]:checked').length;
        const percentage = Math.round((checkedItems / totalItems) * 100);

        progressBar.style.width = percentage + '%';
        completionPercentage.textContent = percentage + '%';
        completionPercentageField.value = percentage;

        // Update section progress
        Object.keys(sectionCounts).forEach(sectionId => {
            const section = document.querySelector(`[data-section="${sectionId}"]`);
            const sectionCheckboxes = section.querySelectorAll('input[type="checkbox"]');
            const sectionChecked = section.querySelectorAll('input[type="checkbox"]:checked').length;
            const sectionProgress = section.parentElement.querySelector('.section-progress');
            sectionProgress.textContent = `${sectionChecked}/${sectionCounts[sectionId]}`;
        });
    }

    function updateChecklistData() {
        const checklistData = {
            machine_room: [],
            floors: [],
            cabin_interior_top: [],
            shaft_interior: []
        };

        checkboxes.forEach(checkbox => {
            const section = checkbox.name.match(/checklist\[(\w+)\]/)[1];
            const itemId = checkbox.name.match(/\[(\w+)\]$/)[1];
            const label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim() : '';

            // Açıklama alanını ve hata durumunu bul
            const checklistItem = checkbox.closest('.checklist-item');
            const noteContainer = checklistItem.querySelector('.note-container');
            const noteTextarea = noteContainer ? noteContainer.querySelector('textarea') : null;
            const noteValue = noteTextarea ? noteTextarea.value : '';
            const errorInput = checklistItem.querySelector('.error-status');
            const hasError = errorInput ? errorInput.value === '1' : false;

            checklistData[section].push({
                id: itemId,
                title: label,
                checked: checkbox.checked,
                has_error: hasError,
                notes: noteValue
            });
        });

        checklistDataField.value = JSON.stringify(checklistData);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateProgress();
            updateChecklistData();
        });
    });

    // Form gönderilmeden hemen önce checklist verisini güncelle
    document.getElementById('routine-report-form').addEventListener('submit', function() {
        updateChecklistData();
    });

    // Initialize
    updateProgress();
    updateChecklistData();
});
</script>
@endsection
