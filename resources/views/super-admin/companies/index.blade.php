@extends('super-admin.layouts.app')

@section('title', 'Firma Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Admin Bilgileri Modal -->
    @if(session('new_company_admin_info'))
        <div id="adminInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-medium text-gray-900">Firma Başarıyla Oluşturuldu!</h3>
                        <div class="mt-4 text-left">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-900 mb-3">Admin Giriş Bilgileri</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Firma:</span>
                                        <span class="text-gray-900">{{ session('new_company_admin_info.company_name') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Email:</span>
                                        <span class="text-gray-900 font-mono">{{ session('new_company_admin_info.admin_email') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Şifre:</span>
                                        <span class="text-gray-900 font-mono">{{ session('new_company_admin_info.admin_password') }}</span>
                                    </div>
                                </div>
                                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                                    <strong>⚠️ Önemli:</strong> Bu bilgileri güvenli bir yere kaydedin. Şifre bir daha görüntülenmeyecektir.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-center">
                        <button onclick="closeAdminModal()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Tamam
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function closeAdminModal() {
                document.getElementById('adminInfoModal').style.display = 'none';
                // Session'dan admin bilgilerini temizle
                fetch('{{ route("super-admin.companies.clear-admin-info") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                });
            }
        </script>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Firma Yönetimi</h1>
            <p class="text-gray-600 mt-1">Sistemdeki tüm firmaları görüntüleyin ve yönetin</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('super-admin.companies.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Yeni Firma Ekle
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Firma Ara</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Firma adı, email..."
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Askıda</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
                </select>
            </div>
            <div>
                <label for="plan" class="block text-sm font-medium text-gray-700 mb-2">Abonelik Paketi</label>
                <select id="plan" name="plan"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    @foreach($subscriptionPlans as $plan)
                        <option value="{{ $plan->slug }}" {{ request('plan') === $plan->slug ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Aktif Firmalar</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_companies'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Süresi Dolan</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['expired_companies'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background: gainsboro !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Askıda</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['suspended_companies'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"style="background: gainsboro !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Aylık Gelir</p>
                    <p class="text-2xl font-semibold text-gray-900">₺{{ number_format($stats['monthly_revenue'] ?? 0, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" >
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" >
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Firma
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Abonelik
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kullanıcılar
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Son Ödeme
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" style="background: gainsboro !important;">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ substr($company->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $company->email }}</div>
                                        <div class="text-xs text-gray-400">{{ $company->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $company->subscription_plan ?? 'Belirtilmemiş' }}</div>
                                <div class="text-sm text-gray-500">₺{{ number_format($company->monthly_fee, 0) }}/ay</div>
                                @if($company->subscription_end)
                                    <div class="text-xs text-gray-400">{{ $company->subscription_end->format('d.m.Y') }}'e kadar</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    <!-- Sistem Durumu -->
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $company->is_active ? '✅ Aktif' : '❌ Askıda' }}
                                    </span>
                                    <!-- Abonelik Durumu -->
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($company->subscription_status === 'active') bg-blue-100 text-blue-800
                                        @elseif($company->subscription_status === 'expired') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $company->subscriptionStatusLabel }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <span>{{ $company->users_count ?? 0 }} kullanıcı</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($company->lastPayment)
                                    <div>₺{{ number_format($company->lastPayment->amount, 0) }}</div>
                                    <div class="text-xs">{{ $company->lastPayment->payment_date->format('d.m.Y') }}</div>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $company->lastPayment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $company->lastPayment->statusLabel }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Ödeme yok</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('super-admin.companies.show', $company) }}"
                                       class="text-primary-600 hover:text-primary-900 transition-colors" title="Detayları Görüntüle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('super-admin.companies.edit', $company) }}"
                                       class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Düzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($company->is_active)
                                        <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Bu firmayı askıya almak istediğinizden emin misiniz?')"
                                                    class="text-red-600 hover:text-red-900 transition-colors" title="Askıya Al">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Bu firmayı aktifleştirmek istediğinizden emin misiniz?')"
                                                    class="text-green-600 hover:text-green-900 transition-colors" title="Aktifleştir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">Henüz firma bulunmuyor</p>
                                    <p class="text-gray-400 text-sm">İlk firmayı eklemek için yukarıdaki butonu kullanın</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($companies, 'links'))
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
