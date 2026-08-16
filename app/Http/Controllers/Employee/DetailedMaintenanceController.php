<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DetailedMaintenanceReport;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceSchedule;
use App\Models\Building;
use App\Models\Employee;
use App\Services\MaintenanceApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailedMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:employee');
    }

    /**
     * Oturumdaki kullanıcının Employee kaydını bulur (user_id ilişkisi üzerinden).
     */
    private function getEmployeeId(): int
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            abort(403, 'Personel kaydınız bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }

        return $employee->id;
    }

    /**
     * Detaylı bakım formu göster
     */
    public function show(MaintenanceSchedule $maintenance)
    {
        abort_if($maintenance->assigned_employee_id !== $this->getEmployeeId(), 403);

        $schedule = $maintenance->load(['building', 'assignedEmployee']);

        // Eğer zaten detaylı rapor varsa göster
        $existingReport = DetailedMaintenanceReport::where('maintenance_schedule_id', $schedule->id)->first();

        if ($existingReport) {
            return view('employee.maintenance.detailed-report', compact('schedule', 'existingReport'));
        }

        return view('employee.maintenance.detailed-form', compact('schedule'));
    }

    /**
     * Detaylı bakım formunu kaydet
     */
    public function store(Request $request, MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        abort_if($maintenance->assigned_employee_id !== $employeeId, 403);

        $schedule = $maintenance;
        $scheduleId = $schedule->id;

        $validated = $request->validate([
            'building_id_number' => 'nullable|string|max:255',
            'maintenance_date' => 'required|date',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'capacity_person' => 'nullable|integer|min:1',
            'capacity_kg' => 'nullable|integer|min:1',
            'floor_count' => 'nullable|integer|min:1',
            'control_type' => 'nullable|string|max:255',

            // Makine dairesi kontrolleri
            'machine_room_checks' => 'nullable|array',
            'machine_room_checks.*' => 'boolean',

            // Kat kontrolleri
            'floor_checks' => 'nullable|array',
            'floor_checks.*' => 'boolean',

            // Kabin kontrolleri
            'cabin_checks' => 'nullable|array',
            'cabin_checks.*' => 'boolean',

            // Kuyu içi kontrolleri
            'shaft_checks' => 'nullable|array',
            'shaft_checks.*' => 'boolean',

            'maintenance_month' => 'nullable|string|max:255',
            'description_warnings' => 'nullable|string',
            'faulty_parts' => 'nullable|array',
            'replaced_parts' => 'nullable|array',
            'building_authority_name' => 'nullable|string|max:255',
            'building_authority_notes' => 'nullable|string',
            'service_authority_name' => 'nullable|string|max:255',
            'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
            'general_notes' => 'nullable|string',
        ]);

        $report = DetailedMaintenanceReport::create([
            'maintenance_schedule_id' => $scheduleId,
            'employee_id' => $employeeId,
            'building_id' => $schedule->building_id,
            'building_id_number' => $validated['building_id_number'] ?? null,
            'maintenance_date' => $validated['maintenance_date'],
            'entry_time' => $validated['entry_time'] ? $validated['maintenance_date'] . ' ' . $validated['entry_time'] : null,
            'exit_time' => $validated['exit_time'] ? $validated['maintenance_date'] . ' ' . $validated['exit_time'] : null,
            'capacity_person' => $validated['capacity_person'] ?? null,
            'capacity_kg' => $validated['capacity_kg'] ?? null,
            'floor_count' => $validated['floor_count'] ?? null,
            'control_type' => $validated['control_type'] ?? null,
            'machine_room_checks' => $validated['machine_room_checks'] ?? DetailedMaintenanceReport::getDefaultMachineRoomChecks(),
            'floor_checks' => $validated['floor_checks'] ?? DetailedMaintenanceReport::getDefaultFloorChecks(),
            'cabin_checks' => $validated['cabin_checks'] ?? DetailedMaintenanceReport::getDefaultCabinChecks(),
            'shaft_checks' => $validated['shaft_checks'] ?? DetailedMaintenanceReport::getDefaultShaftChecks(),
            'maintenance_month' => $validated['maintenance_month'] ?? null,
            'description_warnings' => $validated['description_warnings'] ?? null,
            'faulty_parts' => $validated['faulty_parts'] ?? null,
            'replaced_parts' => $validated['replaced_parts'] ?? null,
            'building_authority_signature' => $request->has('building_authority_signature'),
            'building_authority_name' => $validated['building_authority_name'] ?? null,
            'building_authority_notes' => $validated['building_authority_notes'] ?? null,
            'service_authority_signature' => $request->has('service_authority_signature'),
            'service_authority_name' => $validated['service_authority_name'] ?? null,
            'completion_status' => $validated['completion_status'],
            'general_notes' => $validated['general_notes'] ?? null,
        ]);

        // Maintenance schedule'ı güncelle
        $schedule->update([
            'status' => $validated['completion_status'] === 'tamamlandi' ? 'tamamlandi' : 'baslandi',
            'notes' => 'Detaylı bakım raporu oluşturuldu.'
        ]);

        $flashMessage = 'Detaylı bakım raporu başarıyla oluşturuldu.';

        // Diğer bakım tamamlama akışlarıyla (rapor + onay SMS zinciri) tutarlılık için:
        // tamamlandı olarak işaretlenmişse, henüz bir MaintenanceReport yoksa bir tane oluştur
        // ve bina yöneticisine onay SMS'i gönder.
        if ($validated['completion_status'] === 'tamamlandi' && !$schedule->maintenanceReport) {
            $problems = collect($validated['faulty_parts'] ?? [])->filter()->implode(', ');
            $recommendations = collect($validated['replaced_parts'] ?? [])->filter()->implode(', ');

            $maintenanceReport = MaintenanceReport::create([
                'company_id' => $schedule->company_id,
                'building_id' => $schedule->building_id,
                'maintenance_schedule_id' => $schedule->id,
                'employee_id' => $employeeId,
                'title' => 'Detaylı Bakım Raporu - ' . $schedule->building->name,
                'start_time' => $report->entry_time ?? $validated['maintenance_date'],
                'end_time' => $report->exit_time,
                'work_description' => $validated['general_notes'] ?: ($validated['description_warnings'] ?: 'Detaylı periyodik bakım kontrol formu dolduruldu.'),
                'used_products' => null,
                'total_cost' => 0,
                'problems_found' => $problems ?: null,
                'recommendations' => $recommendations ?: null,
                'completion_status' => 'tamamlandi',
                'customer_signature' => $report->building_authority_signature,
                'customer_name' => $validated['building_authority_name'] ?? null,
                'customer_notes' => $validated['building_authority_notes'] ?? null,
            ]);

            $smsResult = app(MaintenanceApprovalService::class)
                ->initiateApprovalFlow($maintenanceReport->fresh(['building']));

            if ($smsResult['sent'] ?? false) {
                $flashMessage .= ' Bina yöneticisine onay SMS\'i gönderildi.';
            }
        }

        return redirect()->route('employee.maintenance.detailed-report', $scheduleId)
            ->with('success', $flashMessage);
    }

    /**
     * Detaylı bakım raporunu güncelle
     */
    public function update(Request $request, MaintenanceSchedule $maintenance)
    {
        abort_if($maintenance->assigned_employee_id !== $this->getEmployeeId(), 403);

        $schedule = $maintenance;
        $scheduleId = $schedule->id;

        $report = DetailedMaintenanceReport::where('maintenance_schedule_id', $scheduleId)->firstOrFail();

        $validated = $request->validate([
            'building_id_number' => 'nullable|string|max:255',
            'maintenance_date' => 'required|date',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'capacity_person' => 'nullable|integer|min:1',
            'capacity_kg' => 'nullable|integer|min:1',
            'floor_count' => 'nullable|integer|min:1',
            'control_type' => 'nullable|string|max:255',

            'machine_room_checks' => 'nullable|array',
            'machine_room_checks.*' => 'boolean',

            'floor_checks' => 'nullable|array',
            'floor_checks.*' => 'boolean',

            'cabin_checks' => 'nullable|array',
            'cabin_checks.*' => 'boolean',

            'shaft_checks' => 'nullable|array',
            'shaft_checks.*' => 'boolean',

            'maintenance_month' => 'nullable|string|max:255',
            'description_warnings' => 'nullable|string',
            'faulty_parts' => 'nullable|array',
            'replaced_parts' => 'nullable|array',
            'building_authority_name' => 'nullable|string|max:255',
            'building_authority_notes' => 'nullable|string',
            'service_authority_name' => 'nullable|string|max:255',
            'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
            'general_notes' => 'nullable|string',
        ]);

        $report->update([
            'building_id_number' => $validated['building_id_number'] ?? $report->building_id_number,
            'maintenance_date' => $validated['maintenance_date'],
            'entry_time' => $validated['entry_time'] ? $validated['maintenance_date'] . ' ' . $validated['entry_time'] : $report->entry_time,
            'exit_time' => $validated['exit_time'] ? $validated['maintenance_date'] . ' ' . $validated['exit_time'] : $report->exit_time,
            'capacity_person' => $validated['capacity_person'] ?? $report->capacity_person,
            'capacity_kg' => $validated['capacity_kg'] ?? $report->capacity_kg,
            'floor_count' => $validated['floor_count'] ?? $report->floor_count,
            'control_type' => $validated['control_type'] ?? $report->control_type,
            'machine_room_checks' => $validated['machine_room_checks'] ?? $report->machine_room_checks,
            'floor_checks' => $validated['floor_checks'] ?? $report->floor_checks,
            'cabin_checks' => $validated['cabin_checks'] ?? $report->cabin_checks,
            'shaft_checks' => $validated['shaft_checks'] ?? $report->shaft_checks,
            'maintenance_month' => $validated['maintenance_month'] ?? $report->maintenance_month,
            'description_warnings' => $validated['description_warnings'] ?? $report->description_warnings,
            'faulty_parts' => $validated['faulty_parts'] ?? $report->faulty_parts,
            'replaced_parts' => $validated['replaced_parts'] ?? $report->replaced_parts,
            'building_authority_signature' => $request->has('building_authority_signature'),
            'building_authority_name' => $validated['building_authority_name'] ?? $report->building_authority_name,
            'building_authority_notes' => $validated['building_authority_notes'] ?? $report->building_authority_notes,
            'service_authority_signature' => $request->has('service_authority_signature'),
            'service_authority_name' => $validated['service_authority_name'] ?? $report->service_authority_name,
            'completion_status' => $validated['completion_status'],
            'general_notes' => $validated['general_notes'] ?? $report->general_notes,
        ]);

        return redirect()->route('employee.maintenance.detailed-report', $scheduleId)
            ->with('success', 'Detaylı bakım raporu başarıyla güncellendi.');
    }
}
