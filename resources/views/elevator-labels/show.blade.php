@extends('layouts.app')

@section('title', 'Etiket Detayı - ' . $elevatorLabel->building->name)

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏷️ Etiket Detayı</h1>
                <p class="text-gray-600 mt-1">{{ $elevatorLabel->building->name }} - {{ $elevatorLabel->label_color_text }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('elevator-labels.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    ← Geri Dön
                </a>
                @if($elevatorLabel->status === 'aktif')
                    <button onclick="completeLabel({{ $elevatorLabel->id }})"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        ✅ Tamamlandı İşaretle
                    </button>
                    @if($elevatorLabel->canBeSealed())
                        <button onclick="sealElevator({{ $elevatorLabel->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            🔒 Mühürle
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Ana Bilgiler -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Etiket Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Etiket Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etiket Rengi</label>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full
                                @if($elevatorLabel->label_color === 'yesil') bg-green-500
                                @elseif($elevatorLabel->label_color === 'mavi') bg-blue-500
                                @elseif($elevatorLabel->label_color === 'sari') bg-yellow-500
                                @else bg-red-500 @endif"></div>
                            <span class="text-sm text-gray-900">{{ $elevatorLabel->label_color_text }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($elevatorLabel->status === 'aktif') bg-green-100 text-green-800
                            @elseif($elevatorLabel->status === 'tamamlandi') bg-gray-100 text-gray-800
                            @elseif($elevatorLabel->status === 'muhurlendi') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $elevatorLabel->status_text }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontrol Tarihi</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->control_date->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Son Tarih</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->next_control_date->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Takip Süresi</label>
                        <p class="text-sm text-gray-900">
                            @if($elevatorLabel->control_date && $elevatorLabel->next_control_date)
                                {{ $elevatorLabel->control_date->diffInDays($elevatorLabel->next_control_date) }} gün
                            @else
                                Bilinmiyor
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kalan Gün</label>
                        @php $remainingDays = $elevatorLabel->getRemainingDays(); @endphp
                        <p class="text-sm font-medium
                            @if($remainingDays < 0) text-red-600
                            @elseif($remainingDays <= 7) text-orange-600
                            @elseif($remainingDays <= 30) text-yellow-600
                            @else text-green-600 @endif">
                            @if($remainingDays < 0)
                                {{ abs($remainingDays) }} gün geç 🚨
                            @else
                                {{ $remainingDays }} gün
                            @endif
                        </p>
                    </div>
                </div>

                @if($elevatorLabel->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $elevatorLabel->description }}</p>
                </div>
                @endif
            </div>

            <!-- Kontrol Eden Bilgileri -->
            @if($elevatorLabel->inspector_name)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Eden Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Müfettiş</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->inspector_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Firma</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->inspector_company ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lisans No</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->inspector_license ?: '-' }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Etiket Geçmişi -->
            @if($labelHistory->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Etiket Geçmişi</h3>
                <div class="space-y-4">
                    @foreach($labelHistory as $label)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full
                                @if($label->label_color === 'yesil') bg-green-500
                                @elseif($label->label_color === 'mavi') bg-blue-500
                                @elseif($label->label_color === 'sari') bg-yellow-500
                                @else bg-red-500 @endif"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $label->label_color_text }}</p>
                                <p class="text-xs text-gray-500">{{ $label->control_date->format('d.m.Y') }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $label->status_text }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sağ Panel -->
        <div class="space-y-6">
            <!-- Asansör Bilgileri -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Asansör Bilgileri</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bina Adı</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->building->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asansör Kodu</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->building->elevator_code ?: 'Kod Yok' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kapasite</label>
                        <p class="text-sm text-gray-900">
                            {{ $elevatorLabel->building->capacity_person ?: '-' }} kişi /
                            {{ $elevatorLabel->building->capacity_kg ?: '-' }} kg
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Üretici</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->building->manufacturer ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Operasyonel Durum</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($elevatorLabel->building->operational_status === 'aktif') bg-green-100 text-green-800
                            @elseif($elevatorLabel->building->operational_status === 'bakim') bg-yellow-100 text-yellow-800
                            @elseif($elevatorLabel->building->operational_status === 'arizali') bg-orange-100 text-orange-800
                            @elseif($elevatorLabel->building->operational_status === 'muhurlendi') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $elevatorLabel->building->operational_status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sorumlu Kişi -->
            @if($elevatorLabel->building->responsible_person)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sorumlu Kişi</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->building->responsible_person }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefon</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->building->responsible_phone ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">E-posta</label>
                        <p class="text-sm text-gray-900">{{ $elevatorLabel->building->responsible_email ?: '-' }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Risk Durumu -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Durumu</h3>
                <div class="space-y-3">
                    @php $riskLevel = $elevatorLabel->getRiskLevel(); @endphp
                    <div class="p-4 rounded-lg
                        @if($riskLevel === 'critical') bg-red-50 border border-red-200
                        @elseif($riskLevel === 'high') bg-orange-50 border border-orange-200
                        @elseif($riskLevel === 'medium') bg-yellow-50 border border-yellow-200
                        @else bg-green-50 border border-green-200 @endif">
                        <div class="flex items-center space-x-2">
                            @if($riskLevel === 'critical') <span class="text-red-600">🚨</span>
                            @elseif($riskLevel === 'high') <span class="text-orange-600">⚠️</span>
                            @elseif($riskLevel === 'medium') <span class="text-yellow-600">📅</span>
                            @else <span class="text-green-600">✅</span> @endif

                            <span class="text-sm font-medium
                                @if($riskLevel === 'critical') text-red-800
                                @elseif($riskLevel === 'high') text-orange-800
                                @elseif($riskLevel === 'medium') text-yellow-800
                                @else text-green-800 @endif">
                                @if($riskLevel === 'critical') Kritik - Gecikmiş
                                @elseif($riskLevel === 'high') Yüksek Risk - 7 Gün Kala
                                @elseif($riskLevel === 'medium') Orta Risk - 30 Gün Kala
                                @else Düşük Risk - Normal @endif
                            </span>
                        </div>
                    </div>

                    @if($elevatorLabel->canBeSealed())
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <span class="text-red-600">🔒</span>
                            <span class="text-sm font-medium text-red-800">Mühürleme Adayı</span>
                        </div>
                        <p class="text-xs text-red-700 mt-1">Kırmızı etiket süresi dolmuş, mühürleme işlemi gerekebilir.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sağ Panel Bilgileri -->
        <div class="space-y-6">
            <!-- Hızlı İstatistikler -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı Bilgiler</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Kaynak</span>
                        <span class="text-sm font-medium text-gray-900">{{ $elevatorLabel->source_text }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Oluşturan</span>
                        <span class="text-sm font-medium text-gray-900">{{ $elevatorLabel->creator->name ?? 'Sistem' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Oluşturma</span>
                        <span class="text-sm font-medium text-gray-900">{{ $elevatorLabel->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @if($elevatorLabel->correction_date)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Düzeltme</span>
                        <span class="text-sm font-medium text-gray-900">{{ $elevatorLabel->correction_date->format('d.m.Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bildirimler -->
            @if($elevatorLabel->notifications->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Son Bildirimler</h3>
                <div class="space-y-3">
                    @foreach($elevatorLabel->notifications->take(5) as $notification)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $notification->notification_type_text }}</p>
                            <p class="text-xs text-gray-500">{{ $notification->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if($notification->status === 'gonderildi') bg-green-100 text-green-800
                            @elseif($notification->status === 'basarisiz') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $notification->status_text }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Eskalasyonlar -->
            @if($elevatorLabel->escalations->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Eskalasyonlar</h3>
                <div class="space-y-3">
                    @foreach($elevatorLabel->escalations as $escalation)
                    <div class="p-3 border rounded-lg
                        @if($escalation->status === 'aktif') border-red-200 bg-red-50
                        @elseif($escalation->status === 'cozuldu') border-green-200 bg-green-50
                        @else border-gray-200 bg-gray-50 @endif">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900">Seviye {{ $escalation->level }}</span>
                            <span class="text-xs text-gray-500">{{ $escalation->triggered_at->format('d.m.Y') }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $escalation->level_description }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function completeLabel(labelId) {
    if (confirm('Bu etiketi tamamlandı olarak işaretlemek istediğinizden emin misiniz?')) {
        fetch(`/elevator-labels/${labelId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                description: 'Dashboard üzerinden tamamlandı olarak işaretlendi'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            alert('Bir hata oluştu');
        });
    }
}

function sealElevator(labelId) {
    const reason = prompt('Mühürleme sebebini giriniz:');
    if (reason && reason.trim().length > 10) {
        fetch(`/elevator-labels/${labelId}/seal`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                description: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            alert('Bir hata oluştu');
        });
    } else {
        alert('Lütfen en az 10 karakter uzunluğunda bir sebep giriniz.');
    }
}
</script>
@endsection
