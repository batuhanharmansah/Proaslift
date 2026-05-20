<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IssueReport;
use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class IssueReportController extends Controller
{
    public function index()
    {
        $issues = IssueReport::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->with(['building', 'assignedEmployee'])
        ->orderBy('created_at', 'desc')
        ->get();
        
        return response()->json(['data' => $issues]);
    }

    public function show($id)
    {
        $issue = IssueReport::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('id', $id)
        ->with(['building', 'assignedEmployee'])
        ->firstOrFail();
        
        return response()->json(['data' => $issue]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:dusuk,orta,yuksek,acil',
            'issue_type' => 'required|in:ariza,guvenlik,performans,diger',
            'reported_by' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
        ]);

        // Check if building belongs to company
        $building = Building::where('company_id', Auth::user()->company_id)
            ->where('id', $request->building_id)
            ->firstOrFail();

        $issue = IssueReport::create([
            'building_id' => $request->building_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'issue_type' => $request->issue_type,
            'reported_by' => $request->reported_by,
            'contact_phone' => $request->contact_phone,
            'status' => 'beklemede',
        ]);

        return response()->json(['data' => $issue], 201);
    }

    public function update(Request $request, $id)
    {
        $issue = IssueReport::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('id', $id)
        ->firstOrFail();

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|in:dusuk,orta,yuksek,acil',
            'issue_type' => 'sometimes|in:ariza,guvenlik,performans,diger',
            'status' => 'sometimes|in:beklemede,atandi,devam_ediyor,tamamlandi,iptal',
            'resolution_notes' => 'sometimes|string',
        ]);

        $issue->update($request->only([
            'title', 'description', 'priority', 'issue_type', 'status', 'resolution_notes'
        ]));

        return response()->json(['data' => $issue]);
    }

    public function assignEmployee(Request $request, $id)
    {
        $issue = IssueReport::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('id', $id)
        ->firstOrFail();

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'sometimes|string',
        ]);

        $issue->update([
            'assigned_employee_id' => $request->employee_id,
            'status' => 'atandi',
            'assignment_notes' => $request->notes,
        ]);

        return response()->json(['data' => $issue]);
    }
}
