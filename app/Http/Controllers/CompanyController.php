<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function profile()
    {
        $company = auth()->user()->company;

        if (!$company) {
            abort(404, 'Firma bulunamadı.');
        }

        return view('company.profile', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            abort(404, 'Firma bulunamadı.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:5120',
            'stamp' => 'nullable|image|max:5120',
            'brand_primary_color' => 'nullable|string|max:7',
            'brand_secondary_color' => 'nullable|string|max:7',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'name', 'email', 'phone', 'address', 'tax_number', 'notes',
            'brand_primary_color', 'brand_secondary_color',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('company-branding', 'public');
        }

        if ($request->hasFile('stamp')) {
            $data['stamp_path'] = $request->file('stamp')->store('company-branding', 'public');
        }

        $company->update($data);

        return redirect()->route('company.profile')->with('success', 'Firma bilgileri başarıyla güncellendi.');
    }
}

