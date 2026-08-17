<!-- Logo -->
<div class="flex items-center flex-shrink-0 px-4">
    <div class="flex items-center">
        <div class="h-8 w-8 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
            <span class="text-white font-bold text-lg">
                @if(auth()->user()->company)
                    {{ substr(auth()->user()->company->name, 0, 1) }}
                @else
                    H
                @endif
            </span>
        </div>
        <div class="ml-3">
            <h1 class="text-gray-900 font-bold text-lg">{{ auth()->user()->company->name ?? 'Firma' }}</h1>
            <p class="text-green-600 text-xs font-medium">Personel Paneli</p>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="mt-8 flex-1 px-2 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('employee.dashboard') }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employee.dashboard') ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('employee.dashboard') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
        </svg>
        Ana Sayfa
    </a>

    <!-- Bakım İşlerim -->
    <a href="{{ route('employee.maintenance.index') }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employee.maintenance.*') ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('employee.maintenance.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Bakım İşlerim
    </a>

    <!-- Arıza Bildirimlerim -->
    <a href="{{ route('employee.issue-reports.index') }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employee.issue-reports.*') ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('employee.issue-reports.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        Arıza Bildirimlerim
    </a>

    <!-- Rota Planlayıcı -->
    <a href="{{ route('employee.route-planner.index') }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employee.route-planner.*') ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('employee.route-planner.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
        </svg>
        Rota Planlayıcı
    </a>

    <!-- Depo -->
    <a href="{{ route('employee.products.index') }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employee.products.*') ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('employee.products.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        Depo
    </a>

    <!-- Durum Tespit Raporu -->
    <a href="{{ route('employee.compliance-documents.index', ['type' => 'dtr']) }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->route('type') === 'dtr' ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Durum Tespit Raporu
    </a>

    <!-- Kurtarma Formu -->
    <a href="{{ route('employee.compliance-documents.index', ['type' => 'kurtarma']) }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->route('type') === 'kurtarma' ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Kurtarma Formu
    </a>

    <!-- Etiket Takibi -->
    <a href="{{ route('employee.elevator-labels.index') }}"
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employee.elevator-labels.*') ? 'bg-primary-50 border-r-4 border-primary-500 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('employee.elevator-labels.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
        </svg>
        Etiket Takibi
    </a>

    <!-- Spacer -->
    <div class="pt-4">
        <div class="border-t border-gray-200 pt-4">
            <!-- User info -->
            <div class="flex items-center px-2 py-3 text-sm">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
                        <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-gray-500 text-xs">
                        @php
                            $employee = auth()->user()->company->employees()->where('email', auth()->user()->email)->first();
                        @endphp
                        {{ $employee ? $employee->position_label : 'Personel' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Company info -->
<div class="flex-shrink-0 flex border-t border-gray-200 p-4">
    <div class="w-full">
        <div class="text-xs text-gray-500 mb-1">Çalıştığım Firma</div>
        <div class="text-sm font-medium text-gray-900">{{ auth()->user()->company->name ?? 'Firma Adı' }}</div>
        <div class="text-xs text-gray-500">{{ auth()->user()->company->subscription_plan_label ?? '' }}</div>
    </div>
</div>
