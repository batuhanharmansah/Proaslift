<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Erişim Engellendi - Harmanşah Yazılım</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-red-900 to-slate-900 font-sans antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

    <!-- Floating Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-red-500/10 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-48 h-48 bg-pink-500/10 rounded-full blur-xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-10 w-24 h-24 bg-red-600/10 rounded-full blur-xl animate-pulse delay-500"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="relative inline-block mb-8">
                    <div class="w-24 h-24 bg-gradient-to-r from-red-500 to-red-600 rounded-3xl shadow-2xl flex items-center justify-center mx-auto transform hover:scale-105 transition-transform duration-300">
                        <!-- Shield X Icon -->
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-pink-500 rounded-full animate-ping"></div>
                </div>

                <h1 class="text-6xl font-bold text-white mb-4 tracking-tight">403</h1>
                <h2 class="text-2xl font-semibold text-gray-300 mb-2">Erişim Engellendi</h2>
                <div class="w-32 h-1 bg-gradient-to-r from-red-500 to-pink-500 rounded-full mx-auto"></div>
            </div>

            <!-- Main Content -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Bu Sayfaya Erişim Yetkiniz Yok</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        İstediğiniz sayfaya erişim için gerekli yetkiniz bulunmamaktadır.
                    </p>
                </div>

                <!-- Possible Reasons -->
                <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 rounded-r-xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-red-800 mb-2">Olası Nedenler:</h4>
                            <ul class="text-red-700 space-y-2">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Bu sayfa sadece belirli kullanıcı rolleri için erişilebilir</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Oturum süreniz dolmuş olabilir</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Hesabınıza uygun yetkiler atanmamış</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Firma yetkilendirmesi gerekiyor</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User Roles Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Kullanıcı Rolleri
                    </h4>
                    <div class="grid md:grid-cols-3 gap-4 text-blue-700">
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                                <h5 class="font-semibold">Super Admin</h5>
                            </div>
                            <p class="text-sm text-gray-600">Tüm sistem erişimi</p>
                        </div>

                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h5 class="font-semibold">Firma Yöneticisi</h5>
                            </div>
                            <p class="text-sm text-gray-600">Firma yönetim paneli</p>
                        </div>

                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h5 class="font-semibold">Personel</h5>
                            </div>
                            <p class="text-sm text-gray-600">Saha işlemleri</p>
                        </div>
                    </div>
                </div>

                <!-- Solution Steps -->
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Çözüm Önerileri
                    </h4>
                    <div class="space-y-3 text-green-700">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                            <p>Doğru kullanıcı hesabıyla giriş yaptığınızdan emin olun</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                            <p>Sistem yöneticinizden gerekli yetkileri talep edin</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                            <p>Oturumunuzu kapatıp tekrar giriş yapmayı deneyin</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</span>
                            <p>Sorun devam ederse teknik destek ile iletişime geçin</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Teknik Destek</h4>
                            <p class="text-gray-600 text-sm">Yetkilendirme sorunları için</p>
                            <a href="mailto:destek@harmansah.com" class="text-primary-600 hover:text-primary-700 font-medium">destek@harmansah.com</a>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Giriş Sayfası
                    </a>

                    @auth
                    <a href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.dashboard') : (auth()->user()->isEmployee() ? route('employee.dashboard') : route('dashboard')) }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Panelime Dön
                    </a>
                    @endauth
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
