<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
// use App\Models\Payment; // Tablo mevcut değil
use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Genel İstatistikler
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $suspendedCompanies = Company::where('is_active', false)->count();
        $totalUsers = User::whereNotNull('company_id')->count();

        // Bu Ay Gelir (Company monthly_fee'lerinden hesapla)
        $thisMonthRevenue = Company::where('is_active', true)
            ->sum('monthly_fee');

        // Ödeme Durumu (Demo veriler)
        $overduePayments = 0; // Payment tablosu olmadığı için 0
        $pendingPayments = 0; // Payment tablosu olmadığı için 0
        $paidThisMonth = $activeCompanies; // Aktif firma sayısı

        // Son Eklenen Firmalar
        $recentCompanies = Company::latest()
            ->take(5)
            ->get();

        // Gelir Grafiği (Son 6 Ay)
        $revenueChart = $this->getRevenueChart();

        // Sistem İstatistikleri
        $systemStats = [
            'total_buildings' => Building::count(),
            'total_employees' => Employee::count(),
            'active_companies_percentage' => $totalCompanies > 0 ? round(($activeCompanies / $totalCompanies) * 100, 1) : 0,
            'average_monthly_fee' => Company::where('is_active', true)->avg('monthly_fee') ?? 0,
        ];

        // Kritik Durumlar
        $criticalIssues = $this->getCriticalIssues();

        // Son Aktiviteler
        $recentActivities = $this->getRecentActivities();

        return view('super-admin.dashboard.index', compact(
            'totalCompanies', 'activeCompanies', 'suspendedCompanies', 'totalUsers',
            'thisMonthRevenue', 'overduePayments', 'pendingPayments', 'paidThisMonth',
            'recentCompanies', 'revenueChart', 'systemStats', 'criticalIssues', 'recentActivities'
        ));
    }

    private function getRevenueChart()
    {
        $monthlyRevenue = [];
        $monthlyLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            // Payment tablosu olmadığı için sabit gelir
            $revenue = Company::where('is_active', true)->sum('monthly_fee') / 6;
            $monthlyRevenue[] = $revenue ?? 0;
        }

        return [
            'labels' => $monthlyLabels,
            'datasets' => [[
                'label' => 'Aylık Gelir (₺)',
                'data' => $monthlyRevenue,
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'tension' => 0.4,
                'fill' => true
            ]]
        ];
    }

    private function getCriticalIssues()
    {
        $issues = [];

        // Süresi dolmuş abonelikler
        $expiredCount = Company::where('subscription_end', '<', now())->count();
        if ($expiredCount > 0) {
            $issues[] = [
                'title' => 'Süresi Dolmuş Abonelikler',
                'description' => 'Abonelik süresi sona eren firmalar',
                'count' => $expiredCount
            ];
        }

        // Geciken ödemeler (Demo - Payment tablosu yok)
        $overdueCount = 0; // Payment tablosu olmadığı için 0
        if ($overdueCount > 0) {
            $issues[] = [
                'title' => 'Geciken Ödemeler',
                'description' => 'Vadesi geçmiş ödemeler',
                'count' => $overdueCount
            ];
        }

        // Askıya alınmış firmalar
        if ($suspendedCount = Company::where('is_active', false)->count()) {
            $issues[] = [
                'title' => 'Askıya Alınmış Firmalar',
                'description' => 'Hesabı askıya alınan firmalar',
                'count' => $suspendedCount
            ];
        }

        return $issues;
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Son eklenen firmalar
        $recentCompanies = Company::latest()->take(3)->get();
        foreach ($recentCompanies as $company) {
            $activities->push([
                'type' => 'company_created',
                'title' => 'Yeni firma eklendi',
                'description' => $company->name . ' sisteme kaydedildi',
                'time' => $company->created_at,
                'icon' => 'building',
                'color' => 'blue'
            ]);
        }

        // Son ödemeler (Demo - Payment tablosu yok)
        // Payment tablosu olmadığı için bu kısmı atlıyoruz

        // Son bakım raporları
        $recentReports = MaintenanceReport::with('maintenanceSchedule.building.company')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentReports as $report) {
            // Null kontrolü ekle
            if ($report->maintenanceSchedule && $report->maintenanceSchedule->building) {
                $activities->push([
                    'type' => 'maintenance_completed',
                    'title' => 'Bakım tamamlandı',
                    'description' => $report->maintenanceSchedule->building->company->name . ' - ' . $report->maintenanceSchedule->building->name,
                    'time' => $report->created_at,
                    'icon' => 'wrench',
                    'color' => 'purple'
                ]);
            }
        }

        return $activities->sortByDesc('time')->take(6);
    }
}
