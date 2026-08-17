<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Güvenlik kontrolü: Company var mı?
        if (!$user->company) {
            abort(403, 'Firma bilgileriniz bulunamadı.');
        }

        // Employee kaydını bul
        $employee = $user->company->employees()->where('email', $user->email)->first();

        if (!$employee) {
            abort(403, 'Personel kaydınız bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }

        $employeeId = $employee->id;

        // Bugün atandığım işler
        $todayTasks = MaintenanceSchedule::with(['building', 'assignedEmployee'])
            ->where('assigned_employee_id', $employeeId)
            ->whereDate('scheduled_date', today('Europe/Istanbul'))
            ->whereIn('status', ['planli', 'atandi', 'baslandi'])
            ->orderBy('scheduled_time')
            ->get();

        // Bu hafta atandığım işler
        $thisWeekTasks = MaintenanceSchedule::with(['building'])
            ->where('assigned_employee_id', $employeeId)
            ->whereBetween('scheduled_date', [now('Europe/Istanbul')->startOfWeek(), now('Europe/Istanbul')->endOfWeek()])
            ->whereIn('status', ['planli', 'atandi', 'baslandi'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        // Tamamladığım işler (Son 30 gün)
        $completedTasks = MaintenanceReport::with(['maintenanceSchedule.building'])
            ->where('employee_id', $employeeId)
            ->where('created_at', '>=', now('Europe/Istanbul')->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Bekleyen raporlarım
        $pendingReports = MaintenanceSchedule::with(['building'])
            ->where('assigned_employee_id', $employeeId)
            ->where('status', 'tamamlandi')
            ->whereDoesntHave('maintenanceReports')
            ->orderBy('scheduled_date', 'desc')
            ->get();

        // Performans istatistikleri
        $stats = [
            'today_tasks_count' => $todayTasks->count(),
            'week_tasks_count' => $thisWeekTasks->count(),
            'completed_this_month' => MaintenanceReport::where('employee_id', $employeeId)
                ->whereMonth('created_at', now('Europe/Istanbul')->month)
                ->whereYear('created_at', now('Europe/Istanbul')->year)
                ->count(),
            'pending_reports_count' => $pendingReports->count(),
            'average_completion_time' => MaintenanceReport::where('employee_id', $employeeId)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_minutes')
                ->value('avg_minutes') ?? 0,
        ];

        // Son kullandığım ürünler (kendi bakım raporlarımda kullandığım parçalar)
        $recentProducts = MaintenanceReport::where('employee_id', $employeeId)
            ->whereNotNull('used_products')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->flatMap(function ($report) {
                return collect($report->used_products ?? [])->map(function ($item) use ($report) {
                    return (object) array_merge($item, ['used_at' => $report->created_at]);
                });
            })
            ->take(8);

        // Bu ay tamamladığım işlerin grafiği
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now('Europe/Istanbul')->subDays($i);
            $monthlyLabels[] = $date->format('d.m');
            $count = MaintenanceReport::where('employee_id', $employeeId)
                ->whereDate('created_at', $date)
                ->count();
            $monthlyData[] = $count;
        }

        $performanceChart = [
            'labels' => $monthlyLabels,
            'datasets' => [[
                'label' => 'Tamamlanan İşler',
                'data' => $monthlyData,
                'borderColor' => 'rgb(34, 197, 94)',
                'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                'tension' => 0.4
            ]]
        ];

        return view('employee.dashboard.index', compact(
            'todayTasks', 'thisWeekTasks', 'completedTasks', 'pendingReports',
            'stats', 'recentProducts', 'performanceChart'
        ));
    }
}
