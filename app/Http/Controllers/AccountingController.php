<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\Building;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountingEntry::where('company_id', auth()->user()->company_id)->with(['building', 'employee']);

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $entries = $query->orderBy('transaction_date', 'desc')->paginate(15);

        // Özet istatistikler
        $currentMonth = now()->format('Y-m');
        $companyId = auth()->user()->company_id;
        $stats = [
            'monthly_income' => AccountingEntry::where('company_id', $companyId)
                ->where('type', 'gelir')
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
                ->sum('total_amount'),
            'monthly_expense' => AccountingEntry::where('company_id', $companyId)
                ->whereIn('type', ['gider', 'maas'])
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
                ->sum('total_amount'),
            'monthly_vat' => AccountingEntry::where('company_id', $companyId)
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$currentMonth])
                ->sum('vat_amount'),
            'pending_payments' => AccountingEntry::where('company_id', $companyId)
                ->where('status', 'beklemede')->count(),
        ];

        return view('accounting.index', compact('entries', 'stats'));
    }

    public function create()
    {
        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')->get();
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)->get();

        return view('accounting.create', compact('buildings', 'employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:gelir,gider,maas,vergi,sigorta',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'transaction_date' => 'required|date',
            'payment_method' => 'required|in:nakit,banka_havalesi,kredi_karti,cek',
            'building_id' => 'nullable|exists:buildings,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $data['vat_rate'] = $data['vat_rate'] ?? 20; // Varsayılan KDV %20
        $data['status'] = 'beklemede';

        AccountingEntry::create($data);

        return redirect()->route('accounting.index')->with('success', 'Muhasebe kaydı başarıyla eklendi.');
    }

    public function show(AccountingEntry $accounting)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$accounting->exists) {
            abort(404, 'Muhasebe kaydı bulunamadı.');
        }

        $accounting->load(['building', 'employee', 'maintenanceReport']);

        return view('accounting.show', compact('accounting'));
    }

    public function edit(AccountingEntry $accounting)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$accounting->exists) {
            abort(404, 'Muhasebe kaydı bulunamadı.');
        }

        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')->get();
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)->get();

        return view('accounting.edit', compact('accounting', 'buildings', 'employees'));
    }

    public function update(Request $request, AccountingEntry $accounting)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$accounting->exists) {
            abort(404, 'Muhasebe kaydı bulunamadı.');
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:gelir,gider,maas,vergi,sigorta',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'transaction_date' => 'required|date',
            'payment_method' => 'required|in:nakit,banka_havalesi,kredi_karti,cek',
            'status' => 'required|in:beklemede,odendi,tahsil_edildi,iptal',
            'building_id' => 'nullable|exists:buildings,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accounting->update($request->all());

        return redirect()->route('accounting.index')->with('success', 'Muhasebe kaydı güncellendi.');
    }

    public function destroy(AccountingEntry $accounting)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$accounting->exists) {
            abort(404, 'Muhasebe kaydı bulunamadı.');
        }

        $accounting->delete();

        return redirect()->route('accounting.index')->with('success', 'Muhasebe kaydı silindi.');
    }
}
