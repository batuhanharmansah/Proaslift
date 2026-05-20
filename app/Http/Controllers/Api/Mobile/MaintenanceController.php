<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Models\Building;
use App\Models\Employee;
use App\Models\Product;
use App\Models\AccountingEntry;
use App\Services\MaintenanceApprovalService;
use App\Support\MaintenanceReportApprovalSerializer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * 🔧 ENTERPRISE MOBILE MAINTENANCE CONTROLLER
 * Bakım yönetimi, takvim görünümü, rapor sistemi
 */
class MaintenanceController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    private function resolveMobileEmployee($user, ?int $companyId): ?Employee
    {
        return $user->employee
            ?? Employee::where('email', $user->email)
                ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
                ->first();
    }

    /**
     * 📋 Get Maintenance Schedules
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $companyId = $user->company_id;

            // Check if user is an employee - if so, only show their assigned jobs
            // 1. user->employee ilişkisi, 2. Fallback: email ile ara
            $employee = $user->employee;
            if (!$employee && $user->email) {
                $employee = Employee::where('email', $user->email)
                    ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                    ->first();
                if ($employee && !$companyId) {
                    $companyId = $employee->company_id;
                }
            }

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            // Company filtresi: building veya maintenance_schedules.company_id
            $query = MaintenanceSchedule::with(['building', 'assignedEmployee', 'maintenanceReport'])
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                        ->orWhereHas('building', fn($b) => $b->where('company_id', $companyId));
                });

            // Sadece "employee" rolündekiler kendi atamalarını görsün; company_admin tüm planlı işleri görsün
            if ($employee && $user->hasRole('employee')) {
                $query->where('assigned_employee_id', $employee->id);
            }

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('maintenance_type')) {
                $query->where('maintenance_type', $request->maintenance_type);
            }

            if ($request->filled('building_id')) {
                $query->where('building_id', $request->building_id);
            }

            if ($request->filled('employee_id')) {
                $query->where('assigned_employee_id', $request->employee_id);
            }

            if ($request->filled('date_from')) {
                $query->where('scheduled_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('scheduled_date', '<=', $request->date_to);
            }

            // Date range shortcuts
            if ($request->filled('range')) {
                switch ($request->range) {
                    case 'today':
                        $query->whereDate('scheduled_date', now());
                        break;
                    case 'this_week':
                        $query->whereBetween('scheduled_date', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereMonth('scheduled_date', now()->month)
                              ->whereYear('scheduled_date', now()->year);
                        break;
                    case 'overdue':
                        $query->where('scheduled_date', '<', now())
                              ->whereNotIn('status', ['tamamlandi', 'iptal']);
                        break;
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'scheduled_date');
            $sortOrder = $request->get('sort_order', 'asc');

            if ($sortBy === 'priority') {
                $query->orderByRaw("FIELD(priority, 'acil', 'yuksek', 'normal', 'dusuk')");
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Mobilde Planlı/Tamamlanan/Gecikmiş hepsinin gelmesi için per_page destegi (max 200)
            $perPage = (int) $request->get('per_page', 20);
            $perPage = min(max($perPage, 1), 200);
            $maintenances = $query->paginate($perPage);

            // Transform data
            $maintenances->getCollection()->transform(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'building_id' => $schedule->building_id,
                    'assigned_employee_id' => $schedule->assigned_employee_id,
                    'maintenance_type' => $schedule->maintenance_type,
                    'maintenance_type_label' => $schedule->maintenance_type_label,
                    'scheduled_date' => $schedule->scheduled_date,
                    'scheduled_time' => $schedule->scheduled_time,
                    'priority' => $schedule->priority,
                    'priority_label' => $schedule->priority_label,
                    'status' => $schedule->status,
                    'status_label' => $schedule->status_label,
                    'description' => $schedule->description,
                    'notes' => $schedule->notes,
                    'estimated_cost' => $schedule->estimated_cost,
                    'estimated_duration' => $schedule->estimated_duration,
                    'created_at' => $schedule->created_at,
                    'updated_at' => $schedule->updated_at,

                    'building' => $schedule->building ? [
                        'id' => $schedule->building->id,
                        'name' => $schedule->building->name,
                        'address' => $schedule->building->address,
                        'district' => $schedule->building->district,
                        'city' => $schedule->building->city,
                        'elevator_count' => $schedule->building->elevator_count,
                        'elevator_code' => $schedule->building->elevator_code,
                    ] : null,

                    'assigned_employee' => $schedule->assignedEmployee ? [
                        'id' => $schedule->assignedEmployee->id,
                        'full_name' => $schedule->assignedEmployee->full_name,
                        'position' => $schedule->assignedEmployee->position,
                        'position_label' => $schedule->assignedEmployee->position_label,
                        'phone' => $schedule->assignedEmployee->phone,
                    ] : null,

                    'maintenance_report' => $this->formatMaintenanceReportForApi($schedule->maintenanceReport),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $maintenances->items(),
                'pagination' => [
                    'current_page' => $maintenances->currentPage(),
                    'last_page' => $maintenances->lastPage(),
                    'per_page' => $maintenances->perPage(),
                    'total' => $maintenances->total(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Index Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım listesi yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📅 Get Maintenance Calendar
     */
    public function getCalendar(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $startDate = $request->get('start_date', now()->startOfMonth());
            $endDate = $request->get('end_date', now()->endOfMonth());

            $maintenances = MaintenanceSchedule::with(['building', 'assignedEmployee'])
                ->whereHas('building', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->orderBy('scheduled_date')
                ->get();

            // Group by date
            $calendarData = $maintenances->groupBy(function ($item) {
                return $item->scheduled_date->format('Y-m-d');
            })->map(function ($dayMaintenances, $date) {
                return [
                    'date' => $date,
                    'count' => $dayMaintenances->count(),
                    'maintenances' => $dayMaintenances->map(function ($schedule) {
                        return [
                            'id' => $schedule->id,
                            'building_name' => $schedule->building->name,
                            'maintenance_type' => $schedule->maintenance_type,
                            'maintenance_type_label' => $schedule->maintenance_type_label,
                            'scheduled_time' => $schedule->scheduled_time,
                            'priority' => $schedule->priority,
                            'priority_label' => $schedule->priority_label,
                            'status' => $schedule->status,
                            'status_label' => $schedule->status_label,
                            'assigned_employee' => $schedule->assignedEmployee ? [
                                'id' => $schedule->assignedEmployee->id,
                                'full_name' => $schedule->assignedEmployee->full_name,
                            ] : null,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => array_values($calendarData->toArray()),
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Calendar Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım takvimi yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Maintenance (Yeni Bakım Planla)
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Mobile Maintenance Store - request received', ['user_id' => $request->user()->id, 'data' => $request->all()]);
            $companyId = $request->user()->company_id;
            if (!$companyId) {
                return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
            }

            $validator = Validator::make($request->all(), [
                'building_id' => 'required|exists:buildings,id',
                'maintenance_type' => 'required|in:rutin_bakim,ariza_onarim,periyodik_kontrol,modernizasyon',
                'scheduled_date' => 'required|date|after_or_equal:today',
                'scheduled_time' => 'nullable|string|max:10',
                'priority' => 'required|in:dusuk,normal,yuksek,acil',
                'description' => 'required|string|max:2000',
                'assigned_employee_id' => 'nullable|exists:employees,id',
                'notes' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $building = Building::where('id', $request->building_id)->where('company_id', $companyId)->firstOrFail();

            $data = $request->only([
                'building_id', 'maintenance_type', 'scheduled_date', 'scheduled_time',
                'priority', 'assigned_employee_id', 'description', 'notes',
            ]);
            $data['company_id'] = $companyId;
            $data['status'] = 'planli';

            $schedule = MaintenanceSchedule::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Bakım işi planlandı',
                'data' => [
                    'id' => $schedule->id,
                    'scheduled_date' => $schedule->scheduled_date->format('Y-m-d'),
                    'status' => $schedule->status,
                ],
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bina veya personel bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Store Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'user_id' => $request->user()->id]);
            return response()->json(['success' => false, 'message' => config('app.debug') ? $e->getMessage() : 'Bakım planlanamadı'], 500);
        }
    }

    /**
     * 📄 Get Maintenance Detail
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $companyId = $this->resolveMobileCompanyId($request);
            $employee = $this->resolveMobileEmployee($user, $companyId);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                    'data' => null,
                ], 403);
            }

            // Eager load relationships with null safety
            $maintenanceQuery = MaintenanceSchedule::with([
                'building',
                'assignedEmployee',
                'maintenanceReport',
                'maintenanceReports',
            ])
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereHas('building', function ($buildingQuery) use ($companyId) {
                            $buildingQuery->where('company_id', $companyId);
                        });
                });

            if ($user->hasRole('employee')) {
                if (!$employee) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Personel kaydınız bulunamadı',
                        'data' => null,
                    ], 403);
                }

                $maintenanceQuery->where('assigned_employee_id', $employee->id);
            }

            $maintenance = $maintenanceQuery->findOrFail($id);

            // Veri kontrolü - maintenance bulunamazsa 404 döner (findOrFail)
            if (!$maintenance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bakım bulunamadı',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $maintenance->id,
                    'building_id' => $maintenance->building_id,
                    'assigned_employee_id' => $maintenance->assigned_employee_id,
                    'maintenance_type' => $maintenance->maintenance_type,
                    'maintenance_type_label' => $maintenance->maintenance_type_label ?? ($maintenance->maintenance_type ?? null),
                    'scheduled_date' => $maintenance->scheduled_date ? $maintenance->scheduled_date->format('Y-m-d') : null,
                    'scheduled_time' => $maintenance->scheduled_time,
                    'priority' => $maintenance->priority,
                    'priority_label' => $maintenance->priority_label ?? ($maintenance->priority ?? null),
                    'status' => $maintenance->status,
                    'status_label' => $maintenance->status_label ?? ($maintenance->status ?? null),
                    'description' => $maintenance->description,
                    'notes' => $maintenance->notes,
                    'estimated_cost' => $maintenance->estimated_cost ? (float) $maintenance->estimated_cost : null,
                    'estimated_duration' => $maintenance->estimated_duration,
                    'created_at' => $maintenance->created_at ? $maintenance->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $maintenance->updated_at ? $maintenance->updated_at->format('Y-m-d H:i:s') : null,

                    'building' => $maintenance->building ? [
                        'id' => $maintenance->building->id,
                        'name' => $maintenance->building->name,
                        'address' => $maintenance->building->address,
                        'district' => $maintenance->building->district,
                        'city' => $maintenance->building->city,
                        'latitude' => $maintenance->building->latitude ? (float) $maintenance->building->latitude : null,
                        'longitude' => $maintenance->building->longitude ? (float) $maintenance->building->longitude : null,
                        'elevator_count' => $maintenance->building->elevator_count,
                        'elevator_code' => $maintenance->building->elevator_code,
                        'elevator_brand' => $maintenance->building->elevator_brand,
                        'elevator_model' => $maintenance->building->elevator_model,
                    ] : null,

                    'assigned_employee' => $maintenance->assignedEmployee ? [
                        'id' => $maintenance->assignedEmployee->id,
                        'full_name' => $maintenance->assignedEmployee->full_name ?? (($maintenance->assignedEmployee->first_name ?? '') . ' ' . ($maintenance->assignedEmployee->last_name ?? '')),
                        'position' => $maintenance->assignedEmployee->position ?? null,
                        'position_label' => $maintenance->assignedEmployee->position_label ?? ($maintenance->assignedEmployee->position ?? null),
                        'phone' => $maintenance->assignedEmployee->phone ?? null,
                        'email' => $maintenance->assignedEmployee->email ?? null,
                    ] : null,

                    'maintenance_report' => $this->formatMaintenanceReportForApi($maintenance->maintenanceReport),
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Mobile Maintenance Detail Not Found', [
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım bulunamadı veya erişim yetkiniz yok',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Show Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'Bakım detayı yüklenemedi: ' . $e->getMessage() : 'Bakım detayı yüklenemedi',
                'data' => null,
            ], 500);
        }
    }

    /**
     * 🚀 Start Work (İşe Başla)
     * Çalışan sadece kendine atanan işi başlatabilir. Status: planli/atandi → baslandi
     */
    public function start(Request $request, $id)
    {
        try {
            $user = $request->user();
            $companyId = $this->resolveMobileCompanyId($request);
            $employee = $this->resolveMobileEmployee($user, $companyId);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                    'data' => null,
                ], 403);
            }

            $maintenance = MaintenanceSchedule::with(['building', 'assignedEmployee', 'maintenanceReport'])
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereHas('building', function ($buildingQuery) use ($companyId) {
                            $buildingQuery->where('company_id', $companyId);
                        });
                })
                ->findOrFail($id);

            // Çalışan kontrolü: Sadece atanan personel işe başlayabilir
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel kaydınız bulunamadı',
                    'data' => null,
                ], 403);
            }

            if ($maintenance->assigned_employee_id != $employee->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu iş size atanmamış. Sadece size atanan işlere başlayabilirsiniz.',
                    'data' => null,
                ], 403);
            }

            if (!in_array($maintenance->status, ['planli', 'atandi'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu iş başlatılamaz. Durum: ' . ($maintenance->status_label ?? $maintenance->status),
                    'data' => null,
                ], 422);
            }

            $maintenance->update(['status' => 'baslandi']);
            $maintenance->refresh();
            $maintenance->load(['building', 'assignedEmployee', 'maintenanceReport']);

            return response()->json([
                'success' => true,
                'message' => 'İş başarıyla başlatıldı',
                'data' => [
                    'id' => $maintenance->id,
                    'status' => $maintenance->status,
                    'status_label' => $maintenance->status_label,
                    'building' => $maintenance->building ? [
                        'id' => $maintenance->building->id,
                        'name' => $maintenance->building->name,
                        'address' => $maintenance->building->address,
                        'district' => $maintenance->building->district,
                        'city' => $maintenance->building->city,
                        'latitude' => $maintenance->building->latitude ? (float) $maintenance->building->latitude : null,
                        'longitude' => $maintenance->building->longitude ? (float) $maintenance->building->longitude : null,
                        'elevator_count' => $maintenance->building->elevator_count,
                        'elevator_code' => $maintenance->building->elevator_code,
                    ] : null,
                    'assigned_employee' => $maintenance->assignedEmployee ? [
                        'id' => $maintenance->assignedEmployee->id,
                        'full_name' => $maintenance->assignedEmployee->full_name ?? (($maintenance->assignedEmployee->first_name ?? '') . ' ' . ($maintenance->assignedEmployee->last_name ?? '')),
                        'position' => $maintenance->assignedEmployee->position ?? null,
                        'position_label' => $maintenance->assignedEmployee->position_label ?? null,
                    ] : null,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Mobile Maintenance Start Not Found', [
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım bulunamadı veya erişim yetkiniz yok',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Start Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'İş başlatılamadı: ' . $e->getMessage() : 'İş başlatılamadı',
                'data' => null,
            ], 500);
        }
    }

    /**
     * ✏️ Update Maintenance
     */
    public function update(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            $maintenance = MaintenanceSchedule::whereHas('building', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'building_id' => 'nullable|exists:buildings,id',
                'assigned_employee_id' => 'nullable|exists:employees,id',
                'maintenance_type' => 'nullable|in:rutin_bakim,ariza_onarim,periyodik_kontrol,modernizasyon',
                'scheduled_date' => 'nullable|date',
                'scheduled_time' => 'nullable|date_format:H:i',
                'priority' => 'nullable|in:dusuk,normal,yuksek,acil',
                'status' => 'nullable|in:planli,atandi,baslandi,tamamlandi,ertelendi,iptal',
                'description' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000',
                'estimated_cost' => 'nullable|numeric|min:0',
                'estimated_duration' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Building kontrolü - aynı company'ye ait olmalı
            if ($request->filled('building_id')) {
                $building = Building::where('company_id', $companyId)
                    ->findOrFail($request->building_id);
            }

            // Employee kontrolü - aynı company'ye ait olmalı
            if ($request->filled('assigned_employee_id')) {
                $employee = Employee::where('company_id', $companyId)
                    ->findOrFail($request->assigned_employee_id);
            }

            $maintenance->update($request->only([
                'building_id',
                'assigned_employee_id',
                'maintenance_type',
                'scheduled_date',
                'scheduled_time',
                'priority',
                'status',
                'description',
                'notes',
                'estimated_cost',
                'estimated_duration',
            ]));

            $maintenance->refresh();
            $maintenance->load(['building', 'assignedEmployee']);

            return response()->json([
                'success' => true,
                'message' => 'Bakım başarıyla güncellendi',
                'data' => [
                    'id' => $maintenance->id,
                    'building_id' => $maintenance->building_id,
                    'assigned_employee_id' => $maintenance->assigned_employee_id,
                    'maintenance_type' => $maintenance->maintenance_type,
                    'maintenance_type_label' => $maintenance->maintenance_type_label ?? ($maintenance->maintenance_type ?? null),
                    'scheduled_date' => $maintenance->scheduled_date ? $maintenance->scheduled_date->format('Y-m-d') : null,
                    'scheduled_time' => $maintenance->scheduled_time,
                    'priority' => $maintenance->priority,
                    'priority_label' => $maintenance->priority_label ?? ($maintenance->priority ?? null),
                    'status' => $maintenance->status,
                    'status_label' => $maintenance->status_label ?? ($maintenance->status ?? null),
                    'description' => $maintenance->description ?? null,
                    'notes' => $maintenance->notes ?? null,
                    'estimated_cost' => $maintenance->estimated_cost ? (float) $maintenance->estimated_cost : null,
                    'estimated_duration' => $maintenance->estimated_duration ?? null,
                    'created_at' => $maintenance->created_at ? $maintenance->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $maintenance->updated_at ? $maintenance->updated_at->format('Y-m-d H:i:s') : null,

                    'building' => $maintenance->building ? [
                        'id' => $maintenance->building->id,
                        'name' => $maintenance->building->name,
                        'address' => $maintenance->building->address,
                        'district' => $maintenance->building->district,
                        'city' => $maintenance->building->city,
                        'latitude' => $maintenance->building->latitude ? (float) $maintenance->building->latitude : null,
                        'longitude' => $maintenance->building->longitude ? (float) $maintenance->building->longitude : null,
                        'elevator_count' => $maintenance->building->elevator_count,
                        'elevator_code' => $maintenance->building->elevator_code,
                        'elevator_brand' => $maintenance->building->elevator_brand,
                        'elevator_model' => $maintenance->building->elevator_model,
                    ] : null,

                    'assigned_employee' => $maintenance->assignedEmployee ? [
                        'id' => $maintenance->assignedEmployee->id,
                        'full_name' => $maintenance->assignedEmployee->full_name ?? (($maintenance->assignedEmployee->first_name ?? '') . ' ' . ($maintenance->assignedEmployee->last_name ?? '')),
                        'position' => $maintenance->assignedEmployee->position ?? null,
                        'position_label' => $maintenance->assignedEmployee->position_label ?? ($maintenance->assignedEmployee->position ?? null),
                        'phone' => $maintenance->assignedEmployee->phone ?? null,
                        'email' => $maintenance->assignedEmployee->email ?? null,
                    ] : null,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Mobile Maintenance Update Not Found', [
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım bulunamadı veya erişim yetkiniz yok',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Update Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'Bakım güncellenemedi: ' . $e->getMessage() : 'Bakım güncellenemedi',
                'data' => null,
            ], 500);
        }
    }

    /**
     * ✅ Complete Maintenance
     */
    public function complete(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            $maintenance = MaintenanceSchedule::whereHas('building', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'work_description' => 'required|string',
                'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
                'total_cost' => 'nullable|numeric|min:0',
                'problems_found' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'customer_name' => 'nullable|string|max:255',
                'customer_notes' => 'nullable|string',
                'completion_percentage' => 'nullable|integer|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Create maintenance report
            $report = MaintenanceReport::create([
                'maintenance_schedule_id' => $maintenance->id,
                'employee_id' => $request->user()->employee?->id,
                'start_time' => $maintenance->updated_at, // Use last update as start time
                'end_time' => now(),
                'work_description' => $request->work_description,
                'total_cost' => $request->total_cost ?? 0,
                'problems_found' => $request->problems_found,
                'recommendations' => $request->recommendations,
                'completion_status' => $request->completion_status,
                'customer_name' => $request->customer_name,
                'customer_notes' => $request->customer_notes,
                'completion_percentage' => $request->completion_percentage ?? 100,
                'customer_signature' => false, // Mobile'da imza özelliği sonra eklenecek
            ]);

            // Update maintenance status
            $maintenance->update([
                'status' => $request->completion_status === 'tamamlandi' ? 'tamamlandi' : 'baslandi',
            ]);

            $approvalSms = ['sent' => false];
            if ($request->completion_status === 'tamamlandi') {
                $report->load('building');
                if (!$report->building_id && $maintenance->building_id) {
                    $report->update(['building_id' => $maintenance->building_id, 'company_id' => $maintenance->company_id]);
                }
                $approvalSms = app(MaintenanceApprovalService::class)
                    ->initiateApprovalFlow($report->fresh(['building']));
            }

            return response()->json([
                'success' => true,
                'message' => 'Bakım raporu başarıyla kaydedildi',
                'data' => [
                    'report_id' => $report->id,
                    'maintenance_id' => $maintenance->id,
                    'status' => $maintenance->status,
                    'approval_status' => $report->fresh()->approval_status,
                    'approval_sms_sent' => $approvalSms['sent'] ?? false,
                    'approval_sms_error' => $approvalSms['error'] ?? $approvalSms['skipped_reason'] ?? null,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Mobile Maintenance Complete Not Found', [
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım bulunamadı veya erişim yetkiniz yok',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance Complete Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'maintenance_id' => $id,
                'user_id' => $request->user()->id ?? null,
                'company_id' => $request->user()->company_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'Bakım tamamlanamadı: ' . $e->getMessage() : 'Bakım tamamlanamadı',
                'data' => null,
            ], 500);
        }
    }

    /**
     * 📦 Ürün listesi (rapor formu için)
     * Bakım formlarında malzeme seçimi - depo stokları
     */
    public function products(Request $request)
    {
        try {
            $user = $request->user();
            $companyId = $user->company_id;

            // Çalışan kullanıcılar için company_id'yi employee kaydından al
            if (!$companyId) {
                $employee = $user->employee ?? Employee::where('email', $user->email)->first();
                $companyId = $employee?->company_id;
            }

            if (!$companyId) {
                \Log::warning('Mobile products: company_id bulunamadı', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
            }

            // Ürünleri getir (aktif ve stokta olan)
            $products = Product::where('company_id', $companyId)
                ->where(function ($q) {
                    $q->where('is_active', true)
                      ->orWhereNull('is_active'); // is_active null olanları da dahil et
                })
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'category', 'unit', 'sale_price', 'stock_quantity', 'min_stock_level']);

            \Log::info('Mobile products loaded', [
                'company_id' => $companyId,
                'user_id' => $user->id,
                'product_count' => $products->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'code' => $p->code,
                    'brand' => $p->code, // Ürün markası (code ile aynı değer; UI'da "Marka" göstermek için)
                    'category' => $p->category,
                    'category_label' => $p->category_label ?? $p->category,
                    'unit' => $p->unit,
                    'sale_price' => (float) ($p->sale_price ?? 0),
                    'stock_quantity' => (int) ($p->stock_quantity ?? 0),
                    'min_stock_level' => (int) ($p->min_stock_level ?? 0),
                ]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Mobile products error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Ürünler yüklenemedi'], 500);
        }
    }

    /**
     * 📝 Bakım raporu kaydet (Standard + Rutin)
     */
    public function storeReport(Request $request, $id)
    {
        try {
            $user = $request->user();
            $companyId = $this->resolveMobileCompanyId($request);

            if (!$companyId) {
                return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
            }

            $maintenance = MaintenanceSchedule::with('building')
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                        ->orWhereHas('building', fn($b) => $b->where('company_id', $companyId));
                })
                ->findOrFail($id);

            $employee = $this->resolveMobileEmployee($user, $companyId);
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Personel kaydı bulunamadı'], 403);
            }
            if ($maintenance->assigned_employee_id != $employee->id) {
                return response()->json(['success' => false, 'message' => 'Bu iş size atanmamış'], 403);
            }

            $isRoutine = $maintenance->maintenance_type === 'rutin_bakim';

            if ($isRoutine) {
                $validated = $request->validate([
                    'start_time' => 'required|date',
                    'end_time' => 'nullable|date|after:start_time',
                    'recommendations' => 'nullable|string',
                    'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
                    'customer_name' => 'nullable|string|max:255',
                    'customer_notes' => 'nullable|string',
                    'routine_maintenance_checklist' => 'required|string|json',
                    'completion_percentage' => 'required|integer|min:0|max:100',
                ]);
            } else {
                $validated = $request->validate([
                    'start_time' => 'required|date',
                    'end_time' => 'nullable|date|after:start_time',
                    'work_description' => 'required|string|min:10',
                    'problems_found' => 'nullable|string',
                    'recommendations' => 'nullable|string',
                    'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
                    'customer_signature' => 'nullable|boolean',
                    'customer_name' => 'nullable|string|max:255',
                    'customer_notes' => 'nullable|string',
                    'used_products' => 'nullable|array',
                    'used_products.*.product_id' => 'required_with:used_products|exists:products,id',
                    'used_products.*.quantity' => 'required_with:used_products|numeric|min:0.1',
                    'used_products.*.unit_price' => 'required_with:used_products|numeric|min:0',
                ]);
            }

            $report = DB::transaction(function () use ($validated, $maintenance, $employee, $isRoutine) {
                $totalCost = 0;
                $usedProductsData = [];

                if ($isRoutine) {
                    $startTime = \Carbon\Carbon::parse($validated['start_time'])->setTimezone('Europe/Istanbul');
                    $endTime = ($validated['end_time'] ?? null) ? \Carbon\Carbon::parse($validated['end_time'])->setTimezone('Europe/Istanbul') : $startTime->copy()->addHours(2);
                    $totalCost += ($startTime->diffInMinutes($endTime) / 60) * 150;

                    $checklistData = json_decode($validated['routine_maintenance_checklist'], true);
                    $problems = [];
                    foreach ($checklistData ?? [] as $sectionId => $items) {
                        foreach ((array) $items as $item) {
                            if (!empty($item['has_error'])) {
                                $problems[] = '❌ HATA: ' . ($item['title'] ?? '') . (!empty($item['notes']) ? ' - ' . $item['notes'] : '');
                            } elseif (empty($item['checked'])) {
                                $problems[] = '⚠️ Tamamlanmadı: ' . ($item['title'] ?? '');
                            } elseif (!empty($item['notes'])) {
                                $problems[] = 'ℹ️ ' . ($item['title'] ?? '') . ' - Not: ' . $item['notes'];
                            }
                        }
                    }

                    return MaintenanceReport::create([
                        'maintenance_schedule_id' => $maintenance->id,
                        'employee_id' => $employee->id,
                        'start_time' => $validated['start_time'],
                        'end_time' => $validated['end_time'] ?? null,
                        'work_description' => 'Rutin Bakım Kontrolü Tamamlandı',
                        'used_products' => null,
                        'total_cost' => $totalCost,
                        'problems_found' => !empty($problems) ? implode("\n", $problems) : null,
                        'recommendations' => $validated['recommendations'] ?? null,
                        'completion_status' => $validated['completion_status'],
                        'customer_signature' => false,
                        'customer_name' => $validated['customer_name'] ?? null,
                        'customer_notes' => $validated['customer_notes'] ?? null,
                        'photos' => null,
                        'routine_maintenance_checklist' => $checklistData,
                        'completion_percentage' => $validated['completion_percentage'],
                    ]);
                }

                if (!empty($validated['used_products'])) {
                    foreach ($validated['used_products'] as $pd) {
                        if (empty($pd['product_id']) || ($pd['quantity'] ?? 0) <= 0) continue;
                        $product = Product::find($pd['product_id']);
                        if (!$product || $product->company_id != $maintenance->company_id) continue;
                        if ($product->stock_quantity < ($pd['quantity'] ?? 0)) {
                            throw new \Exception("{$product->name} için yeterli stok yok. Mevcut: {$product->stock_quantity}");
                        }
                        $subtotal = ($pd['quantity'] ?? 0) * ($pd['unit_price'] ?? 0);
                        $totalCost += $subtotal;
                        $usedProductsData[] = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'product_code' => $product->code,
                            'product_category' => $product->category,
                            'quantity' => $pd['quantity'],
                            'unit' => $product->unit ?? 'adet',
                            'unit_price' => $pd['unit_price'] ?? 0,
                            'subtotal' => $subtotal,
                        ];
                        $product->decrement('stock_quantity', $pd['quantity']);
                    }
                }
                $startTime = \Carbon\Carbon::parse($validated['start_time'])->setTimezone('Europe/Istanbul');
                $endTime = ($validated['end_time'] ?? null) ? \Carbon\Carbon::parse($validated['end_time'])->setTimezone('Europe/Istanbul') : $startTime->copy()->addHours(2);
                $totalCost += ($startTime->diffInMinutes($endTime) / 60) * 150;

                return MaintenanceReport::create([
                    'maintenance_schedule_id' => $maintenance->id,
                    'employee_id' => $employee->id,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'] ?? null,
                    'work_description' => $validated['work_description'],
                    'used_products' => $usedProductsData,
                    'total_cost' => $totalCost,
                    'problems_found' => $validated['problems_found'] ?? null,
                    'recommendations' => $validated['recommendations'] ?? null,
                    'completion_status' => $validated['completion_status'],
                    'customer_signature' => $validated['customer_signature'] ?? false,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'customer_notes' => $validated['customer_notes'] ?? null,
                    'photos' => null,
                    'routine_maintenance_checklist' => null,
                    'completion_percentage' => null,
                ]);
            });

            $maintenance->update([
                'status' => $validated['completion_status'] === 'tamamlandi' ? 'tamamlandi' : 'baslandi',
            ]);

            if ($validated['completion_status'] === 'tamamlandi' && $report->total_cost > 0) {
                AccountingEntry::create([
                    'company_id' => $maintenance->company_id,
                    'type' => 'gelir',
                    'category' => $maintenance->maintenance_type === 'rutin_bakim' ? 'Bakım Hizmeti' : 'Onarım Hizmeti',
                    'description' => 'Bakım işi tamamlandı - ' . ($maintenance->building->name ?? ''),
                    'amount' => $report->total_cost,
                    'vat_rate' => 20.00,
                    'vat_amount' => $report->total_cost * 0.20,
                    'total_amount' => $report->total_cost * 1.20,
                    'transaction_date' => now('Europe/Istanbul')->toDateString(),
                    'building_id' => $maintenance->building_id,
                    'employee_id' => $employee->id,
                    'maintenance_report_id' => $report->id,
                    'payment_method' => 'banka_havalesi',
                    'status' => 'tahsil_edildi',
                    'created_by' => $user->id,
                ]);
            }

            $approvalSms = ['sent' => false];
            if ($validated['completion_status'] === 'tamamlandi') {
                $approvalSms = app(MaintenanceApprovalService::class)
                    ->initiateApprovalFlow($report->fresh(['building']));
            }

            return response()->json([
                'success' => true,
                'message' => 'Bakım raporu başarıyla kaydedildi',
                'data' => [
                    'report_id' => $report->id,
                    'maintenance_id' => $maintenance->id,
                    'status' => $maintenance->status,
                    'approval_status' => $report->fresh()->approval_status,
                    'approval_sms_sent' => $approvalSms['sent'] ?? false,
                    'approval_sms_error' => $approvalSms['error'] ?? $approvalSms['skipped_reason'] ?? null,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Doğrulama hatası', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bakım bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile storeReport error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => config('app.debug') ? $e->getMessage() : 'Rapor kaydedilemedi'], 500);
        }
    }

    /**
     * Onay SMS tekrar gönder (firma yöneticisi)
     */
    public function resendApprovalSms(Request $request, $id)
    {
        try {
            $companyId = $this->resolveMobileCompanyId($request);
            $maintenance = MaintenanceSchedule::with(['building', 'maintenanceReport'])
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                        ->orWhereHas('building', fn ($b) => $b->where('company_id', $companyId));
                })
                ->findOrFail($id);

            if (!$maintenance->building) {
                return response()->json(['success' => false, 'message' => 'Bina bulunamadı'], 404);
            }

            $result = app(MaintenanceApprovalService::class)
                ->sendApprovalSms($maintenance->building, true);

            return response()->json([
                'success' => $result['sent'] ?? false,
                'message' => ($result['sent'] ?? false)
                    ? 'Onay SMS gönderildi'
                    : ($this->approvalSmsSkipMessage($result['skipped_reason'] ?? null) ?? ($result['error'] ?? 'SMS gönderilemedi')),
                'data' => $result,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bakım bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile resendApprovalSms error', ['error' => $e->getMessage(), 'maintenance_id' => $id]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'SMS gönderilemedi',
            ], 500);
        }
    }

    private function formatMaintenanceReportForApi(?MaintenanceReport $report): ?array
    {
        if (!$report) {
            return null;
        }

        return array_merge([
            'id' => $report->id,
            'start_time' => $report->start_time ? $report->start_time->format('Y-m-d H:i:s') : null,
            'end_time' => $report->end_time ? $report->end_time->format('Y-m-d H:i:s') : null,
            'work_description' => $report->work_description ?? null,
            'total_cost' => $report->total_cost ? (float) $report->total_cost : null,
            'completion_status' => $report->completion_status ?? null,
            'completion_percentage' => $report->completion_percentage ?? null,
            'problems_found' => $report->problems_found ?? null,
            'recommendations' => $report->recommendations ?? null,
            'customer_name' => $report->customer_name ?? null,
            'customer_notes' => $report->customer_notes ?? null,
            'routine_maintenance_checklist' => $report->routine_maintenance_checklist ?? null,
        ], MaintenanceReportApprovalSerializer::forApi($report) ?? []);
    }

    private function approvalSmsSkipMessage(?string $reason): ?string
    {
        return match ($reason) {
            'no_pending' => 'Onay bekleyen iş bulunmuyor',
            'no_contact' => 'Bina için birincil iletişim kişisi tanımlı değil',
            'invalid_phone' => 'İletişim telefonu geçersiz',
            'cooldown' => 'Son SMS gönderiminden sonra bekleme süresi dolmadı',
            default => null,
        };
    }

    /**
     * 📄 Bakım raporu PDF indir
     * GET /api/mobile/maintenance/{id}/report/download
     */
    public function downloadReport(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            $maintenance = MaintenanceSchedule::with(['building', 'assignedEmployee', 'maintenanceReport'])
                ->whereHas('building', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->findOrFail($id);

            if (!$maintenance->maintenanceReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu iş için henüz rapor oluşturulmamış.',
                ], 404);
            }

            $maintenance->load(['building', 'assignedEmployee', 'maintenanceReport.employee']);
            $pdf = Pdf::loadView('pdf.maintenance-report', compact('maintenance'))->setPaper('A4', 'portrait');
            $fileName = 'bakim-raporu-' . \Str::slug($maintenance->building->name) . '-' . $maintenance->scheduled_date->format('Y-m-d') . '.pdf';

            return $pdf->download($fileName);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bakım bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Maintenance downloadReport error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'PDF oluşturulamadı'], 500);
        }
    }
}
