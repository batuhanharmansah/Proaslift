<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Models\Employee;
use App\Models\Building;
use App\Models\EmployeeLocation;
use App\Models\AuditLog;
use App\Services\LocationCheckService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function getAssignments()
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Personel bulunamadı',
            ], 404);
        }

        $assignments = MaintenanceSchedule::with(['building', 'assignedEmployee'])
            ->where('assigned_employee_id', $employee->id)
            ->whereIn('status', ['planli', 'atandi', 'baslandi'])
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments,
            'message' => $assignments->count() > 0 ? 'İş listesi yüklendi' : 'Henüz iş atamanız bulunmuyor',
        ]);
    }

    public function getAssignment($id)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        $assignment = MaintenanceSchedule::with(['building', 'assignedEmployee'])
            ->where('id', $id)
            ->where('assigned_employee_id', $employee->id)
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ataması bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $assignment,
        ]);
    }

    public function startWork($id)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        $assignment = MaintenanceSchedule::where('id', $id)
            ->where('assigned_employee_id', $employee->id)
            ->whereIn('status', ['atandi', 'planli'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ataması bulunamadı veya başlatılamaz',
            ], 404);
        }

        $assignment->update(['status' => 'baslandi']);

        return response()->json([
            'success' => true,
            'message' => 'İş başlatıldı',
            'data' => $assignment->fresh(['building', 'assignedEmployee']),
        ]);
    }

    public function completeWork(Request $request, $id)
    {
        $request->validate([
            'work_description' => 'required|string',
            'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
            'problems_found' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'used_materials' => 'nullable|array',
            'used_materials.*.materialId' => 'required|integer|exists:products,id',
            'used_materials.*.quantity' => 'required|integer|min:1',
            // Routine maintenance specific fields
            'routine_maintenance_checklist' => 'nullable|array',
            'routine_maintenance_checklist.*.id' => 'required_with:routine_maintenance_checklist|string',
            'routine_maintenance_checklist.*.title' => 'required_with:routine_maintenance_checklist|string',
            'routine_maintenance_checklist.*.icon' => 'required_with:routine_maintenance_checklist|string',
            'routine_maintenance_checklist.*.items' => 'required_with:routine_maintenance_checklist|array',
            'routine_maintenance_checklist.*.items.*.id' => 'required|string',
            'routine_maintenance_checklist.*.items.*.title' => 'required|string',
            'routine_maintenance_checklist.*.items.*.checked' => 'required|boolean',
            'routine_maintenance_checklist.*.items.*.notes' => 'nullable|string',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        $assignment = MaintenanceSchedule::where('id', $id)
            ->where('assigned_employee_id', $employee->id)
            ->where('status', 'baslandi')
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ataması bulunamadı veya tamamlanamaz',
            ], 404);
        }

        // İşi tamamlandı olarak işaretle
        $assignment->update(['status' => 'tamamlandi']);

        // Kullanılan malzemeleri stoktan düş
        $totalCost = 0;
        if ($request->used_materials) {
            foreach ($request->used_materials as $usedMaterial) {
                $product = \App\Models\Product::find($usedMaterial['materialId']);
                if ($product && $product->stock_quantity >= $usedMaterial['quantity']) {
                    $product->decrement('stock_quantity', $usedMaterial['quantity']);
                    $totalCost += $product->cost_price * $usedMaterial['quantity'];
                }
            }
        }

        // Rapor oluştur
        $reportData = [
            'maintenance_schedule_id' => $assignment->id,
            'employee_id' => $employee->id,
            'start_time' => now()->subHours(2), // Mock start time
            'end_time' => now(),
            'work_description' => $request->work_description,
            'problems_found' => $request->problems_found,
            'recommendations' => $request->recommendations,
            'completion_status' => $request->completion_status,
            'customer_name' => $request->customer_name,
            'customer_notes' => $request->customer_notes,
            'photos' => $request->photos ? json_encode($request->photos) : null,
            'used_products' => $request->used_materials ? json_encode($request->used_materials) : null,
            'total_cost' => $totalCost,
        ];

        // Rutin bakım kontrol listesi verisini ekle
        if ($request->routine_maintenance_checklist) {
            $reportData['routine_maintenance_checklist'] = json_encode($request->routine_maintenance_checklist);
            $reportData['completion_percentage'] = $request->completion_percentage ?? 0;
        }

        $report = MaintenanceReport::create($reportData);

        return response()->json([
            'success' => true,
            'message' => 'İş tamamlandı ve rapor oluşturuldu',
            'data' => $report,
        ]);
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:maintenance_schedules,id',
            'form_type' => 'required|string',
            'form_data' => 'required|array',
        ]);

        // Form verilerini işle ve kaydet
        // Bu kısım ihtiyaca göre genişletilebilir

        return response()->json([
            'success' => true,
            'message' => 'Form başarıyla gönderildi',
        ]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'maintenance_schedule_id' => 'required|exists:maintenance_schedules,id',
        ]);

        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Personel bulunamadı',
            ], 404);
        }

        $schedule = MaintenanceSchedule::where('id', $request->maintenance_schedule_id)
            ->where('assigned_employee_id', $employee->id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Bu iş size atanmamış veya bulunamadı.',
            ], 403);
        }

        if ($schedule->status === 'tamamlandi') {
            AuditLog::logCustomAction(
                'MaintenanceSchedule',
                (int) $schedule->id,
                'tracking_stopped',
                'Konum güncellemesi reddedildi: iş tamamlanmış.',
                ['jobId' => $schedule->id, 'employee_id' => $employee->id],
                $user->id
            );
            return response()->json([
                'success' => false,
                'message' => 'Bu iş tamamlandı. Konum güncellemesi kabul edilmiyor.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $jobId = (int) $request->maintenance_schedule_id;
            $isFirstLocationRecently = !EmployeeLocation::where('maintenance_schedule_id', $jobId)
                ->where('employee_id', $employee->id)
                ->where('recorded_at', '>=', now()->subMinutes(10))
                ->exists();

            $employeeLocation = EmployeeLocation::create([
                'employee_id' => $employee->id,
                'company_id' => $employee->company_id,
                'maintenance_schedule_id' => $jobId,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'speed' => $request->speed,
                'heading' => $request->heading,
                'recorded_at' => now(),
            ]);

            if ($isFirstLocationRecently) {
                AuditLog::logCustomAction(
                    'MaintenanceSchedule',
                    $jobId,
                    'tracking_started',
                    'Konum takibi başlatıldı (aktif iş).',
                    ['jobId' => $jobId, 'employee_id' => $employee->id],
                    $user->id
                );
            }

            $locationCheckService = new LocationCheckService();
            $checkResults = $locationCheckService->processLocationUpdate($employeeLocation);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Konum güncellendi',
                'data' => [
                    'location' => [
                        'id' => $employeeLocation->id,
                        'employee_id' => $employeeLocation->employee_id,
                        'latitude' => $employeeLocation->latitude,
                        'longitude' => $employeeLocation->longitude,
                        'accuracy' => $employeeLocation->accuracy,
                        'speed' => $employeeLocation->speed,
                        'heading' => $employeeLocation->heading,
                        'recorded_at' => $employeeLocation->recorded_at->format('Y-m-d H:i:s'),
                    ],
                    'check_results' => $checkResults,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Konum güncellenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMaterials()
    {
        $materials = \App\Models\Product::where('is_active', true)
            ->select('id', 'name', 'code', 'category', 'unit', 'stock_quantity')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $materials,
            'message' => 'Malzeme listesi yüklendi',
        ]);
    }
}
