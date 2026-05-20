clear<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\AccountType;
use App\Models\Building;
use App\Models\Employee;
use App\Models\BuildingFinancialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FinancialTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::where('company_id', auth()->user()->company_id)
            ->with(['fromAccount', 'toAccount', 'building', 'employee', 'createdBy']);

        // Filtreler
        if ($request->has('transaction_type') && $request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        if ($request->has('account_id') && $request->account_id) {
            $query->where(function($q) use ($request) {
                $q->where('from_account_id', $request->account_id)
                  ->orWhere('to_account_id', $request->account_id);
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // İstatistikler
        $currentMonth = now()->format('Y-m');
        $companyId = auth()->user()->company_id;

        $stats = [
            'monthly_income' => FinancialTransaction::where('company_id', $companyId)
                ->where('transaction_type', 'gelir')
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
                ->sum('total_amount'),
            'monthly_expense' => FinancialTransaction::where('company_id', $companyId)
                ->where('transaction_type', 'gider')
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
                ->sum('total_amount'),
            'total_balance' => AccountType::where('company_id', $companyId)
                ->where('is_active', true)
                ->sum('current_balance'),
            'today_transactions' => FinancialTransaction::where('company_id', $companyId)
                ->where('transaction_date', today())
                ->count(),
        ];

        // Hesap listesi (filtre için)
        $accounts = AccountType::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        return view('financial-transactions.index', compact('transactions', 'stats', 'accounts'));
    }

    public function create()
    {
        $accounts = AccountType::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')
            ->get();

        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        return view('financial-transactions.create', compact('accounts', 'buildings', 'employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:gelir,gider,transfer,bakiye_aktarimi',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'transaction_date' => 'required|date',
            'transaction_time' => 'nullable|date_format:H:i',
            'from_account_id' => 'nullable|exists:account_types,id',
            'to_account_id' => 'nullable|exists:account_types,id',
            'building_id' => 'nullable|exists:buildings,id',
            'employee_id' => 'nullable|exists:employees,id',
            'payment_method' => 'nullable|in:nakit,banka_havalesi,kredi_karti,cek,pos,transfer',
            'invoice_number' => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['company_id'] = auth()->user()->company_id;
            $data['created_by'] = auth()->id();

            // Transfer işlemi için hesap kontrolü
            if ($request->transaction_type === 'transfer') {
                if (!$request->from_account_id || !$request->to_account_id) {
                    return redirect()->back()->with('error', 'Transfer işlemi için kaynak ve hedef hesap seçilmelidir.');
                }
                if ($request->from_account_id === $request->to_account_id) {
                    return redirect()->back()->with('error', 'Kaynak ve hedef hesap aynı olamaz.');
                }
            }

            $transaction = FinancialTransaction::create($data);

            // Hesap bakiyelerini güncelle
            $this->updateAccountBalances($transaction);

            // Bina geliri ise bina finansal kaydını güncelle
            if ($request->category === 'bina_geliri' && $request->building_id) {
                $this->updateBuildingFinancialRecord($request->building_id, $request->amount);
            }

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'İşlem başarıyla kaydedildi.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'İşlem sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    public function show(FinancialTransaction $transaction)
    {
        if ($transaction->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $transaction->load(['fromAccount', 'toAccount', 'building', 'employee', 'createdBy']);

        return view('financial-transactions.show', compact('transaction'));
    }

    public function edit(FinancialTransaction $transaction)
    {
        if ($transaction->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $accounts = AccountType::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')
            ->get();

        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        return view('financial-transactions.edit', compact('transaction', 'accounts', 'buildings', 'employees'));
    }

    public function update(Request $request, FinancialTransaction $transaction)
    {
        if ($transaction->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:gelir,gider,transfer,bakiye_aktarimi',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'transaction_date' => 'required|date',
            'transaction_time' => 'nullable|date_format:H:i',
            'from_account_id' => 'nullable|exists:account_types,id',
            'to_account_id' => 'nullable|exists:account_types,id',
            'building_id' => 'nullable|exists:buildings,id',
            'employee_id' => 'nullable|exists:employees,id',
            'payment_method' => 'nullable|in:nakit,banka_havalesi,kredi_karti,cek,pos,transfer',
            'invoice_number' => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Eski bakiyeleri geri al
            $this->reverseAccountBalances($transaction);

            $transaction->update($request->all());

            // Yeni bakiyeleri güncelle
            $this->updateAccountBalances($transaction);

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'İşlem güncellendi.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Güncelleme sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy(FinancialTransaction $transaction)
    {
        if ($transaction->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Bakiyeleri geri al
            $this->reverseAccountBalances($transaction);

            $transaction->delete();

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'İşlem silindi.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Silme sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    private function updateAccountBalances($transaction)
    {
        switch ($transaction->transaction_type) {
            case 'gelir':
                if ($transaction->to_account_id) {
                    $account = AccountType::find($transaction->to_account_id);
                    $account->updateBalance($transaction->total_amount, 'add');
                }
                break;

            case 'gider':
                if ($transaction->from_account_id) {
                    $account = AccountType::find($transaction->from_account_id);
                    $account->updateBalance($transaction->total_amount, 'subtract');
                }
                break;

            case 'transfer':
                if ($transaction->from_account_id) {
                    $fromAccount = AccountType::find($transaction->from_account_id);
                    $fromAccount->updateBalance($transaction->total_amount, 'subtract');
                }
                if ($transaction->to_account_id) {
                    $toAccount = AccountType::find($transaction->to_account_id);
                    $toAccount->updateBalance($transaction->total_amount, 'add');
                }
                break;
        }
    }

    private function reverseAccountBalances($transaction)
    {
        switch ($transaction->transaction_type) {
            case 'gelir':
                if ($transaction->to_account_id) {
                    $account = AccountType::find($transaction->to_account_id);
                    $account->updateBalance($transaction->total_amount, 'subtract');
                }
                break;

            case 'gider':
                if ($transaction->from_account_id) {
                    $account = AccountType::find($transaction->from_account_id);
                    $account->updateBalance($transaction->total_amount, 'add');
                }
                break;

            case 'transfer':
                if ($transaction->from_account_id) {
                    $fromAccount = AccountType::find($transaction->from_account_id);
                    $fromAccount->updateBalance($transaction->total_amount, 'add');
                }
                if ($transaction->to_account_id) {
                    $toAccount = AccountType::find($transaction->to_account_id);
                    $toAccount->updateBalance($transaction->total_amount, 'subtract');
                }
                break;
        }
    }

    private function updateBuildingFinancialRecord($buildingId, $amount)
    {
        $record = BuildingFinancialRecord::where('building_id', $buildingId)
            ->where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')
            ->first();

        if ($record) {
            $record->receivePayment($amount);
        }
    }
}
