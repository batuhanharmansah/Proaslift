<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::where('company_id', Auth::user()->company_id)
            ->with(['contacts', 'documents'])
            ->get();
        
        return response()->json(['data' => $buildings]);
    }

    public function show($id)
    {
        $building = Building::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->with(['contacts', 'documents', 'maintenanceSchedules', 'issueReports'])
            ->firstOrFail();
        
        return response()->json(['data' => $building]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'elevator_count' => 'required|integer|min:1',
            'building_type' => 'required|in:residential,commercial,mixed',
            'construction_year' => 'required|integer|min:1900|max:' . date('Y'),
            'total_floors' => 'required|integer|min:1',
        ]);

        $building = Building::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'elevator_count' => $request->elevator_count,
            'building_type' => $request->building_type,
            'construction_year' => $request->construction_year,
            'total_floors' => $request->total_floors,
            'status' => 'active',
        ]);

        return response()->json(['data' => $building], 201);
    }

    public function update(Request $request, $id)
    {
        $building = Building::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'elevator_count' => 'sometimes|integer|min:1',
            'building_type' => 'sometimes|in:residential,commercial,mixed',
            'construction_year' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'total_floors' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $building->update($request->only([
            'name', 'address', 'city', 'postal_code', 'elevator_count',
            'building_type', 'construction_year', 'total_floors', 'status'
        ]));

        return response()->json(['data' => $building]);
    }
}
