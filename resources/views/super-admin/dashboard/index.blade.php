@extends('super-admin.layouts.app')

@section('title', 'Super Admin Dashboard - Harmanşah Yazılım')
@section('page-title', 'Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Super Admin Yönetim Paneli</h1>
        <p class="text-gray-600 mt-1">{{ now()->locale('tr')->isoFormat('D MMMM Y, dddd') }} - Sistem geneli operasyon durumu</p>
    </div>

    <!-- Compact Quick Stats -->
    <div class="flex flex-wrap gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 shadow-sm border border-blue-200 flex items-center space-x-4 min-w-0 flex-1">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-blue-700 truncate">Aktif Firmalar</p>
                <p class="text-2xl font-bold text-blue-800 truncate">{{ $activeCompanies }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 shadow-sm border border-emerald-200 flex items-center space-x-4 min-w-0 flex-1">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-emerald-700 truncate">Bu Ay Gelir</p>
                <p class="text-2xl font-bold text-emerald-800 truncate">₺{{ number_format($thisMonthRevenue, 0) }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 shadow-sm border border-purple-200 flex items-center space-x-4 min-w-0 flex-1">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-purple-700 truncate">Toplam Kullanıcı</p>
                <p class="text-2xl font-bold text-purple-800 truncate">{{ $totalUsers }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 shadow-sm border border-amber-200 flex items-center space-x-4 min-w-0 flex-1">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-amber-700 truncate">Bekleyen Ödemeler</p>
                <p class="text-2xl font-bold text-amber-800 truncate">{{ $pendingPayments }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Son Eklenen Firmalar (Ana Panel) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl shadow-lg border border-slate-200">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Son Eklenen Firmalar</h3>
                        <p class="text-sm text-slate-600 mt-1">Sisteme son kayıt olan firmalar ve durumları</p>
                    </div>
                    <a href="{{ route('super-admin.companies.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        Tümünü Gör
                    </a>
                </div>
            </div>
            <div class="p-6">
                @forelse($recentCompanies as $company)
                    <div class="flex items-center justify-between p-4 mb-4 bg-white rounded-xl hover:bg-slate-50 transition-all duration-200 border border-slate-100 shadow-sm"style="background: gainsboro !important;">
                        <div class="flex items-center space-x-4" >
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md
                                {{ $company->is_active ? 'bg-gradient-to-br from-emerald-500 to-emerald-600' : 'bg-gradient-to-br from-red-500 to-red-600' }}
                                ">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $company->name }}</div>
                                <div class="text-sm text-slate-600">{{ $company->email }}</div>
                                <div class="text-xs text-slate-500">{{ $company->created_at->format('d.m.Y') }} - {{ $company->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-slate-800">{{ $company->subscription_plan_label }}</div>
                            <div class="text-xs text-slate-600">{{ $company->monthly_fee }}₺/ay</div>
                            <div class="mt-1">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $company->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                    {{ $company->is_active ? 'Aktif' : 'Askıda' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-800 mb-2">Henüz firma kaydı yok!</h3>
                        <p class="text-slate-600 mb-6">İlk firmayı eklemek için firma yönetimi bölümünü kullanın.</p>
                        <a href="{{ route('super-admin.companies.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                            İlk Firmayı Ekle
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sağ Panel -->
        <div class="space-y-6">
            <!-- Sistem İstatistikleri -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl shadow-lg border border-indigo-200 p-6">
                <h3 class="text-lg font-bold text-indigo-800 mb-4">Sistem İstatistikleri</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-indigo-100 shadow-sm" style="background: gainsboro !important;">
                        <div class="flex items-center space-x-3" >
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 text-sm">Toplam Bina</div>
                                <div class="text-xs text-slate-600">Sistem geneli</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">{{ $systemStats['total_buildings'] ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-indigo-100 shadow-sm"style="background: gainsboro !important;">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 text-sm">Toplam Personel</div>
                                <div class="text-xs text-slate-600">Tüm firmalar</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-emerald-600">{{ $systemStats['total_employees'] ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-indigo-100 shadow-sm" style="background: gainsboro !important;">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 text-sm">Aktif Firma Oranı</div>
                                <div class="text-xs text-slate-600">Başarı oranı</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-purple-600">%{{ $systemStats['active_companies_percentage'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kritik Durumlar -->
            @if(count($criticalIssues) > 0)
            <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-red-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Kritik Durumlar
                </h3>
                <div class="space-y-3">
                    @foreach($criticalIssues as $issue)
                        <div class="bg-white p-4 rounded-xl border border-red-200 shadow-sm">
                            <div class="font-medium text-red-800">{{ $issue['title'] }}</div>
                            <div class="text-sm text-red-600 mt-1">{{ $issue['description'] }}</div>
                            <div class="text-xs text-red-500 mt-2">{{ $issue['count'] }} adet</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Son Aktiviteler -->
            @if(isset($recentActivities) && $recentActivities->count() > 0)
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200 rounded-2xl p-6" >
                <h3 class="text-lg font-bold text-cyan-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Son Aktiviteler
                </h3>
                <div class="space-y-3">
                    @foreach($recentActivities->take(3) as $activity)
                        <div class="bg-white p-3 rounded-xl border border-cyan-200 shadow-sm" style="background: gainsboro !important;">
                            <div class="flex items-center justify-between">
                                <div class="text-cyan-800 font-medium">{{ $activity['title'] }}</div>
                                <div class="text-cyan-600 text-sm">
                                    {{ $activity['time']->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Alt Panel: Grafik ve Özet -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Gelir Trendi Grafiği -->
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl shadow-lg border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Gelir Trendi</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Finansal Özet -->
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl shadow-lg border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Bu Ay Finansal Durum</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                    <div>
                        <div class="text-sm text-emerald-700 font-medium">Toplam Gelir</div>
                        <div class="text-2xl font-bold text-emerald-800">₺{{ number_format($thisMonthRevenue, 0) }}</div>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
                    <div>
                        <div class="text-sm text-amber-700 font-medium">Bekleyen Ödemeler</div>
                        <div class="text-2xl font-bold text-amber-800">₺{{ number_format($pendingPayments * 1000, 0) }}</div>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <div>
                        <div class="text-sm text-blue-700 font-medium">Aktif Firma Sayısı</div>
                        <div class="text-2xl font-bold text-blue-800">{{ $activeCompanies }}</div>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı Eylemler -->
    <div class="mt-8 bg-gradient-to-r from-slate-600 to-slate-700 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold mb-2">Hızlı İşlemler</h3>
                <p class="text-slate-200">Sık kullanılan işlemlere hızlı erişim</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('super-admin.companies.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Yeni Firma Ekle
                </a>
                <a href="{{ route('super-admin.payments.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ödeme Kaydı
                </a>
                <a href="{{ route('super-admin.companies.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Firma Yönetimi
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = @json($revenueChart);

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₺' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
