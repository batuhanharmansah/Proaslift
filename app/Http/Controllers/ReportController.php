<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Models\FinancialTransaction;
use App\Models\AccountingEntry;
use App\Models\IssueReport;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $currentMonth = now('Europe/Istanbul')->month;
        $currentYear = now('Europe/Istanbul')->year;

        // Bu ay gelir
        $monthlyIncome = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('total_amount') ?? 0;

        // Bu ay gider
        $monthlyExpense = AccountingEntry::where('company_id', $companyId)
            ->whereIn('type', ['gider', 'maas'])
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('total_amount') ?? 0;

        // Tamamlanan bakım (bu ay)
        $completedMaintenance = MaintenanceSchedule::where('company_id', $companyId)
            ->where('status', 'tamamlandi')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->count();

        // Aktif müşteri sayısı (aktif binalar)
        $activeBuildings = Building::where('company_id', $companyId)
            ->where('status', 'aktif')
            ->count();

        return view('reports.index', compact(
            'monthlyIncome', 'monthlyExpense', 'completedMaintenance', 'activeBuildings'
        ));
    }

    // Finansal Raporlar
    public function financial(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Gelir-Gider Özeti
        $income = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $expense = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $profit = $income - $expense;

        // Aylık Trend
        $monthlyData = AccountingEntry::where('company_id', $companyId)
            ->whereBetween('transaction_date', [now()->subMonths(11), now()])
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month,
                        SUM(CASE WHEN type = "gelir" THEN amount ELSE 0 END) as income,
                        SUM(CASE WHEN type = "gider" THEN amount ELSE 0 END) as expense')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Kategori Bazlı Giderler
        $expenseByCategory = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gider')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $expenseByCategory->each(function ($row) use ($expense) {
            $row->total_expense = $row->total;
            $row->percentage = $expense > 0 ? ($row->total / $expense) * 100 : 0;
        });

        // Bina Bazlı Gelirler
        $incomeByBuilding = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereNotNull('building_id')
            ->with('building')
            ->selectRaw('building_id, SUM(amount) as total')
            ->groupBy('building_id')
            ->orderByDesc('total')
            ->get();

        $incomeByBuilding->each(function ($row) use ($income) {
            $row->total_income = $row->total;
            $row->percentage = $income > 0 ? ($row->total / $income) * 100 : 0;
        });

        return view('reports.financial', compact(
            'income', 'expense', 'profit', 'monthlyData',
            'expenseByCategory', 'incomeByBuilding', 'startDate', 'endDate'
        ));
    }

    // Bakım Performans Raporları
    public function maintenance(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Genel İstatistikler
        $totalMaintenance = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->count();

        $completedMaintenance = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->where('status', 'tamamlandi')
            ->count();

        $onTimeCompletion = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->where('status', 'tamamlandi')
            ->count();

        $completionRate = $totalMaintenance > 0 ? ($completedMaintenance / $totalMaintenance) * 100 : 0;
        $onTimeRate = $completedMaintenance > 0 ? ($onTimeCompletion / $completedMaintenance) * 100 : 0;

        // Bakım Türü Dağılımı
        $maintenanceByType = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->selectRaw('maintenance_type, COUNT(*) as count')
            ->groupBy('maintenance_type')
            ->get();

        // Personel Performansı
        $employeePerformance = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->withCount(['maintenanceSchedules as total_assigned' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->withCount(['maintenanceSchedules as completed' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate])
                      ->where('status', 'tamamlandi');
            }])
            ->get()
            ->map(function($employee) {
                $employee->completion_rate = $employee->total_assigned > 0
                    ? ($employee->completed / $employee->total_assigned) * 100
                    : 0;
                return $employee;
            });

        // Aylık Bakım Trendi
        $monthlyMaintenance = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [now()->subMonths(11), now()])
            ->selectRaw('DATE_FORMAT(scheduled_date, "%Y-%m") as month,
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "tamamlandi" THEN 1 ELSE 0 END) as completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.maintenance', compact(
            'totalMaintenance', 'completedMaintenance', 'completionRate', 'onTimeRate',
            'maintenanceByType', 'employeePerformance', 'monthlyMaintenance', 'startDate', 'endDate'
        ));
    }

    // Personel Verimlilik Raporları
    public function employee(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Personel Listesi ve Performans
        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->withCount(['maintenanceSchedules as total_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->withCount(['maintenanceSchedules as completed_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate])
                      ->where('status', 'tamamlandi');
            }])
            ->withCount(['maintenanceSchedules as on_time_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate])
                      ->where('status', 'tamamlandi');
            }])
            ->get()
            ->map(function($employee) {
                $employee->completion_rate = $employee->total_jobs > 0
                    ? ($employee->completed_jobs / $employee->total_jobs) * 100
                    : 0;
                $employee->on_time_rate = $employee->completed_jobs > 0
                    ? ($employee->on_time_jobs / $employee->completed_jobs) * 100
                    : 0;
                return $employee;
            });

        // Pozisyon Bazlı Performans
        $performanceByPosition = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->withCount(['maintenanceSchedules as total_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->withCount(['maintenanceSchedules as completed_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate])
                      ->where('status', 'tamamlandi');
            }])
            ->get()
            ->groupBy('position')
            ->map(function($group, $position) {
                $totalJobs = $group->sum('total_jobs');
                $completedJobs = $group->sum('completed_jobs');
                return (object) [
                    'position' => $position,
                    'total_jobs' => $totalJobs,
                    'completed_jobs' => $completedJobs,
                    'completion_rate' => $totalJobs > 0 ? ($completedJobs / $totalJobs) * 100 : 0,
                    'employee_count' => $group->count()
                ];
            })
            ->values();

        // En Aktif Personel
        $topPerformers = $employees->sortByDesc('completed_jobs')->take(5);

        return view('reports.employee', compact(
            'employees', 'performanceByPosition', 'topPerformers', 'startDate', 'endDate'
        ));
    }

    // Müşteri Analizleri
    public function customer(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Bina İstatistikleri
        $buildings = Building::where('company_id', $companyId)
            ->withCount(['maintenanceSchedules as total_maintenance' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->withCount(['maintenanceSchedules as completed_maintenance' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate])
                      ->where('status', 'tamamlandi');
            }])
            ->withSum(['accountingEntries as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->where('type', 'gelir')
                      ->whereBetween('transaction_date', [$startDate, $endDate]);
            }], 'amount')
            ->get()
            ->map(function($building) {
                $building->maintenance_rate = $building->total_maintenance > 0
                    ? ($building->completed_maintenance / $building->total_maintenance) * 100
                    : 0;
                return $building;
            });

        // Sözleşme Durumu
        $contractStatus = [
            'active' => Building::where('company_id', $companyId)->where('status', 'aktif')->count(),
            'expiring_soon' => Building::where('company_id', $companyId)
                ->where('status', 'aktif')
                ->where('contract_end_date', '<=', now()->addDays(30))
                ->where('contract_end_date', '>', now())
                ->count(),
            'expired' => Building::where('company_id', $companyId)
                ->where('status', 'aktif')
                ->where('contract_end_date', '<', now())
                ->count()
        ];

        // Gelir Dağılımı
        $revenueByBuilding = Building::where('company_id', $companyId)
            ->withSum(['accountingEntries as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->where('type', 'gelir')
                      ->whereBetween('transaction_date', [$startDate, $endDate]);
            }], 'amount')
            ->orderByDesc('total_revenue')
            ->get();

        // Arıza Analizi
        $issueAnalysis = IssueReport::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('issue_type, COUNT(*) as count,
                        SUM(CASE WHEN is_urgent = 1 THEN 1 ELSE 0 END) as urgent_count')
            ->groupBy('issue_type')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                // Türkçe etiket ekle
                $item->issue_type_label = match($item->issue_type) {
                    'elektrik_arizasi' => 'Elektrik Arızası',
                    'mekanik_ariza' => 'Mekanik Arıza',
                    'kapı_arizasi' => 'Kapı Arızası',
                    'ses_sistemi' => 'Ses Sistemi',
                    'acil_durum' => 'Acil Durum',
                    'diger' => 'Diğer',
                    default => 'Bilinmiyor'
                };
                return $item;
            });

        // Özet istatistikler
        $totalBuildings = Building::where('company_id', $companyId)->count();
        $activeContracts = Building::where('company_id', $companyId)->where('status', 'aktif')->count();
        $totalRevenue = $buildings->sum('total_revenue') ?? 0;
        $averageRevenue = $buildings->count() > 0 ? $totalRevenue / $buildings->count() : 0;

        // Bina performans analizi
        $buildingPerformance = $buildings->map(function($building) {
            return (object) [
                'name' => $building->name,
                'status' => $building->status,
                'total_maintenance' => $building->total_maintenance,
                'completed_maintenance' => $building->completed_maintenance,
                'maintenance_rate' => $building->maintenance_rate,
                'total_revenue' => $building->total_revenue ?? 0
            ];
        });

        // En çok gelir getiren binalar
        $topRevenueBuildings = $buildings->sortByDesc('total_revenue')->take(3);

        return view('reports.customer', compact(
            'buildings', 'contractStatus', 'revenueByBuilding', 'issueAnalysis',
            'totalBuildings', 'activeContracts', 'totalRevenue', 'averageRevenue',
            'buildingPerformance', 'topRevenueBuildings', 'startDate', 'endDate'
        ));
    }

    // Excel Export
    public function export(Request $request)
    {
        $type = $request->get('type', 'financial');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        switch ($type) {
            case 'financial':
                return $this->exportFinancial($startDate, $endDate);
            case 'maintenance':
                return $this->exportMaintenance($startDate, $endDate);
            case 'employee':
                return $this->exportEmployee($startDate, $endDate);
            case 'customer':
                return $this->exportCustomer($startDate, $endDate);
            default:
                return back()->with('error', 'Geçersiz rapor türü');
        }
    }

    private function exportFinancial($startDate, $endDate)
    {
        $companyId = auth()->user()->company_id;

        $data = FinancialTransaction::where('company_id', $companyId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->with(['building', 'employee'])
            ->get()
            ->map(function($transaction) {
                return [
                    'Tarih' => $transaction->transaction_date,
                    'Tür' => $transaction->transaction_type,
                    'Kategori' => $transaction->category,
                    'Açıklama' => $transaction->description,
                    'Tutar' => $transaction->amount,
                    'KDV' => $transaction->vat_amount,
                    'Bina' => $transaction->building?->name ?? '-',
                    'Personel' => $transaction->employee?->full_name ?? '-',
                    'Durum' => $transaction->status
                ];
            });

        return response()->json(['data' => $data]);
    }

    private function exportMaintenance($startDate, $endDate)
    {
        $companyId = auth()->user()->company_id;

        $data = MaintenanceSchedule::where('company_id', $companyId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['building', 'assignedEmployee'])
            ->get()
            ->map(function($maintenance) {
                return [
                    'Tarih' => $maintenance->scheduled_date,
                    'Bina' => $maintenance->building?->name ?? '-',
                    'Tür' => $maintenance->maintenance_type_label,
                    'Öncelik' => $maintenance->priority_label,
                    'Durum' => $maintenance->status_label,
                    'Atanan Personel' => $maintenance->assignedEmployee?->full_name ?? '-',
                    'Açıklama' => $maintenance->description,
                    'Tahmini Süre' => $maintenance->estimated_duration . ' saat'
                ];
            });

        return response()->json(['data' => $data]);
    }

    private function exportEmployee($startDate, $endDate)
    {
        $companyId = auth()->user()->company_id;

        $data = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->withCount(['maintenanceSchedules as total_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->withCount(['maintenanceSchedules as completed_jobs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate])
                      ->where('status', 'tamamlandi');
            }])
            ->get()
            ->map(function($employee) {
                return [
                    'Ad Soyad' => $employee->full_name,
                    'Pozisyon' => $employee->position_label,
                    'Toplam İş' => $employee->total_jobs,
                    'Tamamlanan İş' => $employee->completed_jobs,
                    'Tamamlanma Oranı' => $employee->total_jobs > 0
                        ? round(($employee->completed_jobs / $employee->total_jobs) * 100, 2) . '%'
                        : '0%',
                    'Maaş' => $employee->salary,
                    'İşe Başlama' => $employee->hire_date
                ];
            });

        return response()->json(['data' => $data]);
    }

    private function exportCustomer($startDate, $endDate)
    {
        $companyId = auth()->user()->company_id;

        $data = Building::where('company_id', $companyId)
            ->withCount(['maintenanceSchedules as total_maintenance' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->withSum(['accountingEntries as revenue' => function($query) use ($startDate, $endDate) {
                $query->where('type', 'gelir')
                      ->whereBetween('transaction_date', [$startDate, $endDate]);
            }], 'amount')
            ->get()
            ->map(function($building) {
                return [
                    'Bina Adı' => $building->name,
                    'Adres' => $building->address,
                    'Şehir' => $building->city,
                    'Asansör Sayısı' => $building->elevator_count,
                    'Sözleşme Türü' => $building->contract_type_label,
                    'Aylık Ücret' => $building->monthly_fee,
                    'Sözleşme Bitiş' => $building->contract_end_date,
                    'Toplam Bakım' => $building->total_maintenance,
                    'Toplam Gelir' => $building->revenue ?? 0,
                    'Durum' => $building->status_label
                ];
            });

        return response()->json(['data' => $data]);
    }
}
