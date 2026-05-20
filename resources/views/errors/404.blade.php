<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Sayfa Bulunamadı - Harmanşah Yazılım</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 font-sans antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

    <!-- Floating Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-blue-500/10 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-48 h-48 bg-indigo-500/10 rounded-full blur-xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-10 w-24 h-24 bg-blue-600/10 rounded-full blur-xl animate-pulse delay-500"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="relative inline-block mb-8">
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-blue-600 rounded-3xl shadow-2xl flex items-center justify-center mx-auto transform hover:scale-105 transition-transform duration-300">
                        <!-- Search Icon -->
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-indigo-500 rounded-full animate-ping"></div>
                </div>

                <h1 class="text-6xl font-bold text-white mb-4 tracking-tight">404</h1>
                <h2 class="text-2xl font-semibold text-gray-300 mb-2">Sayfa Bulunamadı</h2>
                <div class="w-32 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full mx-auto"></div>
            </div>

            <!-- Main Content -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Aradığınız Sayfa Bulunamadı</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Üzgünüz, istediğiniz sayfa mevcut değil veya taşınmış olabilir.
                    </p>
                </div>

                <!-- Possible Reasons -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded-r-xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-blue-800 mb-2">Olası Nedenler:</h4>
                            <ul class="text-blue-700 space-y-2">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>URL yanlış yazılmış olabilir</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Sayfa başka bir adrese taşınmış</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Link eski veya geçersiz</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Sayfa geçici olarak kaldırılmış</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Hızlı Erişim
                    </h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        @auth
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Super Admin Panel</h5>
                                        <p class="text-sm text-gray-600">Sistem yönetimi</p>
                                    </div>
                                </a>

                                <a href="{{ route('super-admin.companies') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Firma Yönetimi</h5>
                                        <p class="text-sm text-gray-600">Müşteri firmaları</p>
                                    </div>
                                </a>
                            @elseif(auth()->user()->isCompanyAdmin())
                                <a href="{{ route('dashboard') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Dashboard</h5>
                                        <p class="text-sm text-gray-600">Ana sayfa</p>
                                    </div>
                                </a>

                                <a href="{{ route('employees.index') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Personel Yönetimi</h5>
                                        <p class="text-sm text-gray-600">Çalışan işlemleri</p>
                                    </div>
                                </a>
                            @elseif(auth()->user()->isEmployee())
                                <a href="{{ route('employee.dashboard') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Personel Paneli</h5>
                                        <p class="text-sm text-gray-600">Görevlerim</p>
                                    </div>
                                </a>

                                <a href="{{ route('employee.maintenance.index') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Bakım İşlerim</h5>
                                        <p class="text-sm text-gray-600">Atanan görevler</p>
                                    </div>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-gray-800">Giriş Yap</h5>
                                    <p class="text-sm text-gray-600">Sisteme giriş</p>
                                </div>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Search Suggestion -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Arama Önerileri
                    </h4>
                    <div class="space-y-2">
                        <p class="text-gray-600 text-sm mb-3">Aradığınız sayfayı bulmak için şu anahtar kelimeleri deneyin:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Dashboard</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Personel</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">Bakım</span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Raporlar</span>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Ayarlar</span>
                            <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm">Profil</span>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-8">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-1">Yardım Gerekiyor mu?</h4>
                            <p class="text-yellow-700 text-sm">
                                Aradığınız sayfayı bulamıyorsanız, teknik destek ekibimizle iletişime geçin.
                                Size en kısa sürede yardımcı olmaktan mutluluk duyarız.
                            </p>
                            <a href="mailto:destek@harmansah.com" class="text-yellow-600 hover:text-yellow-700 font-medium text-sm">
                                destek@harmansah.com
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('super-admin.dashboard') }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Super Admin Panel
                            </a>
                        @elseif(auth()->user()->isEmployee())
                            <a href="{{ route('employee.dashboard') }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Personel Paneli
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Giriş Yap
                        </a>
                    @endauth

                    <button onclick="history.back()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Geri Dön
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-gray-400 text-sm">© 2025 Harmanşah Yazılım - Tüm Hakları Saklıdır</p>
            </div>
        </div>
    </div>
</body>
</html>
