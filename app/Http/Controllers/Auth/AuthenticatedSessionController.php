<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     * Cache-Control headers prevent browser from serving stale form with expired CSRF token.
     */
    public function create(): View|Response
    {
        $response = response()->view('auth.login');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // Role-based yönlendirme
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('super-admin.dashboard'));
        } elseif ($user->isCompanyAdmin()) {
            // Firma aktif mi kontrol et
            if (!$user->company || !$user->company->is_active) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Firmanızın sistemi askıya alınmıştır.');
            }
            return redirect()->intended(route('dashboard'));
        } elseif ($user->isEmployee()) {
            // Firma aktif mi kontrol et
            if (!$user->company || !$user->company->is_active) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Firmanızın sistemi askıya alınmıştır.');
            }
            return redirect()->intended(route('employee.dashboard'));
        }

        // Rol atanmamış kullanıcı
        auth()->logout();
        return redirect()->route('login')->with('error', 'Hesabınıza rol atanmamış. Lütfen yöneticinizle iletişime geçin.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
