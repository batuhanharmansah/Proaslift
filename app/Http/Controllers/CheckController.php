<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\CompanyCheck;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = CompanyCheck::where('company_id', $companyId)->with('building');

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $checks = $query->orderBy('due_date')->paginate(25)->withQueryString();

        $pendingReceivable = CompanyCheck::where('company_id', $companyId)
            ->where('direction', 'gelen')->where('status', 'bekliyor')->sum('amount');
        $pendingPayable = CompanyCheck::where('company_id', $companyId)
            ->where('direction', 'giden')->where('status', 'bekliyor')->sum('amount');
        $dueSoonCount = CompanyCheck::where('company_id', $companyId)
            ->where('status', 'bekliyor')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(15)->toDateString()])
            ->count();

        $buildings = Building::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);

        return view('checks.index', compact('checks', 'pendingReceivable', 'pendingPayable', 'dueSoonCount', 'buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:cek,senet',
            'direction' => 'required|in:gelen,giden',
            'counterparty_name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'building_id' => 'nullable|exists:buildings,id',
            'notes' => 'nullable|string',
        ]);

        CompanyCheck::create(array_merge($validated, [
            'company_id' => auth()->user()->company_id,
            'status' => 'bekliyor',
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Çek/senet kaydı eklendi.');
    }

    public function updateStatus(Request $request, CompanyCheck $check)
    {
        abort_if($check->company_id !== auth()->user()->company_id, 403);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(CompanyCheck::STATUSES)),
        ]);

        $check->update($validated);

        return back()->with('success', 'Durum güncellendi.');
    }

    public function destroy(CompanyCheck $check)
    {
        abort_if($check->company_id !== auth()->user()->company_id, 403);

        $check->delete();

        return back()->with('success', 'Kayıt silindi.');
    }
}
