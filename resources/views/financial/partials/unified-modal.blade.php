<!-- Tek "Yeni İşlem" modalı: Alacak / Borç + Tek seferlik / Düzenli (ödeme planı aynı modalda) -->
<div id="addFinancialItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-4 md:p-8 border w-full max-w-2xl shadow-2xl rounded-xl bg-white m-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Yeni İşlem Ekle</h3>
            <button type="button" onclick="closeUnifiedModal();" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="unifiedFinancialForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tür *</label>
                <select id="unifiedItemType" name="item_type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">Seçiniz...</option>
                    <option value="alacak">Alacak</option>
                    <option value="borc">Borç</option>
                </select>
            </div>

            <!-- Ortak: Başlık, Tutar -->
            <div id="unifiedCommonFields">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık *</label>
                        <input type="text" name="title" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500" placeholder="Örn: Ocak Bakım Ücreti">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tutar (₺) *</label>
                        <input type="number" name="total_amount" step="0.01" min="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500" placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Alacak alanları -->
            <div id="section-alacak" class="hidden space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme şekli *</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="alacak_plan" value="tek" class="form-radio text-primary-500" required>
                            <span class="ml-2">Tek seferlik</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="alacak_plan" value="duzenli" class="form-radio text-primary-500">
                            <span class="ml-2">Düzenli olarak devam et</span>
                        </label>
                    </div>
                </div>
                <div id="alacak-tek-fields">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina *</label>
                        <select name="building_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                            <option value="">Bina seçiniz...</option>
                            @foreach($buildings ?? [] as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vade Tarihi *</label>
                        <input type="date" name="due_date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div id="alacak-duzenli-fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bina *</label>
                        <select name="building_id_recurring" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                            <option value="">Bina seçiniz...</option>
                            @foreach($buildings ?? [] as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Borç alanları -->
            <div id="section-borc" class="hidden space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme şekli *</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="borc_plan" value="tek" class="form-radio text-primary-500" required>
                            <span class="ml-2">Tek seferlik</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="borc_plan" value="duzenli" class="form-radio text-primary-500">
                            <span class="ml-2">Düzenli olarak devam et</span>
                        </label>
                    </div>
                </div>
                <div id="borc-tek-fields">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vade Tarihi *</label>
                        <input type="date" name="due_date_borc" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
                            <option value="">Seçiniz...</option>
                            <option value="elektrik">Elektrik</option>
                            <option value="su">Su</option>
                            <option value="dogalgaz">Doğalgaz</option>
                            <option value="kira">Kira</option>
                            <option value="maas">Maaş</option>
                            <option value="vergi">Vergi</option>
                            <option value="diger">Diğer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tedarikçi</label>
                        <input type="text" name="supplier_name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500" placeholder="Tedarikçi adı">
                    </div>
                </div>
                <div id="borc-duzenli-fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select name="category_recurring" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
                            <option value="diger">Diğer</option>
                            <option value="elektrik">Elektrik</option>
                            <option value="su">Su</option>
                            <option value="dogalgaz">Doğalgaz</option>
                            <option value="kira">Kira</option>
                            <option value="maas">Maaş</option>
                            <option value="vergi">Vergi</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ödeme planı (Alacak veya Borç + Düzenli seçildiğinde) -->
            <div id="section-recurring-plan" class="hidden space-y-4 border-t border-gray-200 pt-4">
                <p class="text-sm font-medium text-gray-700">Ödeme planı</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sıklık *</label>
                        <select id="recurringFrequency" name="frequency" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="haftalik">Haftalık</option>
                            <option value="aylik">Aylık</option>
                            <option value="uc_aylik">3 Aylık</option>
                            <option value="yillik">Yıllık</option>
                        </select>
                    </div>
                    <div id="paymentDayWrap">
                        <label id="paymentDayLabel" class="block text-sm font-medium text-gray-700 mb-2">Ödeme günü (hafta) *</label>
                        <select id="paymentDaySelect" name="payment_day" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="1">Pazartesi</option>
                            <option value="2">Salı</option>
                            <option value="3">Çarşamba</option>
                            <option value="4">Perşembe</option>
                            <option value="5">Cuma</option>
                            <option value="6">Cumartesi</option>
                            <option value="7">Pazar</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç tarihi *</label>
                        <input type="date" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hesap</label>
                        <select name="account_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="">Seçiniz...</option>
                            @foreach($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500" placeholder="Opsiyonel..."></textarea>
            </div>

            <div class="flex space-x-4 pt-4">
                <button type="button" onclick="closeUnifiedModal();" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium">İptal</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600 font-medium">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeUnifiedModal() {
    document.getElementById('addFinancialItemModal').classList.add('hidden');
    document.getElementById('unifiedFinancialForm').reset();
    toggleUnifiedSections();
}

function toggleUnifiedSections() {
    var t = document.getElementById('unifiedItemType').value;
    document.getElementById('section-alacak').classList.add('hidden');
    document.getElementById('section-borc').classList.add('hidden');
    document.getElementById('section-recurring-plan').classList.add('hidden');
    document.getElementById('alacak-tek-fields').classList.remove('hidden');
    document.getElementById('alacak-duzenli-fields').classList.add('hidden');
    document.getElementById('borc-tek-fields').classList.remove('hidden');
    document.getElementById('borc-duzenli-fields').classList.add('hidden');
    if (t === 'alacak') {
        document.getElementById('section-alacak').classList.remove('hidden');
        var alacakDuzenli = document.querySelector('input[name="alacak_plan"]:checked');
        if (alacakDuzenli && alacakDuzenli.value === 'duzenli') {
            document.getElementById('alacak-tek-fields').classList.add('hidden');
            document.getElementById('alacak-duzenli-fields').classList.remove('hidden');
            document.getElementById('section-recurring-plan').classList.remove('hidden');
        }
    }
    if (t === 'borc') {
        document.getElementById('section-borc').classList.remove('hidden');
        var borcDuzenli = document.querySelector('input[name="borc_plan"]:checked');
        if (borcDuzenli && borcDuzenli.value === 'duzenli') {
            document.getElementById('borc-tek-fields').classList.add('hidden');
            document.getElementById('borc-duzenli-fields').classList.remove('hidden');
            document.getElementById('section-recurring-plan').classList.remove('hidden');
        }
    }
    togglePaymentDayLabel();
}

function togglePaymentDayLabel() {
    var freq = (document.getElementById('recurringFrequency') || {}).value;
    var wrap = document.getElementById('paymentDayWrap');
    var label = document.getElementById('paymentDayLabel');
    var sel = document.getElementById('paymentDaySelect');
    if (!wrap || !label || !sel) return;
    if (freq === 'haftalik') {
        label.textContent = 'Ödeme günü (hafta) *';
        sel.innerHTML = '<option value="1">Pazartesi</option><option value="2">Salı</option><option value="3">Çarşamba</option><option value="4">Perşembe</option><option value="5">Cuma</option><option value="6">Cumartesi</option><option value="7">Pazar</option>';
    } else {
        label.textContent = 'Ödeme günü (ayın günü) *';
        var opts = '';
        for (var d = 1; d <= 28; d++) opts += '<option value="' + d + '">' + d + '</option>';
        sel.innerHTML = opts;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('unifiedItemType').addEventListener('change', function() {
        document.querySelectorAll('input[name="alacak_plan"], input[name="borc_plan"]').forEach(function(r) { r.required = false; });
        var t = this.value;
        if (t === 'alacak') document.querySelectorAll('input[name="alacak_plan"]').forEach(function(r) { r.required = true; });
        if (t === 'borc') document.querySelectorAll('input[name="borc_plan"]').forEach(function(r) { r.required = true; });
        toggleUnifiedSections();
    });
    document.querySelectorAll('input[name="alacak_plan"], input[name="borc_plan"]').forEach(function(radio) {
        radio.addEventListener('change', toggleUnifiedSections);
    });
    var freqEl = document.getElementById('recurringFrequency');
    if (freqEl) freqEl.addEventListener('change', togglePaymentDayLabel);

    document.getElementById('unifiedFinancialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var type = document.getElementById('unifiedItemType').value;
        var form = this;
        var formData = new FormData(form);
        var url, body = {};
        var isDuzenli = false;
        if (type === 'alacak') {
            isDuzenli = formData.get('alacak_plan') === 'duzenli';
            if (!formData.get('alacak_plan')) { alert('Lütfen ödeme şeklini seçiniz (Tek seferlik / Düzenli).'); return; }
            if (isDuzenli) {
                url = '{{ route("financial.add-recurring-payment") }}';
                var dayVal = formData.get('payment_day') || '1';
                body = {
                    _token: formData.get('_token'),
                    title: formData.get('title'),
                    amount: formData.get('total_amount'),
                    type: 'gelir',
                    frequency: formData.get('frequency') || 'aylik',
                    category: 'bina_geliri',
                    start_date: formData.get('start_date'),
                    day_of_month: dayVal,
                    account_id: formData.get('account_id') || '',
                    building_id: formData.get('building_id_recurring') || '',
                    description: formData.get('description') || ''
                };
            } else {
                url = '{{ route("financial.add-receivable") }}';
                body = {
                    _token: formData.get('_token'),
                    building_id: formData.get('building_id'),
                    title: formData.get('title'),
                    total_amount: formData.get('total_amount'),
                    due_date: formData.get('due_date'),
                    description: formData.get('description'),
                    payment_type: 'tek_sefer',
                    priority: 'orta'
                };
            }
        } else if (type === 'borc') {
            isDuzenli = formData.get('borc_plan') === 'duzenli';
            if (!formData.get('borc_plan')) { alert('Lütfen ödeme şeklini seçiniz (Tek seferlik / Düzenli).'); return; }
            if (isDuzenli) {
                url = '{{ route("financial.add-recurring-payment") }}';
                var dayVal = formData.get('payment_day') || '1';
                body = {
                    _token: formData.get('_token'),
                    title: formData.get('title'),
                    amount: formData.get('total_amount'),
                    type: 'gider',
                    frequency: formData.get('frequency') || 'aylik',
                    category: formData.get('category_recurring') || 'diger',
                    start_date: formData.get('start_date'),
                    day_of_month: dayVal,
                    account_id: formData.get('account_id') || '',
                    building_id: '',
                    description: formData.get('description') || ''
                };
            } else {
                url = '{{ route("financial.add-payable") }}';
                body = {
                    _token: formData.get('_token'),
                    title: formData.get('title'),
                    total_amount: formData.get('total_amount'),
                    due_date: formData.get('due_date_borc'),
                    category: formData.get('category') || 'diger',
                    supplier_name: formData.get('supplier_name'),
                    description: formData.get('description'),
                    priority: 'orta'
                };
            }
        } else {
            alert('Lütfen tür seçiniz.');
            return;
        }

        var fd = new FormData();
        for (var k in body) {
            if (body[k] != null && body[k] !== '') fd.append(k, body[k]);
        }

        fetch(url, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    alert('Kayıt başarıyla oluşturuldu.');
                    closeUnifiedModal();
                    location.reload();
                } else {
                    alert(data.message || 'Hata oluştu.');
                }
            })
            .catch(function() { alert('Bir hata oluştu.'); });
    });
});
</script>
