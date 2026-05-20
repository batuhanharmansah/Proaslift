@extends('layouts.app')

@section('title', 'İş Takibi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">İş Takibi</h1>
        <p class="text-gray-600">Personel işlerini canlı olarak takip edin</p>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Personel</label>
                <select id="employee-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Personel</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select id="status-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Durumlar</option>
                    <option value="atandi">Atandı</option>
                    <option value="baslandi">Başlandı</option>
                    <option value="tamamlandi">Tamamlandı</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tarih</label>
                <input type="date" id="date-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div class="flex items-end">
                <button id="refresh-btn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    🔄 Yenile
                </button>
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Toplam İş</p>
                    <p class="text-2xl font-semibold text-gray-900" id="total-jobs">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Devam Eden</p>
                    <p class="text-2xl font-semibold text-gray-900" id="ongoing-jobs">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tamamlanan</p>
                    <p class="text-2xl font-semibold text-gray-900" id="completed-jobs">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Geciken</p>
                    <p class="text-2xl font-semibold text-gray-900" id="overdue-jobs">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- İş Listesi -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Aktif İşler</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bina</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İş Türü</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="jobs-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- JavaScript ile doldurulacak -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- İş Detay Modal -->
<div id="job-detail-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">İş Detayı</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modal-content">
                <!-- Modal içeriği JavaScript ile doldurulacak -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let jobsData = [];

// Sayfa yüklendiğinde verileri yükle
document.addEventListener('DOMContentLoaded', function() {
    loadJobs();
    setInterval(loadJobs, 30000); // 30 saniyede bir güncelle
});

// İşleri yükle
async function loadJobs() {
    try {
        const response = await fetch('/api/maintenance/track');
        const data = await response.json();

        if (data.success) {
            jobsData = data.data;
            updateStats();
            updateTable();
        }
    } catch (error) {
        console.error('Veri yüklenirken hata:', error);
    }
}

// İstatistikleri güncelle
function updateStats() {
    const total = jobsData.length;
    const ongoing = jobsData.filter(job => job.status === 'baslandi').length;
    const completed = jobsData.filter(job => job.status === 'tamamlandi').length;
    const overdue = jobsData.filter(job => {
        const scheduledDate = new Date(job.scheduled_date);
        const today = new Date();
        return scheduledDate < today && job.status !== 'tamamlandi';
    }).length;

    document.getElementById('total-jobs').textContent = total;
    document.getElementById('ongoing-jobs').textContent = ongoing;
    document.getElementById('completed-jobs').textContent = completed;
    document.getElementById('overdue-jobs').textContent = overdue;
}

// Tabloyu güncelle
function updateTable() {
    const tbody = document.getElementById('jobs-table-body');
    const employeeFilter = document.getElementById('employee-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    const dateFilter = document.getElementById('date-filter').value;

    let filteredJobs = jobsData;

    // Filtreleri uygula
    if (employeeFilter) {
        filteredJobs = filteredJobs.filter(job => job.employee_id == employeeFilter);
    }
    if (statusFilter) {
        filteredJobs = filteredJobs.filter(job => job.status === statusFilter);
    }
    if (dateFilter) {
        filteredJobs = filteredJobs.filter(job => job.scheduled_date === dateFilter);
    }

    tbody.innerHTML = filteredJobs.map(job => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600">
                                ${job.employee?.first_name?.charAt(0)}${job.employee?.last_name?.charAt(0)}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            ${job.employee?.first_name} ${job.employee?.last_name}
                        </div>
                        <div class="text-sm text-gray-500">${job.employee?.position}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${job.building?.name}</div>
                <div class="text-sm text-gray-500">${job.building?.address}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${getMaintenanceTypeText(job.maintenance_type)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(job.status)}">
                    ${getStatusText(job.status)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDate(job.scheduled_date)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="showJobDetail(${job.id})" class="text-blue-600 hover:text-blue-900">
                    Detay
                </button>
            </td>
        </tr>
    `).join('');
}

// Yardımcı fonksiyonlar
function getMaintenanceTypeText(type) {
    const types = {
        'rutin_bakim': 'Rutin Bakım',
        'ariza_onarim': 'Arıza Onarımı',
        'periyodik_kontrol': 'Periyodik Kontrol',
        'modernizasyon': 'Modernizasyon'
    };
    return types[type] || type;
}

function getStatusText(status) {
    const statuses = {
        'planli': 'Planlı',
        'atandi': 'Atandı',
        'baslandi': 'Başlandı',
        'tamamlandi': 'Tamamlandı',
        'ertelendi': 'Ertelendi',
        'iptal': 'İptal'
    };
    return statuses[status] || status;
}

function getStatusClass(status) {
    const classes = {
        'planli': 'bg-gray-100 text-gray-800',
        'atandi': 'bg-blue-100 text-blue-800',
        'baslandi': 'bg-yellow-100 text-yellow-800',
        'tamamlandi': 'bg-green-100 text-green-800',
        'ertelendi': 'bg-red-100 text-red-800',
        'iptal': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR');
}

// Modal işlemleri
function showJobDetail(jobId) {
    const job = jobsData.find(j => j.id === jobId);
    if (!job) return;

    document.getElementById('modal-title').textContent = `${job.building?.name} - İş Detayı`;

    document.getElementById('modal-content').innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-medium text-gray-900">İş Bilgileri</h4>
                <p class="text-gray-600">${job.description}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Personel:</span>
                    <p class="text-sm text-gray-900">${job.employee?.first_name} ${job.employee?.last_name}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Durum:</span>
                    <p class="text-sm text-gray-900">${getStatusText(job.status)}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Tarih:</span>
                    <p class="text-sm text-gray-900">${formatDate(job.scheduled_date)}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Saat:</span>
                    <p class="text-sm text-gray-900">${job.scheduled_time || 'Belirtilmemiş'}</p>
                </div>
            </div>
            ${job.notes ? `
                <div>
                    <h4 class="font-medium text-gray-900">Notlar</h4>
                    <p class="text-gray-600">${job.notes}</p>
                </div>
            ` : ''}
        </div>
    `;

    document.getElementById('job-detail-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('job-detail-modal').classList.add('hidden');
}

// Filtre değişikliklerini dinle
document.getElementById('employee-filter').addEventListener('change', updateTable);
document.getElementById('status-filter').addEventListener('change', updateTable);
document.getElementById('date-filter').addEventListener('change', updateTable);
document.getElementById('refresh-btn').addEventListener('click', loadJobs);

// Modal dışına tıklandığında kapat
document.getElementById('job-detail-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
