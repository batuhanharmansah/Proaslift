@extends('layouts.app')

@section('title', 'Konum Takibi - Harmanşah Yazılım')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #map {
        height: calc(100vh - 200px);
        min-height: 600px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
    .location-info-card {
        background: white;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        min-width: 250px;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-on-time { background: #dcfce7; color: #166534; }
    .status-late { background: #fee2e2; color: #991b1b; }
    .status-early { background: #fef3c7; color: #92400e; }
    .status-pending { background: #e5e7eb; color: #374151; }
</style>
@endpush

@section('content')
<div class="p-6" x-data="locationMapData()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📍 Konum Takibi</h1>
                <p class="text-gray-600 mt-1">Sahadaki çalışanların konumlarını ve binaları takip edin</p>
            </div>
            <div class="flex space-x-3 items-center">
                <!-- Date Selector -->
                <div class="flex items-center space-x-2">
                    <label for="schedule-date" class="text-sm font-medium text-gray-700">📅 Tarih:</label>
                    <input type="date"
                           id="schedule-date"
                           x-model="selectedDate"
                           :value="selectedDate"
                           @change="loadMapData()"
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button @click="refreshData()"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    🔄 Yenile
                </button>
                <button @click="autoRefreshEnabled = !autoRefreshEnabled; handleAutoRefreshChange();"
                        :class="autoRefreshEnabled ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600'"
                        class="text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <span x-show="autoRefreshEnabled">🟢 Otomatik</span>
                    <span x-show="!autoRefreshEnabled">⚪ Manuel</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Toplam Bina</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.totalBuildings">0</p>
                    <p class="text-xs text-gray-500 mt-1" x-show="stats.buildingsWithoutCoordinates > 0">
                        <span x-text="stats.buildingsWithoutCoordinates" class="text-red-600 font-semibold"></span> koordinatsız
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Sahada Olan Çalışanlar</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.activeEmployees">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Zamanında Varış</p>
                    <p class="text-2xl font-bold text-green-600" x-text="stats.onTimeArrivals">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Geç Varışlar</p>
                    <p class="text-2xl font-bold text-red-600" x-text="stats.lateArrivals">0</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Seçilen Tarihin İşleri</p>
                    <p class="text-2xl font-bold text-purple-600" x-text="stats.todaySchedules">0</p>
                    <p class="text-xs text-gray-500 mt-1" x-show="stats.todaySchedules > 0">
                        <span x-text="stats.completedSchedules" class="text-green-600 font-semibold"></span> tamamlandı
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Panel - Aktif Kullanıcılar -->
    <div class="mb-6 bg-gray-900 rounded-xl shadow-lg border border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <span class="mr-2">🔍</span>
                Debug Paneli - Aktif Kullanıcılar
            </h2>
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 rounded-full"
                         :class="autoRefreshEnabled ? 'bg-green-500 animate-pulse' : 'bg-gray-500'"
                         x-show="true"></div>
                    <span class="text-sm text-gray-300" x-text="autoRefreshEnabled ? 'Canlı Takip Aktif' : 'Manuel Mod'"></span>
                </div>
                <div class="text-xs text-gray-400" x-show="lastUpdateTime">
                    Son Güncelleme: <span x-text="lastUpdateTime"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-300">Aktif Kullanıcı Sayısı</span>
                    <span class="text-2xl font-bold text-green-400" x-text="employees.length">0</span>
                </div>
                <div class="text-xs text-gray-400">
                    Son 24 saat içinde konum verisi gönderen çalışanlar
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-300">Son Güncelleme</span>
                    <span class="text-sm font-mono text-blue-400" x-text="lastUpdateTime || 'Henüz yüklenmedi'">-</span>
                </div>
                <div class="text-xs text-gray-400">
                    <span x-show="autoRefreshEnabled">Her 10 saniyede bir otomatik güncelleniyor</span>
                    <span x-show="!autoRefreshEnabled">Manuel yenileme modu</span>
                </div>
            </div>
        </div>

        <!-- Aktif Kullanıcı Listesi -->
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="employees.length > 0">
            <h3 class="text-sm font-semibold text-gray-300 mb-3">📋 Aktif Kullanıcı Listesi</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <template x-for="(employee, index) in employees" :key="employee.id">
                    <div class="bg-gray-700 rounded-lg p-3 border border-gray-600 hover:bg-gray-650 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm font-bold text-white" x-text="employee.name"></span>
                                    <span class="text-xs px-2 py-0.5 bg-green-500/20 text-green-400 rounded"
                                          x-text="employee.position"></span>
                                    <span class="text-xs px-2 py-0.5 rounded"
                                          :class="employee.is_online !== false ? 'bg-green-500/30 text-green-300' : 'bg-gray-500/30 text-gray-400'"
                                          x-text="employee.is_online !== false ? '🟢 Çevrimiçi' : '⚫ Çevrimdışı'"></span>
                                </div>
                                <div class="text-xs text-gray-400 space-y-1">
                                    <div x-show="employee.coordinates">
                                        <span class="font-mono">📍</span>
                                        <span x-text="employee.coordinates.lat.toFixed(6)"></span>,
                                        <span x-text="employee.coordinates.lng.toFixed(6)"></span>
                                    </div>
                                    <div x-show="employee.last_update">
                                        <span class="font-mono">🕐</span>
                                        Son Güncelleme: <span x-text="employee.last_update"></span>
                                        <span x-show="employee.minutes_since_update !== null && employee.minutes_since_update !== undefined"
                                              class="text-gray-500"
                                              x-text="' (' + employee.minutes_since_update + ' dk önce)'"></span>
                                    </div>
                                    <div x-show="employee.active_maintenance">
                                        <span class="font-mono">📋</span>
                                        Aktif İş: <span x-text="employee.active_maintenance.building_name"></span>
                                    </div>
                                    <div x-show="employee.phone">
                                        <span class="font-mono">📞</span>
                                        <span x-text="employee.phone"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex flex-col items-end">
                                <div class="w-3 h-3 rounded-full mb-2"
                                     :class="employee.is_online !== false ? 'bg-green-500 animate-pulse' : 'bg-gray-500'"
                                     x-show="employee.coordinates"></div>
                                <span class="text-xs text-gray-500" x-text="'#' + (index + 1)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Aktif Kullanıcı Yok -->
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center" x-show="employees.length === 0">
            <div class="text-gray-400 text-sm">
                <p class="mb-2">⚠️ Aktif kullanıcı bulunamadı</p>
                <p class="text-xs">Son 1 saat içinde konum verisi gönderen çalışan yok.</p>
                <p class="text-xs mt-1">Mobil uygulamadan çalışan olarak login olup konum takibini başlatın.</p>
                <p class="text-xs mt-1 text-yellow-400">💡 Not: Uygulama silinmiş veya 1 saatten fazla süredir güncelleme yapmayan kullanıcılar gösterilmez.</p>
            </div>
        </div>

        <!-- Debug Log -->
        <div class="mt-4 bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="debugLogs.length > 0">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-300">📝 Debug Log</h3>
                <button @click="debugLogs = []" class="text-xs text-gray-400 hover:text-gray-300">
                    Temizle
                </button>
            </div>
            <div class="space-y-1 max-h-32 overflow-y-auto text-xs font-mono">
                <template x-for="(log, index) in debugLogs.slice().reverse().slice(0, 10)" :key="index">
                    <div class="text-gray-400"
                         :class="{
                             'text-green-400': log.type === 'success',
                             'text-red-400': log.type === 'error',
                             'text-blue-400': log.type === 'info'
                         }">
                        <span class="text-gray-500" x-text="log.time"></span> -
                        <span x-text="log.message"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Warning Alert for Buildings Without Coordinates -->
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg" x-show="buildingsWithoutCoordinates.length > 0">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-yellow-800">
                    <span x-text="buildingsWithoutCoordinates.length"></span> binanın haritada gösterilebilmesi için koordinat bilgisi gerekiyor
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <template x-for="building in buildingsWithoutCoordinates" :key="building.id">
                            <li class="flex items-center justify-between">
                                <span><strong x-text="building.name"></strong> - <span x-text="building.address"></span></span>
                                <div class="ml-4 flex space-x-2">
                                    <button @click="geocodeBuilding(building)"
                                            class="text-xs bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded transition">
                                        📍 Adresten Al
                                    </button>
                                    <button @click="enableManualMarking(building)"
                                            class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition">
                                        🗺️ Haritada İşaretle
                                    </button>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4">
        <div id="map"></div>
    </div>

    <!-- Selected Date's Maintenance Jobs Panel -->
    <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200 p-6" x-show="todaySchedules.length > 0">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            📅 <span x-text="getSelectedDateLabel()"></span> Bakım İşleri
            (<span x-text="formatSelectedDate()"></span>)
        </h2>
        <p class="text-sm text-gray-500 mb-4">
            💡 Not: İşler <strong>planlanan tarihe</strong> (scheduled_date) göre gösterilmektedir, oluşturulma tarihine göre değil.
        </p>
        <div class="mb-4 flex items-center space-x-2 text-sm text-gray-600">
            <span class="inline-flex items-center">
                <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                Planlı/Atandı
            </span>
            <span class="inline-flex items-center">
                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                Başlandı
            </span>
            <span class="inline-flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                Tamamlandı
            </span>
            <span class="inline-flex items-center">
                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                Ertelendi
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İş Tipi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bina</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atanan Çalışan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Planlanan Zaman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahmini Süre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öncelik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="schedule in todaySchedules" :key="schedule.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="schedule.maintenance_type_label"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div x-text="schedule.building.name"></div>
                                <div class="text-xs text-gray-400" x-text="schedule.building.address"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="schedule.assigned_employee ? schedule.assigned_employee.name : 'Atanmadı'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="formatScheduleTime(schedule.scheduled_time, schedule.scheduled_date)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="schedule.estimated_duration ? formatDuration(schedule.estimated_duration) : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs px-2 py-1 rounded"
                                      :class="{
                                          'bg-green-100 text-green-800': schedule.priority === 'dusuk',
                                          'bg-blue-100 text-blue-800': schedule.priority === 'normal',
                                          'bg-yellow-100 text-yellow-800': schedule.priority === 'yuksek',
                                          'bg-red-100 text-red-800': schedule.priority === 'acil'
                                      }"
                                      x-text="schedule.priority_label"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge"
                                      :class="{
                                          'bg-purple-100 text-purple-800': schedule.status === 'planli' || schedule.status === 'atandi',
                                          'bg-blue-100 text-blue-800': schedule.status === 'baslandi',
                                          'bg-green-100 text-green-800': schedule.status === 'tamamlandi',
                                          'bg-yellow-100 text-yellow-800': schedule.status === 'ertelendi'
                                      }"
                                      x-text="schedule.status_label"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button @click="focusOnSchedule(schedule)" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Haritada Göster
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Location Checks Panel -->
    <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200 p-6" x-show="locationChecks.length > 0">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📍 Konum Kontrolü Sonuçları</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Çalışan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bina</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontrol Tipi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Planlanan Zaman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gerçek Zaman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fark (dk)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="check in locationChecks" :key="check.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="check.employee_name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="check.building_name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="check.check_type_label"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="check.scheduled_time ? formatDateTime(check.scheduled_time) : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="check.actual_time ? formatDateTime(check.actual_time) : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="check.time_difference_minutes !== null ? check.time_difference_minutes + ' dk' : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge"
                                      :class="{
                                          'status-on-time': check.status === 'on_time',
                                          'status-late': check.status === 'late',
                                          'status-early': check.status === 'early',
                                          'status-pending': check.status === 'pending'
                                      }"
                                      x-text="getStatusLabel(check.status)"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
function locationMapData() {
    return {
        map: null,
        buildings: [],
        buildingsWithoutCoordinates: [],
        employees: [],
        todaySchedules: [],
        locationChecks: [],
        selectedDate: (() => {
            // Yerel saat dilimine göre bugünün tarihi (YYYY-MM-DD formatında)
            // toISOString() UTC kullanır, bu yüzden yerel tarihi manuel hesaplıyoruz
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        })(),
        markers: {
            buildings: [],
            employees: [],
            schedules: []
        },
        stats: {
            totalBuildings: 0,
            buildingsWithoutCoordinates: 0,
            activeEmployees: 0,
            onTimeArrivals: 0,
            lateArrivals: 0,
            todaySchedules: 0,
            completedSchedules: 0
        },
        autoRefreshEnabled: true, // Default olarak aktif
        autoRefreshInterval: null,
        loading: false,
        manualMarkingMode: false,
        manualMarkingBuilding: null,
        tempMarker: null,
        lastUpdateTime: null,
        debugLogs: [],

        async init() {
            // Sayfa yüklendiğinde bugünün tarihini güncelle (timezone farkı için)
            // toISOString() UTC kullanır, bu yüzden yerel tarihi manuel hesaplıyoruz
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            this.selectedDate = `${year}-${month}-${day}`;

            // Tarih input'unun değerini de güncelle
            const dateInput = document.getElementById('schedule-date');
            if (dateInput) {
                dateInput.value = this.selectedDate;
            }

            await this.initMap();
            await this.loadMapData();
            this.startAutoRefresh(); // Auto refresh'i başlat
            this.addDebugLog('success', 'Sayfa yüklendi ve canlı takip başlatıldı');
        },

        async initMap() {
            // İstanbul merkez koordinatları (varsayılan)
            this.map = L.map('map').setView([41.0082, 28.9784], 11);

            // OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            // Map click handler for manual marking
            this.map.on('click', (e) => {
                if (this.manualMarkingMode && this.manualMarkingBuilding) {
                    this.handleMapClick(e.latlng);
                }
            });
        },

        async loadMapData() {
            try {
                this.addDebugLog('info', 'Harita verileri yükleniyor...');
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                // Send selected date to API (default: today)
                const dateParam = this.selectedDate ? `?date=${this.selectedDate}` : '';
                const response = await fetch(`/api/location-map/data${dateParam}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    if (response.status === 401 || response.status === 403) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error('API isteği başarısız: ' + response.status);
                }

                const result = await response.json();

                if (result.success) {
                    const oldEmployeeCount = this.employees.length;
                    this.buildings = result.data.buildings || [];
                    this.buildingsWithoutCoordinates = result.data.buildings_without_coordinates || [];
                    this.employees = result.data.employees || [];
                    this.todaySchedules = result.data.today_schedules || [];
                    this.locationChecks = result.data.location_checks || [];

                    // Update last update time
                    const now = new Date();
                    this.lastUpdateTime = now.toLocaleTimeString('tr-TR', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    // Debug log
                    const newEmployeeCount = this.employees.length;
                    if (newEmployeeCount !== oldEmployeeCount) {
                        this.addDebugLog('success', `${newEmployeeCount} aktif kullanıcı bulundu (önceki: ${oldEmployeeCount})`);
                    } else {
                        this.addDebugLog('success', `Veriler güncellendi - ${newEmployeeCount} aktif kullanıcı`);
                    }

                    // Log employee names
                    if (this.employees.length > 0) {
                        const employeeNames = this.employees.map(e => e.name).join(', ');
                        this.addDebugLog('info', `Aktif kullanıcılar: ${employeeNames}`);
                    }

                    this.updateStats();
                    if (this.map) {
                        this.renderMap();
                    }
                } else {
                    console.error('API error:', result.message);
                    this.addDebugLog('error', 'API hatası: ' + (result.message || 'Bilinmeyen hata'));
                    alert('Harita verileri yüklenirken hata oluştu: ' + (result.message || 'Bilinmeyen hata'));
                }
            } catch (error) {
                console.error('Map data load error:', error);
                this.addDebugLog('error', 'Veri yükleme hatası: ' + error.message);
                alert('Harita verileri yüklenirken hata oluştu. Sayfayı yenileyin.');
            }
        },

        renderMap() {
            // Clear existing markers
            this.markers.buildings.forEach(marker => this.map.removeLayer(marker));
            this.markers.employees.forEach(marker => this.map.removeLayer(marker));
            this.markers.schedules.forEach(marker => this.map.removeLayer(marker));
            this.markers.buildings = [];
            this.markers.employees = [];
            this.markers.schedules = [];

            // Add TODAY's maintenance schedule markers (with assigned employees)
            this.todaySchedules.forEach(schedule => {
                if (schedule.building && schedule.building.coordinates) {
                    const status = schedule.status;
                    let markerColor = '#9333ea'; // Purple (default: planli/atandi)

                    if (status === 'baslandi') markerColor = '#3b82f6'; // Blue
                    else if (status === 'tamamlandi') markerColor = '#10b981'; // Green
                    else if (status === 'ertelendi') markerColor = '#f59e0b'; // Yellow

                    // Format duration for popup (convert minutes to readable format)
                    const durationText = schedule.estimated_duration ? this.formatDuration(schedule.estimated_duration) : '';

                    const marker = L.marker([schedule.building.coordinates.lat, schedule.building.coordinates.lng], {
                        icon: L.divIcon({
                            className: 'schedule-marker',
                            html: `<div style="background: ${markerColor}; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">📋</div>`,
                            iconSize: [35, 35],
                            iconAnchor: [17.5, 17.5]
                        })
                    }).bindPopup(`
                        <div class="location-info-card" style="min-width: 300px;">
                            <h3 class="font-bold text-lg mb-2">📋 ${schedule.maintenance_type_label}</h3>
                            <div class="mb-2">
                                <p class="font-semibold text-gray-900">🏢 ${schedule.building.name}</p>
                                <p class="text-xs text-gray-600">${schedule.building.address}</p>
                                <p class="text-xs text-gray-500">${schedule.building.district}, ${schedule.building.city}</p>
                            </div>
                            ${schedule.assigned_employee ? `
                                <div class="mb-2 pt-2 border-t border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700">👤 Atanan Çalışan:</p>
                                    <p class="text-sm text-gray-900 font-medium">${schedule.assigned_employee.name}</p>
                                    <p class="text-xs text-gray-500">${schedule.assigned_employee.position || ''}</p>
                                    ${schedule.assigned_employee.phone ? `<p class="text-xs text-gray-500">📞 ${schedule.assigned_employee.phone}</p>` : ''}
                                </div>
                            ` : '<p class="text-xs text-gray-500 mb-2">⚠️ Atanan çalışan yok</p>'}

                            <!-- Zaman Bilgileri - Önemli ve Belirgin -->
                            <div class="mb-2 pt-2 border-t border-gray-200 bg-blue-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-blue-900 mb-2 uppercase tracking-wide">⏰ Zaman Planlaması</p>
                                <div class="space-y-1.5">
                                    <p class="text-sm text-gray-800">
                                        <span class="font-semibold">📅 Tarih:</span>
                                        <span class="text-gray-900">${schedule.scheduled_date}</span>
                                    </p>
                                    ${schedule.scheduled_time_display ? `
                                        <p class="text-sm text-gray-800">
                                            <span class="font-semibold">🕐 Gidiş Saati:</span>
                                            <span class="text-blue-700 font-bold text-base ml-1">${schedule.scheduled_time_display}</span>
                                        </p>
                                    ` : '<p class="text-xs text-gray-500 italic">⚠️ Gidiş saati belirtilmemiş</p>'}
                                    ${durationText ? `
                                        <p class="text-sm text-gray-800">
                                            <span class="font-semibold">⏱️ Tahmini Süre:</span>
                                            <span class="text-gray-900 font-medium">${durationText}</span>
                                        </p>
                                    ` : ''}
                                    ${schedule.estimated_end_time ? `
                                        <p class="text-sm text-gray-800">
                                            <span class="font-semibold">⏰ Tahmini Bitiş:</span>
                                            <span class="text-green-700 font-bold text-base">${schedule.estimated_end_time.includes(' ') ? schedule.estimated_end_time.split(' ')[1].substring(0, 5) : ''}</span>
                                        </p>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="pt-2 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-xs px-2 py-1 rounded"
                                      style="background: ${markerColor}20; color: ${markerColor}; font-weight: 600;">
                                    ${schedule.status_label}
                                </span>
                                <span class="text-xs px-2 py-1 rounded"
                                      style="${schedule.priority === 'acil' ? 'background: #fee2e2; color: #991b1b;' :
                                              schedule.priority === 'yuksek' ? 'background: #fef3c7; color: #92400e;' :
                                              schedule.priority === 'normal' ? 'background: #dbeafe; color: #1e40af;' :
                                              'background: #dcfce7; color: #166534;'}">
                                    ${schedule.priority_label}
                                </span>
                            </div>
                        </div>
                    `).addTo(this.map);

                    this.markers.schedules.push(marker);
                }
            });

            // Add building markers (only if no schedule for that building today)
            this.buildings.forEach(building => {
                if (building.coordinates && building.coordinates.lat && building.coordinates.lng) {
                    // Check if this building has a schedule today (don't duplicate markers)
                    const hasSchedule = this.todaySchedules.some(s =>
                        s.building && s.building.id === building.id
                    );

                    if (!hasSchedule) {
                        const marker = L.marker([building.coordinates.lat, building.coordinates.lng], {
                            icon: L.divIcon({
                                className: 'building-marker',
                                html: '<div style="background: #94a3b8; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                                iconSize: [25, 25],
                                iconAnchor: [12.5, 12.5]
                            })
                        }).bindPopup(`
                            <div class="location-info-card">
                                <h3 class="font-bold text-lg mb-2">🏢 ${building.name}</h3>
                                <p class="text-sm text-gray-600 mb-1">${building.address}</p>
                                <p class="text-sm text-gray-500">${building.district}, ${building.city}</p>
                            </div>
                        `).addTo(this.map);

                        this.markers.buildings.push(marker);
                    }
                }
            });

            // Add employee markers with animation (only online employees)
            this.employees.forEach(employee => {
                if (employee.coordinates && employee.coordinates.lat && employee.coordinates.lng) {
                    // Online status check - only show if last update was within 1 hour
                    const isOnline = employee.is_online !== false; // Default to true if not specified
                    const markerColor = isOnline ? '#10b981' : '#6b7280'; // Green for online, gray for offline
                    const statusText = isOnline ? '🟢 Çevrimiçi' : '⚫ Çevrimdışı';
                    const statusBg = isOnline ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';

                    // Create animated marker (only animate if online)
                    const marker = L.marker([employee.coordinates.lat, employee.coordinates.lng], {
                        icon: L.divIcon({
                            className: 'employee-marker',
                            html: `
                                <div style="
                                    background: ${markerColor};
                                    width: 30px;
                                    height: 30px;
                                    border-radius: 50%;
                                    border: 3px solid white;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.4);
                                    ${isOnline ? 'animation: pulse 2s infinite;' : ''}
                                ">
                                    <div style="
                                        position: absolute;
                                        top: 50%;
                                        left: 50%;
                                        transform: translate(-50%, -50%);
                                        color: white;
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">👤</div>
                                </div>
                                <style>
                                    @keyframes pulse {
                                        0% { box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4); }
                                        50% { box-shadow: 0 2px 16px rgba(16, 185, 129, 0.8); }
                                        100% { box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4); }
                                    }
                                </style>
                            `,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        })
                    }).bindPopup(`
                        <div class="location-info-card" style="min-width: 280px;">
                            <h3 class="font-bold text-lg mb-2 flex items-center justify-between">
                                <span class="flex items-center">
                                    <span class="mr-2">👤</span>
                                    <span>${employee.name}</span>
                                </span>
                                <span class="text-xs px-2 py-1 rounded ${statusBg}">
                                    ${statusText}
                                </span>
                            </h3>
                            <div class="mb-2">
                                <p class="text-sm font-semibold text-gray-700">${employee.position}</p>
                                ${employee.phone ? `<p class="text-xs text-gray-500 mt-1">📞 ${employee.phone}</p>` : ''}
                            </div>
                            ${employee.active_maintenance ? `
                                <div class="mb-2 pt-2 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">📋 Aktif İş:</p>
                                    <p class="text-sm text-gray-900 font-medium">${employee.active_maintenance.building_name}</p>
                                    <p class="text-xs text-gray-500">${employee.active_maintenance.building_address || ''}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Planlanan: ${employee.active_maintenance.scheduled_time ? new Date(employee.active_maintenance.scheduled_time).toLocaleString('tr-TR') : '-'}
                                    </p>
                                </div>
                            ` : '<p class="text-xs text-gray-400 mb-2">⚠️ Aktif iş yok</p>'}
                            <div class="pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    <span class="font-semibold">📍 Konum:</span><br>
                                    <span class="font-mono text-xs">${employee.coordinates.lat.toFixed(6)}, ${employee.coordinates.lng.toFixed(6)}</span>
                                </p>
                                <p class="text-xs ${isOnline ? 'text-green-600' : 'text-gray-400'} mt-1">
                                    <span class="font-semibold">🕐 Son Güncelleme:</span><br>
                                    ${employee.last_update || 'Bilinmiyor'}
                                    ${employee.minutes_since_update !== null ? ` (${employee.minutes_since_update} dakika önce)` : ''}
                                </p>
                                ${!isOnline && employee.minutes_since_update ? `
                                    <p class="text-xs text-red-500 mt-1">
                                        ⚠️ Son güncelleme 1 saatten eski - çevrimdışı olabilir
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                    `).addTo(this.map);

                    this.markers.employees.push(marker);
                }
            });

            // Fit map to show all markers (prioritize schedules)
            const allMarkers = [...this.markers.schedules, ...this.markers.buildings, ...this.markers.employees];
            if (allMarkers.length > 0) {
                const group = new L.featureGroup(allMarkers);
                this.map.fitBounds(group.getBounds().pad(0.1));
            }
        },

        updateStats() {
            this.stats.totalBuildings = this.buildings.length + this.buildingsWithoutCoordinates.length;
            this.stats.buildingsWithoutCoordinates = this.buildingsWithoutCoordinates.length;
            this.stats.activeEmployees = this.employees.length;
            this.stats.todaySchedules = this.todaySchedules.length;
            this.stats.completedSchedules = this.todaySchedules.filter(s => s.status === 'tamamlandi').length;

            const arrivals = this.locationChecks.filter(c => c.check_type === 'arrival');
            this.stats.onTimeArrivals = arrivals.filter(c => c.status === 'on_time').length;
            this.stats.lateArrivals = arrivals.filter(c => c.status === 'late').length;
        },

        async refreshData() {
            if (this.autoRefreshEnabled) {
                this.addDebugLog('info', 'Otomatik yenileme başlatıldı...');
            }
            await this.loadMapData();
        },

        startAutoRefresh() {
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
            }

            if (this.autoRefreshEnabled) {
                this.addDebugLog('info', 'Canlı takip başlatıldı (10 saniye aralıklarla)');
                this.autoRefreshInterval = setInterval(() => {
                    this.refreshData();
                }, 10000); // 10 saniyede bir yenile (canlı takip için)
            } else {
                this.addDebugLog('info', 'Canlı takip durduruldu');
            }
        },

        addDebugLog(type, message) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('tr-TR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            this.debugLogs.push({
                type: type,
                message: message,
                time: timeStr
            });

            // Keep only last 50 logs
            if (this.debugLogs.length > 50) {
                this.debugLogs = this.debugLogs.slice(-50);
            }
        },

        formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('tr-TR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        getStatusLabel(status) {
            const labels = {
                'pending': 'Beklemede',
                'on_time': 'Zamanında',
                'late': 'Geç',
                'early': 'Erken',
                'missed': 'Kaçırıldı'
            };
            return labels[status] || status;
        },

        // Auto refresh handler
        handleAutoRefreshChange() {
            this.startAutoRefresh();
        },

        // Geocode building address to get coordinates
        async geocodeBuilding(building) {
            if (this.loading) return;

            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(`/api/location-map/building/${building.id}/update-coordinates`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        geocode_address: true
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Remove from without coordinates list and add to buildings list
                    this.buildingsWithoutCoordinates = this.buildingsWithoutCoordinates.filter(b => b.id !== building.id);
                    this.buildings.push({
                        ...building,
                        has_coordinates: true,
                        coordinates: result.data.coordinates
                    });
                    this.updateStats();
                    this.renderMap();

                    alert('✅ Koordinatlar başarıyla alındı! Bina haritada görüntüleniyor.');
                } else {
                    alert('❌ ' + (result.message || 'Koordinat alınamadı. Lütfen adresi kontrol edin veya haritada manuel işaretleyin.'));
                }
            } catch (error) {
                console.error('Geocoding error:', error);
                alert('❌ Koordinat alınırken bir hata oluştu. Lütfen tekrar deneyin.');
            } finally {
                this.loading = false;
            }
        },

        // Enable manual marking mode
        enableManualMarking(building) {
            this.manualMarkingMode = true;
            this.manualMarkingBuilding = building;

            // Change cursor
            this.map.getContainer().style.cursor = 'crosshair';

            // Show instruction
            alert(`🗺️ Manuel İşaretleme Modu Aktif!\n\n"${building.name}" için haritada doğru konumu tıklayın.`);

            // Focus on existing location if available, otherwise center on Turkey
            if (building.coordinates && building.coordinates.lat && building.coordinates.lng) {
                this.map.setView([building.coordinates.lat, building.coordinates.lng], 15);
            } else {
                this.map.setView([39.9334, 32.8597], 6); // Turkey center
            }
        },

        // Handle map click in manual marking mode
        handleMapClick(latlng) {
            if (!this.manualMarkingMode || !this.manualMarkingBuilding) return;

            // Remove previous temp marker if exists
            if (this.tempMarker) {
                this.map.removeLayer(this.tempMarker);
            }

            // Store reference for popup buttons
            const self = this;
            const confirmLat = latlng.lat;
            const confirmLng = latlng.lng;

            // Create popup content
            const popupContent = document.createElement('div');
            popupContent.className = 'location-info-card';
            popupContent.innerHTML = `
                <h3 class="font-bold text-lg mb-2">📍 ${this.manualMarkingBuilding.name}</h3>
                <p class="text-sm text-gray-600 mb-2">Seçilen Konum:</p>
                <p class="text-xs text-gray-500 mb-3">${confirmLat.toFixed(7)}, ${confirmLng.toFixed(7)}</p>
                <button id="confirm-manual-marking"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm w-full">
                    ✅ Bu Konumu Kaydet
                </button>
                <button id="cancel-manual-marking"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm w-full mt-2">
                    ❌ İptal
                </button>
            `;

            // Add event listeners
            popupContent.querySelector('#confirm-manual-marking').addEventListener('click', () => {
                self.confirmManualMarking(confirmLat, confirmLng);
            });

            popupContent.querySelector('#cancel-manual-marking').addEventListener('click', () => {
                self.cancelManualMarking();
            });

            // Add temporary marker to show selected location
            this.tempMarker = L.marker([latlng.lat, latlng.lng], {
                icon: L.divIcon({
                    className: 'temp-marker',
                    html: '<div style="background: #ef4444; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                })
            }).bindPopup(popupContent).addTo(this.map);

            // Open popup
            this.tempMarker.openPopup();
        },

        // Confirm manual marking
        async confirmManualMarking(lat, lng) {
            if (!this.manualMarkingBuilding) return;

            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(`/api/location-map/building/${this.manualMarkingBuilding.id}/update-coordinates`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Remove temp marker
                    if (this.tempMarker) {
                        this.map.removeLayer(this.tempMarker);
                        this.tempMarker = null;
                    }

                    // Remove from without coordinates list and add to buildings list
                    this.buildingsWithoutCoordinates = this.buildingsWithoutCoordinates.filter(b => b.id !== this.manualMarkingBuilding.id);
                    this.buildings.push({
                        ...this.manualMarkingBuilding,
                        has_coordinates: true,
                        coordinates: { lat: lat, lng: lng }
                    });
                    this.updateStats();
                    this.renderMap();

                    alert('✅ Konum başarıyla kaydedildi!');
                } else {
                    alert('❌ ' + (result.message || 'Konum kaydedilemedi.'));
                }
            } catch (error) {
                console.error('Manual marking error:', error);
                alert('❌ Konum kaydedilirken bir hata oluştu.');
            } finally {
                this.loading = false;
                this.cancelManualMarking();
            }
        },

        // Cancel manual marking
        cancelManualMarking() {
            this.manualMarkingMode = false;
            this.manualMarkingBuilding = null;
            if (this.tempMarker) {
                this.map.removeLayer(this.tempMarker);
                this.tempMarker = null;
            }
            this.map.getContainer().style.cursor = '';
        },

        // Focus on a specific schedule on map
        focusOnSchedule(schedule) {
            if (!schedule.building || !schedule.building.coordinates) return;

            this.map.setView([schedule.building.coordinates.lat, schedule.building.coordinates.lng], 15);

            // Find and open the marker popup
            const scheduleMarker = this.markers.schedules.find(m => {
                const latlng = m.getLatLng();
                return Math.abs(latlng.lat - schedule.building.coordinates.lat) < 0.0001 &&
                       Math.abs(latlng.lng - schedule.building.coordinates.lng) < 0.0001;
            });

            if (scheduleMarker) {
                scheduleMarker.openPopup();
            }
        },

        // Format date for display
        formatDate(date) {
            return date.toLocaleDateString('tr-TR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        // Format selected date for display
        formatSelectedDate() {
            if (!this.selectedDate) {
                // Bugünün tarihini formatla (yerel saat dilimi)
                const today = new Date();
                return this.formatDate(today);
            }

            // Seçilen tarihi parse et (YYYY-MM-DD formatından)
            // Yerel saat diliminde parse etmek için month - 1 kullanıyoruz (0-based index)
            const [year, month, day] = this.selectedDate.split('-');
            const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day), 12, 0, 0); // Öğlen saatinde parse et (timezone farkından kaçınmak için)
            return this.formatDate(date);
        },

        // Get label for selected date
        getSelectedDateLabel() {
            if (!this.selectedDate) return 'Bugünün';

            // Bugünün tarihini yerel saat dilimine göre hesapla
            const today = new Date();
            const todayYear = today.getFullYear();
            const todayMonth = String(today.getMonth() + 1).padStart(2, '0');
            const todayDay = String(today.getDate()).padStart(2, '0');
            const todayStr = `${todayYear}-${todayMonth}-${todayDay}`;

            // Seçilen tarih ile karşılaştır (string karşılaştırması)
            if (this.selectedDate === todayStr) {
                return 'Bugünün';
            } else {
                // Seçilen tarih bugünden sonra mı önce mi? (yerel saat diliminde)
                const [selectedYear, selectedMonth, selectedDay] = this.selectedDate.split('-');
                const [todayYearNum, todayMonthNum, todayDayNum] = todayStr.split('-');

                const selectedDateObj = new Date(parseInt(selectedYear), parseInt(selectedMonth) - 1, parseInt(selectedDay));
                const todayDateObj = new Date(parseInt(todayYearNum), parseInt(todayMonthNum) - 1, parseInt(todayDayNum));

                if (selectedDateObj.getTime() > todayDateObj.getTime()) {
                    return 'Seçilen Tarihin';
                } else {
                    return 'Seçilen Tarihin';
                }
            }
        },

        // Format schedule time for display
        formatScheduleTime(scheduledTime, scheduledDate) {
            if (!scheduledTime) return scheduledDate || '-';

            // If it's a full datetime string (Y-m-d H:i:s)
            if (scheduledTime.includes(' ')) {
                const timePart = scheduledTime.split(' ')[1];
                return timePart ? timePart.substring(0, 5) : scheduledTime;
            }

            // If it's just time (H:i:s)
            if (scheduledTime.includes(':')) {
                return scheduledTime.substring(0, 5);
            }

            return scheduledTime;
        },

        // Format duration (minutes) to readable format
        formatDuration(minutes) {
            if (!minutes) return '-';

            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;

            if (hours > 0 && mins > 0) {
                return `${hours} saat ${mins} dakika`;
            } else if (hours > 0) {
                return `${hours} saat`;
            } else {
                return `${mins} dakika`;
            }
        }
    }
}
</script>
@endpush
@endsection
