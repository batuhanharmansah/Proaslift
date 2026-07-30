@extends('layouts.app')

@section('title', 'Kullanım Kılavuzu - Harmanşah Yazılım')

@section('content')
<div class="p-6" x-data="guideData()">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Kullanım Kılavuzu</h1>
        <p class="text-gray-600 mt-2">Sistemin tüm özelliklerini ve nasıl kullanılacağını öğrenin</p>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text"
                   x-model="searchQuery"
                   @input="filterContent()"
                   class="block w-full pl-10 pr-3 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
                   placeholder="Kılavuzda ara... (örn: bina ekle, rapor, bakım)">
        </div>
    </div>

    <!-- Guide Content -->
    <div class="space-y-8">
        <!-- Ana Sayfa -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('ana sayfa')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v1H8V5z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Ana Sayfa (Dashboard)</h2>
                    <p class="text-gray-600">Sistemin genel durumunu ve önemli bilgileri görüntüleyin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Görüntülenen Veriler</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Hızlı İstatistikler:</strong> Toplam bina, personel, bakım işi sayıları</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bu Hafta Bakım Programı:</strong> Yaklaşan bakım işleri listesi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Son Arıza Bildirimleri:</strong> En son gelen arıza bildirimleri</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Grafikler:</strong> Aylık gelir-gider, bakım performansı</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Kullanılabilir Özellikler</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Gerçek Zamanlı Güncelleme:</strong> Sağ üstteki toggle ile açıp kapatabilirsiniz</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Widget Ayarları:</strong> Hangi kartların görüneceğini seçebilirsiniz</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Veri İşlemleri:</strong> Excel/PDF export, import, yedekleme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Hızlı Erişim:</strong> Kartlardaki butonlarla ilgili sayfalara gidebilirsiniz</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Konum Haritası -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('konum')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Konum Haritası</h2>
                    <p class="text-gray-600">Saha ekibinin canlı konumunu ve günün iş programını harita üzerinde takip edin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🗺️ Harita Görünümü</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Canlı Konum:</strong> Personelin mobil uygulama üzerinden paylaştığı anlık konum</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Günün İşleri:</strong> Bugüne planlanan bakım ve arıza işlerinin bina konumları</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İşaretleyiciler:</strong> Haritadaki bina/personel ikonlarına tıklayarak detaya gitme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Personel Filtreleme:</strong> Haritada gösterilecek çalışanları seçme</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 Kullanım İpuçları</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Konum İzni:</strong> Personelin mobil uygulamada konum paylaşımını açık tutması gerekir</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Otomatik Yenileme:</strong> Konumlar belirli aralıklarla otomatik güncellenir</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Saha Takibi:</strong> Ekibin binaya olan uzaklığını görsel olarak değerlendirme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Aktif Personel:</strong> Sadece o an çalışan personeli haritada listeleme</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Personel Yönetimi -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('personel')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Personel Yönetimi</h2>
                    <p class="text-gray-600">Firma çalışanlarını ekleyin, düzenleyin ve yönetin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Personel Listesi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Personel Bilgileri:</strong> Ad, soyad, telefon, e-posta, pozisyon</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Durum:</strong> Aktif/Pasif personel durumu</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Arama:</strong> İsim veya pozisyona göre filtreleme</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">➕ Yeni Personel Ekleme</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Kişisel Bilgiler:</strong> Ad, soyad, doğum tarihi, cinsiyet</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İletişim:</strong> Telefon, e-posta, adres bilgileri</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İş Bilgileri:</strong> Pozisyon, maaş, işe başlama tarihi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Hesap Bilgileri:</strong> Kullanıcı adı ve şifre oluşturma</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bina Yönetimi -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('bina')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Bina Yönetimi</h2>
                    <p class="text-gray-600">Bakım sözleşmesi yapılan binaları yönetin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏢 Bina Listesi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bina Bilgileri:</strong> Ad, adres, kat sayısı, asansör sayısı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Sözleşme Detayları:</strong> Başlangıç-bitiş tarihi, aylık ücret</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İletişim Kişisi:</strong> Site yöneticisi bilgileri</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Belge Yönetimi:</strong> Sözleşme, teknik çizim, diğer belgeler</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">➕ Yeni Bina Ekleme</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bina Bilgileri:</strong> Ad, adres, ilçe, şehir, kat sayısı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Asansör Bilgileri:</strong> Sayı, tip, marka, model, kurulum yılı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Sözleşme Bilgileri:</strong> Tip, aylık ücret, tarih aralığı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İletişim Kişisi:</strong> Yönetici bilgileri (opsiyonel)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Belgeler:</strong> Sözleşme, teknik çizim, diğer dosyalar</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bakım Takibi -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('bakım')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Bakım Takibi</h2>
                    <p class="text-gray-600">Planlı bakım işlerini oluşturun ve takip edin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Bakım Listesi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bakım Detayları:</strong> Tür, açıklama, planlanan tarih</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Atanan Personel:</strong> Hangi personelin yapacağı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Durum:</strong> Beklemede, Başlandı, Tamamlandı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Öncelik:</strong> Düşük, Orta, Yüksek</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">➕ Yeni Bakım Planlama</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bina Seçimi:</strong> Hangi binada yapılacak</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bakım Türü:</strong> Periyodik, Arıza, Modernizasyon</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Personel Atama:</strong> Hangi personel yapacak</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Tarih Planlama:</strong> Başlangıç ve bitiş tarihi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Öncelik Belirleme:</strong> Aciliyet seviyesi</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Asansör Etiketleme -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('etiket')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Asansör Etiketleme</h2>
                    <p class="text-gray-600">Asansörlerin etiket/mühür durumunu ve renk koduna göre süre takibini yönetin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏷️ Etiket Sistemi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Renk Kodları:</strong> Yeşil (uygun), Mavi ve Sarı (izlemede/aşamalı), Kırmızı (uygunsuz/acil)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Etiket Bilgileri:</strong> Asansör, etiketleme tarihi, geçerlilik süresi, mühür/etiket no</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bina/Asansör Eşleştirme:</strong> Her asansör için ayrı etiketleme kaydı tutma</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Durum Görünümü:</strong> Bina/asansör listesinde güncel etiket rengini görme</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">⏱️ Süre Takibi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Süre Uyarıları:</strong> Geçerlilik süresi dolmak üzere olan etiketlerin takibi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Yenileme:</strong> Süresi dolan asansör için yeni etiketleme kaydı oluşturma</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Geçmiş Kayıtlar:</strong> Önceki etiketleme/mühürleme işlemlerinin geçmişi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Riskli Asansörler:</strong> Kırmızı etiketli asansörlerin listesini görüntüleme</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Finansal Yönetim -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('finansal')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Finansal Yönetim</h2>
                    <p class="text-gray-600">Gelir, gider ve hesap yönetimini yapın</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Ana Özellikler</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Hızlı İşlem:</strong> Gelir, gider veya transfer ekleme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Hesap Yönetimi:</strong> Kasa, banka, nakit hesapları</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İşlem Geçmişi:</strong> Tüm finansal hareketler</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Düzenli Ödemeler:</strong> Tekrarlayan gelir/gider tanımlama</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Alacaklar, Borçlar ve Düzenli Ödemeler:</strong> Tek sayfada tüm işlemler; Yeni İşlem ile tek seferlik veya düzenli alacak/borç ekleyebilirsiniz.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Gün Sonu:</strong> Seçtiğiniz tarihteki hesap hareketlerini ve günlük gelir/gider özetini görüntüleyip Excel veya PDF olarak indirebilirsiniz.</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 İşlem Türleri</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Alacak Ekle:</strong> Gelir kaydı (sözleşme geliri, vb.)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Borç Ekle:</strong> Gider kaydı (personel maaşı, malzeme)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Transfer:</strong> Hesap arası para transferi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Düzenli Ödeme:</strong> Aylık/yıllık tekrarlayan işlemler</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Teklifler -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('teklif')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Teklifler</h2>
                    <p class="text-gray-600">Müşterilere teklif oluşturun, gönderin ve onaylananları faturaya dönüştürün</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📄 Teklif Oluşturma</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Müşteri/Bina Seçimi:</strong> Teklifin kime/hangi binaya ait olduğunu belirleme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Kalem Ekleme:</strong> Ürün/hizmet, miktar ve birim fiyat girme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Toplam Hesaplama:</strong> Ara toplam, KDV ve genel toplamın otomatik hesaplanması</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Geçerlilik Tarihi:</strong> Teklifin son geçerlilik tarihini belirleme</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📤 Gönderim ve Takip</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>PDF Olarak İndirme:</strong> Teklifi PDF çıktısı alarak paylaşma</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Paylaşım Linki:</strong> Müşteriye link ile gönderip online görüntülemesini sağlama</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Durum Takibi:</strong> Taslak, Gönderildi, Kabul Edildi, Reddedildi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Faturaya Dönüştürme:</strong> Kabul edilen teklifi tek adımda faturaya çevirme</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Raporlar -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('rapor')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Raporlar</h2>
                    <p class="text-gray-600">Detaylı analiz ve raporları görüntüleyin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Rapor Türleri</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Finansal Raporlar:</strong> Gelir-gider analizi, kategori bazlı giderler</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bakım Performans:</strong> Tamamlanan işler, ortalama süreler</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Personel Verimlilik:</strong> En iyi performans gösterenler</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Müşteri Analizi:</strong> Bina performansı, arıza türleri</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💾 Görüntüleme Özellikleri</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Ekranda İnceleme:</strong> Raporları filtreleyerek ekran üzerinde detaylı inceleyebilirsiniz</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Tarih Filtresi:</strong> Belirli tarih aralığında rapor alma</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Grafik Görünümü:</strong> Verileri görsel olarak analiz etme</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Arıza Bildirimi -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('arıza')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Arıza Bildirimi</h2>
                    <p class="text-gray-600">Gelen arıza bildirimlerini yönetin ve takip edin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Arıza Listesi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Arıza Detayları:</strong> Tür, açıklama, bildiren kişi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bina Bilgisi:</strong> Hangi binada arıza var</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Öncelik:</strong> Aciliyet seviyesi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Durum:</strong> Yeni, Atandı, Çalışılıyor, Tamamlandı</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Hızlı İşlemler</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Personel Atama:</strong> Arızayı çözecek personeli seçme</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>İş Başlatma:</strong> Arıza çözüm sürecini başlatma</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Bakım Oluşturma:</strong> Arızadan bakım işi oluşturma</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Tamamlama:</strong> Arıza çözümünü tamamlama</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Depo -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8" x-show="isVisible('ürün')">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Depo</h2>
                    <p class="text-gray-600">Asansör parçaları ve malzemeleri yönetin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Ürün Listesi</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Ürün Bilgileri:</strong> Ad, açıklama, kategori</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Fiyat Bilgisi:</strong> Satış fiyatı, maliyet</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Stok Durumu:</strong> Mevcut miktar</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Marka:</strong> Ürün markası</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">➕ Yeni Ürün Ekleme</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Temel Bilgiler:</strong> Ürün adı, açıklama, kategori</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Fiyatlandırma:</strong> Satış fiyatı, maliyet fiyatı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Stok Yönetimi:</strong> Başlangıç miktarı</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <span><strong>Marka:</strong> Ürün markası tanımlama</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function guideData() {
    return {
        searchQuery: '',
        originalContent: document.querySelectorAll('[x-show]'),

        filterContent() {
            const query = this.searchQuery.toLowerCase();

            if (query === '') {
                // Tüm içeriği göster
                document.querySelectorAll('[x-show]').forEach(el => {
                    el.style.display = 'block';
                });
                return;
            }

            // İçeriği filtrele
            document.querySelectorAll('[x-show]').forEach(el => {
                const text = el.textContent.toLowerCase();
                if (text.includes(query)) {
                    el.style.display = 'block';
                } else {
                    el.style.display = 'none';
                }
            });
        },

        isVisible(keyword) {
            if (this.searchQuery === '') return true;
            return this.searchQuery.toLowerCase().includes(keyword.toLowerCase()) ||
                   keyword.toLowerCase().includes(this.searchQuery.toLowerCase());
        }
    }
}
</script>

@endsection
