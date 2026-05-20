<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Sunucu Hatası - Harmanşah Yazılım</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-gray-900 to-slate-900 font-sans antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

    <!-- Floating Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-gray-500/10 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-48 h-48 bg-slate-500/10 rounded-full blur-xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-10 w-24 h-24 bg-gray-600/10 rounded-full blur-xl animate-pulse delay-500"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="relative inline-block mb-8">
                    <div class="w-24 h-24 bg-gradient-to-r from-gray-600 to-gray-700 rounded-3xl shadow-2xl flex items-center justify-center mx-auto transform hover:scale-105 transition-transform duration-300">
                        <!-- Server Icon -->
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full animate-ping"></div>
                </div>

                <h1 class="text-6xl font-bold text-white mb-4 tracking-tight">500</h1>
                <h2 class="text-2xl font-semibold text-gray-300 mb-2">Sunucu Hatası</h2>
                <div class="w-32 h-1 bg-gradient-to-r from-gray-500 to-red-500 rounded-full mx-auto"></div>
            </div>

            <!-- Main Content -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Bir Şeyler Ters Gitti</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Sunucumuzda beklenmedik bir hata oluştu. Teknik ekibimiz durumdan haberdar edildi.
                    </p>
                </div>

                <!-- Error Details -->
                <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 rounded-r-xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-red-800 mb-2">Teknik Detaylar:</h4>
                            <ul class="text-red-700 space-y-2">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>İç sunucu hatası oluştu</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Hata otomatik olarak kayıt altına alındı</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Teknik ekip bilgilendirildi</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Hata ID: {{ uniqid() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- What to do -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ne Yapabilirsiniz?
                    </h4>
                    <div class="space-y-3 text-blue-700">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                            <p>Sayfayı yenilemeyi deneyin (F5 tuşu)</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                            <p>Birkaç dakika bekleyip tekrar deneyin</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                            <p>Tarayıcınızın önbelleğini temizleyin</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</span>
                            <p>Sorun devam ederse teknik destek ile iletişime geçin</p>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2H9z"></path>
                        </svg>
                        Sistem Durumu
                    </h4>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="flex items-center p-3 bg-white rounded-lg">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                            <div>
                                <p class="font-semibold text-green-800">Veritabanı</p>
                                <p class="text-sm text-green-600">Çalışıyor</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-white rounded-lg">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3 animate-pulse"></div>
                            <div>
                                <p class="font-semibold text-red-800">Web Sunucu</p>
                                <p class="text-sm text-red-600">Hata var</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-white rounded-lg">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                            <div>
                                <p class="font-semibold text-green-800">Güvenlik</p>
                                <p class="text-sm text-green-600">Normal</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contacts -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Acil Teknik Destek</h4>
                                <p class="text-gray-600 text-sm">Kritik sistem hataları için</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <p><strong>E-posta:</strong> <a href="mailto:acil@harmansah.com" class="text-red-600 hover:text-red-700">acil@harmansah.com</a></p>
                            <p><strong>Telefon:</strong> <a href="tel:+902121234567" class="text-red-600 hover:text-red-700">+90 (212) 123 45 67</a></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Genel Destek</h4>
                                <p class="text-gray-600 text-sm">Normal destek talepleri</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <p><strong>E-posta:</strong> <a href="mailto:destek@harmansah.com" class="text-blue-600 hover:text-blue-700">destek@harmansah.com</a></p>
                            <p><strong>Çalışma Saatleri:</strong> 09:00 - 18:00</p>
                        </div>
                    </div>
                </div>

                <!-- Status Updates -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-8">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-1">Durum Güncellemeleri</h4>
                            <p class="text-yellow-700 text-sm">
                                Sistem durumu hakkında anlık güncellemeler almak için teknik destek kanalımızı takip edin.
                                Kritik güncellemeler e-posta ile bildirilir.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="window.location.reload()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sayfayı Yenile
                    </button>

                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Ana Sayfaya Dön
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-gray-400 text-sm">© 2025 Harmanşah Yazılım - Tüm Hakları Saklıdır</p>
                <p class="text-gray-500 text-xs mt-1">Hata Zamanı: {{ now()->format('d.m.Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
