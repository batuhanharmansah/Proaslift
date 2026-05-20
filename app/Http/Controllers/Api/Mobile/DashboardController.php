<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Models\AccountingEntry;
use App\Models\IssueReport;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 📊 ENTERPRISE MOBILE DASHBOARD CONTROLLER
 * Gerçek zamanlı istatistikler, performans grafikleri, akıllı analizler
 */
class DashboardController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    /**
     * 📈 Get Dashboard Statistics
     */
    public function getStats(Request $request)
    {
        try {
            $user = $request->user();
            $companyId = $this->resolveMobileCompanyId($request);
            $currentMonth = now('Europe/Istanbul')->month;
            $currentYear = now('Europe/Istanbul')->year;

            // Kullanıcı çalışan mı kontrol et
            // 1. Önce user->employee ilişkisi (user_id ile)
            // 2. Fallback: Email ile Employee ara (SyncEmployeeUsers çalışmamış olabilir)
            $employee = $this->resolveMobileEmployee($user, $companyId);

            // Eğer çalışan ise VE admin değilse, ona özel istatistikler dön
            // Admin: position === 'yonetici' veya 'admin'
            if ($employee && !in_array($employee->position, ['yonetici', 'admin'])) {
                return $this->getEmployeeStats($request, $employee);
            }

            // Company yoksa admin istatistikleri dönemeyiz
            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            // Paralel sorgular ile performans optimizasyonu
            $stats = DB::select("
                SELECT
                    (SELECT COUNT(*) FROM employees WHERE company_id = ? AND is_active = 1) as total_employees,
                    (SELECT COUNT(*) FROM buildings WHERE company_id = ? AND status = 'aktif') as total_buildings,
                    (SELECT COUNT(*) FROM buildings WHERE company_id = ?) as active_customers,
                    (SELECT COUNT(*) FROM maintenance_schedules WHERE company_id = ? AND status IN ('planli', 'atandi', 'baslandi')) as active_maintenances,
                    (SELECT COUNT(*) FROM maintenance_reports mr
                     INNER JOIN maintenance_schedules ms ON mr.maintenance_schedule_id = ms.id
                     WHERE ms.company_id = ? AND MONTH(mr.created_at) = ? AND YEAR(mr.created_at) = ?
                     AND mr.completion_status = 'tamamlandi') as completed_maintenance,
                    (SELECT COUNT(*) FROM issue_reports WHERE company_id = ? AND status NOT IN ('tamamlandi', 'iptal_edildi')) as urgent_issues,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM accounting_entries
                     WHERE company_id = ? AND type = 'gelir' AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?) as this_month_income,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM accounting_entries
                     WHERE company_id = ? AND type IN ('gider', 'maas') AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?) as this_month_expense
            ", [
                $companyId, $companyId, $companyId, $companyId,
                $companyId, $currentMonth, $currentYear,
                $companyId,
                $companyId, $currentMonth, $currentYear,
                $companyId, $currentMonth, $currentYear
            ])[0];

            // Stok uyarıları
            $lowStockProducts = DB::table('products')
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->whereRaw('stock_quantity <= min_stock_level')
                ->count();

            $dashboardStats = [
                'totalEmployees' => (int) $stats->total_employees,
                'totalBuildings' => (int) $stats->total_buildings,
                'activeCustomers' => (int) $stats->active_customers,
                'activeMaintenances' => (int) $stats->active_maintenances,
                'completedMaintenance' => (int) $stats->completed_maintenance,
                'urgentIssues' => (int) $stats->urgent_issues,
                'thisMonthIncome' => (float) $stats->this_month_income,
                'thisMonthExpense' => (float) $stats->this_month_expense,
                'lowStockProducts' => (int) $lowStockProducts,
                'netIncome' => (float) ($stats->this_month_income - $stats->this_month_expense),
                'completionRate' => $stats->active_maintenances > 0
                    ? round(($stats->completed_maintenance / $stats->active_maintenances) * 100, 1)
                    : 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboardStats,
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Dashboard Stats Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İstatistikler yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📊 Get Dashboard Charts
     */
    public function getCharts(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            // Aylık gelir-gider trendi (son 6 ay)
            $monthlyTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $income = AccountingEntry::where('company_id', $companyId)
                    ->where('type', 'gelir')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('total_amount') ?? 0;

                $expense = AccountingEntry::where('company_id', $companyId)
                    ->whereIn('type', ['gider', 'maas'])
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('total_amount') ?? 0;

                $monthlyTrend[] = [
                    'month' => $date->format('M'),
                    'income' => (float) $income,
                    'expense' => (float) $expense,
                    'profit' => (float) ($income - $expense),
                ];
            }

            // Haftalık bakım dağılımı
            $weeklyData = [];
            $weeklyLabels = [];
            for ($i = 0; $i < 7; $i++) {
                $date = now()->startOfWeek()->addDays($i);
                $weeklyLabels[] = $date->format('D');

                $count = MaintenanceSchedule::where('company_id', $companyId)
                    ->whereDate('scheduled_date', $date)
                    ->whereIn('status', ['planli', 'atandi', 'baslandi'])
                    ->count();

                $weeklyData[] = $count;
            }

            // Bakım türü dağılımı
            $maintenanceTypes = MaintenanceSchedule::where('company_id', $companyId)
                ->selectRaw('maintenance_type, COUNT(*) as count')
                ->groupBy('maintenance_type')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->maintenance_type_label,
                        'value' => $item->count,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'monthlyTrend' => $monthlyTrend,
                    'weeklyChart' => [
                        'labels' => $weeklyLabels,
                        'data' => $weeklyData,
                    ],
                    'maintenanceTypes' => $maintenanceTypes,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Dashboard Charts Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Grafikler yüklenemedi',
            ], 500);
        }
    }

    /**
     * ⚡ Get Quick Actions Data
     */
    public function getQuickActions(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            // Yaklaşan bakımlar (7 gün)
            $upcomingMaintenance = MaintenanceSchedule::where('company_id', $companyId)
                ->whereBetween('scheduled_date', [now(), now()->addDays(7)])
                ->whereIn('status', ['planli', 'atandi'])
                ->count();

            // Acil arızalar
            $urgentIssues = IssueReport::where('company_id', $companyId)
                ->where('priority', 'acil')
                ->whereNotIn('status', ['tamamlandi', 'iptal_edildi'])
                ->count();

            // Geciken ödemeler
            $overduePayments = DB::table('receivables')
                ->where('company_id', $companyId)
                ->where('due_date', '<', now())
                ->where('status', '!=', 'tamamlandi')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'upcomingMaintenance' => $upcomingMaintenance,
                    'urgentIssues' => $urgentIssues,
                    'overduePayments' => $overduePayments,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Quick Actions Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hızlı işlemler yüklenemedi',
            ], 500);
        }
    }

    /**
     * 👷 Get Employee-Specific Dashboard Stats
     */
    private function getEmployeeStats(Request $request, $employee)
    {
        try {
            $currentMonth = now('Europe/Istanbul')->month;
            $currentYear = now('Europe/Istanbul')->year;

            // Çalışana atanan bakımlar (Tüm durumlar)
            $assignedMaintenances = MaintenanceSchedule::where('assigned_employee_id', $employee->id)
                ->whereIn('status', ['planli', 'atandi', 'baslandi'])
                ->with(['building', 'assignedEmployee'])
                ->orderBy('scheduled_date', 'asc')
                ->get();

            // Bu ay tamamlanan işler
            $completedThisMonth = MaintenanceReport::whereHas('maintenanceSchedule', function($q) use ($employee) {
                    $q->where('assigned_employee_id', $employee->id);
                })
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('completion_status', 'tamamlandi')
                ->count();

            // Bugün yapılacak işler
            $todayTasks = MaintenanceSchedule::where('assigned_employee_id', $employee->id)
                ->whereDate('scheduled_date', now()->toDateString())
                ->whereIn('status', ['planli', 'atandi', 'baslandi'])
                ->count();

            // Bu hafta yapılacak işler
            $thisWeekTasks = MaintenanceSchedule::where('assigned_employee_id', $employee->id)
                ->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->whereIn('status', ['planli', 'atandi', 'baslandi'])
                ->count();

            // Geciken işler
            $overdueTasks = MaintenanceSchedule::where('assigned_employee_id', $employee->id)
                ->where('scheduled_date', '<', now()->toDateString())
                ->whereIn('status', ['planli', 'atandi', 'baslandi'])
                ->count();

            // Atanan işleri detaylı şekilde formatla (mobil ActiveJobScreen için building, assigned_employee gerekli)
            $formattedTasks = $assignedMaintenances->map(function($task) {
                $building = $task->building;
                $assignedEmployee = $task->assignedEmployee;
                $scheduledDate = $task->scheduled_date;
                $scheduledDateString = $scheduledDate ? $scheduledDate->format('Y-m-d') : null;

                return [
                    'id' => $task->id,
                    'company_id' => $task->company_id,
                    'building_id' => $task->building_id,
                    'assigned_employee_id' => $task->assigned_employee_id,
                    'building_name' => $building?->name ?? 'Bilinmiyor',
                    'building_address' => $building?->address ?? '',
                    'maintenance_type' => $task->maintenance_type,
                    'maintenance_type_label' => $task->maintenance_type_label,
                    'scheduled_date' => $scheduledDateString,
                    'scheduled_time' => $task->scheduled_time,
                    'status' => $task->status,
                    'status_label' => $task->status_label,
                    'priority' => $task->priority ?? 'normal',
                    'description' => $task->description,
                    'estimated_duration' => $task->estimated_duration,
                    'is_overdue' => $scheduledDate ? $scheduledDate->lt(now()->startOfDay()) : false,
                    'days_until' => $scheduledDate ? now()->startOfDay()->diffInDays($scheduledDate, false) : null,
                    'building' => $building ? [
                        'id' => $building->id,
                        'name' => $building->name,
                        'address' => $building->address,
                        'latitude' => $building->latitude ? (float) $building->latitude : null,
                        'longitude' => $building->longitude ? (float) $building->longitude : null,
                        'elevator_code' => $building->elevator_code,
                    ] : null,
                    'assigned_employee' => $assignedEmployee ? [
                        'id' => $assignedEmployee->id,
                        'full_name' => $assignedEmployee->full_name,
                        'position' => $assignedEmployee->position,
                        'position_label' => $assignedEmployee->position_label,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'is_employee' => true,
                'employee_info' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'position' => $employee->position_label,
                ],
                'data' => [
                    'assigned_tasks_count' => $assignedMaintenances->count(),
                    'completed_this_month' => $completedThisMonth,
                    'today_tasks' => $todayTasks,
                    'this_week_tasks' => $thisWeekTasks,
                    'overdue_tasks' => $overdueTasks,
                ],
                'assigned_tasks' => $formattedTasks,
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee Dashboard Stats Error', [
                'error' => $e->getMessage(),
                'employee_id' => $employee->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Çalışan istatistikleri yüklenemedi',
            ], 500);
        }
    }

    private function resolveMobileEmployee($user, ?int $companyId): ?Employee
    {
        $employee = $user->employee;

        if (!$employee && $user->email) {
            $employee = Employee::where('email', $user->email)
                ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->first();
        }

        return $employee;
    }

    /**
     * 📦 Get Stock Warnings
     * Düşük stoklu ürünleri getirir
     */
    public function getStockWarnings(Request $request)
    {
        try {
            $user = $request->user();
            $companyId = $user->company_id;
            $limit = $request->query('limit', 10);

            // Düşük stoklu ürünleri getir
            $lowStockProducts = Product::where('company_id', $companyId)
                ->where('is_active', true)
                ->whereRaw('stock_quantity <= min_stock_level')
                ->orderByRaw('(stock_quantity / GREATEST(min_stock_level, 1)) ASC') // En kritik olanlar önce
                ->limit($limit)
                ->get()
                ->map(function ($product) {
                    // Priority hesapla
                    $stockRatio = $product->min_stock_level > 0 
                        ? ($product->stock_quantity / $product->min_stock_level) 
                        : 0;
                    
                    $priority = 'low';
                    if ($stockRatio <= 0) {
                        $priority = 'critical'; // Stok tükenmiş
                    } elseif ($stockRatio <= 0.25) {
                        $priority = 'critical'; // %25'in altında
                    } elseif ($stockRatio <= 0.5) {
                        $priority = 'high'; // %50'nin altında
                    } elseif ($stockRatio <= 0.75) {
                        $priority = 'medium'; // %75'in altında
                    }

                    return [
                        'id' => $product->id,
                        'material_id' => $product->id,
                        'material_name' => $product->name,
                        'current_stock' => (int) $product->stock_quantity,
                        'min_stock' => (int) $product->min_stock_level,
                        'unit' => $product->unit ?? 'adet',
                        'priority' => $priority,
                        'last_used_date' => null, // İsteğe bağlı: maintenance_reports'tan alınabilir
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $lowStockProducts,
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Stock Warnings Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Stok uyarıları yüklenemedi',
            ], 500);
        }
    }
}
