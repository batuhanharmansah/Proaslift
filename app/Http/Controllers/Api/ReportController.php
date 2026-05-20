<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceReport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Rapor listesini getir
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel bulunamadı'
                ], 404);
            }

            $reports = MaintenanceReport::with(['maintenanceSchedule.building', 'employee'])
                ->where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $reports,
                'message' => 'Raporlar başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Raporlar getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rapor detayını getir
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel bulunamadı'
                ], 404);
            }

            $report = MaintenanceReport::with(['maintenanceSchedule.building', 'employee'])
                ->where('id', $id)
                ->where('employee_id', $employee->id)
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rapor bulunamadı'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Rapor detayı başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapor detayı getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Yeni rapor oluştur
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'maintenance_schedule_id' => 'required|exists:maintenance_schedules,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'work_description' => 'required|string|max:1000',
                'used_products' => 'nullable|array',
                'used_products.*.name' => 'required|string',
                'used_products.*.quantity' => 'required|numeric|min:0',
                'used_products.*.unit' => 'required|string',
                'total_cost' => 'nullable|numeric|min:0',
                'problems_found' => 'nullable|string|max:500',
                'recommendations' => 'nullable|string|max:500',
                'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
                'customer_signature' => 'boolean',
                'customer_name' => 'nullable|string|max:255',
                'customer_notes' => 'nullable|string|max:500',
                'photos' => 'nullable|array',
                'photos.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel bulunamadı'
                ], 404);
            }

            $report = MaintenanceReport::create([
                'maintenance_schedule_id' => $request->maintenance_schedule_id,
                'employee_id' => $employee->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'work_description' => $request->work_description,
                'used_products' => $request->used_products,
                'total_cost' => $request->total_cost ?? 0,
                'problems_found' => $request->problems_found,
                'recommendations' => $request->recommendations,
                'completion_status' => $request->completion_status,
                'customer_signature' => $request->customer_signature ?? false,
                'customer_name' => $request->customer_name,
                'customer_notes' => $request->customer_notes,
                'photos' => $request->photos
            ]);

            $report->load(['maintenanceSchedule.building', 'employee']);

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Rapor başarıyla oluşturuldu'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapor oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rapor güncelle
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time',
                'work_description' => 'sometimes|string|max:1000',
                'used_products' => 'nullable|array',
                'used_products.*.name' => 'required|string',
                'used_products.*.quantity' => 'required|numeric|min:0',
                'used_products.*.unit' => 'required|string',
                'total_cost' => 'nullable|numeric|min:0',
                'problems_found' => 'nullable|string|max:500',
                'recommendations' => 'nullable|string|max:500',
                'completion_status' => 'sometimes|in:tamamlandi,kismi_tamamlandi,ertelendi',
                'customer_signature' => 'boolean',
                'customer_name' => 'nullable|string|max:255',
                'customer_notes' => 'nullable|string|max:500',
                'photos' => 'nullable|array',
                'photos.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel bulunamadı'
                ], 404);
            }

            $report = MaintenanceReport::where('id', $id)
                ->where('employee_id', $employee->id)
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rapor bulunamadı'
                ], 404);
            }

            $report->update($request->only([
                'start_time',
                'end_time',
                'work_description',
                'used_products',
                'total_cost',
                'problems_found',
                'recommendations',
                'completion_status',
                'customer_signature',
                'customer_name',
                'customer_notes',
                'photos'
            ]));

            $report->load(['maintenanceSchedule.building', 'employee']);

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Rapor başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapor güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rapor sil
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel bulunamadı'
                ], 404);
            }

            $report = MaintenanceReport::where('id', $id)
                ->where('employee_id', $employee->id)
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rapor bulunamadı'
                ], 404);
            }

            $report->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rapor başarıyla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapor silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
