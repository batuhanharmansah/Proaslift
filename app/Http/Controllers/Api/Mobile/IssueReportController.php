<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Services\IssueMaintenanceService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 🚨 ENTERPRISE MOBILE ISSUE REPORTS CONTROLLER
 * Arıza bildirimi, acil durum yönetimi, müşteri iletişimi
 */
class IssueReportController extends Controller
{
    protected $notificationService;
    protected $issueMaintenanceService;

    public function __construct(
        NotificationService $notificationService,
        IssueMaintenanceService $issueMaintenanceService
    )
    {
        $this->notificationService = $notificationService;
        $this->issueMaintenanceService = $issueMaintenanceService;
    }
    /**
     * 📋 Get Issue Reports
     */
    public function index(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $query = IssueReport::with(['building', 'assignedEmployee', 'maintenanceSchedule'])
                ->where('company_id', $companyId);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('issue_type')) {
                $query->where('issue_type', $request->issue_type);
            }

            if ($request->filled('building_id')) {
                $query->where('building_id', $request->building_id);
            }

            if ($request->has('is_urgent')) {
                $query->where('is_urgent', $request->boolean('is_urgent'));
            }

            if ($request->has('requires_immediate_attention')) {
                $query->where('requires_immediate_attention', $request->boolean('requires_immediate_attention'));
            }

            // Date filters
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Sorting - Priority first, then date
            $query->orderByRaw("FIELD(priority, 'acil', 'yuksek', 'orta', 'dusuk')")
                  ->orderBy('created_at', 'desc');

            $issues = $query->paginate(20);

            // Transform data
            $issues->getCollection()->transform(function ($issue) {
                return [
                    'id' => $issue->id,
                    'building_id' => $issue->building_id,
                    'reported_by' => $issue->reported_by,
                    'issue_type' => $issue->issue_type,
                    'priority' => $issue->priority,
                    'priority_label' => $issue->priority_label,
                    'description' => $issue->description,
                    'location_details' => $issue->location_details,
                    'contact_name' => $issue->contact_name,
                    'contact_phone' => $issue->contact_phone,
                    'status' => $issue->status,
                    'assigned_employee_id' => $issue->assigned_employee_id,
                    'assigned_at' => $issue->assigned_at,
                    'estimated_completion_time' => $issue->estimated_completion_time,
                    'actual_completion_time' => $issue->actual_completion_time,
                    'customer_notes' => $issue->customer_notes,
                    'is_urgent' => $issue->is_urgent,
                    'requires_immediate_attention' => $issue->requires_immediate_attention,
                    'created_at' => $issue->created_at,
                    'updated_at' => $issue->updated_at,

                    'building' => $issue->building ? [
                        'id' => $issue->building->id,
                        'name' => $issue->building->name,
                        'address' => $issue->building->address,
                        'district' => $issue->building->district,
                        'city' => $issue->building->city,
                        'elevator_code' => $issue->building->elevator_code,
                    ] : null,

                    'assigned_employee' => $issue->assignedEmployee ? [
                        'id' => $issue->assignedEmployee->id,
                        'full_name' => $issue->assignedEmployee->full_name,
                        'position' => $issue->assignedEmployee->position,
                        'phone' => $issue->assignedEmployee->phone,
                    ] : null,
                    'maintenance_schedule_id' => $issue->maintenanceSchedule?->id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $issues->items(),
                'pagination' => [
                    'current_page' => $issues->currentPage(),
                    'last_page' => $issues->lastPage(),
                    'per_page' => $issues->perPage(),
                    'total' => $issues->total(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Issue Reports Index Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Arıza raporları yüklenemedi',
            ], 500);
        }
    }

    /**
     * 🚨 Create New Issue Report
     */
    public function store(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $validator = Validator::make($request->all(), [
                'building_id' => 'required|exists:buildings,id',
                'reported_by' => 'required|string|max:255',
                'issue_type' => 'required|in:elektrik_arizasi,mekanik_ariza,kapı_arizasi,ses_sistemi,acil_durum,diger',
                'priority' => 'required|in:dusuk,orta,yuksek,acil',
                'description' => 'required|string',
                'location_details' => 'nullable|string',
                'contact_name' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'customer_notes' => 'nullable|string',
                'assigned_employee_id' => 'nullable|exists:employees,id',
                'is_urgent' => 'boolean',
                'requires_immediate_attention' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Verify building belongs to company
            $building = Building::where('company_id', $companyId)
                ->findOrFail($request->building_id);

            $assignedEmployee = null;
            if ($request->filled('assigned_employee_id')) {
                $assignedEmployee = Employee::where('company_id', $companyId)
                    ->where('is_active', true)
                    ->findOrFail($request->assigned_employee_id);
            }

            $issue = IssueReport::create([
                'company_id' => $companyId,
                'building_id' => $request->building_id,
                'reported_by' => $request->reported_by,
                'issue_type' => $request->issue_type,
                'priority' => $request->priority,
                'description' => $request->description,
                'location_details' => $request->location_details,
                'contact_name' => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'status' => $assignedEmployee ? 'ekip_atandi' : 'bildirildi',
                'assigned_employee_id' => $assignedEmployee?->id,
                'assigned_at' => $assignedEmployee ? now() : null,
                'customer_notes' => $request->customer_notes,
                'is_urgent' => $request->boolean('is_urgent'),
                'requires_immediate_attention' => $request->boolean('requires_immediate_attention'),
            ]);

            $maintenance = $this->issueMaintenanceService->ensureMaintenanceSchedule($issue->fresh('maintenanceSchedule'));

            // Send notification
            $this->notificationService->notifyIssueReported($issue->fresh(['building', 'assignedEmployee', 'maintenanceSchedule']));

            return response()->json([
                'success' => true,
                'message' => 'Arıza bildirimi başarıyla oluşturuldu',
                'data' => [
                    'id' => $issue->id,
                    'status' => $issue->fresh()->status,
                    'assigned_employee_id' => $issue->assigned_employee_id,
                    'maintenance_schedule_id' => $maintenance->id,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Issue Report Create Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'building_id' => $request->building_id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Arıza bildirimi oluşturulamadı',
            ], 500);
        }
    }

    /**
     * 🔄 Update Issue Status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            $issue = IssueReport::where('company_id', $companyId)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:bildirildi,inceleniyor,ekip_atandi,calisma_basladi,tamamlandi,iptal_edildi',
                'assigned_employee_id' => 'nullable|exists:employees,id',
                'customer_notes' => 'nullable|string',
                'actual_completion_time' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $updateData = [
                'status' => $request->status,
                'customer_notes' => $request->customer_notes,
            ];

            if ($request->filled('assigned_employee_id')) {
                $updateData['assigned_employee_id'] = $request->assigned_employee_id;
                $updateData['assigned_at'] = now();
            }

            if ($request->filled('actual_completion_time')) {
                $updateData['actual_completion_time'] = $request->actual_completion_time;
            }

            $issue->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Arıza durumu güncellendi',
                'data' => [
                    'id' => $issue->id,
                    'status' => $issue->status,
                    'updated_at' => $issue->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Issue Status Update Error', [
                'error' => $e->getMessage(),
                'issue_id' => $id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Arıza durumu güncellenemedi',
            ], 500);
        }
    }

    /**
     * 📄 Get single issue (show)
     */
    public function show(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $issue = IssueReport::with(['building', 'assignedEmployee', 'maintenanceSchedule'])
                ->where('company_id', $companyId)
                ->findOrFail($id);

            $data = [
                'id' => $issue->id,
                'building_id' => $issue->building_id,
                'reported_by' => $issue->reported_by,
                'issue_type' => $issue->issue_type,
                'priority' => $issue->priority,
                'priority_label' => $issue->priority_label,
                'description' => $issue->description,
                'location_details' => $issue->location_details,
                'contact_name' => $issue->contact_name,
                'contact_phone' => $issue->contact_phone,
                'status' => $issue->status,
                'status_label' => $issue->status_label,
                'assigned_employee_id' => $issue->assigned_employee_id,
                'assigned_at' => $issue->assigned_at?->toIso8601String(),
                'estimated_completion_time' => $issue->estimated_completion_time?->toIso8601String(),
                'actual_completion_time' => $issue->actual_completion_time?->toIso8601String(),
                'customer_notes' => $issue->customer_notes,
                'is_urgent' => $issue->is_urgent,
                'requires_immediate_attention' => $issue->requires_immediate_attention,
                'created_at' => $issue->created_at->toIso8601String(),
                'updated_at' => $issue->updated_at->toIso8601String(),
                'building' => $issue->building ? [
                    'id' => $issue->building->id,
                    'name' => $issue->building->name,
                    'address' => $issue->building->address,
                    'district' => $issue->building->district,
                    'city' => $issue->building->city,
                    'elevator_code' => $issue->building->elevator_code,
                ] : null,
                'assigned_employee' => $issue->assignedEmployee ? [
                    'id' => $issue->assignedEmployee->id,
                    'full_name' => $issue->assignedEmployee->full_name,
                    'position' => $issue->assignedEmployee->position,
                    'phone' => $issue->assignedEmployee->phone,
                ] : null,
                'maintenance_schedule_id' => $issue->maintenanceSchedule?->id,
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Arıza bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Issue Show Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Arıza yüklenemedi'], 500);
        }
    }

    /**
     * 👤 Assign employee to issue
     */
    public function assign(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $issue = IssueReport::where('company_id', $companyId)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'assigned_employee_id' => 'required|exists:employees,id',
                'estimated_completion_time' => 'nullable|date|after:now',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Doğrulama hatası', 'errors' => $validator->errors()], 422);
            }

            $employeeId = $request->assigned_employee_id;
            $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);
            $issue->assignEmployee($employeeId);
            $issue->load('maintenanceSchedule');
            $this->issueMaintenanceService->syncMaintenanceAssignment($issue);

            if ($request->filled('estimated_completion_time')) {
                $issue->update(['estimated_completion_time' => $request->estimated_completion_time]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Personel atandı',
                'data' => ['id' => $issue->id, 'assigned_employee_id' => $issue->assigned_employee_id, 'status' => $issue->status],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Arıza veya personel bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Issue Assign Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Atama yapılamadı'], 500);
        }
    }

    /**
     * ▶️ Start work on issue
     */
    public function startWork(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $issue = IssueReport::where('company_id', $companyId)->findOrFail($id);

            $issue->startWork();
            $issue->load('maintenanceSchedule');
            $this->issueMaintenanceService->syncMaintenanceStarted($issue);

            return response()->json([
                'success' => true,
                'message' => 'Çalışma başlatıldı',
                'data' => ['id' => $issue->id, 'status' => $issue->status],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Arıza bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Issue StartWork Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'İşlem başarısız'], 500);
        }
    }

    /**
     * 🔧 Create maintenance schedule from issue
     */
    public function createMaintenance(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $issue = IssueReport::with(['building', 'maintenanceSchedule'])->where('company_id', $companyId)->findOrFail($id);
            $schedule = $this->issueMaintenanceService->ensureMaintenanceSchedule($issue);

            return response()->json([
                'success' => true,
                'message' => 'Bakım kaydı oluşturuldu',
                'data' => [
                    'maintenance_schedule_id' => $schedule->id,
                    'issue_id' => $issue->id,
                    'already_exists' => $issue->maintenanceSchedule !== null,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Arıza bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Issue CreateMaintenance Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Bakım oluşturulamadı'], 500);
        }
    }

    /**
     * ✅ Complete issue
     */
    public function complete(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $issue = IssueReport::where('company_id', $companyId)->findOrFail($id);

            $issue->update([
                'status' => 'tamamlandi',
                'actual_completion_time' => now(),
                'customer_notes' => $request->input('customer_notes', $issue->customer_notes),
            ]);
            $issue->load('maintenanceSchedule');
            $this->issueMaintenanceService->syncMaintenanceCompleted($issue);

            return response()->json([
                'success' => true,
                'message' => 'Arıza tamamlandı olarak işaretlendi',
                'data' => ['id' => $issue->id, 'status' => $issue->status],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Arıza bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Issue Complete Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'İşlem başarısız'], 500);
        }
    }

    /**
     * 📊 Get issue stats
     */
    public function getStats(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $stats = [
                'total' => IssueReport::where('company_id', $companyId)->count(),
                'bildirildi' => IssueReport::where('company_id', $companyId)->where('status', 'bildirildi')->count(),
                'inceleniyor' => IssueReport::where('company_id', $companyId)->where('status', 'inceleniyor')->count(),
                'ekip_atandi' => IssueReport::where('company_id', $companyId)->where('status', 'ekip_atandi')->count(),
                'calisma_basladi' => IssueReport::where('company_id', $companyId)->where('status', 'calisma_basladi')->count(),
                'tamamlandi' => IssueReport::where('company_id', $companyId)->where('status', 'tamamlandi')->count(),
                'iptal_edildi' => IssueReport::where('company_id', $companyId)->where('status', 'iptal_edildi')->count(),
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            \Log::error('Mobile Issue GetStats Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'İstatistikler yüklenemedi'], 500);
        }
    }
}
