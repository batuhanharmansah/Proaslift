<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\BulkMaintenanceController;
use App\Http\Controllers\Controller;
use App\Models\AccountingEntry;
use App\Models\Building;
use App\Models\CompanyCheck;
use App\Models\CompanyVehicle;
use App\Models\ComplianceDocument;
use App\Models\CustomChecklistItem;
use App\Models\Employee;
use App\Models\EmployeeAbsence;
use App\Models\EmployeeBonus;
use App\Models\IssueReport;
use App\Models\MaintenanceSchedule;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ParityController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    private function companyOrFail(Request $request)
    {
        $companyId = $this->resolveMobileCompanyId($request);
        if ($companyId === null) {
            return [null, response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403)];
        }

        return [$companyId, null];
    }

    public function quotations(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $items = Quotation::with('building')
            ->where('company_id', $companyId)
            ->latest()
            ->limit(100)
            ->get()
            ->map(function (Quotation $q) {
                return [
                    'id' => $q->id,
                    'quote_no' => $q->quote_no,
                    'type' => $q->type,
                    'type_label' => $q->type_label ?? $q->type,
                    'status' => $q->status,
                    'status_label' => $q->status_label ?? $q->status,
                    'customer_name' => $q->customer_name,
                    'grand_total' => (float) $q->grand_total,
                    'currency' => $q->currency,
                    'valid_until' => optional($q->valid_until)->format('Y-m-d'),
                    'building_name' => $q->building?->name,
                    'created_at' => optional($q->created_at)->format('Y-m-d H:i'),
                ];
            });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function quotationShow(Request $request, $id)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $q = Quotation::with(['building', 'items'])->where('company_id', $companyId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $q->id,
                'quote_no' => $q->quote_no,
                'type' => $q->type,
                'type_label' => $q->type_label ?? $q->type,
                'status' => $q->status,
                'status_label' => $q->status_label ?? $q->status,
                'customer_name' => $q->customer_name,
                'customer_phone' => $q->customer_phone,
                'customer_email' => $q->customer_email,
                'scope_summary' => $q->scope_summary,
                'grand_total' => (float) $q->grand_total,
                'currency' => $q->currency,
                'valid_until' => optional($q->valid_until)->format('Y-m-d'),
                'building_name' => $q->building?->name,
                'items' => $q->items->map(fn ($item) => [
                    'id' => $item->id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->getAttributes()['unit'] ?? null,
                    'line_total' => (float) $item->line_total,
                ]),
            ],
        ]);
    }

    public function checks(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $items = CompanyCheck::where('company_id', $companyId)
            ->orderByDesc('due_date')
            ->limit(100)
            ->get()
            ->map(fn (CompanyCheck $c) => [
                'id' => $c->id,
                'type' => $c->type,
                'type_label' => $c->type === 'cek' ? 'Çek' : ($c->type === 'senet' ? 'Senet' : $c->type),
                'direction' => $c->direction,
                'direction_label' => $c->direction === 'gelen' ? 'Gelen' : ($c->direction === 'giden' ? 'Giden' : $c->direction),
                'counterparty_name' => $c->counterparty_name,
                'serial_number' => $c->serial_number,
                'bank_name' => $c->bank_name,
                'amount' => (float) $c->amount,
                'due_date' => optional($c->due_date)->format('Y-m-d'),
                'status' => $c->status,
                'status_label' => $c->status_label,
            ]);

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function compliance(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $type = $request->get('type');
        $query = ComplianceDocument::with('building')->where('company_id', $companyId);
        if (in_array($type, ['dtr', 'kurtarma'], true)) {
            $query->where('document_type', $type);
        }

        $items = $query->latest()->limit(100)->get()->map(fn (ComplianceDocument $d) => [
            'id' => $d->id,
            'document_type' => $d->document_type,
            'document_type_label' => ComplianceDocument::TYPES[$d->document_type] ?? $d->document_type,
            'event_date' => optional($d->event_date)->format('Y-m-d'),
            'inspector_or_technician_name' => $d->inspector_or_technician_name,
            'description' => $d->description,
            'status' => $d->status,
            'status_label' => $d->status_label,
            'building_name' => $d->building?->name,
        ]);

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function hrFleet(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bonuses' => EmployeeBonus::where('company_id', $companyId)->with('employee')->orderByDesc('bonus_date')->limit(50)->get()
                    ->map(fn ($b) => [
                        'id' => $b->id,
                        'employee_name' => $b->employee?->full_name,
                        'bonus_date' => optional($b->bonus_date)->format('Y-m-d'),
                        'type' => $b->type,
                        'amount' => (float) $b->amount,
                        'description' => $b->description,
                    ]),
                'vehicles' => CompanyVehicle::where('company_id', $companyId)->with('driver')->orderBy('plate')->get()
                    ->map(fn ($v) => [
                        'id' => $v->id,
                        'plate' => $v->plate,
                        'brand_model' => $v->brand_model,
                        'driver_name' => $v->driver?->full_name,
                        'inspection_due_date' => optional($v->inspection_due_date)->format('Y-m-d'),
                        'insurance_due_date' => optional($v->insurance_due_date)->format('Y-m-d'),
                    ]),
                'absences' => EmployeeAbsence::where('company_id', $companyId)->with('employee')->orderByDesc('start_date')->limit(50)->get()
                    ->map(fn ($a) => [
                        'id' => $a->id,
                        'employee_name' => $a->employee?->full_name,
                        'start_date' => optional($a->start_date)->format('Y-m-d'),
                        'end_date' => optional($a->end_date)->format('Y-m-d'),
                        'type' => $a->type,
                    ]),
            ],
        ]);
    }

    public function checklistSettings(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $items = CustomChecklistItem::where('company_id', $companyId)
            ->orderBy('section_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (CustomChecklistItem $i) => [
                'id' => $i->id,
                'section_id' => $i->section_id,
                'section_label' => CustomChecklistItem::SECTIONS[$i->section_id] ?? $i->section_id,
                'title' => $i->title,
                'is_active' => $i->is_active,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'sections' => CustomChecklistItem::SECTIONS,
                'items' => $items,
            ],
        ]);
    }

    public function storeChecklistItem(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $validated = $request->validate([
            'section_id' => 'required|in:' . implode(',', array_keys(CustomChecklistItem::SECTIONS)),
            'title' => 'required|string|max:500',
        ]);

        $maxSort = CustomChecklistItem::where('company_id', $companyId)
            ->where('section_id', $validated['section_id'])
            ->max('sort_order') ?? 0;

        $item = CustomChecklistItem::create([
            'company_id' => $companyId,
            'section_id' => $validated['section_id'],
            'item_key' => 'custom_' . $companyId . '_' . Str::random(10),
            'title' => $validated['title'],
            'sort_order' => $maxSort + 1,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Madde eklendi.', 'data' => ['id' => $item->id]], 201);
    }

    public function destroyChecklistItem(Request $request, $id)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $item = CustomChecklistItem::where('company_id', $companyId)->findOrFail($id);
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Madde silindi.']);
    }

    public function guide()
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['title' => 'Ana Sayfa', 'body' => 'Dashboard’da bina, personel, bakım ve arıza özetlerini görün. Widget’ları ihtiyaca göre açıp kapatabilirsiniz.'],
                ['title' => 'Binalar', 'body' => 'Bina kaydı oluşturun, düzenleyin, silin ve bakım sözleşmesini takip edin. QR ile sahada binayı hızlıca bulun.'],
                ['title' => 'Bakım', 'body' => 'Planlı bakımları atayın, sahada başlatın, kontrol listesini doldurun ve raporu tamamlayın.'],
                ['title' => 'Arıza Bildirimi', 'body' => 'Arıza kaydı açın, ekip atayın, çalışmayı başlatın ve tamamlayın.'],
                ['title' => 'Teklifler', 'body' => 'Bakım, modernizasyon, montaj ve onarım tekliflerini listeler; web panelinden yeni teklif oluşturulur.'],
                ['title' => 'Depo', 'body' => 'Yedek parça stoklarını ekleyin, güncelleyin ve düşük stok uyarılarını izleyin.'],
                ['title' => 'Finans', 'body' => 'Kasa, gelir-gider, alacak ve borç kayıtlarını yönetin. Gün sonu raporu alın.'],
                ['title' => 'Konum Takibi', 'body' => 'Aktif saha personelinin son konumunu ve koordinatı olan binaları görün.'],
                ['title' => 'Toplu Bakım', 'body' => 'Seçilen ay için tüm aktif binalara rutin bakım kaydı üretin; önizleyip onaylayın.'],
                ['title' => 'Çek & Senet', 'body' => 'Gelen/giden çek ve senet vadesini, tutarını ve durumunu takip edin.'],
            ],
        ]);
    }

    public function reportsHub(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $month = now('Europe/Istanbul')->month;
        $year = now('Europe/Istanbul')->year;

        $monthlyIncome = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('total_amount');

        $monthlyExpense = (float) AccountingEntry::where('company_id', $companyId)
            ->whereIn('type', ['gider', 'maas'])
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('total_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'monthly_income' => $monthlyIncome,
                'monthly_expense' => $monthlyExpense,
                'monthly_profit' => $monthlyIncome - $monthlyExpense,
                'completed_maintenance' => MaintenanceSchedule::where('company_id', $companyId)
                    ->where('status', 'tamamlandi')
                    ->whereMonth('updated_at', $month)
                    ->whereYear('updated_at', $year)
                    ->count(),
                'active_buildings' => Building::where('company_id', $companyId)->where('status', 'aktif')->count(),
                'total_employees' => Employee::where('company_id', $companyId)->where('is_active', true)->count(),
                'open_issues' => IssueReport::where('company_id', $companyId)
                    ->whereNotIn('status', ['tamamlandi', 'iptal_edildi'])
                    ->count(),
                'low_stock_products' => Product::where('company_id', $companyId)
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->count(),
            ],
        ]);
    }

    public function reportsFinancial(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $start = $request->get('start_date', now()->startOfMonth()->toDateString());
        $end = $request->get('end_date', now()->endOfMonth()->toDateString());

        $income = (float) AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('total_amount');
        $expense = (float) AccountingEntry::where('company_id', $companyId)
            ->whereIn('type', ['gider', 'maas'])
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('total_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ],
        ]);
    }

    public function reportsMaintenance(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $start = $request->get('start_date', now()->startOfMonth()->toDateString());
        $end = $request->get('end_date', now()->endOfMonth()->toDateString());

        $total = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$start, $end])
            ->count();
        $completed = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$start, $end])
            ->where('status', 'tamamlandi')
            ->count();

        $byType = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$start, $end])
            ->selectRaw('maintenance_type, COUNT(*) as count')
            ->groupBy('maintenance_type')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'total' => $total,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                'by_type' => $byType,
            ],
        ]);
    }

    public function reportsEmployee(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $start = $request->get('start_date', now()->startOfMonth()->toDateString());
        $end = $request->get('end_date', now()->endOfMonth()->toDateString());

        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->withCount([
                'maintenanceSchedules as total_jobs' => fn ($q) => $q->whereBetween('scheduled_date', [$start, $end]),
                'maintenanceSchedules as completed_jobs' => fn ($q) => $q->whereBetween('scheduled_date', [$start, $end])->where('status', 'tamamlandi'),
            ])
            ->orderBy('first_name')
            ->get()
            ->map(fn (Employee $e) => [
                'id' => $e->id,
                'name' => $e->full_name,
                'total_jobs' => (int) $e->total_jobs,
                'completed_jobs' => (int) $e->completed_jobs,
                'completion_rate' => $e->total_jobs > 0 ? round(($e->completed_jobs / $e->total_jobs) * 100, 1) : 0,
            ]);

        return response()->json(['success' => true, 'data' => $employees]);
    }

    public function routePlanner(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        $date = $request->get('date', now('Europe/Istanbul')->toDateString());

        $jobs = MaintenanceSchedule::with('building')
            ->where('company_id', $companyId)
            ->whereDate('scheduled_date', $date)
            ->whereNotIn('status', ['iptal', 'tamamlandi'])
            ->orderBy('scheduled_date')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'building_id' => $m->building_id,
                'building_name' => $m->building?->name,
                'address' => $m->building?->address,
                'district' => $m->building?->district,
                'latitude' => $m->building?->latitude,
                'longitude' => $m->building?->longitude,
                'status' => $m->status,
                'priority' => $m->priority,
            ]);

        return response()->json(['success' => true, 'data' => ['date' => $date, 'jobs' => $jobs]]);
    }

    public function bulkMaintenancePreview(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        try {
            $plan = app(BulkMaintenanceController::class)->preview($request)->getData(true);

            return response()->json(['success' => true, 'data' => $plan]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Doğrulama hatası', 'errors' => $e->errors()], 422);
        }
    }

    public function bulkMaintenanceStore(Request $request)
    {
        [$companyId, $error] = $this->companyOrFail($request);
        if ($error) {
            return $error;
        }

        try {
            $plan = app(BulkMaintenanceController::class)->preview($request)->getData(true);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Doğrulama hatası', 'errors' => $e->errors()], 422);
        }

        $created = 0;
        DB::transaction(function () use ($plan, $companyId, &$created) {
            foreach ($plan['items'] ?? [] as $item) {
                if (!empty($item['skipped'])) {
                    continue;
                }
                MaintenanceSchedule::create([
                    'company_id' => $companyId,
                    'building_id' => $item['building_id'],
                    'assigned_employee_id' => $item['assigned_employee_id'] ?? null,
                    'maintenance_type' => 'rutin_bakim',
                    'title' => 'Aylık Toplu Bakım',
                    'scheduled_date' => $item['scheduled_date'],
                    'priority' => 'normal',
                    'status' => !empty($item['assigned_employee_id']) ? 'atandi' : 'planli',
                    'description' => 'Toplu bakım sihirbazı ile otomatik oluşturuldu.',
                ]);
                $created++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$created} adet bakım kaydı oluşturuldu.",
            'data' => ['created' => $created, 'preview_count' => count($plan['items'] ?? [])],
        ]);
    }
}
