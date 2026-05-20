<?php

namespace App\Http\Controllers;

use App\Services\MaintenanceApprovalService;
use Illuminate\Http\Request;

class MaintenanceApprovalController extends Controller
{
    public function __construct(
        private readonly MaintenanceApprovalService $approvalService
    ) {
    }

    public function show(string $token)
    {
        $tokenRecord = $this->approvalService->findValidToken($token);

        if (!$tokenRecord) {
            return response()->view('approval.expired', [], 410);
        }

        $building = $tokenRecord->building;
        $pending = $this->approvalService->getPendingReportsForBuilding($building->id);

        $jobs = $pending->map(fn ($report) => $this->approvalService->formatReportForApprovalList($report));

        return view('approval.form', [
            'token' => $token,
            'buildingName' => $building->name ?? 'Bina',
            'jobs' => $jobs,
            'pendingCount' => $jobs->count(),
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $tokenRecord = $this->approvalService->findValidToken($token);

        if (!$tokenRecord) {
            return response()->view('approval.expired', [], 410);
        }

        $validated = $request->validate([
            'approved_by_name' => 'required|string|min:3|max:255',
            'accept_terms' => 'accepted',
        ], [
            'approved_by_name.required' => 'Ad soyad zorunludur.',
            'accept_terms.accepted' => 'Onay kutusunu işaretlemeniz gerekmektedir.',
        ]);

        try {
            $result = $this->approvalService->approveAllPending(
                $token,
                $validated['approved_by_name'],
                $request->ip()
            );
        } catch (\InvalidArgumentException $e) {
            return response()->view('approval.expired', [], 410);
        }

        if ($result['approved_count'] === 0) {
            return view('approval.thankyou', [
                'buildingName' => $result['building']->name ?? 'Bina',
                'approvedCount' => 0,
                'alreadyApproved' => true,
            ]);
        }

        return view('approval.thankyou', [
            'buildingName' => $result['building']->name ?? 'Bina',
            'approvedCount' => $result['approved_count'],
            'alreadyApproved' => false,
        ]);
    }
}
