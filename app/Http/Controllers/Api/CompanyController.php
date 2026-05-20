<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Building;
use App\Models\MaintenanceSchedule;
use App\Models\IssueReport;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function getDashboardStats()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_buildings' => Building::where('company_id', $companyId)->count(),
            'active_maintenance' => MaintenanceSchedule::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->whereIn('status', ['planlandi', 'devam_ediyor'])->count(),
            'pending_issues' => IssueReport::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->whereIn('status', ['beklemede', 'atandi'])->count(),
            'active_employees' => Employee::where('company_id', $companyId)->where('is_active', true)->count(),
            'total_income' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gelir')->sum('amount'),
            'total_expense' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gider')->sum('amount'),
        ];

        return response()->json(['data' => $stats]);
    }

        public function getEmployees()
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->get();

        return response()->json(['data' => $employees]);
    }

    public function getEmployee($id)
    {
        $employee = Employee::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json(['data' => $employee]);
    }

    public function createEmployee(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $employee = Employee::create([
            'company_id' => Auth::user()->company_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'is_active' => $request->is_active,
        ]);

        return response()->json(['data' => $employee], 201);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:employees,email,' . $id,
            'phone' => 'sometimes|string|max:20',
            'position' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $employee->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'position', 'is_active'
        ]));

        return response()->json(['data' => $employee]);
    }

    public function getProfile()
    {
        $company = Company::findOrFail(Auth::user()->company_id);
        return response()->json(['data' => $company]);
    }

    public function updateProfile(Request $request)
    {
        $company = Company::findOrFail(Auth::user()->company_id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email',
            'tax_number' => 'sometimes|string|max:50',
        ]);

        $company->update($request->only([
            'name', 'address', 'phone', 'email', 'tax_number'
        ]));

        return response()->json(['data' => $company]);
    }
}
