<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\AccountingEntry;
use App\Models\Building;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\RecurringPayment;
use App\Exports\DayEndExport;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 💰 ENTERPRISE MOBILE FINANCIAL CONTROLLER
 * Kapsamlı finansal yönetim - Hesaplar, İşlemler, Alacaklar, Borçlar, Düzenli Ödemeler
 * SADECE ADMIN KULLANICILAR İÇİN
 */
class FinancialController extends Controller
{
    // ==================== DASHBOARD & SUMMARY ====================

    /**
     * 📊 Get Financial Dashboard Summary
     */
    public function getSummary(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
            $currentMonth = now('Europe/Istanbul')->format('Y-m');

            // Hesaplar
            $accounts = AccountType::where('company_id', $companyId)
                ->where('is_active', true)
                ->get();

            // İstatistikler
            $totalBalance = $accounts->sum('current_balance');

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

            // Son işlemler
            $recentTransactions = AccountingEntry::where('company_id', $companyId)
                ->with(['building:id,name', 'accountType:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'type' => $transaction->type,
                        'type_label' => $transaction->type_label,
                        'category' => $transaction->category,
                        'description' => $transaction->description,
                        'amount' => (float) $transaction->amount,
                        'total_amount' => (float) $transaction->total_amount,
                        'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                        'payment_method' => $transaction->payment_method,
                        'payment_method_label' => $transaction->payment_method_label,
                        'status' => $transaction->status,
                        'status_label' => $transaction->status_label,
                        'building' => $transaction->building ? [
                            'id' => $transaction->building->id,
                            'name' => $transaction->building->name,
                        ] : null,
                        'account_type' => $transaction->accountType ? [
                            'id' => $transaction->accountType->id,
                            'name' => $transaction->accountType->name,
                        ] : null,
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            // Yaklaşan vadeler
            $upcomingReceivables = Receivable::where('company_id', $companyId)
                ->where('status', '!=', 'tamamlandi')
                ->where('due_date', '>=', now('Europe/Istanbul'))
                ->where('due_date', '<=', now('Europe/Istanbul')->addDays(30))
                ->with('building:id,name')
                ->orderBy('due_date')
                ->limit(5)
                ->get()
                ->map(function ($receivable) {
                    return [
                        'id' => $receivable->id,
                        'title' => $receivable->title,
                        'building' => $receivable->building ? [
                            'id' => $receivable->building->id,
                            'name' => $receivable->building->name,
                        ] : null,
                        'total_amount' => (float) $receivable->total_amount,
                        'remaining_amount' => (float) $receivable->remaining_amount,
                        'due_date' => $receivable->due_date->format('Y-m-d'),
                        'status' => $receivable->status,
                        'status_label' => $receivable->status_label,
                        'priority' => $receivable->priority,
                        'priority_label' => $receivable->priority_label,
                    ];
                });

            $upcomingPayables = Payable::where('company_id', $companyId)
                ->where('status', '!=', 'tamamlandi')
                ->where('due_date', '>=', now('Europe/Istanbul'))
                ->where('due_date', '<=', now('Europe/Istanbul')->addDays(30))
                ->orderBy('due_date')
                ->limit(5)
                ->get()
                ->map(function ($payable) {
                    return [
                        'id' => $payable->id,
                        'title' => $payable->title,
                        'category' => $payable->category,
                        'category_label' => $payable->category_label,
                        'total_amount' => (float) $payable->total_amount,
                        'remaining_amount' => (float) $payable->remaining_amount,
                        'due_date' => $payable->due_date->format('Y-m-d'),
                        'status' => $payable->status,
                        'status_label' => $payable->status_label,
                        'priority' => $payable->priority,
                        'priority_label' => $payable->priority_label,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_balance' => (float) $totalBalance,
                        'monthly_income' => (float) $monthlyIncome,
                        'monthly_expense' => (float) $monthlyExpense,
                        'total_receivables' => (float) $totalReceivables,
                        'total_payables' => (float) $totalPayables,
                        'net_income' => (float) ($monthlyIncome - $monthlyExpense),
                    ],
                    'accounts_count' => $accounts->count(),
                    'recent_transactions' => $recentTransactions,
                    'upcoming_receivables' => $upcomingReceivables,
                    'upcoming_payables' => $upcomingPayables,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Financial Summary Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Finansal özet yüklenemedi: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==================== ACCOUNT TYPES (Hesap Yönetimi) ====================

    /**
     * 📋 Get Account Types List
     */
    public function getAccounts(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $accounts = AccountType::where('company_id', $companyId)
                ->where('is_active', true)
                ->orderBy('type')
                ->orderBy('name')
                ->get()
                ->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'type' => $account->type,
                        'type_label' => $account->type_label,
                        'account_number' => $account->account_number,
                        'bank_name' => $account->bank_name,
                        'branch_name' => $account->branch_name,
                        'initial_balance' => (float) $account->initial_balance,
                        'current_balance' => (float) $account->current_balance,
                        'is_active' => $account->is_active,
                        'notes' => $account->notes,
                        'created_at' => $account->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $account->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $accounts,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Accounts Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hesaplar yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Account
     */
    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:kasa,banka,nakit,pos',
            'initial_balance' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account = AccountType::create([
                'company_id' => $request->user()->company_id,
                'name' => $request->name,
                'type' => $request->type,
                'initial_balance' => $request->initial_balance,
                'current_balance' => $request->initial_balance,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'notes' => $request->notes,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hesap başarıyla oluşturuldu',
                'data' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => $account->type,
                    'type_label' => $account->type_label,
                    'current_balance' => (float) $account->current_balance,
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Create Account Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hesap oluşturulurken hata oluştu',
            ], 500);
        }
    }

    /**
     * ✏️ Update Account
     */
    public function updateAccount(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:kasa,banka,nakit,pos',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account = AccountType::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            $account->update($request->only([
                'name', 'type', 'account_number', 'bank_name', 'branch_name', 'notes', 'is_active'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Hesap güncellendi',
                'data' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => $account->type,
                    'type_label' => $account->type_label,
                    'current_balance' => (float) $account->current_balance,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hesap bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update Account Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hesap güncellenirken hata oluştu',
            ], 500);
        }
    }

    /**
     * 🔄 Update Account Balance
     */
    public function updateAccountBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:account_types,id',
            'new_balance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account = AccountType::where('company_id', $request->user()->company_id)
                ->findOrFail($request->account_id);

            $account->update(['current_balance' => $request->new_balance]);

            return response()->json([
                'success' => true,
                'message' => 'Bakiye güncellendi',
                'data' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'current_balance' => (float) $account->current_balance,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hesap bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update Account Balance Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bakiye güncellenirken hata oluştu',
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Account
     */
    public function deleteAccount(Request $request, $id)
    {
        try {
            $account = AccountType::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            // Hesaba bağlı işlem var mı kontrol et
            $hasTransactions = AccountingEntry::where('account_type_id', $id)->exists();

            if ($hasTransactions) {
                // İşlem varsa pasif yap, silme
                $account->update(['is_active' => false]);

                return response()->json([
                    'success' => true,
                    'message' => 'Hesaba bağlı işlemler olduğu için hesap pasif yapıldı',
                    'data' => [
                        'id' => $account->id,
                        'is_active' => false,
                    ],
                ]);
            }

            // İşlem yoksa sil
            $account->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hesap silindi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hesap bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete Account Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hesap silinirken hata oluştu',
            ], 500);
        }
    }

    // ==================== ACCOUNTING ENTRIES (İşlemler) ====================

    /**
     * 📅 Gün Sonu: Seçilen tarihteki işlemler + günlük gelir/gider özeti
     * GET /api/mobile/financial/day-end?date=YYYY-MM-DD
     */
    public function getDayEnd(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
            $requestedDate = $request->get('date');
            if ($requestedDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
                $selectedDate = $requestedDate;
            } else {
                $selectedDate = now('Europe/Istanbul')->format('Y-m-d');
            }

            $transactions = AccountingEntry::where('company_id', $companyId)
                ->whereDate('transaction_date', $selectedDate)
                ->with(['accountType:id,name', 'building:id,name'])
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

            $list = $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'type' => $t->type,
                    'type_label' => $t->type === 'gelir' ? 'Gelir' : ($t->type === 'gider' ? 'Gider' : 'Transfer'),
                    'description' => $t->description,
                    'amount' => (float) $t->amount,
                    'transaction_date' => $t->transaction_date ? $t->transaction_date->format('Y-m-d H:i') : null,
                    'account_type' => $t->accountType ? ['id' => $t->accountType->id, 'name' => $t->accountType->name] : null,
                    'building' => $t->building ? ['id' => $t->building->id, 'name' => $t->building->name] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'selected_date' => $selectedDate,
                    'day_income' => $dayIncome,
                    'day_expense' => $dayExpense,
                    'day_net' => $dayIncome - $dayExpense,
                    'transactions' => $list,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Mobile Day-End Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gün sonu verisi yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📋 Get Accounting Entries (Transactions) List
     */
    public function getTransactions(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $query = AccountingEntry::where('company_id', $companyId)
                ->with(['accountType:id,name,type', 'building:id,name', 'employee:id,first_name,last_name']);

            // Filters
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('account_type_id')) {
                $query->where('account_type_id', $request->account_type_id);
            }

            if ($request->filled('building_id')) {
                $query->where('building_id', $request->building_id);
            }

            if ($request->filled('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }

            // Search
            if ($request->filled('search')) {
                $search = addcslashes($request->search, '%_\\');
                $query->where(function($q) use ($search) {
                    $q->where('description', 'LIKE', "%{$search}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('notes', 'LIKE', "%{$search}%")
                      ->orWhereHas('building', function($buildingQuery) use ($search) {
                          $buildingQuery->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'transaction_date');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSorts = ['transaction_date', 'amount', 'created_at', 'type'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('transaction_date', 'desc');
            }

            // Pagination
            $perPage = min($request->get('per_page', 20), 100);
            $transactions = $query->paginate($perPage);

            // Transform data
            $transactions->getCollection()->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'type_label' => $transaction->type_label,
                    'category' => $transaction->category,
                    'description' => $transaction->description,
                    'amount' => (float) $transaction->amount,
                    'vat_rate' => $transaction->vat_rate ? (float) $transaction->vat_rate : null,
                    'vat_amount' => $transaction->vat_amount ? (float) $transaction->vat_amount : null,
                    'total_amount' => (float) $transaction->total_amount,
                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                    'payment_method' => $transaction->payment_method,
                    'payment_method_label' => $transaction->payment_method_label,
                    'status' => $transaction->status,
                    'status_label' => $transaction->status_label,
                    'invoice_number' => $transaction->invoice_number,
                    'notes' => $transaction->notes,
                    'account_type' => $transaction->accountType ? [
                        'id' => $transaction->accountType->id,
                        'name' => $transaction->accountType->name,
                        'type' => $transaction->accountType->type,
                    ] : null,
                    'building' => $transaction->building ? [
                        'id' => $transaction->building->id,
                        'name' => $transaction->building->name,
                    ] : null,
                    'employee' => $transaction->employee ? [
                        'id' => $transaction->employee->id,
                        'name' => $transaction->employee->first_name . ' ' . $transaction->employee->last_name,
                    ] : null,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $transaction->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Transactions Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'İşlemler yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📄 Get Transaction Detail
     */
    public function getTransaction(Request $request, $id)
    {
        try {
            $transaction = AccountingEntry::where('company_id', $request->user()->company_id)
                ->with(['accountType', 'building', 'employee', 'createdBy'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'type_label' => $transaction->type_label,
                    'category' => $transaction->category,
                    'description' => $transaction->description,
                    'amount' => (float) $transaction->amount,
                    'vat_rate' => $transaction->vat_rate ? (float) $transaction->vat_rate : null,
                    'vat_amount' => $transaction->vat_amount ? (float) $transaction->vat_amount : null,
                    'total_amount' => (float) $transaction->total_amount,
                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                    'payment_method' => $transaction->payment_method,
                    'payment_method_label' => $transaction->payment_method_label,
                    'status' => $transaction->status,
                    'status_label' => $transaction->status_label,
                    'invoice_number' => $transaction->invoice_number,
                    'notes' => $transaction->notes,
                    'account_type' => $transaction->accountType ? [
                        'id' => $transaction->accountType->id,
                        'name' => $transaction->accountType->name,
                        'type' => $transaction->accountType->type,
                    ] : null,
                    'building' => $transaction->building ? [
                        'id' => $transaction->building->id,
                        'name' => $transaction->building->name,
                    ] : null,
                    'employee' => $transaction->employee ? [
                        'id' => $transaction->employee->id,
                        'name' => $transaction->employee->first_name . ' ' . $transaction->employee->last_name,
                    ] : null,
                    'created_by' => $transaction->createdBy ? [
                        'id' => $transaction->createdBy->id,
                        'name' => $transaction->createdBy->name,
                    ] : null,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $transaction->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Get Transaction Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'İşlem detayı yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Transaction (Quick Transaction)
     */
    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:gelir,gider,transfer',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:account_types,id',
            'target_account_id' => 'nullable|exists:account_types,id',
            'building_id' => 'nullable|exists:buildings,id',
            'payment_method' => 'nullable|in:nakit,banka_havalesi,kredi_karti,cek',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'transaction_date' => 'nullable|date',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $companyId = $request->user()->company_id;
            $userId = $request->user()->id;

            // Building kontrolü
            if ($request->building_id) {
                $building = Building::where('company_id', $companyId)
                    ->find($request->building_id);

                if (!$building) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Bina bulunamadı veya erişim yetkiniz yok',
                    ], 403);
                }
            }

            // Account kontrolü
            $account = AccountType::where('company_id', $companyId)
                ->find($request->account_id);

            if (!$account) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Hesap bulunamadı veya erişim yetkiniz yok',
                ], 403);
            }

            // Transfer için hedef hesap kontrolü
            if ($request->type === 'transfer' && $request->target_account_id) {
                $targetAccount = AccountType::where('company_id', $companyId)
                    ->find($request->target_account_id);

                if (!$targetAccount) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Hedef hesap bulunamadı veya erişim yetkiniz yok',
                    ], 403);
                }

                if ($request->account_id === $request->target_account_id) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Kaynak ve hedef hesap aynı olamaz',
                    ], 422);
                }
            }

            // VAT hesaplama
            $vatRate = $request->vat_rate ?? 20; // Varsayılan %20
            $amount = (float) $request->amount;
            $vatAmount = $amount * ($vatRate / 100);
            $totalAmount = $amount + $vatAmount;

            // Transaction oluştur
            $data = [
                'company_id' => $companyId,
                'account_type_id' => $request->account_id,
                'type' => $request->type,
                'category' => $request->category ?? 'genel',
                'description' => $request->description,
                'amount' => $amount,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'transaction_date' => $request->transaction_date ?? now('Europe/Istanbul')->format('Y-m-d'),
                'payment_method' => $request->payment_method ?? 'nakit',
                'status' => 'odendi',
                'invoice_number' => $request->invoice_number,
                'building_id' => $request->building_id,
                'notes' => $request->notes,
                'created_by' => $userId,
            ];

            $transaction = AccountingEntry::create($data);

            // Hesap bakiyelerini güncelle
            if ($request->type === 'gelir') {
                $account->updateBalance($amount, 'add');
            } elseif ($request->type === 'gider') {
                $account->updateBalance($amount, 'subtract');
            } elseif ($request->type === 'transfer' && $request->target_account_id) {
                // Transfer: Kaynak hesaptan çıkar, hedef hesaba ekle
                $account->updateBalance($amount, 'subtract');
                $targetAccount->updateBalance($amount, 'add');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'İşlem başarıyla kaydedildi',
                'data' => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'amount' => (float) $transaction->amount,
                    'total_amount' => (float) $transaction->total_amount,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Create Transaction Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İşlem kaydedilirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✏️ Update Transaction
     */
    public function updateTransaction(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|in:gelir,gider,maas,vergi,sigorta',
            'category' => 'nullable|string|max:255',
            'description' => 'sometimes|required|string|max:500',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'account_id' => 'sometimes|required|exists:account_types,id',
            'building_id' => 'nullable|exists:buildings,id',
            'payment_method' => 'nullable|in:nakit,banka_havalesi,kredi_karti,cek',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'transaction_date' => 'nullable|date',
            'status' => 'sometimes|required|in:beklemede,odendi,tahsil_edildi,iptal',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $transaction = AccountingEntry::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            // Eski değerleri kaydet (bakiye geri alma için)
            $oldAmount = $transaction->amount;
            $oldType = $transaction->type;
            $oldAccountId = $transaction->account_type_id;

            // Güncelleme verilerini hazırla
            $updateData = $request->only([
                'type', 'category', 'description', 'account_type_id', 'building_id',
                'payment_method', 'vat_rate', 'transaction_date', 'status',
                'invoice_number', 'notes'
            ]);

            // Amount güncellendi ise
            if ($request->has('amount')) {
                $newAmount = (float) $request->amount;
                $vatRate = $updateData['vat_rate'] ?? $transaction->vat_rate ?? 20;
                $vatAmount = $newAmount * ($vatRate / 100);
                $totalAmount = $newAmount + $vatAmount;

                $updateData['amount'] = $newAmount;
                $updateData['vat_amount'] = $vatAmount;
                $updateData['total_amount'] = $totalAmount;
            }

            // Account ID güncellendi ise
            if ($request->has('account_id')) {
                $updateData['account_type_id'] = $request->account_id;
            }

            // Güncelle
            $transaction->update($updateData);

            // Bakiye güncellemesi (eğer amount, type veya account değiştiyse)
            if ($request->has('amount') || $request->has('type') || $request->has('account_id')) {
                // Eski işlemi geri al
                $oldAccount = AccountType::find($oldAccountId);
                if ($oldAccount) {
                    if ($oldType === 'gelir') {
                        $oldAccount->updateBalance($oldAmount, 'subtract');
                    } elseif ($oldType === 'gider') {
                        $oldAccount->updateBalance($oldAmount, 'add');
                    }
                }

                // Yeni işlemi uygula
                $newAmount = $request->has('amount') ? (float) $request->amount : $transaction->amount;
                $newType = $request->has('type') ? $request->type : $transaction->type;
                $newAccountId = $request->has('account_id') ? $request->account_id : $transaction->account_type_id;

                $newAccount = AccountType::where('company_id', $request->user()->company_id)
                    ->find($newAccountId);

                if ($newAccount) {
                    if ($newType === 'gelir') {
                        $newAccount->updateBalance($newAmount, 'add');
                    } elseif ($newType === 'gider') {
                        $newAccount->updateBalance($newAmount, 'subtract');
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'İşlem güncellendi',
                'data' => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'amount' => (float) $transaction->amount,
                    'total_amount' => (float) $transaction->total_amount,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update Transaction Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'İşlem güncellenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Transaction
     */
    public function deleteTransaction(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = AccountingEntry::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            // İşlemi geri al (bakiye güncellemesi)
            $account = AccountType::find($transaction->account_type_id);
            if ($account) {
                if ($transaction->type === 'gelir') {
                    $account->updateBalance($transaction->amount, 'subtract');
                } elseif ($transaction->type === 'gider') {
                    $account->updateBalance($transaction->amount, 'add');
                }
            }

            // İşlemi sil
            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'İşlem silindi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Delete Transaction Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'İşlem silinirken hata oluştu',
            ], 500);
        }
    }

    // ==================== RECEIVABLES (Alacaklar) ====================

    /**
     * 📋 Get Receivables List
     */
    public function getReceivables(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $query = Receivable::where('company_id', $companyId)
                ->with(['building:id,name', 'createdBy:id,name']);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('building_id')) {
                $query->where('building_id', $request->building_id);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('date_from')) {
                $query->where('due_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('due_date', '<=', $request->date_to);
            }

            // Search
            if ($request->filled('search')) {
                $search = addcslashes($request->search, '%_\\');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhereHas('building', function($buildingQuery) use ($search) {
                          $buildingQuery->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'due_date');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSorts = ['due_date', 'total_amount', 'remaining_amount', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 20), 100);
            $receivables = $query->paginate($perPage);

            // Transform data
            $receivables->getCollection()->transform(function ($receivable) {
                return [
                    'id' => $receivable->id,
                    'title' => $receivable->title,
                    'description' => $receivable->description,
                    'total_amount' => (float) $receivable->total_amount,
                    'received_amount' => (float) $receivable->received_amount,
                    'remaining_amount' => (float) $receivable->remaining_amount,
                    'due_date' => $receivable->due_date->format('Y-m-d'),
                    'status' => $receivable->status,
                    'status_label' => $receivable->status_label,
                    'payment_type' => $receivable->payment_type,
                    'payment_type_label' => $receivable->payment_type_label,
                    'installment_count' => $receivable->installment_count,
                    'paid_installments' => $receivable->paid_installments,
                    'installment_amount' => $receivable->installment_amount ? (float) $receivable->installment_amount : null,
                    'priority' => $receivable->priority,
                    'priority_label' => $receivable->priority_label,
                    'notes' => $receivable->notes,
                    'building' => $receivable->building ? [
                        'id' => $receivable->building->id,
                        'name' => $receivable->building->name,
                    ] : null,
                    'created_by' => $receivable->createdBy ? [
                        'id' => $receivable->createdBy->id,
                        'name' => $receivable->createdBy->name,
                    ] : null,
                    'created_at' => $receivable->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $receivable->updated_at->format('Y-m-d H:i:s'),
                    'is_overdue' => $receivable->due_date < now() && $receivable->status !== 'tamamlandi',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $receivables,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Receivables Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Alacaklar yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📄 Get Receivable Detail
     */
    public function getReceivable(Request $request, $id)
    {
        try {
            $receivable = Receivable::where('company_id', $request->user()->company_id)
                ->with(['building', 'createdBy'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $receivable->id,
                    'title' => $receivable->title,
                    'description' => $receivable->description,
                    'total_amount' => (float) $receivable->total_amount,
                    'received_amount' => (float) $receivable->received_amount,
                    'remaining_amount' => (float) $receivable->remaining_amount,
                    'due_date' => $receivable->due_date->format('Y-m-d'),
                    'status' => $receivable->status,
                    'status_label' => $receivable->status_label,
                    'payment_type' => $receivable->payment_type,
                    'payment_type_label' => $receivable->payment_type_label,
                    'installment_count' => $receivable->installment_count,
                    'paid_installments' => $receivable->paid_installments,
                    'installment_amount' => $receivable->installment_amount ? (float) $receivable->installment_amount : null,
                    'priority' => $receivable->priority,
                    'priority_label' => $receivable->priority_label,
                    'notes' => $receivable->notes,
                    'building' => $receivable->building ? [
                        'id' => $receivable->building->id,
                        'name' => $receivable->building->name,
                    ] : null,
                    'created_by' => $receivable->createdBy ? [
                        'id' => $receivable->createdBy->id,
                        'name' => $receivable->createdBy->name,
                    ] : null,
                    'created_at' => $receivable->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $receivable->updated_at->format('Y-m-d H:i:s'),
                    'is_overdue' => $receivable->due_date < now() && $receivable->status !== 'tamamlandi',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alacak bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Get Receivable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Alacak detayı yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Receivable
     */
    public function createReceivable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0.01|max:999999999.99',
            'due_date' => 'required|date|after_or_equal:today',
            'payment_type' => 'required|in:tek_sefer,taksitli',
            'installment_count' => 'required_if:payment_type,taksitli|integer|min:1|max:120',
            'priority' => 'required|in:dusuk,orta,yuksek',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Building kontrolü
            $building = Building::where('company_id', $request->user()->company_id)
                ->find($request->building_id);

            if (!$building) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bina bulunamadı veya erişim yetkiniz yok',
                ], 403);
            }

            $data = [
                'company_id' => $request->user()->company_id,
                'building_id' => $request->building_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_amount' => $request->total_amount,
                'due_date' => $request->due_date,
                'payment_type' => $request->payment_type,
                'installment_count' => $request->payment_type === 'taksitli' ? ($request->installment_count ?? 1) : 1,
                'paid_installments' => 0,
                'installment_amount' => $request->payment_type === 'taksitli' && $request->installment_count ?
                    ($request->total_amount / $request->installment_count) : null,
                'priority' => $request->priority,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
                'received_amount' => 0,
                'remaining_amount' => $request->total_amount,
                'status' => 'beklemede',
            ];

            $receivable = Receivable::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Alacak başarıyla oluşturuldu',
                'data' => [
                    'id' => $receivable->id,
                    'title' => $receivable->title,
                    'total_amount' => (float) $receivable->total_amount,
                    'remaining_amount' => (float) $receivable->remaining_amount,
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Create Receivable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Alacak oluşturulurken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✏️ Update Receivable
     */
    public function updateReceivable(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'total_amount' => 'sometimes|required|numeric|min:0.01|max:999999999.99',
            'due_date' => 'sometimes|required|date',
            'payment_type' => 'sometimes|required|in:tek_sefer,taksitli',
            'installment_count' => 'required_if:payment_type,taksitli|integer|min:1|max:120',
            'priority' => 'sometimes|required|in:dusuk,orta,yuksek',
            'status' => 'sometimes|required|in:beklemede,kismi_odendi,tamamlandi,gecikti',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $receivable = Receivable::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            $updateData = $request->only([
                'title', 'description', 'due_date', 'payment_type', 'priority', 'status', 'notes'
            ]);

            // Total amount güncellendi ise
            if ($request->has('total_amount')) {
                $newTotalAmount = (float) $request->total_amount;
                $receivedAmount = (float) $receivable->received_amount;
                $remainingAmount = $newTotalAmount - $receivedAmount;

                $updateData['total_amount'] = $newTotalAmount;
                $updateData['remaining_amount'] = max(0, $remainingAmount);

                // Durumu güncelle
                if ($remainingAmount <= 0 && $receivedAmount > 0) {
                    $updateData['status'] = 'tamamlandi';
                } elseif ($receivedAmount > 0 && $remainingAmount > 0) {
                    $updateData['status'] = 'kismi_odendi';
                }
            }

            // Installment count güncellendi ise
            if ($request->has('payment_type') || $request->has('installment_count')) {
                $paymentType = $request->get('payment_type', $receivable->payment_type);
                $installmentCount = $request->get('installment_count', $receivable->installment_count);

                if ($paymentType === 'taksitli' && $installmentCount) {
                    $totalAmount = $request->get('total_amount', $receivable->total_amount);
                    $updateData['installment_count'] = $installmentCount;
                    $updateData['installment_amount'] = $totalAmount / $installmentCount;
                } else {
                    $updateData['installment_count'] = 1;
                    $updateData['installment_amount'] = null;
                }
            }

            $receivable->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Alacak güncellendi',
                'data' => [
                    'id' => $receivable->id,
                    'title' => $receivable->title,
                    'total_amount' => (float) $receivable->total_amount,
                    'remaining_amount' => (float) $receivable->remaining_amount,
                    'status' => $receivable->status,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alacak bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update Receivable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Alacak güncellenirken hata oluştu',
            ], 500);
        }
    }

    /**
     * 💰 Receive Payment (Alacak Ödemesi)
     */
    public function receivePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receivable_id' => 'required|exists:receivables,id',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:account_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Pessimistic locking ile race condition önleme
            $receivable = Receivable::lockForUpdate()
                ->where('company_id', $request->user()->company_id)
                ->findOrFail($request->receivable_id);

            // Account kontrolü
            $account = AccountType::where('company_id', $request->user()->company_id)
                ->findOrFail($request->account_id);

            if ($receivable->remaining_amount < $request->amount) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Ödeme tutarı kalan tutardan fazla olamaz',
                ], 422);
            }

            // Alacak ödemesini kaydet
            $receivable->receivePayment($request->amount);

            // Finansal işlem oluştur
            $transaction = AccountingEntry::create([
                'company_id' => $request->user()->company_id,
                'account_type_id' => $request->account_id,
                'type' => 'gelir',
                'category' => 'bina_geliri',
                'description' => $receivable->title . ' - Ödeme',
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'transaction_date' => now('Europe/Istanbul')->format('Y-m-d'),
                'payment_method' => 'nakit',
                'status' => 'tahsil_edildi',
                'building_id' => $receivable->building_id,
                'created_by' => $request->user()->id,
            ]);

            // Hesap bakiyesini güncelle
            $account->updateBalance($request->amount, 'add');

            // Bildirim gönder
            $this->notificationService->notifyPaymentReceived($receivable, $request->amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ödeme başarıyla kaydedildi',
                'data' => [
                    'receivable' => [
                        'id' => $receivable->id,
                        'title' => $receivable->title,
                        'received_amount' => (float) $receivable->received_amount,
                        'remaining_amount' => (float) $receivable->remaining_amount,
                        'status' => $receivable->status,
                    ],
                    'transaction' => [
                        'id' => $transaction->id,
                        'amount' => (float) $transaction->amount,
                    ],
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Alacak veya hesap bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Receive Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme kaydedilirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Receivable
     */
    public function deleteReceivable(Request $request, $id)
    {
        try {
            $receivable = Receivable::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            // Eğer ödeme alınmışsa silme
            if ($receivable->received_amount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu alacağa ödeme alınmış olduğu için silinemez',
                ], 422);
            }

            $receivable->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alacak silindi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alacak bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete Receivable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Alacak silinirken hata oluştu',
            ], 500);
        }
    }

    // ==================== PAYABLES (Borçlar) ====================

    /**
     * 📋 Get Payables List
     */
    public function getPayables(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $query = Payable::where('company_id', $companyId)
                ->with(['createdBy:id,name']);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('date_from')) {
                $query->where('due_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('due_date', '<=', $request->date_to);
            }

            // Search
            if ($request->filled('search')) {
                $search = addcslashes($request->search, '%_\\');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('supplier_name', 'LIKE', "%{$search}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'due_date');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSorts = ['due_date', 'total_amount', 'remaining_amount', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 20), 100);
            $payables = $query->paginate($perPage);

            // Transform data
            $payables->getCollection()->transform(function ($payable) {
                return [
                    'id' => $payable->id,
                    'title' => $payable->title,
                    'description' => $payable->description,
                    'total_amount' => (float) $payable->total_amount,
                    'paid_amount' => (float) $payable->paid_amount,
                    'remaining_amount' => (float) $payable->remaining_amount,
                    'due_date' => $payable->due_date->format('Y-m-d'),
                    'status' => $payable->status,
                    'status_label' => $payable->status_label,
                    'category' => $payable->category,
                    'category_label' => $payable->category_label,
                    'priority' => $payable->priority,
                    'priority_label' => $payable->priority_label,
                    'invoice_number' => $payable->invoice_number,
                    'supplier_name' => $payable->supplier_name,
                    'notes' => $payable->notes,
                    'created_by' => $payable->createdBy ? [
                        'id' => $payable->createdBy->id,
                        'name' => $payable->createdBy->name,
                    ] : null,
                    'created_at' => $payable->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $payable->updated_at->format('Y-m-d H:i:s'),
                    'is_overdue' => $payable->due_date < now() && $payable->status !== 'tamamlandi',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $payables,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Payables Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Borçlar yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📄 Get Payable Detail
     */
    public function getPayable(Request $request, $id)
    {
        try {
            $payable = Payable::where('company_id', $request->user()->company_id)
                ->with(['createdBy'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $payable->id,
                    'title' => $payable->title,
                    'description' => $payable->description,
                    'total_amount' => (float) $payable->total_amount,
                    'paid_amount' => (float) $payable->paid_amount,
                    'remaining_amount' => (float) $payable->remaining_amount,
                    'due_date' => $payable->due_date->format('Y-m-d'),
                    'status' => $payable->status,
                    'status_label' => $payable->status_label,
                    'category' => $payable->category,
                    'category_label' => $payable->category_label,
                    'priority' => $payable->priority,
                    'priority_label' => $payable->priority_label,
                    'invoice_number' => $payable->invoice_number,
                    'supplier_name' => $payable->supplier_name,
                    'notes' => $payable->notes,
                    'created_by' => $payable->createdBy ? [
                        'id' => $payable->createdBy->id,
                        'name' => $payable->createdBy->name,
                    ] : null,
                    'created_at' => $payable->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $payable->updated_at->format('Y-m-d H:i:s'),
                    'is_overdue' => $payable->due_date < now() && $payable->status !== 'tamamlandi',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Borç bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Get Payable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Borç detayı yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Payable
     */
    public function createPayable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'category' => 'required|in:elektrik,su,dogalgaz,internet,telefon,maas,vergi,sigorta,kira,diger',
            'priority' => 'required|in:dusuk,orta,yuksek',
            'invoice_number' => 'nullable|string|max:100',
            'supplier_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'company_id' => $request->user()->company_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_amount' => $request->total_amount,
                'due_date' => $request->due_date,
                'category' => $request->category,
                'priority' => $request->priority,
                'invoice_number' => $request->invoice_number,
                'supplier_name' => $request->supplier_name,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
                'paid_amount' => 0,
                'remaining_amount' => $request->total_amount,
                'status' => 'beklemede',
            ];

            $payable = Payable::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Borç başarıyla oluşturuldu',
                'data' => [
                    'id' => $payable->id,
                    'title' => $payable->title,
                    'total_amount' => (float) $payable->total_amount,
                    'remaining_amount' => (float) $payable->remaining_amount,
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Create Payable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Borç oluşturulurken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✏️ Update Payable
     */
    public function updatePayable(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'total_amount' => 'sometimes|required|numeric|min:0.01',
            'due_date' => 'sometimes|required|date',
            'category' => 'sometimes|required|in:elektrik,su,dogalgaz,internet,telefon,maas,vergi,sigorta,kira,diger',
            'priority' => 'sometimes|required|in:dusuk,orta,yuksek',
            'status' => 'sometimes|required|in:beklemede,kismi_odendi,tamamlandi,gecikti',
            'invoice_number' => 'nullable|string|max:100',
            'supplier_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payable = Payable::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            $updateData = $request->only([
                'title', 'description', 'due_date', 'category', 'priority', 'status',
                'invoice_number', 'supplier_name', 'notes'
            ]);

            // Total amount güncellendi ise
            if ($request->has('total_amount')) {
                $newTotalAmount = (float) $request->total_amount;
                $paidAmount = (float) $payable->paid_amount;
                $remainingAmount = $newTotalAmount - $paidAmount;

                $updateData['total_amount'] = $newTotalAmount;
                $updateData['remaining_amount'] = max(0, $remainingAmount);

                // Durumu güncelle
                if ($remainingAmount <= 0 && $paidAmount > 0) {
                    $updateData['status'] = 'tamamlandi';
                } elseif ($paidAmount > 0 && $remainingAmount > 0) {
                    $updateData['status'] = 'kismi_odendi';
                }
            }

            $payable->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Borç güncellendi',
                'data' => [
                    'id' => $payable->id,
                    'title' => $payable->title,
                    'total_amount' => (float) $payable->total_amount,
                    'remaining_amount' => (float) $payable->remaining_amount,
                    'status' => $payable->status,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Borç bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update Payable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Borç güncellenirken hata oluştu',
            ], 500);
        }
    }

    /**
     * 💳 Make Payment (Borç Ödemesi)
     */
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payable_id' => 'required|exists:payables,id',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:account_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Pessimistic locking ile race condition önleme
            $payable = Payable::lockForUpdate()
                ->where('company_id', $request->user()->company_id)
                ->findOrFail($request->payable_id);

            // Account kontrolü
            $account = AccountType::where('company_id', $request->user()->company_id)
                ->findOrFail($request->account_id);

            if ($payable->remaining_amount < $request->amount) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Ödeme tutarı kalan tutardan fazla olamaz',
                ], 422);
            }

            // Borç ödemesini kaydet
            $payable->makePayment($request->amount);

            // Kategori eşleştirmesi
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
                'company_id' => $request->user()->company_id,
                'account_type_id' => $request->account_id,
                'type' => 'gider',
                'category' => $mappedCategory,
                'description' => $payable->title . ' - Ödeme',
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'transaction_date' => now('Europe/Istanbul')->format('Y-m-d'),
                'payment_method' => 'nakit',
                'status' => 'odendi',
                'created_by' => $request->user()->id,
            ]);

            // Hesap bakiyesini güncelle
            $account->updateBalance($request->amount, 'subtract');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ödeme başarıyla kaydedildi',
                'data' => [
                    'payable' => [
                        'id' => $payable->id,
                        'title' => $payable->title,
                        'paid_amount' => (float) $payable->paid_amount,
                        'remaining_amount' => (float) $payable->remaining_amount,
                        'status' => $payable->status,
                    ],
                    'transaction' => [
                        'id' => $transaction->id,
                        'amount' => (float) $transaction->amount,
                    ],
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Borç veya hesap bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Make Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme kaydedilirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Payable
     */
    public function deletePayable(Request $request, $id)
    {
        try {
            $payable = Payable::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            // Eğer ödeme yapılmışsa silme
            if ($payable->paid_amount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu borca ödeme yapılmış olduğu için silinemez',
                ], 422);
            }

            $payable->delete();

            return response()->json([
                'success' => true,
                'message' => 'Borç silindi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Borç bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete Payable Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Borç silinirken hata oluştu',
            ], 500);
        }
    }

    // ==================== RECURRING PAYMENTS (Düzenli Ödemeler) ====================

    /**
     * 📋 Get Recurring Payments List
     */
    public function getRecurringPayments(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $query = RecurringPayment::where('company_id', $companyId)
                ->with(['building:id,name', 'account:id,name', 'createdBy:id,name']);

            // Filters
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('frequency')) {
                $query->where('frequency', $request->frequency);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'next_payment_date');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSorts = ['next_payment_date', 'start_date', 'amount', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 20), 100);
            $recurringPayments = $query->paginate($perPage);

            // Transform data
            $recurringPayments->getCollection()->transform(function ($payment) {
                return [
                    'id' => $payment->id,
                    'title' => $payment->title,
                    'description' => $payment->description,
                    'amount' => (float) $payment->amount,
                    'type' => $payment->type,
                    'type_label' => $payment->type_label,
                    'frequency' => $payment->frequency,
                    'frequency_label' => $payment->frequency_label,
                    'start_date' => $payment->start_date->format('Y-m-d'),
                    'end_date' => $payment->end_date ? $payment->end_date->format('Y-m-d') : null,
                    'next_payment_date' => $payment->next_payment_date ? $payment->next_payment_date->format('Y-m-d') : null,
                    'last_payment_date' => $payment->last_payment_date ? $payment->last_payment_date->format('Y-m-d') : null,
                    'is_active' => $payment->is_active,
                    'building' => $payment->building ? [
                        'id' => $payment->building->id,
                        'name' => $payment->building->name,
                    ] : null,
                    'account' => $payment->account ? [
                        'id' => $payment->account->id,
                        'name' => $payment->account->name,
                    ] : null,
                    'notes' => $payment->notes,
                    'created_by' => $payment->createdBy ? [
                        'id' => $payment->createdBy->id,
                        'name' => $payment->createdBy->name,
                    ] : null,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $payment->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $recurringPayments,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Recurring Payments Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Düzenli ödemeler yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Recurring Payment
     */
    public function createRecurringPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:gelir,gider',
            'frequency' => 'required|in:gunluk,haftalik,aylik,uc_aylik,alti_aylik,yillik',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'building_id' => 'required_if:type,gelir|nullable|exists:buildings,id',
            'account_id' => 'nullable|exists:account_types,id',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Building kontrolü (gelir için)
            if ($request->type === 'gelir' && $request->building_id) {
                $building = Building::where('company_id', $request->user()->company_id)
                    ->find($request->building_id);

                if (!$building) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bina bulunamadı veya erişim yetkiniz yok',
                    ], 403);
                }
            }

            // Next payment date hesapla
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $nextPaymentDate = $startDate->copy();

            // Frequency'ye göre next payment date
            switch ($request->frequency) {
                case 'gunluk':
                    // Zaten bugün veya geçmişse, yarın
                    if ($nextPaymentDate->isPast()) {
                        $nextPaymentDate->addDay();
                    }
                    break;
                case 'haftalik':
                    if ($nextPaymentDate->isPast()) {
                        $nextPaymentDate->addWeek();
                    }
                    break;
                case 'aylik':
                    // Day of month varsa ayın o günü
                    if ($request->day_of_month) {
                        $nextPaymentDate->day($request->day_of_month);
                    }
                    if ($nextPaymentDate->isPast()) {
                        $nextPaymentDate->addMonth();
                    }
                    break;
                case 'uc_aylik':
                    if ($nextPaymentDate->isPast()) {
                        $nextPaymentDate->addMonths(3);
                    }
                    break;
                case 'alti_aylik':
                    if ($nextPaymentDate->isPast()) {
                        $nextPaymentDate->addMonths(6);
                    }
                    break;
                case 'yillik':
                    if ($nextPaymentDate->isPast()) {
                        $nextPaymentDate->addYear();
                    }
                    break;
            }

            $data = [
                'company_id' => $request->user()->company_id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'frequency' => $request->frequency,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'next_payment_date' => $nextPaymentDate->format('Y-m-d'),
                'building_id' => $request->type === 'gelir' ? $request->building_id : null,
                'account_id' => $request->account_id,
                'is_active' => true,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ];

            $recurringPayment = RecurringPayment::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Düzenli ödeme başarıyla oluşturuldu',
                'data' => [
                    'id' => $recurringPayment->id,
                    'title' => $recurringPayment->title,
                    'amount' => (float) $recurringPayment->amount,
                    'next_payment_date' => $recurringPayment->next_payment_date->format('Y-m-d'),
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Create Recurring Payment Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Düzenli ödeme oluşturulurken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✏️ Update Recurring Payment
     */
    public function updateRecurringPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'type' => 'sometimes|required|in:gelir,gider',
            'frequency' => 'sometimes|required|in:gunluk,haftalik,aylik,uc_aylik,alti_aylik,yillik',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'building_id' => 'required_if:type,gelir|nullable|exists:buildings,id',
            'account_id' => 'nullable|exists:account_types,id',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $recurringPayment = RecurringPayment::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            $updateData = $request->only([
                'title', 'description', 'amount', 'type', 'frequency',
                'start_date', 'end_date', 'building_id', 'account_id', 'is_active', 'notes'
            ]);

            $recurringPayment->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Düzenli ödeme güncellendi',
                'data' => [
                    'id' => $recurringPayment->id,
                    'title' => $recurringPayment->title,
                    'is_active' => $recurringPayment->is_active,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Düzenli ödeme bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update Recurring Payment Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Düzenli ödeme güncellenirken hata oluştu',
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Recurring Payment
     */
    public function deleteRecurringPayment(Request $request, $id)
    {
        try {
            $recurringPayment = RecurringPayment::where('company_id', $request->user()->company_id)
                ->findOrFail($id);

            $recurringPayment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Düzenli ödeme silindi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Düzenli ödeme bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete Recurring Payment Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Düzenli ödeme silinirken hata oluştu',
            ], 500);
        }
    }

    /**
     * 📥 Gün sonu Excel indir
     * GET /api/mobile/financial/day-end/export?date=YYYY-MM-DD&format=excel
     */
    public function dayEndExportExcel(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
            $requestedDate = $request->get('date');
            if ($requestedDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
                $selectedDate = $requestedDate;
            } else {
                $selectedDate = now('Europe/Istanbul')->format('Y-m-d');
            }
            $filename = 'gun-sonu-' . $selectedDate . '.xlsx';
            return Excel::download(new DayEndExport($companyId, $selectedDate), $filename);
        } catch (\Exception $e) {
            \Log::error('Mobile Day-End Excel Export Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Excel oluşturulamadı'], 500);
        }
    }

    /**
     * 📥 Gün sonu PDF indir
     * GET /api/mobile/financial/day-end/export?date=YYYY-MM-DD&format=pdf
     */
    public function dayEndExportPdf(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
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

            $company = $request->user()->company;
            $dateFormatted = \Carbon\Carbon::parse($selectedDate)->locale('tr')->translatedFormat('d F Y');
            $pdf = Pdf::loadView('financial.day-end-pdf', compact(
                'transactions', 'selectedDate', 'dayIncome', 'dayExpense', 'company', 'dateFormatted'
            ))->setPaper('a4', 'portrait');

            return $pdf->download('gun-sonu-' . $selectedDate . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Mobile Day-End PDF Export Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'PDF oluşturulamadı'], 500);
        }
    }
}
