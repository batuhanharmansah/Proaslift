<!-- Sade ve Koyu Renkli Sidebar -->
<div class="w-72 flex-shrink-0 flex flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 border-r border-gray-700 shadow-2xl">
        <!-- Logo -->
        <div class="flex h-20 shrink-0 items-center justify-center border-b border-gray-700">
            <div class="flex items-center">
                <div class="relative">
                    <div class="h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg">
                        <span class="text-gray-100 font-bold text-xl">H</span>
                    </div>
                    <div class="absolute -top-1 -right-1 h-3 w-3 bg-green-500 rounded-full border-2 border-gray-900"></div>
                </div>
                <div class="ml-3">
                    <h1 class="text-gray-100 font-bold text-base tracking-tight">Harmanşah Yazılım</h1>
                    <div class="flex items-center mt-0.5">
                        <div class="h-1 w-1 bg-blue-400 rounded-full mr-2"></div>
                        <p class="text-blue-400 text-xs font-medium">Super Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-1 flex-col mt-2">
            <ul role="list" class="flex flex-1 flex-col gap-y-2">
                <li>
                    <!-- Main Menu Label -->
                    <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider mb-3">Ana Menü</div>
                    <ul role="list" class="space-y-1">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('super-admin.dashboard') }}"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 {{ request()->routeIs('super-admin.dashboard') ? 'bg-blue-600 text-gray-100' : 'text-gray-300 hover:text-gray-100 hover:bg-gray-800' }}">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('super-admin.dashboard') ? 'bg-blue-700' : 'bg-gray-700 group-hover:bg-gray-600' }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                </div>
                                <span class="truncate">Dashboard</span>
                            </a>
                        </li>

                        <!-- Firma Yönetimi -->
                        <li>
                            <a href="{{ route('super-admin.companies.index') }}"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 {{ request()->routeIs('super-admin.companies.*') ? 'bg-blue-600 text-gray-100' : 'text-gray-300 hover:text-gray-100 hover:bg-gray-800' }}">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('super-admin.companies.*') ? 'bg-blue-700' : 'bg-gray-700 group-hover:bg-gray-600' }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span class="truncate">Firmalar</span>
                            </a>
                        </li>

                        <!-- Ödeme Yönetimi -->
                        <li>
                            <a href="{{ route('super-admin.payments.index') }}"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 {{ request()->routeIs('super-admin.payments.*') ? 'bg-blue-600 text-gray-100' : 'text-gray-300 hover:text-gray-100 hover:bg-gray-800' }}">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('super-admin.payments.*') ? 'bg-blue-700' : 'bg-gray-700 group-hover:bg-gray-600' }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="truncate">Ödemeler</span>
                            </a>
                        </li>

                        <!-- Abonelik Planları -->
                        <li>
                            <a href="#"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 text-gray-500 cursor-not-allowed">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <span class="truncate">Paketler</span>
                                <span class="ml-auto text-xs bg-gray-700 text-gray-400 px-2 py-0.5 rounded-full">Yakında</span>
                            </a>
                        </li>

                        <!-- Raporlar -->
                        <li>
                            <a href="#"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 text-gray-500 cursor-not-allowed">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2H9z" />
                                    </svg>
                                </div>
                                <span class="truncate">Raporlar</span>
                                <span class="ml-auto text-xs bg-gray-700 text-gray-400 px-2 py-0.5 rounded-full">Yakında</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sistem -->
                <li class="mt-6">
                    <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider mb-3">Sistem</div>
                    <ul role="list" class="space-y-1">
                        <li>
                            <a href="#"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 text-gray-500 cursor-not-allowed">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span class="truncate">Ayarlar</span>
                            </a>
                        </li>

                        <li>
                            <a href="mailto:destek@harmansah.com"
                               class="group flex items-center gap-x-3 rounded-lg p-3 text-sm leading-6 font-medium transition-all duration-200 text-gray-300 hover:text-gray-100 hover:bg-gray-800">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-700 group-hover:bg-gray-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <span class="truncate">Destek</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User Info -->
                <li class="mt-auto">
                    <div class="border-t border-gray-700 pt-4">
                        <div class="group relative">
                            <div class="flex items-center gap-x-3 rounded-lg p-3 bg-gray-800 border border-gray-700 hover:bg-gray-700 transition-all duration-200">
                                <div class="relative">
                                    <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center shadow-lg">
                                        <span class="text-sm font-bold text-gray-100">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                    <div class="absolute -top-1 -right-1 h-3 w-3 bg-green-500 rounded-full border-2 border-gray-900"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-100 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-gray-600 transition-colors" title="Çıkış Yap">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>


