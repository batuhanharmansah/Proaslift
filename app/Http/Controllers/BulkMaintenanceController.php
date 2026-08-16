<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Aylık Toplu Bakım Üretme Sihirbazı (rakip analizi karşılaştırması sonucu eklendi).
 * Tüm/filtrelenmiş binalar için tek seferde, tarih dağıtımı + teknisyen atama
 * stratejisiyle bakım kaydı oluşturur. "Önizle" adımı DB'ye hiçbir şey yazmaz.
 */
class BulkMaintenanceController extends Controller
{
    /**
     * Sabit tarihli resmi tatiller. Dini bayramlar (Ramazan/Kurban) her yıl değiştiği
     * ve hesaplanması ayrı bir kütüphane gerektirdiği için kapsam dışı bırakıldı —
     * bu tarihler manuel olarak dikkate alınmalıdır.
     */
    private const FIXED_HOLIDAYS_MD = ['01-01', '04-23', '05-01', '05-19', '07-15', '08-30', '10-29'];

    public function create()
    {
        $companyId = auth()->user()->company_id;

        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('maintenance.bulk-create', compact('employees'));
    }

    public function preview(Request $request)
    {
        return response()->json($this->buildPlan($request));
    }

    public function store(Request $request)
    {
        $plan = $this->buildPlan($request);
        $companyId = auth()->user()->company_id;

        $created = 0;

        DB::transaction(function () use ($plan, $companyId, &$created) {
            foreach ($plan['items'] as $item) {
                if ($item['skipped']) {
                    continue;
                }

                MaintenanceSchedule::create([
                    'company_id' => $companyId,
                    'building_id' => $item['building_id'],
                    'assigned_employee_id' => $item['assigned_employee_id'],
                    'maintenance_type' => 'rutin_bakim',
                    'title' => 'Aylık Toplu Bakım',
                    'scheduled_date' => $item['scheduled_date'],
                    'priority' => 'normal',
                    'status' => $item['assigned_employee_id'] ? 'atandi' : 'planli',
                    'description' => 'Toplu bakım sihirbazı ile otomatik oluşturuldu.',
                ]);

                $created++;
            }
        });

        return redirect()
            ->route('maintenance.bulk.create')
            ->with('success', "{$created} adet bakım kaydı oluşturuldu.");
    }

    private function buildPlan(Request $request): array
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'start_day' => 'required|integer|min:1|max:28',
            'shift_holidays' => 'nullable|boolean',
            'distribution' => 'required|in:single_day,spread',
            'spread_days' => 'nullable|integer|min:1|max:28',
            'assignment_strategy' => 'required|in:building_default,single_employee,round_robin,none',
            'fallback_employee_id' => 'nullable|exists:employees,id',
            'single_employee_id' => 'nullable|exists:employees,id',
            'round_robin_employee_ids' => 'nullable|array',
            'round_robin_employee_ids.*' => 'exists:employees,id',
            'only_with_fee' => 'nullable|boolean',
        ]);

        $companyId = auth()->user()->company_id;

        $query = Building::where('company_id', $companyId)->where('status', 'aktif');

        if ($request->boolean('only_with_fee')) {
            $query->where('monthly_fee', '>', 0);
        }

        $buildings = $query->orderBy('name')->get();

        $spreadDays = $validated['distribution'] === 'spread'
            ? max(1, (int) ($validated['spread_days'] ?? 1))
            : 1;

        $dates = $this->buildDateSequence(
            (int) $validated['year'],
            (int) $validated['month'],
            (int) $validated['start_day'],
            $spreadDays,
            $request->boolean('shift_holidays')
        );

        $roundRobinIds = $validated['round_robin_employee_ids'] ?? [];
        $roundRobinIndex = 0;

        $items = [];

        foreach ($buildings as $index => $building) {
            $skipped = false;
            $skipReason = null;

            if ($request->boolean('only_with_fee') && (float) $building->monthly_fee <= 0) {
                $skipped = true;
                $skipReason = 'Bakım ücreti tanımlı değil';
            }

            $assignedEmployeeId = null;
            switch ($validated['assignment_strategy']) {
                case 'building_default':
                    $assignedEmployeeId = $building->default_employee_id ?: ($validated['fallback_employee_id'] ?? null);
                    break;
                case 'single_employee':
                    $assignedEmployeeId = $validated['single_employee_id'] ?? null;
                    break;
                case 'round_robin':
                    if (!empty($roundRobinIds)) {
                        $assignedEmployeeId = $roundRobinIds[$roundRobinIndex % count($roundRobinIds)];
                        $roundRobinIndex++;
                    }
                    break;
                case 'none':
                default:
                    $assignedEmployeeId = null;
            }

            $dateIndex = $spreadDays > 0 ? $index % count($dates) : 0;
            $scheduledDate = $dates[$dateIndex] ?? $dates[0];

            $existing = MaintenanceSchedule::where('building_id', $building->id)
                ->where('maintenance_type', 'rutin_bakim')
                ->whereYear('scheduled_date', $validated['year'])
                ->whereMonth('scheduled_date', $validated['month'])
                ->exists();

            if ($existing && !$skipped) {
                $skipped = true;
                $skipReason = 'Bu ay için zaten bakım kaydı var';
            }

            $employee = $assignedEmployeeId ? Employee::find($assignedEmployeeId) : null;

            $items[] = [
                'building_id' => $building->id,
                'building_name' => $building->name,
                'scheduled_date' => $scheduledDate,
                'assigned_employee_id' => $skipped ? null : $assignedEmployeeId,
                'assigned_employee_name' => $employee ? trim($employee->first_name . ' ' . $employee->last_name) : null,
                'skipped' => $skipped,
                'skip_reason' => $skipReason,
            ];
        }

        return [
            'total' => count($items),
            'will_create' => collect($items)->where('skipped', false)->count(),
            'will_skip' => collect($items)->where('skipped', true)->count(),
            'items' => $items,
        ];
    }

    /**
     * @return array<int, string> Y-m-d formatında tarih listesi
     */
    private function buildDateSequence(int $year, int $month, int $startDay, int $spreadDays, bool $shiftHolidays): array
    {
        $dates = [];
        $cursor = Carbon::create($year, $month, min($startDay, Carbon::create($year, $month, 1)->daysInMonth));

        for ($i = 0; $i < $spreadDays; $i++) {
            $date = $cursor->copy()->addDays($i);

            if ($shiftHolidays) {
                $date = $this->shiftToNextWorkday($date);
            }

            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    private function shiftToNextWorkday(Carbon $date): Carbon
    {
        $date = $date->copy();
        $attempts = 0;

        while ($attempts < 14) {
            $isWeekend = $date->isWeekend();
            $isFixedHoliday = in_array($date->format('m-d'), self::FIXED_HOLIDAYS_MD, true);

            if (!$isWeekend && !$isFixedHoliday) {
                break;
            }

            $date->addDay();
            $attempts++;
        }

        return $date;
    }
}
