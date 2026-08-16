<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\AccountingEntry; // FinancialTransaction yerine AccountingEntry
use App\Models\Building;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\RecurringPayment;
use App\Exports\DayEndExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class FinancialController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        // Hesaplar
        $accounts = AccountType::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Son işlemler
        $recentTransactions = AccountingEntry::where('company_id', $companyId)
            ->with(['building', 'accountType'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // İstatistikler
        $totalBalance = $accounts->sum('current_balance');
        $currentMonth = now('Europe/Istanbul')->format('Y-m');

        $monthlyIncome = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
            ->sum('amount');

        $monthlyExpense = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
            ->sum('amount');

        // Alacaklar ve Borçlar
        $totalReceivables = Receivable::where('company_id', $companyId)
            ->where('status', '!=', 'tamamlandi')
            ->sum('remaining_amount');

        $totalPayables = Payable::where('company_id', $companyId)
            ->where('status', '!=', 'tamamlandi')
            ->sum('remaining_amount');

        // Binalar (hızlı gelir kaydı için)
        $buildings = Building::where('company_id', $companyId)
            ->where('status', 'aktif')
            ->get();

        // Yaklaşan vadeler
        $upcomingReceivables = Receivable::where('company_id', $companyId)
            ->where('status', '!=', 'tamamlandi')
            ->where('due_date', '>=', now('Europe/Istanbul'))
            ->where('due_date', '<=', now('Europe/Istanbul')->addDays(30))
            ->with('building')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $upcomingPayables = Payable::where('company_id', $companyId)
            ->where('status', '!=', 'tamamlandi')
            ->where('due_date', '>=', now('Europe/Istanbul'))
            ->where('due_date', '<=', now('Europe/Istanbul')->addDays(30))
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('financial.index', compact(
            'accounts', 'recentTransactions', 'totalBalance', 'monthlyIncome', 'monthlyExpense', 'buildings',
            'totalReceivables', 'totalPayables', 'upcomingReceivables', 'upcomingPayables'
        ));
    }

    /**
     * Gün Sonu: Seçilen tarihteki hesap hareketlerini listeler (eski tarihler seçilebilir).
     */
    public function dayEnd(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $requestedDate = $request->get('date');
        $tz = 'Europe/Istanbul';

        if ($requestedDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
            $selectedDate = $requestedDate;
        } else {
            $selectedDate = now($tz)->format('Y-m-d');
        }

        $transactions = AccountingEntry::where('company_id', $companyId)
            ->whereDate('transaction_date', $selectedDate)
            ->with(['accountType', 'building', 'createdBy'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $dayIncome = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereDate('transaction_date', $selectedDate)
            ->sum('amount');

        $dayExpense = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->whereDate('transaction_date', $selectedDate)
            ->sum('amount');

        return view('financial.day-end', compact(
            'transactions',
            'selectedDate',
            'dayIncome',
            'dayExpense'
        ));
    }

    /**
     * Gün Sonu — Excel indir (seçilen tarih).
     */
    public function dayEndExportExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $requestedDate = $request->get('date');
        if ($requestedDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
            $selectedDate = $requestedDate;
        } else {
            $selectedDate = now('Europe/Istanbul')->format('Y-m-d');
        }
        $filename = 'gun-sonu-' . $selectedDate . '.xlsx';
        return Excel::download(new DayEndExport($companyId, $selectedDate), $filename);
    }

    /**
     * Gün Sonu — PDF indir (seçilen tarih).
     */
    public function dayEndExportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $requestedDate = $request->get('date');
        if ($requestedDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
            $selectedDate = $requestedDate;
        } else {
            $selectedDate = now('Europe/Istanbul')->format('Y-m-d');
        }
        $transactions = AccountingEntry::where('company_id', $companyId)
            ->whereDate('transaction_date', $selectedDate)
            ->with(['accountType', 'building'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
        $dayIncome = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereDate('transaction_date', $selectedDate)
            ->sum('amount');
        $dayExpense = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->whereDate('transaction_date', $selectedDate)
            ->sum('amount');
        $company = auth()->user()->company;
        $dateFormatted = \Carbon\Carbon::parse($selectedDate)->locale('tr')->translatedFormat('d F Y');
        $pdf = Pdf::loadView('financial.day-end-pdf', compact(
            'transactions', 'selectedDate', 'dayIncome', 'dayExpense', 'company', 'dateFormatted'
        ))->setPaper('a4', 'portrait');
        return $pdf->download('gun-sonu-' . $selectedDate . '.pdf');
    }

    public function history()
    {
        $companyId = auth()->user()->company_id;

        // Tüm finansal işlemleri getir
        $transactions = AccountingEntry::where('company_id', $companyId)
            ->with(['accountType', 'building', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // İstatistikler
        $totalIncome = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->sum('amount');

        $totalExpense = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->sum('amount');

        return view('financial.history', compact(
            'transactions', 'totalIncome', 'totalExpense'
        ));
    }

    public function quickTransaction(Request $request)
    {
        \Log::info('Quick Transaction Debug - Request Data:', $request->all());

        $companyId = auth()->user()->company_id;

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:gelir,gider,transfer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'account_id' => ['required', Rule::exists('account_types', 'id')->where('company_id', $companyId)],
            'target_account_id' => ['nullable', Rule::exists('account_types', 'id')->where('company_id', $companyId)],
            'building_id' => 'nullable|exists:buildings,id',
            'tag' => 'nullable|string|max:50',
        ]);

        $validator->after(function ($v) use ($request) {
            if ($request->type === 'transfer') {
                if (empty($request->target_account_id)) {
                    $v->errors()->add('target_account_id', 'Transfer işleminde hedef hesap seçilmelidir.');
                } elseif ((int) $request->target_account_id === (int) $request->account_id) {
                    $v->errors()->add('target_account_id', 'Gönderen ve alan hesap aynı olamaz.');
                }
            }
        });

        if ($validator->fails()) {
            \Log::error('Hızlı işlem doğrulaması başarısız:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            $data = [
                'company_id' => auth()->user()->company_id,
                'type' => $request->type,
                'category' => $request->category ?? 'genel', // Varsayılan kategori
                'description' => $request->description,
                'amount' => $request->amount,
                'total_amount' => $request->amount, // total_amount = amount for simple transactions
                'transaction_date' => now('Europe/Istanbul')->format('Y-m-d'),
                'created_by' => auth()->id(),
                'payment_method' => $request->payment_method ?? 'nakit', // Varsayılan ödeme yöntemi
                'tag' => $request->tag ?: null,
            ];

            \Log::info('Quick Transaction - Initial Data:', $data);

            // Hesap atamaları
            if ($request->type === 'gelir') {
                $data['account_type_id'] = $request->account_id;
            } elseif ($request->type === 'gider') {
                $data['account_type_id'] = $request->account_id;
            } elseif ($request->type === 'transfer') {
                $data['account_type_id'] = $request->account_id;
                // Transfer için ayrı bir kayıt gerekebilir
            }

            // Bina geliri ise bina ID'sini ekle (reference olarak)
            if ($request->building_id) {
                $data['reference_type'] = 'Building';
                $data['reference_id'] = $request->building_id;
            }

            \Log::info('Quick Transaction - Final Data Before Create:', $data);

            $transaction = AccountingEntry::create($data);

            \Log::info('Quick Transaction - Transaction Created:', [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'account_type_id' => $transaction->account_type_id
            ]);

            $targetAccountId = $request->type === 'transfer' ? (int) $request->target_account_id : null;
            $this->updateAccountBalances($transaction, $targetAccountId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'İşlem başarıyla kaydedildi',
                'transaction' => $transaction->load(['accountType', 'building'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Quick Transaction Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
        }
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:kasa,banka,nakit,pos',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        try {
            $account = AccountType::create([
                'company_id' => auth()->user()->company_id,
                'name' => $request->name,
                'type' => $request->type,
                'initial_balance' => $request->initial_balance,
                'current_balance' => $request->initial_balance,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hesap başarıyla oluşturuldu',
                'account' => $account
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Hesap oluşturulurken hata oluştu']);
        }
    }

    public function updateAccountBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:account_types,id',
            'new_balance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Geçersiz veri']);
        }

        try {
            $account = AccountType::find($request->account_id);

            if ($account->company_id !== auth()->user()->company_id) {
                return response()->json(['success' => false, 'message' => 'Yetkisiz işlem']);
            }

            $account->update(['current_balance' => $request->new_balance]);

            return response()->json([
                'success' => true,
                'message' => 'Bakiye güncellendi',
                'account' => $account
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Bakiye güncellenirken hata oluştu']);
        }
    }

    private function getCategoryByType($type)
    {
        $categories = [
            'gelir' => 'diger',
            'gider' => 'genel_gider',
            'transfer' => 'banka_transfer'
        ];

        return $categories[$type] ?? 'diger';
    }

    // Alacak işlemleri
    public function addReceivable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01|max:999999999.99',
            'due_date' => 'required|date|after_or_equal:today',
            'payment_type' => 'required|in:tek_sefer,taksitli',
            'installment_count' => 'required_if:payment_type,taksitli|integer|min:1|max:120',
            'priority' => 'required|in:dusuk,orta,yuksek',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        try {
            $receivable = Receivable::create([
                'company_id' => auth()->user()->company_id,
                'building_id' => $request->building_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_amount' => $request->total_amount,
                'due_date' => $request->due_date,
                'payment_type' => $request->payment_type,
                'installment_count' => $request->installment_count ?? 1,
                'priority' => $request->priority,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alacak başarıyla oluşturuldu',
                'receivable' => $receivable->load('building')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'İşlem sırasında hata oluştu']);
        }
    }

    public function receivePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receivable_id' => 'required|exists:receivables,id',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => ['required', Rule::exists('account_types', 'id')->where('company_id', auth()->user()->company_id)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            // Pessimistic locking ile race condition önleme
            $receivable = Receivable::lockForUpdate()->find($request->receivable_id);

            if ($receivable->company_id !== auth()->user()->company_id) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'Yetkisiz işlem']);
            }

            if ($receivable->remaining_amount < $request->amount) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'Ödeme tutarı kalan tutardan fazla olamaz']);
            }

            // Alacak ödemesini kaydet
            $receivable->receivePayment($request->amount);

            // Finansal işlem oluştur
            $transaction = AccountingEntry::create([
                'company_id' => auth()->user()->company_id,
                'account_type_id' => $request->account_id,
                'type' => 'gelir',
                'category' => 'genel',
                'description' => $receivable->title . ' - Ödeme',
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'transaction_date' => now('Europe/Istanbul'),
                'payment_method' => $request->payment_method ?? 'nakit',
                'created_by' => auth()->id(),
            ]);

            // Hesap bakiyesini güncelle
            $account = AccountType::find($request->account_id);
            $account->updateBalance($request->amount, 'add');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ödeme başarıyla kaydedildi',
                'receivable' => $receivable->load('building'),
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Receive Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
        }
    }

    // Borç işlemleri
    public function addPayable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'category' => 'required|in:elektrik,su,dogalgaz,internet,telefon,maas,vergi,sigorta,kira,diger',
            'priority' => 'required|in:dusuk,orta,yuksek',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        try {
            $payable = Payable::create([
                'company_id' => auth()->user()->company_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_amount' => $request->total_amount,
                'due_date' => $request->due_date,
                'category' => $request->category,
                'priority' => $request->priority,
                'invoice_number' => $request->invoice_number,
                'supplier_name' => $request->supplier_name,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Borç başarıyla oluşturuldu',
                'payable' => $payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'İşlem sırasında hata oluştu']);
        }
    }

    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payable_id' => 'required|exists:payables,id',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => ['required', Rule::exists('account_types', 'id')->where('company_id', auth()->user()->company_id)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            // Pessimistic locking ile race condition önleme
            $payable = Payable::lockForUpdate()->find($request->payable_id);

            if ($payable->company_id !== auth()->user()->company_id) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'Yetkisiz işlem']);
            }

            if ($payable->remaining_amount < $request->amount) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'Ödeme tutarı kalan tutardan fazla olamaz']);
            }

            // Borç ödemesini kaydet
            $payable->makePayment($request->amount);

            // Kategori eşleştirmesi yap
            $categoryMapping = [
                'elektrik' => 'genel_gider',
                'su' => 'genel_gider',
                'dogalgaz' => 'genel_gider',
                'internet' => 'genel_gider',
                'telefon' => 'genel_gider',
                'maas' => 'personel_maasi',
                'vergi' => 'vergi',
                'sigorta' => 'sigorta',
                'kira' => 'genel_gider',
                'diger' => 'genel_gider'
            ];

            $mappedCategory = $categoryMapping[$payable->category] ?? 'genel_gider';

            // Finansal işlem oluştur
            $transaction = AccountingEntry::create([
                'company_id' => auth()->user()->company_id,
                'account_type_id' => $request->account_id,
                'type' => 'gider',
                'category' => $mappedCategory,
                'description' => $payable->title . ' - Ödeme',
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'transaction_date' => now('Europe/Istanbul'),
                'payment_method' => $request->payment_method ?? 'nakit',
                'created_by' => auth()->id(),
            ]);

            // Hesap bakiyesini güncelle
            $account = AccountType::find($request->account_id);
            $account->updateBalance($request->amount, 'subtract');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ödeme başarıyla kaydedildi',
                'payable' => $payable,
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Make Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->only(['payable_id', 'amount', 'account_id']),
            ]);
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'İşlem sırasında hata oluştu: ' . $e->getMessage() : 'İşlem sırasında hata oluştu',
            ]);
        }
    }

    // Düzenli ödemeler
    public function addRecurringPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:gelir,gider',
            'frequency' => 'required|in:gunluk,haftalik,aylik,uc_aylik,alti_aylik,yillik',
            'start_date' => 'required|date',
            'building_id' => 'required_if:type,gelir|nullable|exists:buildings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }

        try {
            $recurringPayment = RecurringPayment::create([
                'company_id' => auth()->user()->company_id,
                'title' => $request->title,
                'description' => $request->description ?? '',
                'amount' => $request->amount,
                'type' => $request->type ?? 'gider',
                'category' => $request->category ?? 'diger',
                'frequency' => $request->frequency,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? null,
                'day_of_month' => $request->day_of_month ?? 1,
                'account_id' => $request->account_id ?? null,
                'is_active' => true,
                'building_id' => ($request->type === 'gelir' ? $request->building_id : null) ?? $request->building_id,
                'notes' => $request->notes ?? '',
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Düzenli ödeme başarıyla oluşturuldu',
                'recurringPayment' => $recurringPayment
            ]);

        } catch (\Exception $e) {
            \Log::error('Add Recurring Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
        }
    }

    public function processRecurringPayments()
    {
        $companyId = auth()->user()->company_id;
        $recurringPayments = RecurringPayment::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $processed = 0;
        foreach ($recurringPayments as $payment) {
            if ($payment->shouldCreatePayment()) {
                $payment->createPayment();
                $processed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => $processed . ' adet düzenli ödeme işlendi'
        ]);
    }

    private function updateAccountBalances($transaction, ?int $targetAccountId = null)
    {
        \Log::info('Update Account Balances Debug:', [
            'transaction_type' => $transaction->type,
            'transaction_amount' => $transaction->amount,
            'account_type_id' => $transaction->account_type_id,
            'target_account_id' => $targetAccountId,
        ]);

        try {
            // Gelir işlemi: hesaba para ekle
            if ($transaction->type === 'gelir' && $transaction->account_type_id) {
                $account = AccountType::find($transaction->account_type_id);
                \Log::info('Gelir - Hesap:', $account ? ['id' => $account->id, 'name' => $account->name, 'current_balance' => $account->current_balance] : 'Hesap bulunamadı');
                if ($account) {
                    $account->updateBalance($transaction->amount, 'add');
                    \Log::info('Gelir - Balance Updated:', ['new_balance' => $account->current_balance]);
                }
            }

            // Gider işlemi: hesaptan para çıkar
            if ($transaction->type === 'gider' && $transaction->account_type_id) {
                $account = AccountType::find($transaction->account_type_id);
                \Log::info('Gider - Hesap:', $account ? ['id' => $account->id, 'name' => $account->name, 'current_balance' => $account->current_balance] : 'Hesap bulunamadı');
                if ($account) {
                    $account->updateBalance($transaction->amount, 'subtract');
                    \Log::info('Gider - Balance Updated:', ['new_balance' => $account->current_balance]);
                }
            }

            // Transfer işlemi: kaynak hesaptan çıkar, hedef hesaba ekle
            if ($transaction->type === 'transfer') {
                if ($transaction->account_type_id) {
                    $fromAccount = AccountType::find($transaction->account_type_id);
                    \Log::info('Transfer - Kaynak hesap:', $fromAccount ? ['id' => $fromAccount->id, 'name' => $fromAccount->name, 'current_balance' => $fromAccount->current_balance] : 'Kaynak hesap bulunamadı');
                    if ($fromAccount) {
                        $fromAccount->updateBalance($transaction->amount, 'subtract');
                        \Log::info('Transfer - From Balance Updated:', ['new_balance' => $fromAccount->current_balance]);
                    }
                }
                if ($targetAccountId) {
                    $toAccount = AccountType::find($targetAccountId);
                    \Log::info('Transfer - Hedef hesap:', $toAccount ? ['id' => $toAccount->id, 'name' => $toAccount->name, 'current_balance' => $toAccount->current_balance] : 'Hedef hesap bulunamadı');
                    if ($toAccount) {
                        $toAccount->updateBalance($transaction->amount, 'add');
                        \Log::info('Transfer - To Balance Updated:', ['new_balance' => $toAccount->current_balance]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Update Account Balances Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Alacaklar sayfası
     */
    public function receivables(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Receivable::where('company_id', $companyId)
                          ->with(['building', 'createdBy']);

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tarih filtresi
        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('building', function($buildingQuery) use ($search) {
                      $buildingQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $receivables = $query->orderBy('due_date', 'asc')->paginate(20);

        // İstatistikler
        $stats = [
            'total_count' => Receivable::where('company_id', $companyId)->count(),
            'total_amount' => Receivable::where('company_id', $companyId)->sum('total_amount'),
            'total_received' => Receivable::where('company_id', $companyId)->sum('received_amount'),
            'total_remaining' => Receivable::where('company_id', $companyId)->sum('remaining_amount'),
            'overdue_count' => Receivable::where('company_id', $companyId)
                                       ->where('due_date', '<', now())
                                       ->where('status', '!=', 'tamamlandi')
                                       ->count(),
            'due_this_month' => Receivable::where('company_id', $companyId)
                                        ->whereMonth('due_date', now()->month)
                                        ->whereYear('due_date', now()->year)
                                        ->sum('remaining_amount'),
        ];

        $buildings = Building::where('company_id', $companyId)->orderBy('name')->get();
        $accounts = AccountType::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return view('financial.receivables', compact('receivables', 'stats', 'buildings', 'accounts'));
    }

    /**
     * Borçlar sayfası
     */
    public function payables(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Payable::where('company_id', $companyId)
                        ->with(['createdBy']);

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Tarih filtresi
        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        $payables = $query->orderBy('due_date', 'asc')->paginate(20);

        // İstatistikler
        $stats = [
            'total_count' => Payable::where('company_id', $companyId)->count(),
            'total_amount' => Payable::where('company_id', $companyId)->sum('total_amount'),
            'total_paid' => Payable::where('company_id', $companyId)->sum('paid_amount'),
            'total_remaining' => Payable::where('company_id', $companyId)->sum('remaining_amount'),
            'overdue_count' => Payable::where('company_id', $companyId)
                                     ->where('due_date', '<', now())
                                     ->where('status', '!=', 'tamamlandi')
                                     ->count(),
            'due_this_month' => Payable::where('company_id', $companyId)
                                      ->whereMonth('due_date', now()->month)
                                      ->whereYear('due_date', now()->year)
                                      ->sum('remaining_amount'),
        ];

        $accounts = AccountType::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return view('financial.payables', compact('payables', 'stats', 'accounts'));
    }

    /**
     * Düzenli ödemeler sayfası
     */
    public function recurringPayments(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = RecurringPayment::where('company_id', $companyId)
                                ->with(['building', 'account', 'createdBy']);

        // Durum filtresi
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        // Tür filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sıklık filtresi
        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        $recurringPayments = $query->orderBy('next_payment_date', 'asc')->paginate(20);

        // İstatistikler
        $stats = [
            'total_count' => RecurringPayment::where('company_id', $companyId)->count(),
            'active_count' => RecurringPayment::where('company_id', $companyId)->where('is_active', true)->count(),
            'total_amount' => RecurringPayment::where('company_id', $companyId)
                                             ->where('is_active', true)
                                             ->sum('amount'),
            'next_payments' => RecurringPayment::where('company_id', $companyId)
                                             ->where('is_active', true)
                                             ->where('next_payment_date', '<=', now()->addDays(7))
                                             ->count(),
        ];

        $buildings = Building::where('company_id', $companyId)->orderBy('name')->get();
        $accounts = AccountType::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return view('financial.recurring-payments', compact('recurringPayments', 'stats', 'buildings', 'accounts'));
    }

    /**
     * Birleşik sayfa: Alacaklar + Borçlar + Düzenli Ödemeler (tek liste, tek modal)
     */
    public function unifiedIndex(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $typeFilter = $request->get('type', 'all'); // all | alacak | borc | duzenli
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = 20;
        $page = $request->get('page', 1);

        $items = collect();

        // Alacaklar
        if ($typeFilter === 'all' || $typeFilter === 'alacak') {
            $rQuery = Receivable::where('company_id', $companyId)->with('building');
            if ($statusFilter) {
                $rQuery->where('status', $statusFilter);
            }
            if ($dateFrom) {
                $rQuery->where('due_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $rQuery->where('due_date', '<=', $dateTo);
            }
            if ($search) {
                $rQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('building', fn($b) => $b->where('name', 'like', "%{$search}%"));
                });
            }
            foreach ($rQuery->orderBy('due_date', 'asc')->get() as $r) {
                $items->push((object)[
                    'row_type' => 'alacak',
                    'source_model' => 'receivable',
                    'source_id' => $r->id,
                    'title' => $r->title,
                    'amount' => $r->total_amount,
                    'remaining' => $r->remaining_amount,
                    'date' => $r->due_date,
                    'status' => $r->status,
                    'status_label' => $r->status_label,
                    'building_name' => $r->building->name ?? null,
                    'category' => null,
                    'frequency' => null,
                    'raw' => $r,
                ]);
            }
        }

        // Borçlar
        if ($typeFilter === 'all' || $typeFilter === 'borc') {
            $pQuery = Payable::where('company_id', $companyId);
            if ($statusFilter) {
                $pQuery->where('status', $statusFilter);
            }
            if ($dateFrom) {
                $pQuery->where('due_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $pQuery->where('due_date', '<=', $dateTo);
            }
            if ($search) {
                $pQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('supplier_name', 'like', "%{$search}%");
                });
            }
            foreach ($pQuery->orderBy('due_date', 'asc')->get() as $p) {
                $items->push((object)[
                    'row_type' => 'borc',
                    'source_model' => 'payable',
                    'source_id' => $p->id,
                    'title' => $p->title,
                    'amount' => $p->total_amount,
                    'remaining' => $p->remaining_amount,
                    'date' => $p->due_date,
                    'status' => $p->status,
                    'status_label' => $p->status_label,
                    'building_name' => null,
                    'category' => $p->category_label ?? null,
                    'frequency' => null,
                    'raw' => $p,
                ]);
            }
        }

        // Düzenli ödemeler
        if ($typeFilter === 'all' || $typeFilter === 'duzenli') {
            $recQuery = RecurringPayment::where('company_id', $companyId)->with(['building', 'account']);
            if ($statusFilter === 'active') {
                $recQuery->where('is_active', true);
            } elseif ($statusFilter === 'inactive') {
                $recQuery->where('is_active', false);
            }
            if ($search) {
                $recQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            foreach ($recQuery->orderBy('next_payment_date', 'asc')->get() as $rec) {
                $items->push((object)[
                    'row_type' => 'duzenli',
                    'source_model' => 'recurring_payment',
                    'source_id' => $rec->id,
                    'title' => $rec->title,
                    'amount' => $rec->amount,
                    'remaining' => null,
                    'date' => $rec->next_payment_date,
                    'status' => $rec->is_active ? 'aktif' : 'pasif',
                    'status_label' => $rec->is_active ? 'Aktif' : 'Pasif',
                    'building_name' => $rec->building->name ?? null,
                    'category' => $rec->category_label ?? null,
                    'frequency' => $rec->frequency_label ?? null,
                    'raw' => $rec,
                ]);
            }
        }

        // Tarihe göre sırala (null sonra)
        $items = $items->sortBy(function ($i) {
            if (!$i->date) {
                return '9999-12-31';
            }
            return $i->date instanceof \Carbon\Carbon
                ? $i->date->format('Y-m-d')
                : (string) $i->date;
        })->values();

        $total = $items->count();
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // İstatistikler
        $stats = [
            'total_receivables' => Receivable::where('company_id', $companyId)->sum('remaining_amount'),
            'total_payables' => Payable::where('company_id', $companyId)->sum('remaining_amount'),
            'receivables_count' => Receivable::where('company_id', $companyId)->count(),
            'payables_count' => Payable::where('company_id', $companyId)->count(),
            'recurring_count' => RecurringPayment::where('company_id', $companyId)->count(),
            'recurring_active' => RecurringPayment::where('company_id', $companyId)->where('is_active', true)->count(),
        ];

        $buildings = Building::where('company_id', $companyId)->orderBy('name')->get();
        $accounts = AccountType::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return view('financial.unified', compact('paginator', 'stats', 'buildings', 'accounts'));
    }

    /**
     * Kategori kodunu Türkçe etikete çevirir (rapor sayfası için).
     */
    private function categoryLabel(?string $category): string
    {
        $labels = [
            'bina_geliri' => 'Bina Geliri',
            'bakim_geliri' => 'Bakım Geliri',
            'yedek_parca' => 'Yedek Parça',
            'personel_maas' => 'Personel Maaş',
            'yakıt' => 'Yakıt',
            'elektrik' => 'Elektrik',
            'su' => 'Su',
            'dogalgaz' => 'Doğalgaz',
            'internet' => 'İnternet',
            'telefon' => 'Telefon',
            'maas' => 'Maaş',
            'personel_maasi' => 'Personel Maaşı',
            'vergi' => 'Vergi',
            'sigorta' => 'Sigorta',
            'kira' => 'Kira',
            'genel_gider' => 'Genel Gider',
            'genel' => 'Genel',
            'diger' => 'Diğer',
        ];

        return $labels[$category] ?? ($category ?: 'Diğer');
    }

    /**
     * Finansal rapor sayfası
     */
    public function report(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $currentStart = now('Europe/Istanbul')->startOfMonth();
        $currentEnd = now('Europe/Istanbul')->endOfMonth();
        $lastStart = now('Europe/Istanbul')->subMonth()->startOfMonth();
        $lastEnd = now('Europe/Istanbul')->subMonth()->endOfMonth();

        $sumFor = function ($start, $end, $type) use ($companyId) {
            return (float) AccountingEntry::where('company_id', $companyId)
                ->where('type', $type)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('total_amount');
        };

        $currentIncome = $sumFor($currentStart, $currentEnd, 'gelir');
        $currentExpense = $sumFor($currentStart, $currentEnd, 'gider');
        $lastIncome = $sumFor($lastStart, $lastEnd, 'gelir');
        $lastExpense = $sumFor($lastStart, $lastEnd, 'gider');

        $monthlyStats = [
            'current' => [
                'income' => $currentIncome,
                'expense' => $currentExpense,
                'profit' => $currentIncome - $currentExpense,
            ],
            'last' => [
                'income' => $lastIncome,
                'expense' => $lastExpense,
                'profit' => $lastIncome - $lastExpense,
            ],
        ];

        $accounts = AccountType::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Bu ay kategori bazlı analiz
        $categoryStats = AccountingEntry::where('company_id', $companyId)
            ->whereBetween('transaction_date', [$currentStart, $currentEnd])
            ->selectRaw('type as transaction_type, category, SUM(total_amount) as total')
            ->groupBy('type', 'category')
            ->get()
            ->map(function ($row) {
                $row->category_label = $this->categoryLabel($row->category);
                return $row;
            });

        // Bu ay etiket bazlı gelir/gider kırılımı (Elevatora.com karşılaştırması Özellik E4)
        $tagStats = AccountingEntry::where('company_id', $companyId)
            ->whereBetween('transaction_date', [$currentStart, $currentEnd])
            ->whereNotNull('tag')
            ->where('tag', '!=', '')
            ->selectRaw('type as transaction_type, tag, SUM(total_amount) as total')
            ->groupBy('type', 'tag')
            ->orderByDesc('total')
            ->get();

        return view('financial.report', compact(
            'monthlyStats', 'accounts', 'categoryStats', 'tagStats'
        ));
    }

    /**
     * Kâr / Maliyet Raporu — Elevatora.com karşılaştırması Özellik E9.
     * Net satış - maliyet ayrımını, maliyet dağılımını (personel vs diğer)
     * ve son 6 ayın trendini gösteren ayrı bir sayfa.
     */
    public function karMaliyetRaporu(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $currentStart = now('Europe/Istanbul')->startOfMonth();
        $currentEnd = now('Europe/Istanbul')->endOfMonth();

        $currentIncome = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereBetween('transaction_date', [$currentStart, $currentEnd])
            ->sum('total_amount');

        $expenseQuery = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->whereBetween('transaction_date', [$currentStart, $currentEnd]);

        $currentExpenseTotal = (float) (clone $expenseQuery)->sum('total_amount');

        $personnelCost = (float) (clone $expenseQuery)
            ->whereIn('category', ['maas', 'personel_maasi'])
            ->sum('total_amount');

        $otherCost = $currentExpenseTotal - $personnelCost;

        $currentProfit = $currentIncome - $currentExpenseTotal;
        $marginPercent = $currentIncome > 0 ? round(($currentProfit / $currentIncome) * 100, 1) : 0;

        // Son 6 ay trendi
        $monthlyTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($companyId) {
            $start = now('Europe/Istanbul')->subMonths($monthsAgo)->startOfMonth();
            $end = now('Europe/Istanbul')->subMonths($monthsAgo)->endOfMonth();

            $income = (float) AccountingEntry::where('company_id', $companyId)
                ->where('type', 'gelir')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('total_amount');

            $expense = (float) AccountingEntry::where('company_id', $companyId)
                ->where('type', 'gider')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('total_amount');

            return [
                'label' => $start->translatedFormat('M Y'),
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ];
        });

        return view('financial.kar-maliyet', compact(
            'currentIncome', 'currentExpenseTotal', 'personnelCost', 'otherCost',
            'currentProfit', 'marginPercent', 'monthlyTrend'
        ));
    }

}
