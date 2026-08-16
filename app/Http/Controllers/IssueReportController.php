<?php

namespace App\Http\Controllers;

use App\Models\IssueReport;
use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Models\Product;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IssueReportController extends Controller
{
    public function index(Request $request)
    {
        $query = IssueReport::with(['building', 'assignedEmployee'])
            ->where('company_id', auth()->user()->company_id);

        // Filtreler
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reported_by', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%");
            });
        }

        $issueReports = $query->latest()->paginate(15);
        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('issue-reports.index', compact('issueReports', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('issue-reports.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'reported_by' => 'required|string|max:255',
            'issue_type' => 'required|in:elektrik_arizasi,mekanik_ariza,kapı_arizasi,ses_sistemi,acil_durum,diger',
            'priority' => 'required|in:dusuk,orta,yuksek,acil',
            'description' => 'required|string|min:10',
            'location_details' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'customer_notes' => 'nullable|string',
            'is_urgent' => 'boolean',
            'requires_immediate_attention' => 'boolean',
        ]);

        $issueReport = IssueReport::create([
            'company_id' => auth()->user()->company_id,
            'building_id' => $validated['building_id'],
            'reported_by' => $validated['reported_by'],
            'issue_type' => $validated['issue_type'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'location_details' => $validated['location_details'] ?? null,
            'contact_name' => $validated['contact_name'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'customer_notes' => $validated['customer_notes'] ?? null,
            'is_urgent' => $validated['is_urgent'] ?? false,
            'requires_immediate_attention' => $validated['requires_immediate_attention'] ?? false,
            'status' => 'bildirildi'
        ]);

        return redirect()->route('issue-reports.show', $issueReport)
            ->with('success', 'Arıza bildirimi başarıyla oluşturuldu.');
    }

    public function show(IssueReport $issueReport)
    {
        $issueReport->load(['building', 'assignedEmployee', 'maintenanceSchedule.maintenanceReport']);

        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return view('issue-reports.show', compact('issueReport', 'employees'));
    }

    public function edit(IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $buildings = Building::where('company_id', auth()->user()->company_id)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('issue-reports.edit', compact('issueReport', 'buildings'));
    }

    public function update(Request $request, IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'reported_by' => 'required|string|max:255',
            'issue_type' => 'required|in:elektrik_arizasi,mekanik_ariza,kapı_arizasi,ses_sistemi,acil_durum,diger',
            'priority' => 'required|in:dusuk,orta,yuksek,acil',
            'description' => 'required|string|min:10',
            'location_details' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'customer_notes' => 'nullable|string',
            'is_urgent' => 'boolean',
            'requires_immediate_attention' => 'boolean',
        ]);

        $issueReport->update($validated);

        return redirect()->route('issue-reports.show', $issueReport)
            ->with('success', 'Arıza bildirimi güncellendi.');
    }

    public function assignEmployee(Request $request, IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'estimated_completion_time' => 'nullable|date|after:now',
        ]);

        $issueReport->assignEmployee($validated['employee_id']);

        if ($request->filled('estimated_completion_time')) {
            $issueReport->update([
                'estimated_completion_time' => $validated['estimated_completion_time']
            ]);
        }

        return redirect()->route('issue-reports.show', $issueReport)
            ->with('success', 'Ekip başarıyla atandı.');
    }

    public function startWork(IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $issueReport->startWork();

        return redirect()->route('issue-reports.show', $issueReport)
            ->with('success', 'Çalışma başlatıldı.');
    }

    public function createMaintenanceFromIssue(IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Bakım planı oluştur
        $maintenanceSchedule = MaintenanceSchedule::create([
            'company_id' => $issueReport->company_id,
            'building_id' => $issueReport->building_id,
            'issue_report_id' => $issueReport->id,
            'maintenance_type' => 'ariza_onarim',
            'title' => "Arıza Onarımı - {$issueReport->issue_type_label}",
            'description' => $issueReport->description,
            'scheduled_date' => now(),
            'assigned_employee_id' => $issueReport->assigned_employee_id,
            'priority' => $issueReport->priority,
            'status' => 'planli',
            'estimated_duration' => 2, // 2 saat varsayılan
            'notes' => "Arıza bildirimi #{$issueReport->id} üzerinden oluşturuldu."
        ]);

        // Arıza bildirimini güncelle
        $issueReport->update([
            'status' => 'ekip_atandi'
        ]);

        return redirect()->route('maintenance.show', $maintenanceSchedule)
            ->with('success', 'Arıza bildirimi bakım planına dönüştürüldü.');
    }

    public function complete(IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $issueReport->complete();

        return redirect()->route('issue-reports.show', $issueReport)
            ->with('success', 'Arıza bildirimi tamamlandı olarak işaretlendi.');
    }

    public function destroy(IssueReport $issueReport)
    {
        // Yetki kontrolü
        if ($issueReport->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $issueReport->delete();

        return redirect()->route('issue-reports.index')
            ->with('success', 'Arıza bildirimi silindi.');
    }

    // Dashboard için istatistikler
    public function getStats()
    {
        $companyId = auth()->user()->company_id;

        $stats = [
            'total_issues' => IssueReport::where('company_id', $companyId)->count(),
            'pending_issues' => IssueReport::where('company_id', $companyId)
                ->whereIn('status', ['bildirildi', 'inceleniyor'])->count(),
            'assigned_issues' => IssueReport::where('company_id', $companyId)
                ->whereIn('status', ['ekip_atandi', 'calisma_basladi'])->count(),
            'completed_issues' => IssueReport::where('company_id', $companyId)
                ->where('status', 'tamamlandi')->count(),
            'urgent_issues' => IssueReport::where('company_id', $companyId)
                ->where('is_urgent', true)
                ->whereNotIn('status', ['tamamlandi', 'iptal_edildi'])->count(),
        ];

        return response()->json($stats);
    }
}
