@extends('layouts.app')

@section('title', 'Aylık Toplu Bakım - Harmanşah Yazılım')

@section('content')
<div class="p-6" x-data="bulkMaintenance()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Aylık Toplu Bakım Oluştur</h1>
            <p class="text-gray-600 mt-1">Tüm (veya filtrelenmiş) binalar için tek seferde bakım kaydı oluşturun. Önce önizleyin, onaylamadan hiçbir kayıt oluşturulmaz.</p>
        </div>
        <a href="{{ route('maintenance.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            Bakım Listesine Dön
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        <!-- 1) Dönem -->
        <div>
            <h2 class="text-sm font-bold text-gray-500 uppercase mb-3">1) Dönem</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yıl *</label>
                    <input type="number" x-model.number="form.year" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ay *</label>
                    <select x-model.number="form.month" class="w-full rounded-lg border-gray-300">
                        <template x-for="(name, i) in months" :key="i">
                            <option :value="i + 1" x-text="name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Günü (1-28)</label>
                    <input type="number" min="1" max="28" x-model.number="form.start_day" class="w-full rounded-lg border-gray-300">
                </div>
            </div>
            <label class="flex items-center gap-2 mt-3 text-sm text-gray-700">
                <input type="checkbox" x-model="form.shift_holidays" class="rounded border-gray-300">
                Hafta sonu veya resmi tatile denk gelirse ilk iş gününe kaydır
                <span class="text-xs text-gray-400">(dini bayramlar hariç, sadece sabit tarihli resmi tatiller)</span>
            </label>
        </div>

        <!-- 2) Tarih Dağılımı -->
        <div>
            <h2 class="text-sm font-bold text-gray-500 uppercase mb-3">2) Tarih Dağılımı</h2>
            <div class="flex gap-4 mb-3">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" value="single_day" x-model="form.distribution" class="border-gray-300">
                    Hepsi aynı güne (tek gün)
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" value="spread" x-model="form.distribution" class="border-gray-300">
                    Birden fazla iş gününe yay
                </label>
            </div>
            <div x-show="form.distribution === 'spread'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kaç güne yayılsın?</label>
                <input type="number" min="1" max="28" x-model.number="form.spread_days" class="w-40 rounded-lg border-gray-300">
            </div>
        </div>

        <!-- 3) Teknisyen Ataması -->
        <div>
            <h2 class="text-sm font-bold text-gray-500 uppercase mb-3">3) Teknisyen Ataması</h2>
            <select x-model="form.assignment_strategy" class="w-full rounded-lg border-gray-300 mb-3">
                <option value="building_default">Bina varsayılan teknisyenini kullan (önerilen)</option>
                <option value="single_employee">Hepsine aynı teknisyeni ata</option>
                <option value="round_robin">Birden fazla teknisyene eşit dağıt (round-robin)</option>
                <option value="none">Atama yapma — boş bırak</option>
            </select>

            <div x-show="form.assignment_strategy === 'building_default'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Yedek Teknisyen (bina varsayılanı yoksa)</label>
                <select x-model="form.fallback_employee_id" class="w-full rounded-lg border-gray-300">
                    <option value="">— Seçilmedi —</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="form.assignment_strategy === 'single_employee'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Teknisyen</label>
                <select x-model="form.single_employee_id" class="w-full rounded-lg border-gray-300">
                    <option value="">— Seçilmedi —</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="form.assignment_strategy === 'round_robin'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Teknisyenler (çoklu seçim)</label>
                <select multiple x-model="form.round_robin_employee_ids" class="w-full rounded-lg border-gray-300" size="5">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- 4) Hedef -->
        <div>
            <h2 class="text-sm font-bold text-gray-500 uppercase mb-3">4) Hedef (opsiyonel)</h2>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" x-model="form.only_with_fee" class="rounded border-gray-300">
                Sadece bakım ücreti (monthly_fee) tanımlı binalar için kayıt oluştur
            </label>
        </div>

        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button @click="preview()" :disabled="loading" class="bg-gray-800 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-900 disabled:opacity-50">
                <span x-show="!loading">Önizle</span>
                <span x-show="loading">Yükleniyor...</span>
            </button>
            <button x-show="plan" @click="submit()" :disabled="submitting" class="bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 disabled:opacity-50">
                <span x-show="!submitting">Onayla ve Oluştur</span>
                <span x-show="submitting">Oluşturuluyor...</span>
            </button>
        </div>
    </div>

    <!-- Önizleme -->
    <div x-show="plan" x-cloak class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            Önizleme —
            <span class="text-green-700" x-text="plan?.will_create"></span> oluşturulacak,
            <span class="text-yellow-700" x-text="plan?.will_skip"></span> atlanacak
            (toplam <span x-text="plan?.total"></span> bina)
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Bina</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Tarih</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Teknisyen</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in plan?.items" :key="item.building_id">
                        <tr :class="item.skipped ? 'bg-yellow-50' : ''">
                            <td class="px-4 py-2" x-text="item.building_name"></td>
                            <td class="px-4 py-2" x-text="item.scheduled_date"></td>
                            <td class="px-4 py-2" x-text="item.assigned_employee_name || '— Atanmamış —'"></td>
                            <td class="px-4 py-2">
                                <span x-show="!item.skipped" class="text-green-700">Oluşturulacak</span>
                                <span x-show="item.skipped" class="text-yellow-700" x-text="'Atlandı: ' + item.skip_reason"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function bulkMaintenance() {
    return {
        months: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
        form: {
            year: new Date().getFullYear(),
            month: new Date().getMonth() + 1,
            start_day: 1,
            shift_holidays: true,
            distribution: 'spread',
            spread_days: 5,
            assignment_strategy: 'building_default',
            fallback_employee_id: '',
            single_employee_id: '',
            round_robin_employee_ids: [],
            only_with_fee: true,
        },
        plan: null,
        loading: false,
        submitting: false,

        async preview() {
            this.loading = true;
            this.plan = null;
            try {
                const res = await fetch('{{ route('maintenance.bulk.preview') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });
                this.plan = await res.json();
            } catch (e) {
                alert('Önizleme başarısız: ' + e);
            } finally {
                this.loading = false;
            }
        },

        async submit() {
            if (!confirm('Bakım kayıtları oluşturulacak, emin misiniz?')) return;
            this.submitting = true;
            try {
                const res = await fetch('{{ route('maintenance.bulk.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });
                if (res.redirected) {
                    window.location.href = res.url;
                }
            } catch (e) {
                alert('Oluşturma başarısız: ' + e);
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
@endsection
