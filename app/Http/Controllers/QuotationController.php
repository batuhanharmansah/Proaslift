<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationUnit;
use App\Models\Receivable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Quotation::with('building')
            ->where('company_id', $companyId)
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = addcslashes($request->search, '%_\\');
            $query->where(function ($q) use ($search) {
                $q->where('quote_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_contact_name', 'like', "%{$search}%")
                    ->orWhereHas('building', fn ($building) => $building->where('name', 'like', "%{$search}%"));
            });
        }

        $quotations = $query->paginate(20)->withQueryString();

        return view('quotations.index', compact('quotations'));
    }

    public function create(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $buildings = Building::where('company_id', $companyId)->orderBy('name')->get();
        $selectedBuilding = null;

        if ($request->filled('building_id')) {
            $selectedBuilding = Building::where('company_id', $companyId)
                ->with('activeLabel')
                ->find($request->building_id);
        }

        return view('quotations.create', compact('buildings', 'selectedBuilding'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'type' => ['required', Rule::in(['maintenance', 'modernization', 'installation', 'repair'])],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_contact_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'valid_until' => ['required', 'date', 'after_or_equal:today'],
            'currency' => ['required', 'string', 'max:10'],
            'scope_summary' => ['nullable', 'string'],
            'scope_inclusions' => ['nullable', 'string'],
            'scope_exclusions' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'unit.elevator_code' => ['nullable', 'string', 'max:100'],
            'unit.location' => ['nullable', 'string', 'max:255'],
            'unit.elevator_type' => ['nullable', 'string', 'max:100'],
            'unit.brand' => ['nullable', 'string', 'max:100'],
            'unit.model' => ['nullable', 'string', 'max:100'],
            'unit.capacity_kg' => ['nullable', 'integer', 'min:0'],
            'unit.capacity_person' => ['nullable', 'integer', 'min:0'],
            'unit.speed' => ['nullable', 'string', 'max:50'],
            'unit.stop_count' => ['nullable', 'integer', 'min:0'],
            'unit.door_type' => ['nullable', 'string', 'max:100'],
            'unit.drive_type' => ['nullable', 'string', 'max:100'],
            'unit.machine_room' => ['nullable', 'string', 'max:50'],
            'unit.current_label_color' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.category' => ['required', Rule::in(['service', 'material', 'labor', 'inspection', 'discount'])],
            'items.*.description' => ['required', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $building = null;
        if (!empty($validated['building_id'])) {
            $building = Building::where('company_id', $companyId)->findOrFail($validated['building_id']);
        }

        $quotation = DB::transaction(function () use ($validated, $request, $companyId, $building) {
            $quotation = Quotation::create([
                'company_id' => $companyId,
                'type' => $validated['type'],
                'status' => 'draft',
                'building_id' => $building?->id,
                'customer_name' => $validated['customer_name'],
                'customer_contact_name' => $validated['customer_contact_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'valid_until' => $validated['valid_until'],
                'currency' => $validated['currency'],
                'scope_summary' => $validated['scope_summary'] ?? null,
                'scope_inclusions' => $validated['scope_inclusions'] ?? null,
                'scope_exclusions' => $validated['scope_exclusions'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $unitData = $validated['unit'] ?? [];
            if ($building || collect($unitData)->filter()->isNotEmpty()) {
                QuotationUnit::create([
                    'quotation_id' => $quotation->id,
                    'building_id' => $building?->id,
                    'elevator_code' => $unitData['elevator_code'] ?? $building?->elevator_code,
                    'location' => $unitData['location'] ?? $building?->name,
                    'elevator_type' => $unitData['elevator_type'] ?? $building?->elevator_type,
                    'brand' => $unitData['brand'] ?? $building?->elevator_brand,
                    'model' => $unitData['model'] ?? $building?->elevator_model,
                    'capacity_kg' => $unitData['capacity_kg'] ?? null,
                    'capacity_person' => $unitData['capacity_person'] ?? null,
                    'speed' => $unitData['speed'] ?? null,
                    'stop_count' => $unitData['stop_count'] ?? $building?->floor_count,
                    'door_type' => $unitData['door_type'] ?? null,
                    'drive_type' => $unitData['drive_type'] ?? null,
                    'machine_room' => $unitData['machine_room'] ?? null,
                    'current_label_color' => $unitData['current_label_color'] ?? $building?->current_label_color,
                    'last_control_date' => null,
                ]);
            }

            foreach ($validated['items'] as $index => $item) {
                $totals = QuotationItem::totalsFor(
                    $item['quantity'],
                    $item['unit_price'],
                    $item['discount_rate'] ?? 0,
                    $item['vat_rate'] ?? 20
                );

                QuotationItem::create(array_merge($totals, [
                    'quotation_id' => $quotation->id,
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'adet',
                    'unit_price' => $item['unit_price'],
                    'discount_rate' => $item['discount_rate'] ?? 0,
                    'vat_rate' => $item['vat_rate'] ?? 20,
                    'sort_order' => $index + 1,
                ]));
            }

            $quotation->recalculateTotals();

            return $quotation;
        });

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Teklif taslak olarak oluşturuldu.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeCompany($quotation);

        $quotation->load(['building', 'units', 'items', 'acceptances', 'convertedReceivable']);

        return view('quotations.show', compact('quotation'));
    }

    public function markAsSent(Quotation $quotation)
    {
        $this->authorizeCompany($quotation);

        if ($quotation->status === 'draft') {
            $quotation->update([
                'status' => 'sent',
                'sent_at' => now('Europe/Istanbul'),
            ]);
        }

        return back()->with('success', 'Teklif gönderildi olarak işaretlendi. Müşteri linki aktif.');
    }

    public function cancel(Quotation $quotation)
    {
        $this->authorizeCompany($quotation);

        if (!in_array($quotation->status, ['accepted', 'converted'], true)) {
            $quotation->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Teklif iptal edildi.');
    }

    public function pdf(Quotation $quotation)
    {
        $this->authorizeCompany($quotation);

        $quotation->load(['company', 'building', 'units', 'items']);
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))->setPaper('a4');

        return $pdf->download($quotation->quote_no . '.pdf');
    }

    public function convertToReceivable(Quotation $quotation)
    {
        $this->authorizeCompany($quotation);

        if (!in_array($quotation->status, ['accepted', 'viewed', 'sent'], true)) {
            return back()->with('error', 'Sadece gönderilmiş veya kabul edilmiş teklifler alacağa dönüştürülebilir.');
        }

        if ($quotation->converted_receivable_id) {
            return back()->with('success', 'Bu teklif daha önce alacağa dönüştürülmüş.');
        }

        if (!$quotation->building_id) {
            return back()->with('error', 'Bu teklifte bina seçilmemiş. Alacağa dönüştürmeden önce lütfen tekliften bir bina ilişkilendirin.');
        }

        $receivable = Receivable::create([
            'company_id' => $quotation->company_id,
            'building_id' => $quotation->building_id,
            'title' => $quotation->quote_no . ' - ' . $quotation->type_label,
            'description' => $quotation->scope_summary ?: 'Tekliften oluşturulan alacak kaydı.',
            'total_amount' => $quotation->grand_total,
            'received_amount' => 0,
            'remaining_amount' => $quotation->grand_total,
            'due_date' => now('Europe/Istanbul')->addDays(15)->toDateString(),
            'status' => 'beklemede',
            'payment_type' => 'tek_sefer',
            'installment_count' => 1,
            'paid_installments' => 0,
            'priority' => 'orta',
            'notes' => 'Teklif No: ' . $quotation->quote_no,
            'created_by' => auth()->id(),
        ]);

        $quotation->update([
            'status' => 'converted',
            'converted_at' => now('Europe/Istanbul'),
            'converted_receivable_id' => $receivable->id,
        ]);

        return back()->with('success', 'Teklif alacak kaydına dönüştürüldü.');
    }

    private function authorizeCompany(Quotation $quotation): void
    {
        if ($quotation->company_id !== auth()->user()->company_id) {
            abort(403, 'Bu teklife erişim yetkiniz yok.');
        }
    }
}
