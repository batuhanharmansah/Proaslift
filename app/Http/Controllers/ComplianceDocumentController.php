<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ComplianceDocument;
use Illuminate\Http\Request;

/**
 * DTR (Durum Tespit Raporu) + Kurtarma Formu — Özellik #11.
 */
class ComplianceDocumentController extends Controller
{
    public function index(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, ComplianceDocument::TYPES), 404);

        $companyId = auth()->user()->company_id;

        $documents = ComplianceDocument::where('company_id', $companyId)
            ->where('document_type', $type)
            ->with('building')
            ->orderByDesc('event_date')
            ->paginate(25);

        $buildings = Building::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);

        return view('compliance-documents.index', [
            'documents' => $documents,
            'buildings' => $buildings,
            'type' => $type,
            'typeLabel' => ComplianceDocument::TYPES[$type],
        ]);
    }

    public function store(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, ComplianceDocument::TYPES), 404);

        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'event_date' => 'required|date',
            'inspector_or_technician_name' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        $building = Building::findOrFail($validated['building_id']);
        abort_if($building->company_id !== auth()->user()->company_id, 403);

        ComplianceDocument::create(array_merge($validated, [
            'company_id' => auth()->user()->company_id,
            'document_type' => $type,
            'status' => 'taslak',
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', ComplianceDocument::TYPES[$type] . ' kaydı eklendi.');
    }

    public function updateStatus(Request $request, ComplianceDocument $complianceDocument)
    {
        abort_if($complianceDocument->company_id !== auth()->user()->company_id, 403);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(ComplianceDocument::STATUSES)),
        ]);

        $complianceDocument->update($validated);

        return back()->with('success', 'Durum güncellendi.');
    }

    public function destroy(ComplianceDocument $complianceDocument)
    {
        abort_if($complianceDocument->company_id !== auth()->user()->company_id, 403);

        $type = $complianceDocument->document_type;
        $complianceDocument->delete();

        return back()->with('success', 'Kayıt silindi.');
    }
}
