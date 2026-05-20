<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Services\MaintenanceApprovalService;
use App\Models\Employee;
use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenanceSchedules = MaintenanceSchedule::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->with(['building', 'assignedEmployee'])
        ->orderBy('scheduled_date', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $maintenanceSchedules,
        ]);
    }

    public function show($id)
    {
        $maintenance = MaintenanceSchedule::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('id', $id)
        ->with(['building', 'assignedEmployee'])
        ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $maintenance,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'maintenance_type' => 'required|in:rutin,ariza,periyodik,acil,rutin_bakim,ariza_onarim,periyodik_kontrol,modernizasyon',
            'description' => 'required|string',
            'scheduled_date' => 'required|date',
            'priority' => 'required|in:dusuk,orta,yuksek,normal,acil',
            'estimated_duration' => 'required|integer|min:1',
        ]);

        // Check if building belongs to company
        $building = Building::where('company_id', Auth::user()->company_id)
            ->where('id', $request->building_id)
            ->firstOrFail();

        $maintenance = MaintenanceSchedule::create([
            'building_id' => $request->building_id,
            'maintenance_type' => $request->maintenance_type,
            'description' => $request->description,
            'scheduled_date' => $request->scheduled_date,
            'priority' => $request->priority,
            'estimated_duration' => $request->estimated_duration,
            'status' => 'planlandi',
        ]);

        return response()->json([
            'success' => true,
            'data' => $maintenance,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $maintenance = MaintenanceSchedule::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('id', $id)
        ->firstOrFail();

        $request->validate([
            'maintenance_type' => 'sometimes|in:rutin,ariza,periyodik,acil,rutin_bakim,ariza_onarim,periyodik_kontrol,modernizasyon',
            'description' => 'sometimes|string',
            'scheduled_date' => 'sometimes|date',
            'priority' => 'sometimes|in:dusuk,orta,yuksek,normal,acil',
            'estimated_duration' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:planlandi,devam_ediyor,tamamlandi,iptal',
        ]);

        $maintenance->update($request->only([
            'maintenance_type', 'description', 'scheduled_date', 'priority', 'estimated_duration', 'status'
        ]));

        return response()->json([
            'success' => true,
            'data' => $maintenance,
        ]);
    }

    public function assignEmployee(Request $request, $id)
    {
        $maintenance = MaintenanceSchedule::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('id', $id)
        ->firstOrFail();

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $maintenance->update([
            'assigned_employee_id' => $request->employee_id,
            'status' => 'atandi',
        ]);

        return response()->json([
            'success' => true,
            'data' => $maintenance,
        ]);
    }

    public function storeReport(Request $request, $id)
    {
        $request->validate([
            'work_description' => 'required|string',
            'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
            'problems_found' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'photos' => 'nullable|array',
        ]);

        $maintenance = MaintenanceSchedule::findOrFail($id);

        $report = MaintenanceReport::create([
            'maintenance_schedule_id' => $maintenance->id,
            'employee_id' => $maintenance->assigned_employee_id,
            'start_time' => now()->subHours(2),
            'end_time' => now(),
            'work_description' => $request->work_description,
            'problems_found' => $request->problems_found,
            'recommendations' => $request->recommendations,
            'completion_status' => $request->completion_status,
            'customer_name' => $request->customer_name,
            'customer_notes' => $request->customer_notes,
            'photos' => $request->photos ? json_encode($request->photos) : null,
            'total_cost' => 0,
        ]);

        // İşi tamamlandı olarak işaretle
        $maintenance->update(['status' => 'tamamlandi']);

        $approvalSms = ['sent' => false];
        if ($request->completion_status === 'tamamlandi') {
            $approvalSms = app(MaintenanceApprovalService::class)
                ->initiateApprovalFlow($report->fresh(['building']));
        }

        return response()->json([
            'success' => true,
            'message' => 'Rapor başarıyla oluşturuldu',
            'data' => $report->fresh(),
            'approval_sms_sent' => $approvalSms['sent'] ?? false,
            'approval_sms_error' => $approvalSms['error'] ?? $approvalSms['skipped_reason'] ?? null,
        ]);
    }

    public function track()
    {
        $maintenanceSchedules = MaintenanceSchedule::with(['building', 'employee'])
            ->whereIn('status', ['atandi', 'baslandi', 'tamamlandi'])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $maintenanceSchedules,
        ]);
    }
}

