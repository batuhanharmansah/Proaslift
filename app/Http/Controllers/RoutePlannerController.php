<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

/**
 * Bina Rota Planlayıcı (rakip analizi karşılaştırması sonucu eklendi).
 * Haritada binaları gösterir, seçilenleri en yakına göre sıralar ve
 * Google Maps'te yol tarifi olarak açar. Rota optimizasyonu (akıllı sıralama)
 * tarayıcıda (JS) basit bir "en yakın komşu" algoritmasıyla yapılır.
 */
class RoutePlannerController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('maintenance.route-planner', compact('employees'));
    }

    public function buildings(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $filter = $request->get('filter', 'all');
        $employeeId = $request->get('employee_id');

        $query = Building::where('company_id', $companyId)
            ->where('status', 'aktif')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($filter === 'assigned_to_me' || $filter === 'employee') {
            $targetEmployeeId = $filter === 'assigned_to_me'
                ? optional(Employee::where('company_id', $companyId)->where('email', auth()->user()->email)->first())->id
                : $employeeId;

            if ($targetEmployeeId) {
                $buildingIds = MaintenanceSchedule::where('assigned_employee_id', $targetEmployeeId)
                    ->whereIn('status', ['planli', 'atandi'])
                    ->pluck('building_id');
                $query->whereIn('id', $buildingIds);
            }
        } elseif ($filter === 'overdue') {
            $buildingIds = MaintenanceSchedule::where('company_id', $companyId)
                ->whereIn('status', ['planli', 'atandi'])
                ->where('scheduled_date', '<', now()->toDateString())
                ->pluck('building_id');
            $query->whereIn('id', $buildingIds);
        }

        $buildings = $query->get(['id', 'name', 'address', 'district', 'city', 'latitude', 'longitude']);

        return response()->json([
            'success' => true,
            'data' => $buildings,
        ]);
    }
}
