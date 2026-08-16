<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BuildingPortalAccount;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        if (session('portal_account_id')) {
            return redirect()->route('portal.dashboard');
        }

        return view('portal.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $phone = PhoneNormalizer::normalize($validated['phone']);

        $account = BuildingPortalAccount::where('phone', $phone)->where('is_active', true)->first();

        if (!$account || !Hash::check($validated['password'], $account->password)) {
            return back()->withErrors(['phone' => 'Telefon numarası veya şifre hatalı.'])->withInput();
        }

        $request->session()->regenerate();
        session(['portal_account_id' => $account->id]);
        $account->update(['last_login_at' => now()]);

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('portal_account_id');
        $request->session()->regenerate();

        return redirect()->route('portal.login');
    }
}
