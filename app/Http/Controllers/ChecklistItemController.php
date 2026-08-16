<?php

namespace App\Http\Controllers;

use App\Models\CustomChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChecklistItemController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $items = CustomChecklistItem::where('company_id', $companyId)
            ->orderBy('section_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section_id');

        return view('settings.checklist-items', [
            'items' => $items,
            'sections' => CustomChecklistItem::SECTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|in:' . implode(',', array_keys(CustomChecklistItem::SECTIONS)),
            'title' => 'required|string|max:500',
        ]);

        $companyId = auth()->user()->company_id;

        $maxSort = CustomChecklistItem::where('company_id', $companyId)
            ->where('section_id', $validated['section_id'])
            ->max('sort_order') ?? 0;

        CustomChecklistItem::create([
            'company_id' => $companyId,
            'section_id' => $validated['section_id'],
            'item_key' => 'custom_' . $companyId . '_' . Str::random(10),
            'title' => $validated['title'],
            'sort_order' => $maxSort + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Özel madde eklendi.');
    }

    public function destroy(CustomChecklistItem $checklistItem)
    {
        abort_if($checklistItem->company_id !== auth()->user()->company_id, 403);

        $checklistItem->delete();

        return back()->with('success', 'Özel madde silindi.');
    }
}
