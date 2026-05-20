<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin kontrolü geçer
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Kullanıcının firması var mı?
        if (!$user->company) {
            return redirect()->route('login')->with('error', 'Firma bilgileriniz bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }

        // Firma aktif mi?
        if (!$user->company->is_active) {
            return view('errors.company-suspended', [
                'company' => $user->company,
                'message' => 'Firmanızın sistemi askıya alınmıştır. Ödeme durumunuzu kontrol ediniz.'
            ]);
        }

        // Abonelik süresi dolmuş mu?
        if ($user->company->is_subscription_expired) {
            return view('errors.subscription-expired', [
                'company' => $user->company,
                'message' => 'Abonelik süreniz dolmuştur. Lütfen ödemenizi yaparak sistemi aktifleştirin.'
            ]);
        }

        return $next($request);
    }
}
