@extends('layouts.app')

@section('title', 'Rota Planlayıcı - Harmanşah Yazılım')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

@php
    $isEmployee = auth()->user()->hasRole('employee');
    $buildingsRoute = $isEmployee ? route('employee.route-planner.buildings') : route('maintenance.route-planner.buildings');
    $backRoute = $isEmployee ? route('employee.maintenance.index') : route('maintenance.index');
@endphp

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bina Rota Planlayıcı</h1>
            <p class="text-gray-600 mt-1">Haritada binaları görün, en yakınları seçin, akıllı sıralayın ve Google Maps'te yol tarifini açın.</p>
        </div>
        <a href="{{ $backRoute }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            Bakım Listesine Dön
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-center">
        <select id="filter-select" class="rounded-lg border-gray-300 text-sm">
            @if($isEmployee)
                <option value="assigned_to_me">Bana Atanmış</option>
                <option value="all">Tüm Binalar</option>
                <option value="overdue">Vade Geçen</option>
            @else
                <option value="all">Tüm Binalar</option>
                <option value="assigned_to_me">Bana Atanmış</option>
                <option value="overdue">Vade Geçen</option>
                <option value="employee">Teknisyene Göre</option>
            @endif
        </select>
        @unless($isEmployee)
        <select id="employee-select" class="rounded-lg border-gray-300 text-sm hidden">
            <option value="">— Teknisyen Seçin —</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
            @endforeach
        </select>
        @endunless
        <button id="btn-load" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-900">Yükle</button>

        <span class="border-l border-gray-200 h-6 mx-2"></span>

        <button id="btn-my-location" class="bg-white border border-blue-300 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50">📍 Kendi Konumumu Kullan</button>
        <button id="btn-nearest-5" class="bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">En Yakın 5</button>
        <button id="btn-nearest-10" class="bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">En Yakın 10</button>
        <button id="btn-clear" class="bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Seçimi Temizle</button>
        <button id="btn-sort" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">✨ Akıllı Sırala</button>
        <button id="btn-directions" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">🚗 Yol Tarifi (Google Maps)</button>

        <span id="selection-count" class="text-sm text-gray-500 ml-auto">0 bina · 0 seçili</span>
    </div>
    <p class="text-xs text-gray-400 -mt-2 mb-4">İpucu: Başlangıç noktası olarak haritaya tıklayabilir veya "Kendi Konumumu Kullan" ile telefonunuzun/tarayıcınızın GPS konumunu kullanabilirsiniz.</p>

    <div id="map" class="rounded-xl border border-gray-200" style="height: 600px;"></div>

    <div id="route-list" class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-4 hidden">
        <h2 class="text-sm font-bold text-gray-500 uppercase mb-3">Seçili Rota Sırası</h2>
        <ol id="route-list-items" class="list-decimal list-inside text-sm text-gray-700 space-y-1"></ol>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const map = L.map('map').setView([39.0, 35.0], 6); // Türkiye geneli varsayılan görünüm
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    let buildings = [];
    let markers = {};
    let selectedIds = [];
    let startPoint = null; // {lat, lng}
    let startMarker = null;

    function updateSelectionCount() {
        document.getElementById('selection-count').textContent = `${buildings.length} bina · ${selectedIds.length} seçili`;
    }

    function distanceKm(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function toggleSelect(id) {
        const idx = selectedIds.indexOf(id);
        if (idx >= 0) {
            selectedIds.splice(idx, 1);
        } else {
            selectedIds.push(id);
        }
        renderMarkers();
        renderRouteList();
        updateSelectionCount();
    }

    function renderMarkers() {
        Object.values(markers).forEach(m => map.removeLayer(m));
        markers = {};

        buildings.forEach(b => {
            const isSelected = selectedIds.includes(b.id);
            const marker = L.circleMarker([b.latitude, b.longitude], {
                radius: 8,
                color: isSelected ? '#16a34a' : '#2563eb',
                fillColor: isSelected ? '#16a34a' : '#2563eb',
                fillOpacity: 0.8,
            }).addTo(map);
            marker.bindPopup(`<strong>${b.name}</strong><br>${b.address ?? ''}<br><button onclick="window.__toggleBuilding(${b.id})" style="margin-top:4px;color:#2563eb;text-decoration:underline;">${isSelected ? 'Seçimi kaldır' : 'Rotaya ekle'}</button>`);
            markers[b.id] = marker;
        });

        if (startPoint) {
            if (startMarker) map.removeLayer(startMarker);
            startMarker = L.marker([startPoint.lat, startPoint.lng], {
                icon: L.divIcon({ html: '📍', className: '', iconSize: [24, 24] }),
            }).addTo(map).bindPopup('Başlangıç Noktası');
        }
    }

    window.__toggleBuilding = toggleSelect;

    function renderRouteList() {
        const list = document.getElementById('route-list-items');
        const container = document.getElementById('route-list');
        if (selectedIds.length === 0) {
            container.classList.add('hidden');
            return;
        }
        container.classList.remove('hidden');
        list.innerHTML = selectedIds.map(id => {
            const b = buildings.find(x => x.id === id);
            return b ? `<li>${b.name} — ${b.district ?? ''}/${b.city ?? ''}</li>` : '';
        }).join('');
    }

    async function loadBuildings() {
        const filter = document.getElementById('filter-select').value;
        const employeeSelect = document.getElementById('employee-select');
        const employeeId = employeeSelect ? employeeSelect.value : '';
        const params = new URLSearchParams({ filter });
        if (filter === 'employee' && employeeId) params.set('employee_id', employeeId);

        const res = await fetch(`{{ $buildingsRoute }}?${params}`);
        const json = await res.json();
        buildings = json.data || [];
        selectedIds = [];
        renderMarkers();
        renderRouteList();
        updateSelectionCount();

        if (buildings.length > 0) {
            const bounds = L.latLngBounds(buildings.map(b => [b.latitude, b.longitude]));
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    }

    document.getElementById('filter-select').addEventListener('change', function () {
        const employeeSelect = document.getElementById('employee-select');
        if (employeeSelect) employeeSelect.classList.toggle('hidden', this.value !== 'employee');
    });

    document.getElementById('btn-load').addEventListener('click', loadBuildings);

    document.getElementById('btn-my-location').addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Tarayıcınız konum özelliğini desteklemiyor.');
            return;
        }
        const btn = this;
        btn.disabled = true;
        btn.textContent = '📍 Konum alınıyor...';
        navigator.geolocation.getCurrentPosition(
            function (position) {
                startPoint = { lat: position.coords.latitude, lng: position.coords.longitude };
                map.setView([startPoint.lat, startPoint.lng], 13);
                renderMarkers();
                btn.disabled = false;
                btn.textContent = '📍 Kendi Konumumu Kullan';
            },
            function () {
                alert('Konumunuz alınamadı. Lütfen tarayıcı konum izinlerini kontrol edin veya haritaya tıklayarak başlangıç noktası seçin.');
                btn.disabled = false;
                btn.textContent = '📍 Kendi Konumumu Kullan';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    document.getElementById('btn-clear').addEventListener('click', function () {
        selectedIds = [];
        renderMarkers();
        renderRouteList();
        updateSelectionCount();
    });

    function selectNearest(n) {
        if (!startPoint) {
            alert('Önce haritada bir başlangıç noktası seçin: haritaya tıklayın.');
            return;
        }
        const sorted = [...buildings].sort((a, b) =>
            distanceKm(startPoint.lat, startPoint.lng, a.latitude, a.longitude) -
            distanceKm(startPoint.lat, startPoint.lng, b.latitude, b.longitude)
        );
        selectedIds = sorted.slice(0, n).map(b => b.id);
        renderMarkers();
        renderRouteList();
        updateSelectionCount();
    }

    document.getElementById('btn-nearest-5').addEventListener('click', () => selectNearest(5));
    document.getElementById('btn-nearest-10').addEventListener('click', () => selectNearest(10));

    // Akıllı Sırala: basit "en yakın komşu" (nearest neighbor) rota optimizasyonu.
    // Başlangıç noktasından (veya ilk seçili binadan) her adımda en yakın kalan binayı seçer.
    document.getElementById('btn-sort').addEventListener('click', function () {
        if (selectedIds.length < 2) return;

        let remaining = selectedIds.map(id => buildings.find(b => b.id === id));
        const ordered = [];
        let currentPoint = startPoint || { lat: remaining[0].latitude, lng: remaining[0].longitude };

        while (remaining.length > 0) {
            remaining.sort((a, b) =>
                distanceKm(currentPoint.lat, currentPoint.lng, a.latitude, a.longitude) -
                distanceKm(currentPoint.lat, currentPoint.lng, b.latitude, b.longitude)
            );
            const next = remaining.shift();
            ordered.push(next);
            currentPoint = { lat: next.latitude, lng: next.longitude };
        }

        selectedIds = ordered.map(b => b.id);
        renderRouteList();
    });

    document.getElementById('btn-directions').addEventListener('click', function () {
        if (selectedIds.length === 0) {
            alert('Önce rotaya en az bir bina ekleyin.');
            return;
        }
        const points = selectedIds.map(id => {
            const b = buildings.find(x => x.id === id);
            return `${b.latitude},${b.longitude}`;
        });

        const origin = startPoint ? `${startPoint.lat},${startPoint.lng}` : points[0];
        const destination = points[points.length - 1];
        const waypoints = points.slice(startPoint ? 0 : 1, -1).join('|');

        let url = `https://www.google.com/maps/dir/?api=1&origin=${origin}&destination=${destination}`;
        if (waypoints) url += `&waypoints=${waypoints}`;

        window.open(url, '_blank');
    });

    loadBuildings(); // sayfa açılışında varsayılan filtreyle (tüm binalar) otomatik yükle

    map.on('click', function (e) {
        startPoint = { lat: e.latlng.lat, lng: e.latlng.lng };
        renderMarkers();
    });
})();
</script>
@endsection
