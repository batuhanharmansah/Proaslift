<!-- Sade ve Koyu Renkli Navbar -->
<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-700 bg-gray-800 px-4 shadow-lg sm:gap-x-6 sm:px-6 lg:px-8">
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex items-center gap-x-4 lg:gap-x-6">
            <!-- Page Title -->
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1 text-sm text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span>Super Admin</span>
                </div>
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <h1 class="text-lg font-bold leading-6 text-gray-100">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
        </div>

        <div class="ml-auto flex items-center gap-x-3 lg:gap-x-4">
            <!-- System Status -->
            <div class="hidden lg:flex items-center gap-x-2 px-3 py-1.5 bg-green-900/20 rounded-lg border border-green-700/30">
                <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                <span class="text-green-400 text-sm font-medium">Sistem Aktif</span>
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button type="button" class="relative p-2 text-gray-400 hover:text-gray-100 hover:bg-gray-700 rounded-lg transition-all duration-200">
                    <span class="sr-only">Bildirimler</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-600 text-xs text-gray-100 flex items-center justify-center font-medium">3</span>
                </button>
            </div>

            <!-- Separator -->
            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-600" aria-hidden="true"></div>

            <!-- Profile dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" class="flex items-center gap-x-3 p-2 hover:bg-gray-700 rounded-lg transition-all duration-200" x-on:click="open = !open">
                    <div class="relative">
                        <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-lg">
                            <span class="text-sm font-bold text-gray-100">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                        <div class="absolute -top-1 -right-1 h-3 w-3 bg-green-500 rounded-full border-2 border-gray-900"></div>
                    </div>
                    <span class="hidden lg:flex lg:flex-col lg:items-start lg:min-w-0">
                        <span class="text-sm font-semibold leading-5 text-gray-100 truncate">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-gray-400 truncate">Super Admin</span>
                    </span>
                    <svg class="hidden lg:block h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open" x-on:click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-lg bg-gray-800 py-2 shadow-lg ring-1 ring-gray-700 border border-gray-700">

                    <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-300 hover:bg-gray-700 hover:text-gray-100">Profil</a>
                    <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-300 hover:bg-gray-700 hover:text-gray-100">Ayarlar</a>

                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-300 hover:bg-gray-700 hover:text-gray-100">
                            Çıkış Yap
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
