@extends('layouts.app')

@section('title', 'Dashboard - Harmanşah Yazılım')

@section('content')
<div class="p-6" x-data="dashboardData()">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">İşletme Yönetim Paneli</h1>
                <p class="text-gray-600 mt-1">{{ now()->locale('tr')->isoFormat('D MMMM Y, dddd') }} - Günlük operasyon durumu</p>
            </div>
            <div class="flex space-x-3">
                <!-- Real-time Update Toggle -->
                <button @click="toggleRealTime()"
                        :class="realTimeEnabled ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600'"
                        class="text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <span x-show="realTimeEnabled">🟢 Canlı</span>
                    <span x-show="!realTimeEnabled">⚪ Durağan</span>
                </button>

                <!-- Widget Settings -->
                <button @click="showWidgetSettings = true"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    ⚙️ Widget Ayarları
                </button>

                <!-- Export/Import -->
                <div class="relative" x-data="{ showExportMenu: false, exportType: '', exportFormat: 'excel' }">
                    <button @click="showExportMenu = !showExportMenu"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        📊 Veri İşlemleri
                    </button>
                    <div x-show="showExportMenu" @click.away="showExportMenu = false"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="py-2">
                            <!-- Export Seçenekleri -->
                            <div class="px-4 py-2 border-b border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">📤 Export Formatı</h4>
                                <div class="space-y-1">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="exportFormat" value="excel" class="mr-2">
                                        <span class="text-sm text-gray-600">📊 Excel (.xlsx)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="exportFormat" value="pdf" class="mr-2">
                                        <span class="text-sm text-gray-600">📄 PDF (.pdf)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="exportFormat" value="json" class="mr-2">
                                        <span class="text-sm text-gray-600">📋 JSON (.json)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Export Butonları -->
                            <div class="px-4 py-2">
                                <button @click="exportData('maintenance')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    📋 Bakım Verilerini Export Et
                                </button>
                                <button @click="exportData('financial')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    💰 Finansal Verileri Export Et
                                </button>
                                <button @click="exportData('employees')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    👥 Personel Verilerini Export Et
                                </button>
                            </div>

                            <hr class="my-2">

                            <!-- Backup/Import -->
                            <div class="px-4 py-2">
                                <button @click="createBackup()" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    💾 Backup Oluştur
                                </button>
                                <button @click="showImportModal = true" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    📥 Veri Import Et
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Quick Stats -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Aktif Personel</p>
                <p class="text-sm font-bold text-blue-600 truncate" x-text="data.totalEmployees">{{ $dashboardData['totalEmployees'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Sözleşmeli Binalar</p>
                <p class="text-sm font-bold text-green-600 truncate" x-text="data.totalBuildings">{{ $dashboardData['totalBuildings'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Aktif Bakımlar</p>
                <p class="text-sm font-bold text-orange-600 truncate" x-text="data.activeMaintenances">{{ $dashboardData['activeMaintenances'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex items-center space-x-3 min-w-0 flex-1">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">Stok Uyarıları</p>
                <p class="text-sm font-bold text-red-600 truncate" x-text="data.lowStockProducts">{{ $dashboardData['lowStockProducts'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Bu Hafta Bakımı Olan Binalar (Ana Panel) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.weekly_maintenance">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Bu Hafta Bakım Programı</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ now()->startOfWeek()->format('d.m') }} - {{ now()->endOfWeek()->format('d.m.Y') }} arası planlanan işler</p>
                    </div>
                    <a href="{{ route('maintenance.index') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Tümünü Gör
                    </a>
                </div>
            </div>
            <div class="p-6 h-96 overflow-y-auto">
                <template x-for="maintenance in data.thisWeekMaintenances" :key="maintenance.id">
                    <div class="p-4 mb-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition cursor-pointer"
                         @click="window.location.href = '{{ url('bakim-takibi') }}/' + maintenance.id">
                        <!-- Üst Kısım: Bina ve Tarih -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="maintenance.priority === 'acil' ? 'bg-red-100 text-red-600' :
                                             maintenance.priority === 'yuksek' ? 'bg-orange-100 text-orange-600' :
                                             maintenance.priority === 'normal' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 text-lg" x-text="maintenance.building?.name"></div>
                                    <div class="text-sm text-gray-600" x-text="maintenance.maintenance_type_label"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900" x-text="formatDate(maintenance.scheduled_date)"></div>
                                <div class="text-sm text-gray-500" x-text="formatTime(maintenance.scheduled_date)"></div>
                            </div>
                        </div>

                        <!-- Alt Kısım: Detaylar -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Öncelik Badge -->
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-medium px-2 py-1 rounded-full"
                                          :class="maintenance.priority === 'acil' ? 'bg-red-100 text-red-700' :
                                                  maintenance.priority === 'yuksek' ? 'bg-orange-100 text-orange-700' :
                                                  maintenance.priority === 'normal' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'">
                                        <span x-text="maintenance.priority === 'acil' ? '🚨 Acil' :
                                                    maintenance.priority === 'yuksek' ? '⚠️ Yüksek' :
                                                    maintenance.priority === 'normal' ? '📋 Normal' : '📝 Düşük'"></span>
                                    </span>
                                </div>

                                <!-- Durum Badge -->
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-medium px-2 py-1 rounded-full"
                                          :class="maintenance.status === 'planli' ? 'bg-yellow-100 text-yellow-700' :
                                                  maintenance.status === 'atandi' ? 'bg-blue-100 text-blue-700' :
                                                  maintenance.status === 'baslandi' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
                                        <span x-text="maintenance.status === 'planli' ? '📅 Planlı' :
                                                    maintenance.status === 'atandi' ? '👤 Atandı' :
                                                    maintenance.status === 'baslandi' ? '🔄 Başlandı' : '✅ Tamamlandı'"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Atanan Personel -->
                            <div class="text-right">
                                <template x-if="maintenance.assigned_employee">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-700" x-text="getInitials(maintenance.assigned_employee.full_name)"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900" x-text="maintenance.assigned_employee.full_name"></div>
                                            <div class="text-xs text-gray-500" x-text="maintenance.assigned_employee.position_label"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!maintenance.assigned_employee">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs text-gray-500">?</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Atanmamış</div>
                                            <div class="text-xs text-gray-400">Personel bekleniyor</div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="data.thisWeekMaintenances.length === 0">
                    <div class="text-center py-8 text-gray-500">
                        Bu hafta planlanmış bakım işi bulunmuyor.
                    </div>
                </template>
            </div>
        </div>

        <!-- Sağ Panel -->
        <div class="space-y-8">

            <!-- Acil Durumlar -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.urgent_issues">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Acil Durumlar</h3>
                    <p class="text-sm text-gray-600 mt-1">Öncelikli bakım işleri</p>
                </div>
                <div class="p-6 h-48 overflow-y-auto">
                    <template x-for="maintenance in data.urgentMaintenances" :key="maintenance.id">
                        <div class="flex items-center space-x-3 mb-4 p-3 bg-red-50 rounded-lg">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900" x-text="maintenance.building?.name"></div>
                                <div class="text-xs text-gray-500" x-text="formatDate(maintenance.scheduled_date)"></div>
                            </div>
                        </div>
                    </template>
                    <template x-if="data.urgentMaintenances.length === 0">
                        <div class="text-center py-4 text-gray-500 text-sm">
                            Acil durum bulunmuyor.
                        </div>
                    </template>
                </div>
            </div>

            <!-- Finansal Özet -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.financial_summary">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Bu Ay Finansal Özet</h3>
                    <p class="text-sm text-gray-600 mt-1">Gelir ve gider durumu</p>
                </div>
                <div class="p-6 h-48 overflow-y-auto">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Gelir:</span>
                            <span class="text-sm font-bold text-green-600" x-text="formatCurrency(data.thisMonthIncome)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Gider:</span>
                            <span class="text-sm font-bold text-red-600" x-text="formatCurrency(data.thisMonthExpense)"></span>
                        </div>
                        <hr>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">Net:</span>
                            <span class="text-sm font-bold"
                                  :class="(data.thisMonthIncome - data.thisMonthExpense) >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="formatCurrency(data.thisMonthIncome - data.thisMonthExpense)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Uyarıları -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.stock_alerts">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Stok Uyarıları</h3>
                    <p class="text-sm text-gray-600 mt-1">Kritik seviyedeki ürünler</p>
                </div>
                <div class="p-6 h-48 overflow-y-auto">
                    <template x-for="product in data.criticalStock" :key="product.id">
                        <div class="flex items-center justify-between mb-3 p-3 bg-yellow-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900" x-text="product.name"></div>
                                <div class="text-xs text-gray-500" x-text="`Stok: ${product.stock_quantity}`"></div>
                            </div>
                            <div class="text-xs text-yellow-600 font-medium">Kritik</div>
                        </div>
                    </template>
                    <template x-if="data.criticalStock.length === 0">
                        <div class="text-center py-4 text-gray-500 text-sm">
                            Stok uyarısı bulunmuyor.
                        </div>
                    </template>
                </div>
            </div>

            <!-- Asansör Etiket Takibi -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.elevator_labels">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">🏷️ Asansör Etiket Takibi</h3>
                            <p class="text-sm text-gray-600 mt-1">Periyodik kontrol durumu</p>
                        </div>
                        <a href="{{ route('elevator-labels.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Tümünü Gör →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Etiket Dağılımı -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="flex items-center space-x-2 p-2 rounded-lg bg-green-50">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-xs text-green-700 font-medium" x-text="`Yeşil: ${data.elevatorStats?.label_distribution?.yesil || 0}`"></span>
                        </div>
                        <div class="flex items-center space-x-2 p-2 rounded-lg bg-blue-50">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-xs text-blue-700 font-medium" x-text="`Mavi: ${data.elevatorStats?.label_distribution?.mavi || 0}`"></span>
                        </div>
                        <div class="flex items-center space-x-2 p-2 rounded-lg bg-yellow-50">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-xs text-yellow-700 font-medium" x-text="`Sarı: ${data.elevatorStats?.label_distribution?.sari || 0}`"></span>
                        </div>
                        <div class="flex items-center space-x-2 p-2 rounded-lg bg-red-50">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-xs text-red-700 font-medium" x-text="`Kırmızı: ${data.elevatorStats?.label_distribution?.kirmizi || 0}`"></span>
                        </div>
                    </div>

                    <!-- Kritik Durumlar -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-red-50" x-show="data.elevatorStats?.overdue > 0">
                            <span class="text-xs text-red-700 font-medium">🚨 Gecikmiş</span>
                            <span class="text-xs text-red-800 font-bold" x-text="data.elevatorStats?.overdue || 0"></span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-orange-50" x-show="data.elevatorStats?.due_soon?.['7_days'] > 0">
                            <span class="text-xs text-orange-700 font-medium">⚠️ 7 Gün Kala</span>
                            <span class="text-xs text-orange-800 font-bold" x-text="data.elevatorStats?.due_soon?.['7_days'] || 0"></span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-yellow-50" x-show="data.elevatorStats?.due_soon?.['30_days'] > 0">
                            <span class="text-xs text-yellow-700 font-medium">📅 30 Gün Kala</span>
                            <span class="text-xs text-yellow-800 font-bold" x-text="data.elevatorStats?.due_soon?.['30_days'] || 0"></span>
                        </div>
                    </div>

                    <!-- Yaklaşan Son Tarihler -->
                    <div class="h-32 overflow-y-auto">
                        <template x-for="label in data.upcomingDueLabels" :key="label.building_name">
                            <div class="flex items-center justify-between mb-2 p-2 rounded-lg border"
                                 :class="{
                                     'bg-red-50 border-red-200': label.is_overdue,
                                     'bg-orange-50 border-orange-200': !label.is_overdue && label.remaining_days <= 7,
                                     'bg-yellow-50 border-yellow-200': !label.is_overdue && label.remaining_days <= 30,
                                     'bg-gray-50 border-gray-200': !label.is_overdue && label.remaining_days > 30
                                 }">
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-gray-900 truncate" x-text="label.building_name"></div>
                                    <div class="text-xs text-gray-500 truncate" x-text="label.elevator_code"></div>
                                </div>
                                <div class="text-right">
                                    <div class="w-3 h-3 rounded-full mb-1"
                                         :class="{
                                             'bg-green-500': label.label_color === 'yesil',
                                             'bg-blue-500': label.label_color === 'mavi',
                                             'bg-yellow-500': label.label_color === 'sari',
                                             'bg-red-500': label.label_color === 'kirmizi'
                                         }"></div>
                                    <div class="text-xs font-medium"
                                         :class="{
                                             'text-red-600': label.is_overdue,
                                             'text-orange-600': !label.is_overdue && label.remaining_days <= 7,
                                             'text-yellow-600': !label.is_overdue && label.remaining_days <= 30,
                                             'text-gray-600': !label.is_overdue && label.remaining_days > 30
                                         }"
                                         x-text="label.is_overdue ? `${Math.abs(label.remaining_days)}g geç` : `${label.remaining_days}g`">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="data.upcomingDueLabels?.length === 0">
                            <div class="text-center py-4 text-gray-500 text-sm">
                                Yaklaşan son tarih bulunmuyor.
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Yaklaşan Sözleşme Sonları -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.expiring_contracts">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Yaklaşan Sözleşme Sonları</h3>
                    <p class="text-sm text-gray-600 mt-1">30 gün içinde bitecek sözleşmeler</p>
                </div>
                <div class="p-6 h-48 overflow-y-auto">
                    <template x-for="building in data.expiringContracts" :key="building.id">
                        <div class="flex items-center justify-between mb-3 p-3 bg-orange-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900" x-text="building.name"></div>
                                <div class="text-xs text-gray-500" x-text="formatDate(building.contract_end_date)"></div>
                            </div>
                            <div class="text-xs text-orange-600 font-medium">Yakında</div>
                        </div>
                    </template>
                    <template x-if="data.expiringContracts.length === 0">
                        <div class="text-center py-4 text-gray-500 text-sm">
                            Yaklaşan sözleşme sonu bulunmuyor.
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafikler Bölümü -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Haftalık Bakım Grafiği -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.performance_chart">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Haftalık Bakım Grafiği</h3>
                <p class="text-sm text-gray-600 mt-1">Bu hafta planlanan bakım işleri</p>
            </div>
            <div class="p-6">
                <canvas id="weeklyChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Aylık Gelir-Gider Trendi -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100" x-show="userWidgets.revenue_trend">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Aylık Gelir-Gider Trendi</h3>
                <p class="text-sm text-gray-600 mt-1">Son 6 ay finansal performans</p>
            </div>
            <div class="p-6">
                <canvas id="monthlyTrendChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Widget Settings Modal -->
    <div x-show="showWidgetSettings" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Widget Ayarları</h3>
            <div class="space-y-3">
                <template x-for="(enabled, widget) in userWidgets" :key="widget">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="userWidgets[widget]" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700" x-text="getWidgetName(widget)"></span>
                    </label>
                </template>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button @click="showWidgetSettings = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    İptal
                </button>
                <button @click="saveWidgetSettings()" class="px-4 py-2 bg-primary-500 text-white rounded-lg text-sm hover:bg-primary-600">
                    Kaydet
                </button>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="showImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Veri Import Et</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Import Türü</label>
                    <select x-model="importType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="maintenance">Bakım Verileri</option>
                        <option value="financial">Finansal Veriler</option>
                        <option value="employees">Personel Verileri</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dosya Seç</label>
                    <input type="file" @change="handleFileUpload($event)" accept=".json,.csv,.xlsx" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button @click="showImportModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    İptal
                </button>
                <button @click="importData()" class="px-4 py-2 bg-primary-500 text-white rounded-lg text-sm hover:bg-primary-600">
                    Import Et
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function dashboardData() {
    return {
        data: @json($dashboardData),
        userWidgets: @json($userWidgets),
        realTimeEnabled: false,
        realTimeInterval: null,
        showWidgetSettings: false,
        showImportModal: false,
        importType: 'maintenance',
        selectedFile: null,
        charts: {},

        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },

        async initCharts() {
            // Chart.js'i lazy loading ile yükle
            await window.loadChartJS();

            // Haftalık Bakım Grafiği
            const weeklyCtx = document.getElementById('weeklyChart');
            if (weeklyCtx) {
                this.charts.weekly = new Chart(weeklyCtx, {
                    type: 'line',
                    data: {
                        labels: this.data.weeklyChart.labels,
                        datasets: [{
                            label: 'Bakım İşleri',
                            data: this.data.weeklyChart.data,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Aylık Trend Grafiği
            const monthlyCtx = document.getElementById('monthlyTrendChart');
            if (monthlyCtx) {
                this.charts.monthly = new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: this.data.monthlyTrend.labels,
                        datasets: [{
                            label: 'Gelir',
                            data: this.data.monthlyTrend.data.map(item => item.income),
                            backgroundColor: 'rgba(34, 197, 94, 0.5)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
                        }, {
                            label: 'Gider',
                            data: this.data.monthlyTrend.data.map(item => item.expense),
                            backgroundColor: 'rgba(239, 68, 68, 0.5)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        },

        toggleRealTime() {
            this.realTimeEnabled = !this.realTimeEnabled;
            if (this.realTimeEnabled) {
                this.startRealTimeUpdates();
            } else {
                this.stopRealTimeUpdates();
            }
        },

        startRealTimeUpdates() {
            if (this.realTimeInterval) {
                clearInterval(this.realTimeInterval);
            }

            this.realTimeInterval = setInterval(() => {
                this.updateData();
            }, 30000); // 30 saniyede bir güncelle
        },

        stopRealTimeUpdates() {
            if (this.realTimeInterval) {
                clearInterval(this.realTimeInterval);
                this.realTimeInterval = null;
            }
        },

        async updateData() {
            try {
                const response = await fetch('/dashboard/real-time-data');
                const result = await response.json();

                if (result.success) {
                    this.data = result.data;
                    this.updateCharts();
                }
            } catch (error) {
                console.error('Real-time update error:', error);
            }
        },

        updateCharts() {
            // Grafikleri güncelle
            if (this.charts.weekly) {
                this.charts.weekly.data.labels = this.data.weeklyChart.labels;
                this.charts.weekly.data.datasets[0].data = this.data.weeklyChart.data;
                this.charts.weekly.update();
            }

            if (this.charts.monthly) {
                this.charts.monthly.data.labels = this.data.monthlyTrend.labels;
                this.charts.monthly.data.datasets[0].data = this.data.monthlyTrend.data.map(item => item.income);
                this.charts.monthly.data.datasets[1].data = this.data.monthlyTrend.data.map(item => item.expense);
                this.charts.monthly.update();
            }
        },

        async saveWidgetSettings() {
            try {
                const response = await fetch('/dashboard/update-widgets', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ widgets: this.userWidgets })
                });

                const result = await response.json();
                if (result.success) {
                    this.showWidgetSettings = false;
                    // Başarı mesajı göster
                }
            } catch (error) {
                console.error('Widget settings save error:', error);
            }
        },

        async exportData(type) {
            try {
                // Seçilen format'ı al
                const format = this.exportFormat || 'excel';

                // URL'yi oluştur
                const url = `/dashboard/export?type=${type}&format=${format}`;

                // Excel ve PDF için direkt download, JSON için blob
                if (format === 'json') {
                    const response = await fetch(url);
                    const result = await response.json();

                    if (result.success) {
                        // JSON dosyasını indir
                        const blob = new Blob([JSON.stringify(result.data, null, 2)], { type: 'application/json' });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = result.filename;
                        a.click();
                        window.URL.revokeObjectURL(url);
                    }
                } else {
                    // Excel ve PDF için direkt download
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `dashboard_export_${type}_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.${format === 'excel' ? 'xlsx' : 'pdf'}`;
                    a.click();
                }

                // Export menüsünü kapat
                this.showExportMenu = false;

            } catch (error) {
                console.error('Dışa aktarma hatası:', error);
                alert('Dışa aktarma sırasında hata oluştu: ' + error.message);
            }
        },

        async createBackup() {
            try {
                const response = await fetch('/dashboard/backup', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                if (result.success) {
                    alert('Yedek başarıyla oluşturuldu: ' + result.filename);
                }
            } catch (error) {
                console.error('Yedekleme hatası:', error);
            }
        },

        handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
        },

        async importData() {
            if (!this.selectedFile) {
                alert('Lütfen bir dosya seçin');
                return;
            }

            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('type', this.importType);

            try {
                const response = await fetch('/dashboard/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    this.showImportModal = false;
                    this.selectedFile = null;
                    alert('Veri başarıyla içe aktarıldı');
                    this.updateData(); // Dashboard'u güncelle
                } else {
                    alert('İçe aktarma hatası: ' + result.error);
                }
            } catch (error) {
                console.error('İçe aktarma hatası:', error);
                alert('İçe aktarma sırasında hata oluştu');
            }
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('tr-TR');
        },

        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('tr-TR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount);
        },

        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        },

        getWidgetName(widget) {
            const names = {
                'quick_stats': 'Hızlı İstatistikler',
                'weekly_maintenance': 'Haftalık Bakım',
                'urgent_issues': 'Acil Durumlar',
                'financial_summary': 'Finansal Özet',
                'stock_alerts': 'Stok Uyarıları',
                'expiring_contracts': 'Sözleşme Sonları',
                'performance_chart': 'Performans Grafiği',
                'revenue_trend': 'Gelir Trendi',
                'issue_analysis': 'Arıza Analizi',
                'elevator_labels': 'Asansör Etiket Takibi'
            };
            return names[widget] || widget;
        }
    }
}
</script>
@endsection
