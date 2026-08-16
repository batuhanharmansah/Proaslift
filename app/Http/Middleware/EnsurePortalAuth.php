<?php

namespace App\Http\Middleware;

use App\Models\BuildingPortalAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Müşteri Portalı için ana auth sisteminden tamamen izole, session tabanlı
 * basit bir koruma. Laravel'in guard config'ine dokunmaz.
 */
class EnsurePortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $accountId = session('portal_account_id');

        if (!$accountId) {
            return redirect()->route('portal.login');
        }

        $account = BuildingPortalAccount::where('id', $accountId)->where('is_active', true)->first();

        if (!$account) {
            session()->forget('portal_account_id');
            return redirect()->route('portal.login');
        }

        $request->attributes->set('portalAccount', $account);

        return $next($request);
    }
}
