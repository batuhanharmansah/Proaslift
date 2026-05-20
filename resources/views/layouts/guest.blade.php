<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Proaslift - Giriş</title>
        @php
            $brandLogo = asset('brand/proaslift-logo.png');
            $brandLogoFallback = asset('public/brand/proaslift-logo.png');
        @endphp

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-900">
            <!-- Background Animation -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

            <!-- Floating Elements -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-primary-500/10 rounded-full blur-xl animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-accent-gold/10 rounded-full blur-xl animate-pulse delay-1000"></div>
            <div class="absolute top-1/2 left-10 w-24 h-24 bg-accent-green/10 rounded-full blur-xl animate-pulse delay-500"></div>

            <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
                <!-- Dil Seçici -->


                <div class="w-full max-w-md">
                    <!-- Logo/Header -->
                    <div class="text-center mb-8">
                        <div class="mx-auto max-w-[220px]">
                            <div class="mb-4 mx-auto transform hover:scale-[1.02] transition-transform duration-300">
                                <img
                                    src="{{ $brandLogo }}"
                                    alt="Proaslift"
                                    onerror="this.onerror=null;this.src='{{ $brandLogoFallback }}';"
                                    class="w-24 sm:w-28 h-auto mx-auto drop-shadow-2xl"
                                >
                            </div>
                        </div>
                        <h1 class="text-2xl font-bold text-white tracking-tight">Proaslift</h1>
                        <p class="mt-2 text-sm text-gray-300">Harmanşah Yazılım</p>
                        <div class="w-16 h-1 bg-gradient-to-r from-primary-500 to-accent-gold rounded-full mx-auto mt-4"></div>
                    </div>

                    <!-- Login Card -->
                    <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 hover:shadow-3xl transition-all duration-500">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Hoş Geldiniz</h2>
                            <p class="text-gray-600 text-center text-sm">Hesabınıza giriş yapın</p>
                        </div>
                        {{ $slot }}
                    </div>

                    <!-- Bottom Banner -->
                    <div class="mt-6 text-center">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl px-5 py-4 border border-white/20 shadow-xl">
                            <p class="text-sm text-gray-200 font-medium">Asansor bakim ve yonetim operasyonlarinizi tek merkezden yonetin.</p>
                            <p class="text-xs text-gray-400 mt-2">Harmanşah Yazılım altyapısı ile güvenli ve hızlı erişim.</p>
                        </div>
                    </div>

                    <!-- Copyright -->
                    <div class="mt-6 text-center">
                        <p class="text-gray-400 text-xs">© 2025 Harmanşah Yazılım - Tüm Hakları Saklıdır</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
