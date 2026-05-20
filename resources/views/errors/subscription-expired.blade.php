<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abonelik Süresi Doldu - Harmanşah Yazılım</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-orange-900 to-slate-900 font-sans antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

    <!-- Floating Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-orange-500/10 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-48 h-48 bg-yellow-500/10 rounded-full blur-xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-10 w-24 h-24 bg-orange-600/10 rounded-full blur-xl animate-pulse delay-500"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="relative inline-block mb-8">
                    <div class="w-24 h-24 bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl shadow-2xl flex items-center justify-center mx-auto transform hover:scale-105 transition-transform duration-300">
                        <!-- Clock Icon -->
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-500 rounded-full animate-ping"></div>
                </div>

                <h1 class="text-4xl font-bold text-white mb-4 tracking-tight">Abonelik Süresi Doldu</h1>
                <p class="text-xl text-gray-300 mb-2">{{ $company->name ?? 'Firmanız' }}</p>
                <div class="w-32 h-1 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-full mx-auto"></div>
            </div>

            <!-- Main Content -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">Abonelik Yenileme Gerekli</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        {{ $message ?? 'Abonelik süreniz dolmuştur. Sistemi kullanmaya devam etmek için ödemenizi yapın.' }}
                    </p>
                </div>

                <!-- Subscription Info -->
                @if(isset($company) && $company->subscription_end)
                <div class="bg-orange-50 border-l-4 border-orange-500 p-6 mb-8 rounded-r-xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 0v8m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-orange-800">Abonelik Detayları</h3>
                            <p class="text-orange-700 mt-1">
                                <strong>Son Kullanım Tarihi:</strong> {{ $company->subscription_end->format('d.m.Y H:i') }}<br>
                                @if($company->subscription_plan)
                                    <strong>Mevcut Plan:</strong> {{ $company->subscription_plan }}<br>
                                @endif
                                @if($company->monthly_fee)
                                    <strong>Aylık Ücret:</strong> ₺{{ number_format($company->monthly_fee, 2) }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Renewal Benefits -->
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Yenileme Avantajları
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4 text-green-700">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p>Kesintisiz sistem erişimi</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p>Tüm verileriniz korunur</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p>7/24 teknik destek</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p>Yeni özellik güncellemeleri</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p>Otomatik yedekleme</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p>Güvenlik güncellemeleri</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Steps -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Ödeme Adımları
                    </h3>
                    <div class="space-y-3 text-blue-700">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                            <p>Firma yöneticinizle ödeme durumunu görüşün</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                            <p>Harmanşah Yazılım ile ödeme planını belirleyin</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                            <p>Ödeme tamamlandıktan sonra sistem otomatik aktifleşir</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-800">Ödeme Desteği</h4>
                        </div>
                        <p class="text-gray-600 text-sm mb-2">Ödeme ile ilgili sorular</p>
                        <a href="mailto:odeme@harmansah.com" class="text-primary-600 hover:text-primary-700 font-medium">odeme@harmansah.com</a>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-800">Hızlı Çözüm</h4>
                        </div>
                        <p class="text-gray-600 text-sm mb-2">Acil aktivasyon için</p>
                        <a href="tel:+902121234567" class="text-green-600 hover:text-green-700 font-medium">+90 (212) 123 45 67</a>
                    </div>
                </div>

                <!-- Warning Note -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-8">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-1">Önemli Uyarı</h4>
                            <p class="text-yellow-700 text-sm">
                                Ödeme gecikirse verileriniz güvenli olarak saklanır ancak sisteme erişim kısıtlanır.
                                30 gün içinde ödeme yapılmazsa veri kaybı riski oluşabilir.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Giriş Sayfasına Dön
                    </a>
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
