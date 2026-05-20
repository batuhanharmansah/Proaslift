<!-- Tahsilat / Ödeme modal (Alacak veya Borç için tek modal) -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-4 border w-full max-w-lg shadow-lg rounded-xl bg-white m-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900" id="paymentModalTitle">Ödeme</h3>
            <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="paymentForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kalem</label>
                <p class="text-gray-900 font-medium" id="paymentTitle"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tutar *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">₺</span>
                    <input type="number" id="paymentAmount" step="0.01" min="0.01" required class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hesap *</label>
                <select id="paymentAccountId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->id }}">{{ $account->name }} (₺{{ number_format($account->current_balance, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">İptal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600" id="paymentSubmitBtn">Kaydet</button>
            </div>
        </form>
    </div>
</div>
