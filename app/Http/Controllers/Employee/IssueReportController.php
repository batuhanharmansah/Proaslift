<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use Illuminate\Http\Request;

class IssueReportController extends Controller
{
    private function getEmployeeId()
    {
        $user = auth()->user();

        if (!$user->company) {
            abort(403, 'Firma bilgileriniz bulunamadı.');
        }

        $employee = $user->company->employees()->where('email', $user->email)->first();

        if (!$employee) {
            abort(403, 'Personel kaydınız bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }

        return $employee->id;
    }

    public function index(Request $request)
    {
        $employeeId = $this->getEmployeeId();

        $query = IssueReport::with('building')
            ->where('assigned_employee_id', $employeeId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $issueReports = $query->latest()->paginate(15);

        return view('employee.issue-reports.index', compact('issueReports'));
    }

    public function show(IssueReport $issueReport)
    {
        $employeeId = $this->getEmployeeId();

        if ($issueReport->assigned_employee_id != $employeeId) {
            abort(403, 'Bu arıza bildirimi size atanmamış.');
        }

        $issueReport->load(['building', 'maintenanceSchedule.maintenanceReport']);

        return view('employee.issue-reports.show', compact('issueReport'));
    }

    public function startWork(IssueReport $issueReport)
    {
        $employeeId = $this->getEmployeeId();

        if ($issueReport->assigned_employee_id != $employeeId) {
            abort(403, 'Bu arıza bildirimi size atanmamış.');
        }

        $issueReport->startWork();

        return redirect()->route('employee.issue-reports.show', $issueReport)
            ->with('success', 'Çalışma başlatıldı.');
    }

    public function complete(IssueReport $issueReport)
    {
        $employeeId = $this->getEmployeeId();

        if ($issueReport->assigned_employee_id != $employeeId) {
            abort(403, 'Bu arıza bildirimi size atanmamış.');
        }

        $issueReport->complete();

        return redirect()->route('employee.issue-reports.show', $issueReport)
            ->with('success', 'Arıza bildirimi tamamlandı olarak işaretlendi.');
    }
}
