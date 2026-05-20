<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ElevatorLabel;
use App\Models\ElevatorNotification;
use App\Models\ElevatorEscalation;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElevatorLabelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Etiket listesi
     */
    public function index(Request $request)
    {
        $query = Building::with(['activeLabel', 'company'])
                         ->where('company_id', auth()->user()->company_id);

        // Filtreler
        if ($request->filled('label_color')) {
            $query->byLabelColor($request->label_color);
        }

        if ($request->filled('status')) {
            $query->whereHas('activeLabel', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('risk_level')) {
            switch ($request->risk_level) {
                case 'overdue':
                    $query->overdue();
                    break;
                case 'due_soon':
                    $query->dueSoon();
                    break;
                case 'sealing_candidate':
                    $query->sealingCandidates();
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('elevator_code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $buildings = $query->paginate(20);

        // İstatistikler
        $stats = $this->getStats();

        return view('elevator-labels.index', compact('buildings', 'stats'));
    }

    /**
     * Etiket oluşturma formu
     */
    public function create(Request $request)
    {
        $buildingId = $request->building_id;
        $building = null;

        if ($buildingId) {
            $building = Building::where('company_id', auth()->user()->company_id)
                              ->findOrFail($buildingId);
        }

        $buildings = Building::where('company_id', auth()->user()->company_id)
                           ->orderBy('name')
                           ->get();

        return view('elevator-labels.create', compact('building', 'buildings'));
    }

    /**
     * Etiket kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'label_color' => 'required|in:yesil,mavi,sari,kirmizi',
            'control_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string',
            'source' => 'nullable|in:periyodik_kontrol,takip_kontrol,manuel',
            'inspector_name' => 'nullable|string|max:255',
            'inspector_company' => 'nullable|string|max:255',
            'inspector_license' => 'nullable|string|max:255',
        ]);

        // Building'in bu şirkete ait olduğunu kontrol et
        $building = Building::where('company_id', auth()->user()->company_id)
                          ->findOrFail($validated['building_id']);

        // Takip süresini belirle
        $followUpDays = ElevatorLabel::FOLLOW_UP_DAYS[$validated['label_color']] ?? 365;
        $controlDate = \Carbon\Carbon::parse($validated['control_date']);

        // Yeni etiket oluştur
        $label = ElevatorLabel::create([
            'building_id' => $building->id,
            'label_color' => $validated['label_color'],
            'control_date' => $validated['control_date'],
            'follow_up_days' => $followUpDays,
            'due_date' => $controlDate->copy()->addDays($followUpDays),
            'description' => $validated['description'] ?? null,
            'source' => $validated['source'] ?? 'manuel',
            'inspector_name' => $validated['inspector_name'] ?? null,
            'inspector_company' => $validated['inspector_company'] ?? null,
            'inspector_license' => $validated['inspector_license'] ?? null,
            'status' => 'aktif',
            'created_by' => auth()->id(),
        ]);

        // Audit log
        AuditLog::logModelChange($label, 'created', 'Yeni etiket oluşturuldu', auth()->id());

        return redirect()->route('elevator-labels.show', $label)
                        ->with('success', 'Etiket başarıyla oluşturuldu.');
    }

    /**
     * Etiket detayı göster
     */
    public function show(ElevatorLabel $elevatorLabel)
    {
        // Yetki kontrolü - sadece aynı şirketin etiketlerini görebilir
        if ($elevatorLabel->building->company_id !== auth()->user()->company_id) {
            abort(403, 'Bu etikete erişim yetkiniz yok.');
        }

        $elevatorLabel->load([
            'building',
            'creator',
            'updater',
            'notifications' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'escalations' => function ($query) {
                $query->orderBy('triggered_at', 'desc');
            }
        ]);

        // Etiket geçmişi
        $labelHistory = ElevatorLabel::where('building_id', $elevatorLabel->building_id)
                                   ->where('id', '!=', $elevatorLabel->id)
                                   ->orderBy('control_date', 'desc')
                                   ->limit(5)
                                   ->get();

        return view('elevator-labels.show', compact('elevatorLabel', 'labelHistory'));
    }

    /**
     * Detaylı rapor sayfası
     */
    public function report(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Filtreleme parametreleri
        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'label_color' => $request->get('label_color'),
            'status' => $request->get('status'),
            'risk_level' => $request->get('risk_level'),
        ];

        // Temel sorgu
        $query = Building::where('company_id', $companyId)
            ->with(['activeLabel', 'company']);

        // Tarih filtresi
        if ($filters['date_from']) {
            $query->whereHas('activeLabel', function ($q) use ($filters) {
                $q->where('control_date', '>=', $filters['date_from']);
            });
        }
        if ($filters['date_to']) {
            $query->whereHas('activeLabel', function ($q) use ($filters) {
                $q->where('control_date', '<=', $filters['date_to']);
            });
        }

        // Etiket rengi filtresi
        if ($filters['label_color']) {
            $query->byLabelColor($filters['label_color']);
        }

        // Durum filtresi
        if ($filters['status']) {
            $query->whereHas('activeLabel', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });
        }

        // Risk seviyesi filtresi
        if ($filters['risk_level']) {
            switch ($filters['risk_level']) {
                case 'overdue':
                    $query->overdue();
                    break;
                case 'due_soon':
                    $query->dueSoon();
                    break;
                case 'sealing_candidate':
                    $query->sealingCandidates();
                    break;
            }
        }

        $buildings = $query->get();

        // Rapor verilerini hesapla
        $reportData = $this->generateReportData($buildings, $filters);

        return view('elevator-labels.report', compact('buildings', 'reportData', 'filters'));
    }

    /**
     * İstatistikleri hesapla
     */
    private function getStats()
    {
        $companyId = auth()->user()->company_id;

        // Cache key oluştur (10 dakika cache)
        $cacheKey = 'elevator_labels_stats_' . $companyId;

        return Cache::remember($cacheKey, 600, function () use ($companyId) {
            $totalBuildings = Building::where('company_id', $companyId)->count();

            $labelCounts = Building::where('company_id', $companyId)
                ->withActiveLabel()
                ->get()
                ->groupBy('current_label_color')
                ->map->count();

            $dueSoon = Building::where('company_id', $companyId)->dueSoon(30)->count();
            $dueSoon7 = Building::where('company_id', $companyId)->dueSoon(7)->count();
            $dueSoon1 = Building::where('company_id', $companyId)->dueSoon(1)->count();
            $overdue = Building::where('company_id', $companyId)->overdue()->count();
            $sealingCandidates = Building::where('company_id', $companyId)->sealingCandidates()->count();

            return [
                'total_buildings' => $totalBuildings,
                'label_distribution' => [
                    'yesil' => $labelCounts['yesil'] ?? 0,
                    'mavi' => $labelCounts['mavi'] ?? 0,
                    'sari' => $labelCounts['sari'] ?? 0,
                    'kirmizi' => $labelCounts['kirmizi'] ?? 0,
                ],
                'due_soon' => [
                    '30_days' => $dueSoon,
                    '7_days' => $dueSoon7,
                    '1_day' => $dueSoon1,
                ],
                'overdue' => $overdue,
                'sealing_candidates' => $sealingCandidates,
            ];
        });
    }

    /**
     * Rapor verilerini oluştur
     */
    private function generateReportData($buildings, $filters)
    {
        $totalBuildings = $buildings->count();

        // Etiket dağılımı
        $labelDistribution = $buildings->groupBy(function ($building) {
            return $building->activeLabel ? $building->activeLabel->label_number : 'etiket_yok';
        })->map->count();

        // Risk analizi - Optimize edilmiş query ile
        $riskAnalysis = DB::select("
            SELECT
                SUM(CASE WHEN el.next_control_date < NOW() THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN el.next_control_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY) AND el.next_control_date >= NOW() THEN 1 ELSE 0 END) as due_soon_30,
                SUM(CASE WHEN el.next_control_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) AND el.next_control_date >= NOW() THEN 1 ELSE 0 END) as due_soon_7,
                SUM(CASE WHEN el.next_control_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY) AND el.next_control_date >= NOW() THEN 1 ELSE 0 END) as due_soon_1,
                SUM(CASE WHEN el.label_color IN ('yesil', 'mavi') AND el.next_control_date < DATE_ADD(NOW(), INTERVAL -30 DAY) THEN 1 ELSE 0 END) as sealing_candidates,
                SUM(CASE WHEN el.next_control_date > DATE_ADD(NOW(), INTERVAL 30 DAY) AND el.next_control_date >= NOW() THEN 1 ELSE 0 END) as normal
            FROM elevator_labels el
            WHERE el.company_id = ? AND el.status = 'aktif'
        ", [$companyId])[0];

        // Durum dağılımı
        $statusDistribution = $buildings->groupBy(function ($building) {
            return $building->activeLabel ? $building->activeLabel->status : 'etiket_yok';
        })->map->count();

        // Son kontrol tarihleri analizi
        $controlDateAnalysis = [
            'last_month' => $buildings->filter(function ($building) {
                if (!$building->activeLabel) return false;
                return $building->activeLabel->control_date->gte(now()->subMonth());
            })->count(),
            'last_3_months' => $buildings->filter(function ($building) {
                if (!$building->activeLabel) return false;
                return $building->activeLabel->control_date->gte(now()->subMonths(3));
            })->count(),
            'last_6_months' => $buildings->filter(function ($building) {
                if (!$building->activeLabel) return false;
                return $building->activeLabel->control_date->gte(now()->subMonths(6));
            })->count(),
            'older_than_6_months' => $buildings->filter(function ($building) {
                if (!$building->activeLabel) return false;
                return $building->activeLabel->control_date->lt(now()->subMonths(6));
            })->count(),
        ];

        // Aylık trend analizi (son 12 ay)
        $monthlyTrend = [];
        $buildingIds = $buildings->pluck('id')->toArray();

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyTrend[$month->format('Y-m')] = [
                'month_name' => $month->format('M Y'),
                'labels_created' => empty($buildingIds) ? 0 : ElevatorLabel::whereIn('building_id', $buildingIds)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'labels_expired' => $buildings->filter(function ($building) use ($month) {
                    if (!$building->activeLabel) return false;
                    return $building->activeLabel->next_control_date &&
                           $building->activeLabel->next_control_date->lt($month->endOfMonth());
                })->count(),
            ];
        }

        return [
            'total_buildings' => $totalBuildings,
            'label_distribution' => $labelDistribution,
            'risk_analysis' => $riskAnalysis,
            'status_distribution' => $statusDistribution,
            'control_date_analysis' => $controlDateAnalysis,
            'monthly_trend' => $monthlyTrend,
            'generated_at' => now(),
            'filters_applied' => $filters,
        ];
    }
}
