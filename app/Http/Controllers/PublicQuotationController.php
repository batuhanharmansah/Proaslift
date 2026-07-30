<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicQuotationController extends Controller
{
    public function show($token)
    {
        $quotation = Quotation::with(['company', 'building', 'units', 'items'])
            ->where('public_token', $token)
            ->firstOrFail();

        if ($quotation->valid_until && $quotation->valid_until->isPast() && !in_array($quotation->status, ['accepted', 'converted'], true)) {
            $quotation->update(['status' => 'expired']);
        } elseif ($quotation->status === 'sent') {
            $quotation->update([
                'status' => 'viewed',
                'viewed_at' => now('Europe/Istanbul'),
            ]);
        }

        return view('quotations.public-show', compact('quotation'));
    }

    public function respond(Request $request, $token)
    {
        $validated = $request->validate([
            'action' => 'required|in:accepted,rejected',
            'accepted_by_name' => 'required|string|max:255',
            'accepted_by_phone' => 'nullable|string|max:50',
            'accepted_by_email' => 'nullable|email|max:255',
            'customer_note' => 'nullable|string|max:2000',
        ]);

        $quotation = Quotation::where('public_token', $token)->firstOrFail();

        if (!in_array($quotation->status, ['sent', 'viewed'], true)) {
            return back()->with('error', 'Bu teklif için işlem yapılamaz.');
        }

        DB::transaction(function () use ($quotation, $validated, $request) {
            QuotationAcceptance::create([
                'quotation_id' => $quotation->id,
                'action' => $validated['action'],
                'accepted_by_name' => $validated['accepted_by_name'],
                'accepted_by_phone' => $validated['accepted_by_phone'] ?? null,
                'accepted_by_email' => $validated['accepted_by_email'] ?? null,
                'accepted_ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'customer_note' => $validated['customer_note'] ?? null,
                'terms_snapshot' => $quotation->terms,
                'accepted_at' => now('Europe/Istanbul'),
            ]);

            $quotation->update([
                'status' => $validated['action'],
                'accepted_at' => $validated['action'] === 'accepted' ? now('Europe/Istanbul') : null,
                'rejected_at' => $validated['action'] === 'rejected' ? now('Europe/Istanbul') : null,
            ]);
        });

        return redirect()->route('quotations.public.show', $quotation->public_token)
            ->with('success', $validated['action'] === 'accepted' ? 'Teklifi kabul ettiniz.' : 'Teklifi reddettiniz.');
    }
}
