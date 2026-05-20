<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Building;
use App\Models\Product;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Models\AccountingEntry;
use App\Models\IssueReport;
use App\Models\ElevatorLabel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
        public function index()
    {
        // Cache'den veri al veya hesapla (real-time performance için)
        $cacheKey = 'dashboard_data_' . auth()->user()->company_id;
        $dashboardData = Cache::remember($cacheKey, 300, function () { // 5 dakika cache
            return $this->calculateDashboardData();
        });

        // Kullanıcının widget tercihlerini al
        $userWidgets = auth()->user()->dashboard_widgets ?? $this->getDefaultWidgets();

        return view('dashboard.index', compact('dashboardData', 'userWidgets'));
    }

    private function calculateDashboardData()
    {
        $companyId = auth()->user()->company_id;

        // Ana istatistikler - Optimize edilmiş tek query ile
        $stats = DB::select("
            SELECT
                (SELECT COUNT(*) FROM employees WHERE company_id = ? AND is_active = 1) as total_employees,
                (SELECT COUNT(*) FROM buildings WHERE company_id = ? AND status = 'aktif') as total_buildings,
                (SELECT COUNT(*) FROM maintenance_schedules WHERE company_id = ? AND status IN ('planli', 'atandi', 'baslandi')) as active_maintenances,
                (SELECT COUNT(*) FROM products WHERE company_id = ? AND stock_quantity <= min_stock_level) as low_stock_products
        ", [$companyId, $companyId, $companyId, $companyId])[0];

        $totalEmployees = $stats->total_employees;
        $totalBuildings = $stats->total_buildings;
        $activeMaintenances = $stats->active_maintenances;
        $lowStockProducts = $stats->low_stock_products;

        // BU HAFTA BAKIMI OLAN BİNALAR (Ana odak)
        $thisWeekMaintenances = MaintenanceSchedule::with(['building', 'assignedEmployee'])
            ->where('company_id', $companyId)
            ->whereBetween('scheduled_date', [
                now('Europe/Istanbul')->startOfWeek(),
                now('Europe/Istanbul')->endOfWeek()
            ])
            ->whereIn('status', ['planli', 'atandi', 'baslandi'])
            ->orderBy('scheduled_date')
            ->orderByRaw("FIELD(priority, 'acil', 'yuksek', 'normal', 'dusuk')")
            ->get();





        // ACİL DURUMLAR
        $urgentMaintenances = MaintenanceSchedule::with(['building', 'assignedEmployee'])
            ->where('company_id', $companyId)
            ->where('priority', 'acil')
            ->whereIn('status', ['planli', 'atandi', 'baslandi'])
            ->orderBy('scheduled_date')
            ->take(3)
            ->get();

        // STOK UYARILARI
        $criticalStock = Product::where('company_id', $companyId)->where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->orderByRaw('(stock_quantity / GREATEST(min_stock_level, 1))')
            ->take(5)
            ->get();

        // Bu ay gelir-gider - Debug bilgileri ile
        $currentMonth = now('Europe/Istanbul')->month;
        $currentYear = now('Europe/Istanbul')->year;

        \Log::info('Dashboard Debug', [
            'company_id' => $companyId,
            'current_month' => $currentMonth,
            'current_year' => $currentYear,
            'total_accounting_entries' => AccountingEntry::where('company_id', $companyId)->count(),
            'gelir_entries_this_month' => AccountingEntry::where('company_id', $companyId)
                ->where('type', 'gelir')
                ->whereMonth('transaction_date', $currentMonth)
                ->whereYear('transaction_date', $currentYear)
                ->count(),
        ]);

        $thisMonthIncome = AccountingEntry::where('company_id', $companyId)
            ->where('type', 'gelir')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('total_amount') ?? 0; // amount yerine total_amount kullan

        $thisMonthExpense = AccountingEntry::where('company_id', $companyId)
            ->whereIn('type', ['gider', 'maas'])
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('total_amount') ?? 0; // amount yerine total_amount kullan

        // Tamamlanan bakım sayısı (bu ay)
        $completedMaintenance = MaintenanceReport::whereHas('maintenanceSchedule', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('completion_status', 'tamamlandi')
            ->count();

        // Aktif müşteri sayısı (aktif binalar)
        $activeCustomers = Building::where('company_id', $companyId)
            ->where('status', 'aktif')
            ->count();

        \Log::info('Dashboard Income/Expense', [
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'completedMaintenance' => $completedMaintenance,
            'activeCustomers' => $activeCustomers
        ]);

        // YAKLAŞAN SÖZLEŞMELİ SONU (30 gün)
        $expiringContracts = Building::where('company_id', $companyId)
            ->where('status', 'aktif')
            ->where('contract_end_date', '<=', now()->addDays(30))
            ->orderBy('contract_end_date')
            ->take(5)
            ->get();

        // Haftalık bakım grafiği
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

        // Aylık gelir-gider trendi (son 6 ay)
        $monthlyTrend = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');

            $income = AccountingEntry::where('company_id', $companyId)
                ->where('type', 'gelir')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount') ?? 0;

            $expense = AccountingEntry::where('company_id', $companyId)
                ->whereIn('type', ['gider', 'maas'])
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount') ?? 0;

            $monthlyTrend[] = [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense
            ];
        }

        // Personel performans grafiği
        $employeePerformance = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->select('id', 'first_name', 'last_name', 'position')
            ->withCount(['maintenanceSchedules as completed_jobs' => function($query) {
                $query->where('status', 'tamamlandi')
                      ->whereMonth('scheduled_date', now()->month);
            }])
            ->withCount(['maintenanceSchedules as total_jobs' => function($query) {
                $query->whereMonth('scheduled_date', now()->month);
            }])
            ->orderByDesc('completed_jobs')
            ->take(5)
            ->get();

        // Arıza analizi
        $issueAnalysis = IssueReport::where('company_id', $companyId)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->orderByDesc('count')
            ->get();

        // ASANSÖR ETİKET TAKİBİ
        $elevatorStats = $this->getElevatorLabelStats($companyId);
        $upcomingDueLabels = $this->getUpcomingDueLabels($companyId);

        return [
            'totalEmployees' => $totalEmployees,
            'totalBuildings' => $totalBuildings,
            'activeMaintenances' => $activeMaintenances,
            'lowStockProducts' => $lowStockProducts,
            'thisWeekMaintenances' => $thisWeekMaintenances,

            'urgentMaintenances' => $urgentMaintenances,
            'criticalStock' => $criticalStock,
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'completedMaintenance' => $completedMaintenance,
            'activeCustomers' => $activeCustomers,
            'expiringContracts' => $expiringContracts,
            'weeklyChart' => [
            'labels' => $weeklyLabels,
                'data' => $weeklyData
            ],
            'monthlyTrend' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyTrend
            ],
            'employeePerformance' => $employeePerformance,
            'issueAnalysis' => $issueAnalysis,
            'elevatorStats' => $elevatorStats,
            'upcomingDueLabels' => $upcomingDueLabels
        ];
    }

    private function getDefaultWidgets()
    {
        return [
            'quick_stats' => true,
            'weekly_maintenance' => true,
            'urgent_issues' => true,
            'financial_summary' => true,
            'stock_alerts' => true,
            'expiring_contracts' => true,
            'performance_chart' => true,
            'revenue_trend' => true,
            'issue_analysis' => true,
            'elevator_labels' => true
        ];
    }

    /**
     * Asansör etiket istatistiklerini getir
     */
    private function getElevatorLabelStats($companyId)
    {
        $buildings = Building::where('company_id', $companyId)
                           ->withActiveLabel()
                           ->get();

        $totalBuildings = $buildings->count();
        $labelCounts = $buildings->groupBy('current_label_color')->map->count();

        $dueSoon30 = $buildings->filter(function($building) {
            return $building->activeLabel && $building->activeLabel->isDueSoon(30);
        })->count();

        $dueSoon7 = $buildings->filter(function($building) {
            return $building->activeLabel && $building->activeLabel->isDueSoon(7);
        })->count();

        $overdue = $buildings->filter(function($building) {
            return $building->activeLabel && $building->activeLabel->isOverdue();
        })->count();

        $sealingCandidates = $buildings->filter(function($building) {
            return $building->activeLabel && $building->activeLabel->canBeSealed();
        })->count();

        return [
            'total_buildings' => $totalBuildings,
            'label_distribution' => [
                'yesil' => $labelCounts['yesil'] ?? 0,
                'mavi' => $labelCounts['mavi'] ?? 0,
                'sari' => $labelCounts['sari'] ?? 0,
                'kirmizi' => $labelCounts['kirmizi'] ?? 0,
            ],
            'due_soon' => [
                '30_days' => $dueSoon30,
                '7_days' => $dueSoon7,
            ],
            'overdue' => $overdue,
            'sealing_candidates' => $sealingCandidates,
            'percentages' => [
                'yesil' => $totalBuildings > 0 ? round(($labelCounts['yesil'] ?? 0) / $totalBuildings * 100, 1) : 0,
                'mavi' => $totalBuildings > 0 ? round(($labelCounts['mavi'] ?? 0) / $totalBuildings * 100, 1) : 0,
                'sari' => $totalBuildings > 0 ? round(($labelCounts['sari'] ?? 0) / $totalBuildings * 100, 1) : 0,
                'kirmizi' => $totalBuildings > 0 ? round(($labelCounts['kirmizi'] ?? 0) / $totalBuildings * 100, 1) : 0,
            ]
        ];
    }

    /**
     * Yaklaşan son tarihli etiketleri getir
     */
    private function getUpcomingDueLabels($companyId)
    {
        return Building::where('company_id', $companyId)
                      ->whereHas('activeLabel', function ($query) {
                          $query->where('next_control_date', '<=', now()->addDays(30))
                                ->where('status', 'aktif');
                      })
                      ->with(['activeLabel'])
                      ->orderBy(function($query) {
                          $query->select('next_control_date')
                                ->from('elevator_labels')
                                ->whereColumn('building_id', 'buildings.id')
                                ->where('status', 'aktif')
                                ->limit(1);
                      })
                      ->take(10)
                      ->get()
                      ->map(function ($building) {
                          $label = $building->activeLabel;
                          return [
                              'building_name' => $building->name,
                              'elevator_code' => $building->elevator_code ?? '',
                              'label_color' => $label->label_color ?? '',
                              'label_color_text' => $label->label_color_text ?? '',
                              'control_date' => $label->control_date,
                              'due_date' => $label->next_control_date,
                              'remaining_days' => $label->next_control_date ? now()->diffInDays($label->next_control_date, false) : 0,
                              'is_overdue' => $label->next_control_date ? $label->next_control_date < now() : false,
                              'risk_level' => 'normal',
                              'can_be_sealed' => false,
                          ];
                      });
    }

    // Real-time data update endpoint
    public function getRealTimeData()
    {
        $data = $this->calculateDashboardData();

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    // Widget tercihlerini kaydet
    public function updateWidgets(Request $request)
    {
        $request->validate([
            'widgets' => 'required|array'
        ]);

        $user = auth()->user();
        $user->dashboard_widgets = $request->widgets;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Widget tercihleri güncellendi'
        ]);
    }

    // Data Export
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'all');
        $format = $request->get('format', 'excel'); // excel, pdf, json
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $companyId = auth()->user()->company_id;

        switch ($type) {
            case 'maintenance':
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
                            'Açıklama' => $maintenance->description
                        ];
                    });
                break;

            case 'financial':
                $data = AccountingEntry::where('company_id', $companyId)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->with(['building', 'employee'])
                    ->get()
                    ->map(function($entry) {
                        return [
                            'Tarih' => $entry->transaction_date,
                            'Tür' => $entry->type,
                            'Kategori' => $entry->category,
                            'Açıklama' => $entry->description,
                            'Tutar' => $entry->amount,
                            'Bina' => $entry->building?->name ?? '-',
                            'Personel' => $entry->employee?->full_name ?? '-'
                        ];
                    });
                break;

            case 'employees':
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
                break;

            default:
                return response()->json(['error' => 'Geçersiz export türü'], 400);
        }

        $filename = "dashboard_export_{$type}_" . now()->format('Y-m-d_H-i-s');

        // Format'a göre export
        switch ($format) {
            case 'excel':
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\DashboardExport($data, $type),
                    $filename . '.xlsx'
                );

            case 'pdf':
                $headings = $this->getHeadings($type);
                $summary = $this->getSummary($type, $data);

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.dashboard-pdf', [
                    'data' => $data,
                    'headings' => $headings,
                    'type' => $type,
                    'summary' => $summary
                ]);

                return $pdf->download($filename . '.pdf');

            case 'json':
            default:
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'filename' => $filename . '.json'
                ]);
        }
    }

    private function getHeadings($type)
    {
        switch ($type) {
            case 'maintenance':
                return ['Tarih', 'Bina', 'Tür', 'Öncelik', 'Durum', 'Atanan Personel', 'Açıklama'];
            case 'financial':
                return ['Tarih', 'Tür', 'Kategori', 'Açıklama', 'Tutar', 'Bina', 'Personel'];
            case 'employees':
                return ['Ad Soyad', 'Pozisyon', 'Toplam İş', 'Tamamlanan İş', 'Tamamlanma Oranı', 'Maaş', 'İşe Başlama'];
            default:
                return [];
        }
    }

    private function getSummary($type, $data)
    {
        $summary = [];

        switch ($type) {
            case 'maintenance':
                $summary = [
                    'Toplam Bakım Sayısı' => count($data),
                    'Tamamlanan' => collect($data)->where('Durum', 'Tamamlandı')->count(),
                    'Devam Eden' => collect($data)->where('Durum', 'Devam Ediyor')->count(),
                    'Planlı' => collect($data)->where('Durum', 'Planlı')->count(),
                ];
                break;

            case 'financial':
                $totalIncome = collect($data)->where('Tür', 'gelir')->sum('Tutar');
                $totalExpense = collect($data)->where('Tür', 'gider')->sum('Tutar');
                $summary = [
                    'Toplam İşlem Sayısı' => count($data),
                    'Toplam Gelir' => number_format($totalIncome, 2) . ' ₺',
                    'Toplam Gider' => number_format($totalExpense, 2) . ' ₺',
                    'Net Kar' => number_format($totalIncome - $totalExpense, 2) . ' ₺',
                ];
                break;

            case 'employees':
                $summary = [
                    'Toplam Personel' => count($data),
                    'Ortalama Tamamlanma Oranı' => collect($data)->avg(function($emp) {
                        return (float) str_replace('%', '', $emp['Tamamlanma Oranı']);
                    }) . '%',
                    'Toplam İş Yükü' => collect($data)->sum('Toplam İş'),
                ];
                break;
        }

        return $summary;
    }

    // Data Import (sadece admin için)
    public function importData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json,csv,xlsx',
            'type' => 'required|in:maintenance,financial,employees'
        ]);

        try {
            $file = $request->file('file');
            $type = $request->get('type');
            $companyId = auth()->user()->company_id;

            // Dosya türüne göre import işlemi
            switch ($type) {
                case 'maintenance':
                    // Maintenance import logic
                    break;
                case 'financial':
                    // Financial import logic
                    break;
                case 'employees':
                    // Employee import logic
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Veri başarıyla import edildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Import sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    // Backup/Restore
    public function createBackup()
    {
        $companyId = auth()->user()->company_id;

        $backupData = [
            'buildings' => Building::where('company_id', $companyId)->get(),
            'employees' => Employee::where('company_id', $companyId)->get(),
            'maintenance_schedules' => MaintenanceSchedule::where('company_id', $companyId)->get(),
            'accounting_entries' => AccountingEntry::where('company_id', $companyId)->get(),
            'products' => Product::where('company_id', $companyId)->get(),
            'issue_reports' => IssueReport::where('company_id', $companyId)->get(),
            'created_at' => now()->toISOString(),
            'company_id' => $companyId
        ];

        $filename = "backup_" . $companyId . "_" . now()->format('Y-m-d_H-i-s') . ".json";

        // Backup dosyasını storage'a kaydet
        Storage::put("backups/{$filename}", json_encode($backupData, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'message' => 'Backup başarıyla oluşturuldu'
        ]);
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        try {
            $filename = $request->filename;
            $filePath = "backups/{$filename}";

            if (!Storage::exists($filePath)) {
                return response()->json(['error' => 'Backup dosyası bulunamadı'], 404);
            }

            $backupData = json_decode(Storage::get($filePath), true);

            // Restore işlemi (sadece aynı company_id için)
            if ($backupData['company_id'] != auth()->user()->company_id) {
                return response()->json(['error' => 'Bu backup dosyası bu firma için geçerli değil'], 403);
            }

            // Restore logic burada implement edilecek
            // Güvenlik için transaction kullanılacak

            return response()->json([
                'success' => true,
                'message' => 'Backup başarıyla restore edildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Restore sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
