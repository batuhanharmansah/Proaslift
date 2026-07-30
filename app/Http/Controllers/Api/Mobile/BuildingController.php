<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingContact;
use App\Models\BuildingDocument;
use App\Models\Employee;
use App\Models\ElevatorLabel;
use App\Models\IssueReport;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 🏢 ENTERPRISE MOBILE BUILDINGS CONTROLLER
 * Bina yönetimi, asansör takibi, QR kod desteği
 */
class BuildingController extends Controller
{
    /**
     * 📋 Get Buildings List
     */
    public function index(Request $request)
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
                return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
            }

            $query = Building::with(['primaryContact', 'activeLabel'])
                ->where('company_id', $companyId);

            // Filters
            if ($request->filled('search')) {
                $search = addcslashes($request->search, '%_\\');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%")
                      ->orWhere('district', 'LIKE', "%{$search}%")
                      ->orWhere('elevator_code', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('district')) {
                $query->where('district', $request->district);
            }

            if ($request->filled('contract_type')) {
                $query->where('contract_type', $request->contract_type);
            }

            if ($request->filled('operational_status')) {
                $query->where('operational_status', $request->operational_status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSorts = ['name', 'created_at', 'contract_end_date', 'monthly_fee'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
            $buildings = $query->paginate($perPage);

            // Transform data for mobile
            $buildings->getCollection()->transform(function ($building) {
                return [
                    'id' => $building->id,
                    'name' => $building->name,
                    'address' => $building->address,
                    'district' => $building->district,
                    'city' => $building->city,
                    'floor_count' => $building->floor_count,
                    'elevator_count' => $building->elevator_count,
                    'elevator_type' => $building->elevator_type,
                    'elevator_brand' => $building->elevator_brand,
                    'elevator_model' => $building->elevator_model,
                    'contract_type' => $building->contract_type,
                    'monthly_fee' => (float) $building->monthly_fee,
                    'contract_start_date' => $building->contract_start_date,
                    'contract_end_date' => $building->contract_end_date,
                    'status' => $building->status,
                    'operational_status' => $building->operational_status,
                    'elevator_code' => $building->elevator_code,
                    'capacity_kg' => $building->capacity_kg,
                    'capacity_person' => $building->capacity_person,
                    'manufacturer' => $building->manufacturer,
                    'model' => $building->model,
                    'serial_number' => $building->serial_number,
                    'responsible_person' => $building->responsible_person,
                    'responsible_phone' => $building->responsible_phone,
                    'responsible_email' => $building->responsible_email,
                    'created_at' => $building->created_at,
                    'updated_at' => $building->updated_at,
                    'primary_contact' => $building->primaryContact ? [
                        'id' => $building->primaryContact->id,
                        'name' => $building->primaryContact->name,
                        'title' => $building->primaryContact->title,
                        'phone' => $building->primaryContact->phone,
                        'email' => $building->primaryContact->email,
                    ] : null,
                    'active_label' => $building->activeLabel ? [
                        'id' => $building->activeLabel->id,
                        'label_color' => $building->activeLabel->label_color,
                        'label_color_text' => $building->activeLabel->label_color_text ?? ($building->activeLabel->label_color ?? null),
                        'control_date' => $building->activeLabel->control_date,
                        'due_date' => $building->activeLabel->due_date,
                        'status' => $building->activeLabel->status,
                        'status_text' => $building->activeLabel->status_text ?? ($building->activeLabel->status ?? null),
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $buildings->items(),
                'pagination' => [
                    'current_page' => $buildings->currentPage(),
                    'last_page' => $buildings->lastPage(),
                    'per_page' => $buildings->perPage(),
                    'total' => $buildings->total(),
                    'from' => $buildings->firstItem(),
                    'to' => $buildings->lastItem(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Buildings Index Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Binalar yüklenemedi',
            ], 500);
        }
    }

    /**
     * ➕ Create Building (mobil - yeni bina ekleme)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'floor_count' => 'required|integer|min:1',
            'elevator_count' => 'required|integer|min:1',
            'elevator_type' => 'required|in:yolcu,yuk,hasta,karma',
            'contract_type' => 'required|in:bakim,onarim,modernizasyon',
            'monthly_fee' => 'required|numeric|min:0',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'elevator_code' => 'nullable|string|max:255',
            'capacity_kg' => 'nullable|integer|min:1',
            'capacity_person' => 'nullable|integer|min:1',
            'responsible_person' => 'nullable|string|max:255',
            'responsible_phone' => 'nullable|string|max:255',
            'responsible_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();
            $companyId = $user->company_id;

            if (!$companyId) {
                $employee = $user->employee ?? Employee::where('email', $user->email)->first();
                $companyId = $employee?->company_id;
            }

            if (!$companyId) {
                return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
            }

            $building = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $companyId) {
                $building = Building::create(array_merge($request->only([
                    'name', 'address', 'district', 'city', 'floor_count', 'elevator_count',
                    'elevator_type', 'contract_type', 'monthly_fee', 'contract_start_date', 'contract_end_date',
                    'elevator_code', 'capacity_kg', 'capacity_person',
                    'responsible_person', 'responsible_phone', 'responsible_email',
                ]), [
                    'company_id' => $companyId,
                    'operational_status' => 'aktif',
                ]));

                app(\App\Services\BuildingFinancialService::class)->createInitialRecords(
                    $building,
                    $request->contract_start_date,
                    $request->contract_end_date,
                    $companyId,
                    $request->user()->id
                );

                return $building;
            });

            return response()->json([
                'success' => true,
                'message' => 'Bina başarıyla eklendi.',
                'data' => [
                    'id' => $building->id,
                    'name' => $building->name,
                    'address' => $building->address,
                    'district' => $building->district,
                    'city' => $building->city,
                    'floor_count' => $building->floor_count,
                    'elevator_count' => $building->elevator_count,
                    'elevator_type' => $building->elevator_type,
                    'contract_type' => $building->contract_type,
                    'monthly_fee' => (float) $building->monthly_fee,
                    'contract_start_date' => $building->contract_start_date?->format('Y-m-d'),
                    'contract_end_date' => $building->contract_end_date?->format('Y-m-d'),
                    'operational_status' => $building->operational_status,
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Mobile Building Store Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bina eklenirken bir hata oluştu.',
            ], 500);
        }
    }

    /**
     * 🏢 Get Building Detail
     */
    public function show(Request $request, $id)
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
                return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
            }

            // Eager load relationships - issueReports relationship'i kontrol et
            $building = Building::with([
                'contacts',
                'documents' => function ($query) {
                    $query->where('status', 'aktif')->latest()->take(10);
                },
                'elevatorLabels' => function ($query) {
                    $query->orderBy('control_date', 'desc')->take(5);
                },
                'maintenanceSchedules' => function ($query) {
                    $query->with('assignedEmployee')->latest()->take(10);
                },
                'maintenanceSchedules.assignedEmployee',
            ])
            ->where('company_id', $companyId)
            ->findOrFail($id);
            
            // issueReports'u ayrı yükle - relationship kontrolü ile
            try {
                $building->load(['issueReports' => function ($query) {
                    $query->whereNotIn('status', ['tamamlandi', 'iptal_edildi'])->latest()->take(5);
                }]);
            } catch (\Exception $e) {
                \Log::warning('IssueReports relationship failed', [
                    'building_id' => $building->id,
                    'error' => $e->getMessage()
                ]);
                // Relationship yoksa boş collection ata
                $building->setRelation('issueReports', collect([]));
            }

            // Calculate additional metrics
            $totalMaintenanceThisYear = MaintenanceSchedule::where('building_id', $building->id)
                ->whereYear('scheduled_date', now()->year)
                ->count();

            $completedMaintenanceThisYear = MaintenanceSchedule::where('building_id', $building->id)
                ->whereYear('scheduled_date', now()->year)
                ->where('status', 'tamamlandi')
                ->count();

            $buildingData = [
                'id' => $building->id,
                'name' => $building->name,
                'address' => $building->address,
                'district' => $building->district,
                'city' => $building->city,
                'latitude' => $building->latitude ? (float) $building->latitude : null,
                'longitude' => $building->longitude ? (float) $building->longitude : null,
                'floor_count' => $building->floor_count,
                'elevator_count' => $building->elevator_count,
                'elevator_type' => $building->elevator_type,
                'elevator_brand' => $building->elevator_brand,
                'elevator_model' => $building->elevator_model,
                'installation_year' => $building->installation_year,
                'contract_type' => $building->contract_type,
                'monthly_fee' => (float) $building->monthly_fee,
                'contract_start_date' => $building->contract_start_date ? $building->contract_start_date->format('Y-m-d') : null,
                'contract_end_date' => $building->contract_end_date ? $building->contract_end_date->format('Y-m-d') : null,
                'status' => $building->status,
                'operational_status' => $building->operational_status,
                'elevator_code' => $building->elevator_code,
                'capacity_kg' => $building->capacity_kg,
                'capacity_person' => $building->capacity_person,
                'manufacturer' => $building->manufacturer,
                'model' => $building->model,
                'serial_number' => $building->serial_number,
                'responsible_person' => $building->responsible_person,
                'responsible_phone' => $building->responsible_phone,
                'responsible_email' => $building->responsible_email,
                'elevator_notes' => $building->elevator_notes,
                'notes' => $building->notes,
                'created_at' => $building->created_at ? $building->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $building->updated_at ? $building->updated_at->format('Y-m-d H:i:s') : null,

                // Relations
                'contacts' => $building->contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'title' => $contact->title,
                        'phone' => $contact->phone,
                        'email' => $contact->email,
                        'apartment_no' => $contact->apartment_no,
                        'is_primary' => $contact->is_primary,
                        'is_active' => $contact->is_active,
                    ];
                }),

                'elevator_labels' => $building->elevatorLabels->map(function ($label) {
                    return [
                        'id' => $label->id,
                        'label_color' => $label->label_color,
                        'label_color_text' => $label->label_color_text ?? ($label->label_color ?? null),
                        'control_date' => $label->control_date ? $label->control_date->format('Y-m-d') : null,
                        'due_date' => $label->due_date ? $label->due_date->format('Y-m-d') : null,
                        'status' => $label->status,
                        'status_text' => $label->status_text ?? ($label->status ?? null),
                        'description' => $label->description,
                        'inspector_name' => $label->inspector_name,
                        'inspector_company' => $label->inspector_company,
                    ];
                }),

                'maintenance_schedules' => $building->maintenanceSchedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'maintenance_type' => $schedule->maintenance_type,
                        'maintenance_type_label' => $schedule->maintenance_type_label ?? ($schedule->maintenance_type ?? null),
                        'scheduled_date' => $schedule->scheduled_date ? $schedule->scheduled_date->format('Y-m-d') : null,
                        'priority' => $schedule->priority,
                        'priority_label' => $schedule->priority_label ?? ($schedule->priority ?? null),
                        'status' => $schedule->status,
                        'status_label' => $schedule->status_label ?? ($schedule->status ?? null),
                        'description' => $schedule->description,
                        'assigned_employee' => $schedule->assignedEmployee ? [
                            'id' => $schedule->assignedEmployee->id,
                            'full_name' => $schedule->assignedEmployee->full_name ?? null,
                            'position' => $schedule->assignedEmployee->position ?? null,
                        ] : null,
                    ];
                }),

                'issue_reports' => ($building->relationLoaded('issueReports') && $building->issueReports) 
                    ? $building->issueReports->map(function ($issue) {
                        return [
                            'id' => $issue->id,
                            'issue_type' => $issue->issue_type,
                            'priority' => $issue->priority,
                            'priority_label' => $issue->priority_label ?? ($issue->priority ?? null),
                            'description' => $issue->description,
                            'status' => $issue->status,
                            'reported_by' => $issue->reported_by,
                            'contact_name' => $issue->contact_name,
                            'contact_phone' => $issue->contact_phone,
                            'is_urgent' => $issue->is_urgent ?? false,
                            'created_at' => $issue->created_at ? $issue->created_at->format('Y-m-d H:i:s') : null,
                        ];
                    })
                    : [],

                // Metrics
                'metrics' => [
                    'total_maintenance_this_year' => $totalMaintenanceThisYear,
                    'completed_maintenance_this_year' => $completedMaintenanceThisYear,
                    'completion_rate' => $totalMaintenanceThisYear > 0
                        ? round(($completedMaintenanceThisYear / $totalMaintenanceThisYear) * 100, 1)
                        : 0,
                    'days_until_contract_end' => $building->contract_end_date 
                        ? (int) now()->diffInDays($building->contract_end_date, false) 
                        : null,
                    'is_contract_expiring' => $building->contract_end_date 
                        ? (int) now()->diffInDays($building->contract_end_date, false) <= 30 
                        : false,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $buildingData,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Mobile Building Detail Not Found', [
                'building_id' => $id,
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bina bulunamadı veya erişim yetkiniz yok',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Building Detail Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'building_id' => $id,
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'Bina detayları yüklenemedi: ' . $e->getMessage() : 'Bina detayları yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📞 Get Building Contacts
     */
    public function getContacts(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            // Verify building belongs to company
            $building = Building::where('company_id', $companyId)->findOrFail($id);

            $contacts = BuildingContact::where('building_id', $building->id)
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'title' => $contact->title,
                        'phone' => $contact->phone,
                        'email' => $contact->email,
                        'apartment_no' => $contact->apartment_no,
                        'is_primary' => $contact->is_primary,
                        'notes' => $contact->notes,
                        'created_at' => $contact->created_at,
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Building Contacts Error', [
                'error' => $e->getMessage(),
                'building_id' => $id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bina kişileri yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📄 Get Building Documents
     */
    public function getDocuments(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            // Verify building belongs to company
            $building = Building::where('company_id', $companyId)->findOrFail($id);

            $documents = BuildingDocument::where('building_id', $building->id)
                ->where('status', 'aktif')
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $documents->map(function ($document) {
                    return [
                        'id' => $document->id,
                        'title' => $document->title,
                        'description' => $document->description,
                        'file_name' => $document->file_name,
                        'file_type' => $document->file_type,
                        'file_size' => $document->file_size,
                        'document_type' => $document->document_type,
                        'payment_month' => $document->payment_month,
                        'payment_amount' => $document->payment_amount,
                        'created_at' => $document->created_at,
                        'download_url' => asset('storage/' . $document->file_path),
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Building Documents Error', [
                'error' => $e->getMessage(),
                'building_id' => $id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bina belgeleri yüklenemedi',
            ], 500);
        }
    }

    /**
     * 🔧 Get Building Maintenance History
     */
    public function getMaintenanceHistory(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            // Verify building belongs to company
            $building = Building::where('company_id', $companyId)->findOrFail($id);

            $maintenanceHistory = MaintenanceSchedule::with(['assignedEmployee', 'maintenanceReport'])
                ->where('building_id', $building->id)
                ->orderBy('scheduled_date', 'desc')
                ->take(50)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $maintenanceHistory->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'maintenance_type' => $schedule->maintenance_type,
                        'maintenance_type_label' => $schedule->maintenance_type_label,
                        'scheduled_date' => $schedule->scheduled_date,
                        'scheduled_time' => $schedule->scheduled_time,
                        'priority' => $schedule->priority,
                        'priority_label' => $schedule->priority_label,
                        'status' => $schedule->status,
                        'status_label' => $schedule->status_label,
                        'description' => $schedule->description,
                        'estimated_cost' => $schedule->estimated_cost,
                        'estimated_duration' => $schedule->estimated_duration,
                        'notes' => $schedule->notes,
                        'created_at' => $schedule->created_at,
                        'assigned_employee' => $schedule->assignedEmployee ? [
                            'id' => $schedule->assignedEmployee->id,
                            'full_name' => $schedule->assignedEmployee->full_name,
                            'position' => $schedule->assignedEmployee->position,
                            'position_label' => $schedule->assignedEmployee->position_label,
                            'phone' => $schedule->assignedEmployee->phone,
                        ] : null,
                        'maintenance_report' => $schedule->maintenanceReport ? [
                            'id' => $schedule->maintenanceReport->id,
                            'start_time' => $schedule->maintenanceReport->start_time,
                            'end_time' => $schedule->maintenanceReport->end_time,
                            'work_description' => $schedule->maintenanceReport->work_description,
                            'total_cost' => $schedule->maintenanceReport->total_cost,
                            'completion_status' => $schedule->maintenanceReport->completion_status,
                            'completion_percentage' => $schedule->maintenanceReport->completion_percentage,
                        ] : null,
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Building Maintenance History Error', [
                'error' => $e->getMessage(),
                'building_id' => $id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bakım geçmişi yüklenemedi',
            ], 500);
        }
    }

    /**
     * 🔍 Search Buildings by QR Code
     */
    public function searchByQR(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'qr_code' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR kod gerekli',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $companyId = $request->user()->company_id;
            $qrCode = $request->qr_code;

            // Search by elevator code or building ID
            $building = Building::where('company_id', $companyId)
                ->where(function ($query) use ($qrCode) {
                    $query->where('elevator_code', $qrCode)
                          ->orWhere('id', $qrCode);
                })
                ->with(['primaryContact', 'activeLabel'])
                ->first();

            if (!$building) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR kod ile eşleşen bina bulunamadı',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bina bulundu',
                'data' => [
                    'id' => $building->id,
                    'name' => $building->name,
                    'address' => $building->address,
                    'district' => $building->district,
                    'city' => $building->city,
                    'elevator_code' => $building->elevator_code,
                    'status' => $building->status,
                    'operational_status' => $building->operational_status,
                    'primary_contact' => $building->primaryContact ? [
                        'name' => $building->primaryContact->name,
                        'phone' => $building->primaryContact->phone,
                    ] : null,
                    'active_label' => $building->activeLabel ? [
                        'label_color' => $building->activeLabel->label_color,
                        'label_color_text' => $building->activeLabel->label_color_text,
                        'control_date' => $building->activeLabel->control_date,
                        'due_date' => $building->activeLabel->due_date,
                    ] : null,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile QR Search Error', [
                'error' => $e->getMessage(),
                'qr_code' => $request->qr_code ?? 'N/A',
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'QR kod araması başarısız',
            ], 500);
        }
    }

    /**
     * 📊 Get Building Statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $stats = [
                'total_buildings' => Building::where('company_id', $companyId)->count(),
                'active_buildings' => Building::where('company_id', $companyId)->where('status', 'aktif')->count(),
                'buildings_by_status' => Building::where('company_id', $companyId)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status'),
                'buildings_by_district' => Building::where('company_id', $companyId)
                    ->selectRaw('district, COUNT(*) as count')
                    ->groupBy('district')
                    ->orderByDesc('count')
                    ->get()
                    ->pluck('count', 'district'),
                'total_elevators' => Building::where('company_id', $companyId)->sum('elevator_count'),
                'average_monthly_fee' => Building::where('company_id', $companyId)->avg('monthly_fee'),
                'expiring_contracts' => Building::where('company_id', $companyId)
                    ->where('contract_end_date', '<=', now()->addDays(30))
                    ->where('contract_end_date', '>', now())
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Building Statistics Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bina istatistikleri yüklenemedi',
            ], 500);
        }
    }
}
