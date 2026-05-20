<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * 👥 ENTERPRISE MOBILE EMPLOYEES CONTROLLER
 * Personel yönetimi, performans takibi, görev dağılımı
 */
class EmployeeController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    /**
     * 📋 Get Employees List
     */
    public function index(Request $request)
    {
        try {
            $companyId = $this->resolveMobileCompanyId($request);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $query = Employee::where('company_id', $companyId);

            // Filters
            if ($request->filled('search')) {
                $search = addcslashes($request->search, '%_\\');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('position')) {
                $query->where('position', $request->position);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'first_name');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSorts = ['first_name', 'last_name', 'hire_date', 'position', 'salary'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $employees = $query->paginate(20);

            // Transform data for mobile
            $employees->getCollection()->transform(function ($employee) {
                // Get current month performance
                $currentMonthTasks = MaintenanceSchedule::where('assigned_employee_id', $employee->id)
                    ->whereMonth('scheduled_date', now()->month)
                    ->whereYear('scheduled_date', now()->year)
                    ->count();

                $completedTasks = MaintenanceSchedule::where('assigned_employee_id', $employee->id)
                    ->whereMonth('scheduled_date', now()->month)
                    ->whereYear('scheduled_date', now()->year)
                    ->where('status', 'tamamlandi')
                    ->count();

                return [
                    'id' => $employee->id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'full_name' => $employee->full_name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'address' => $employee->address,
                    'position' => $employee->position,
                    'position_label' => $employee->position_label,
                    'salary' => (float) $employee->salary,
                    'hire_date' => $employee->hire_date,
                    'is_active' => $employee->is_active,
                    'notes' => $employee->notes,
                    'created_at' => $employee->created_at,
                    'updated_at' => $employee->updated_at,

                    // Performance metrics
                    'performance' => [
                        'current_month_tasks' => $currentMonthTasks,
                        'completed_tasks' => $completedTasks,
                        'completion_rate' => $currentMonthTasks > 0
                            ? round(($completedTasks / $currentMonthTasks) * 100, 1)
                            : 0,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $employees->items(),
                'pagination' => [
                    'current_page' => $employees->currentPage(),
                    'last_page' => $employees->lastPage(),
                    'per_page' => $employees->perPage(),
                    'total' => $employees->total(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Employees Index Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Personel listesi yüklenemedi',
            ], 500);
        }
    }

    /**
     * 👤 Get Employee Detail
     */
    public function show(Request $request, $id)
    {
        try {
            $companyId = $this->resolveMobileCompanyId($request);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $employee = Employee::where('company_id', $companyId)->findOrFail($id);

            // Get detailed performance metrics
            $performanceMetrics = $this->getEmployeePerformance($employee->id);

            // Get recent maintenance schedules
            $recentMaintenances = MaintenanceSchedule::with(['building', 'maintenanceReport'])
                ->where('assigned_employee_id', $employee->id)
                ->orderBy('scheduled_date', 'desc')
                ->take(10)
                ->get();

            $employeeData = [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'address' => $employee->address,
                'position' => $employee->position,
                'position_label' => $employee->position_label,
                'salary' => (float) $employee->salary,
                'hire_date' => $employee->hire_date,
                'is_active' => $employee->is_active,
                'notes' => $employee->notes,
                'created_at' => $employee->created_at,
                'updated_at' => $employee->updated_at,

                // Performance data
                'performance' => $performanceMetrics,

                // Recent work
                'recent_maintenances' => $recentMaintenances->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'building_name' => $schedule->building->name,
                        'maintenance_type' => $schedule->maintenance_type,
                        'maintenance_type_label' => $schedule->maintenance_type_label,
                        'scheduled_date' => $schedule->scheduled_date,
                        'status' => $schedule->status,
                        'status_label' => $schedule->status_label,
                        'priority' => $schedule->priority,
                        'priority_label' => $schedule->priority_label,
                        'is_completed' => $schedule->status === 'tamamlandi',
                        'completion_date' => $schedule->maintenanceReport?->end_time,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $employeeData,
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Employee Detail Error', [
                'error' => $e->getMessage(),
                'employee_id' => $id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Personel detayları yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📊 Get Employee Performance
     */
    public function getPerformance(Request $request, $id)
    {
        try {
            $companyId = $this->resolveMobileCompanyId($request);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            // Verify employee belongs to company
            $employee = Employee::where('company_id', $companyId)->findOrFail($id);

            $performance = $this->getEmployeePerformance($employee->id);

            return response()->json([
                'success' => true,
                'data' => $performance,
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Employee Performance Error', [
                'error' => $e->getMessage(),
                'employee_id' => $id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Personel performansı yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📈 Calculate Employee Performance Metrics
     */
    private function getEmployeePerformance($employeeId): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Bu ay görevleri
        $thisMonthTasks = MaintenanceSchedule::where('assigned_employee_id', $employeeId)
            ->whereMonth('scheduled_date', $currentMonth)
            ->whereYear('scheduled_date', $currentYear)
            ->count();

        $completedThisMonth = MaintenanceSchedule::where('assigned_employee_id', $employeeId)
            ->whereMonth('scheduled_date', $currentMonth)
            ->whereYear('scheduled_date', $currentYear)
            ->where('status', 'tamamlandi')
            ->count();

        // Bu yıl toplam
        $thisYearTasks = MaintenanceSchedule::where('assigned_employee_id', $employeeId)
            ->whereYear('scheduled_date', $currentYear)
            ->count();

        $completedThisYear = MaintenanceSchedule::where('assigned_employee_id', $employeeId)
            ->whereYear('scheduled_date', $currentYear)
            ->where('status', 'tamamlandi')
            ->count();

        // Ortalama tamamlanma süresi
        $averageCompletionTime = MaintenanceReport::whereHas('maintenanceSchedule', function ($query) use ($employeeId) {
                $query->where('assigned_employee_id', $employeeId);
            })
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        // Son 6 ay performans trendi
        $monthlyPerformance = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $monthTasks = MaintenanceSchedule::where('assigned_employee_id', $employeeId)
                ->whereMonth('scheduled_date', $date->month)
                ->whereYear('scheduled_date', $date->year)
                ->count();

            $monthCompleted = MaintenanceSchedule::where('assigned_employee_id', $employeeId)
                ->whereMonth('scheduled_date', $date->month)
                ->whereYear('scheduled_date', $date->year)
                ->where('status', 'tamamlandi')
                ->count();

            $monthlyPerformance[] = [
                'month' => $date->format('M'),
                'tasks' => $monthTasks,
                'completed' => $monthCompleted,
                'rate' => $monthTasks > 0 ? round(($monthCompleted / $monthTasks) * 100, 1) : 0,
            ];
        }

        return [
            'current_month' => [
                'tasks' => $thisMonthTasks,
                'completed' => $completedThisMonth,
                'completion_rate' => $thisMonthTasks > 0
                    ? round(($completedThisMonth / $thisMonthTasks) * 100, 1)
                    : 0,
            ],
            'current_year' => [
                'tasks' => $thisYearTasks,
                'completed' => $completedThisYear,
                'completion_rate' => $thisYearTasks > 0
                    ? round(($completedThisYear / $thisYearTasks) * 100, 1)
                    : 0,
            ],
            'average_completion_time_minutes' => round($averageCompletionTime, 1),
            'monthly_performance' => $monthlyPerformance,
        ];
    }

    /**
     * ✏️ Update employee profile (mobile)
     */
    public function update(Request $request, $id)
    {
        try {
            $companyId = $this->resolveMobileCompanyId($request);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $employee = Employee::where('company_id', $companyId)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'address' => 'nullable|string',
                'position' => 'required|in:teknisyen,usta,muhendis,yonetici,muhasebe',
                'salary' => 'required|numeric|min:0',
                'hire_date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $employee->update($request->only([
                'first_name', 'last_name', 'phone', 'email', 'address',
                'position', 'salary', 'hire_date', 'notes',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Çalışan bilgileri güncellendi',
                'data' => [
                    'id' => $employee->id,
                    'full_name' => $employee->full_name,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Çalışan bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile Employee Update Error', [
                'error' => $e->getMessage(),
                'employee_id' => $id,
                'user_id' => $request->user()->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Güncelleme yapılamadı',
            ], 500);
        }
    }
}
