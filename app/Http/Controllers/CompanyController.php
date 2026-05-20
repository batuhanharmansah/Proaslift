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
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company->update($request->only(['name', 'email', 'phone', 'address', 'notes']));

        return redirect()->route('company.profile')->with('success', 'Firma bilgileri başarıyla güncellendi.');
    }
}

