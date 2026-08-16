<?php

namespace App\Http\Controllers;

use App\Models\CompanyVehicle;
use App\Models\Employee;
use App\Models\EmployeeAbsence;
use App\Models\EmployeeBonus;
use Illuminate\Http\Request;

/**
 * Hakediş + Araç Takip + Devamsızlık — Özellik #13 (rakip analizi sonucu eklendi).
 * Tek sayfa, sekmeli görünüm; her biri kendi basit CRUD'una sahip.
 */
class HrFleetController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->orderBy('first_name')->get();
        $bonuses = EmployeeBonus::where('company_id', $companyId)->with('employee')->orderByDesc('bonus_date')->limit(50)->get();
        $vehicles = CompanyVehicle::where('company_id', $companyId)->with('driver')->orderBy('plate')->get();
        $absences = EmployeeAbsence::where('company_id', $companyId)->with('employee')->orderByDesc('start_date')->limit(50)->get();

        return view('hr-fleet.index', compact('employees', 'bonuses', 'vehicles', 'absences'));
    }

    public function storeBonus(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bonus_date' => 'required|date',
            'type' => 'required|in:' . implode(',', array_keys(EmployeeBonus::TYPES)),
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        EmployeeBonus::create(array_merge($validated, [
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Hakediş kaydı eklendi.');
    }

    public function destroyBonus(EmployeeBonus $bonus)
    {
        abort_if($bonus->company_id !== auth()->user()->company_id, 403);
        $bonus->delete();
        return back()->with('success', 'Kayıt silindi.');
    }

    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'plate' => 'required|string|max:20',
            'brand_model' => 'nullable|string|max:150',
            'driver_employee_id' => 'nullable|exists:employees,id',
            'inspection_due_date' => 'nullable|date',
            'insurance_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        CompanyVehicle::create(array_merge($validated, ['company_id' => auth()->user()->company_id]));

        return back()->with('success', 'Araç eklendi.');
    }

    public function destroyVehicle(CompanyVehicle $vehicle)
    {
        abort_if($vehicle->company_id !== auth()->user()->company_id, 403);
        $vehicle->delete();
        return back()->with('success', 'Araç silindi.');
    }

    public function storeAbsence(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:' . implode(',', array_keys(EmployeeAbsence::TYPES)),
            'note' => 'nullable|string|max:500',
        ]);

        EmployeeAbsence::create(array_merge($validated, ['company_id' => auth()->user()->company_id]));

        return back()->with('success', 'Devamsızlık kaydı eklendi.');
    }

    public function destroyAbsence(EmployeeAbsence $absence)
    {
        abort_if($absence->company_id !== auth()->user()->company_id, 403);
        $absence->delete();
        return back()->with('success', 'Kayıt silindi.');
    }
}
