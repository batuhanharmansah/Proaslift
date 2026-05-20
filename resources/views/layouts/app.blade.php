<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Harmanşah Yazılım - Yönetim Paneli')</title>

        <!-- Fonts - Optimized loading -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
        <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Chart.js - Lazy loaded -->
        <script>
            // Chart.js'i lazy loading ile yükle
            window.loadChartJS = function() {
                if (window.Chart) return Promise.resolve();

                return new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            };
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Optimized CSS -->
        <link rel="stylesheet" href="{{ asset('css/layout-optimized.css') }}">

        @stack('styles')
    </head>
    <body class="font-sans antialiased" data-role="{{ auth()->user()->getCurrentRole()->slug ?? 'company' }}">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            @include('partials.sidebar')

            <!-- Main content -->
            <div class="flex-1 md:ml-64 flex flex-col">
                <!-- Navbar -->
                @include('partials.navbar')

                <!-- Page Content -->
                <main class="flex-1">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>

                <!-- Footer -->
                <div class="mt-auto">
                    @include('partials.footer')
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
