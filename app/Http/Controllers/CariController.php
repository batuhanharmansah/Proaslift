<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Employee;
use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Http\Request;

/**
 * Cariler — Özellik #5 (rakip analizi karşılaştırması sonucu eklendi).
 *
 * BİLİNÇLİ KAPSAM SINIRLAMASI: Rakipteki gibi tam bir "unified cari hesap"
 * mimarisine (ayrı Customer/Cari tablosu, bina devir mekanizması, tüm hareketlerin
 * tek bir hesaba bağlanması) GEÇİLMEDİ — bu, canlıda çalışan ve satışa hazırlanan
 * Receivable/Payable/AccountingEntry tablolarını yeniden yapılandırmayı gerektirir,
 * riski satış öncesi almaya değmez. Bunun yerine SADECE OKUMA amaçlı, mevcut
 * verilerden (Building→Receivable, Payable.supplier_name, Employee) birleştirilmiş
 * bir "kim bize ne borçlu / biz kime ne borçluyuz" özet ekranı sunulur.
 */
class CariController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $type = $request->get('type', 'all');

        $customers = collect();
        if (in_array($type, ['all', 'customer'], true)) {
            $customers = Building::where('company_id', $companyId)
                ->withSum(['receivables as receivable_balance' => function ($q) {
                    $q->where('status', '!=', 'odendi');
                }], 'remaining_amount')
                ->get()
                ->map(fn($b) => [
                    'type' => 'customer',
                    'type_label' => 'Müşteri (Bina)',
                    'name' => $b->name,
                    'balance' => (float) ($b->receivable_balance ?? 0),
                    'balance_label' => 'Bize borçlu',
                    'link' => route('buildings.show', $b->id),
                ]);
        }

        $suppliers = collect();
        if (in_array($type, ['all', 'supplier'], true)) {
            $suppliers = Payable::where('company_id', $companyId)
                ->where('status', '!=', 'odendi')
                ->whereNotNull('supplier_name')
                ->selectRaw('supplier_name, SUM(remaining_amount) as balance')
                ->groupBy('supplier_name')
                ->get()
                ->map(fn($p) => [
                    'type' => 'supplier',
                    'type_label' => 'Tedarikçi / Gider',
                    'name' => $p->supplier_name,
                    'balance' => (float) $p->balance,
                    'balance_label' => 'Biz borçluyuz',
                    'link' => route('financial.payables'),
                ]);
        }

        $employees = collect();
        if (in_array($type, ['all', 'employee'], true)) {
            $employees = Employee::where('company_id', $companyId)
                ->where('is_active', true)
                ->get()
                ->map(fn($e) => [
                    'type' => 'employee',
                    'type_label' => 'Personel',
                    'name' => trim($e->first_name . ' ' . $e->last_name),
                    'balance' => (float) ($e->salary ?? 0),
                    'balance_label' => 'Sabit maaş (bilgi amaçlı, gerçek cari hareketi değil)',
                    'link' => route('employees.show', $e->id),
                ]);
        }

        $rows = $customers->concat($suppliers)->concat($employees);

        $totalReceivable = $customers->sum('balance');
        $totalPayable = $suppliers->sum('balance');

        return view('financial.cariler', compact('rows', 'type', 'totalReceivable', 'totalPayable'));
    }
}
