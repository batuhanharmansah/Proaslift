<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo/Brand -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <div class="h-8 w-8 bg-primary-500 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-sm">
                                @if(Auth::user()->company_id)
                                    {{ substr(Auth::user()->company->name, 0, 1) }}
                                @else
                                    H
                                @endif
                            </span>
                        </div>
                        <span class="font-semibold text-xl text-gray-900">
                            @if(Auth::user()->company_id)
                                {{ Auth::user()->company->name }}
                            @else
                                Harmanşah Yazılım
                            @endif
                        </span>
                    </a>
                </div>
            </div>

            <!-- Search (Optional) -->
            <div class="hidden md:flex items-center flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <input type="text" placeholder="Ara..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Dil Seçici (EN/TR) -->
            <div class="flex items-center gap-1 mr-4">
                <a href="{{ route('locale.set', 'tr') }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ app()->getLocale() === 'tr' ? 'bg-primary-500 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                    TR
                </a>
                <a href="{{ route('locale.set', 'en') }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary-500 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                    EN
                </a>
            </div>

            <!-- User Menu -->
            <div class="flex items-center">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                            <span class="text-gray-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="hidden md:block text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                        <svg class="ml-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button @click="$dispatch('toggle-sidebar')" class="md:hidden ml-4 p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
