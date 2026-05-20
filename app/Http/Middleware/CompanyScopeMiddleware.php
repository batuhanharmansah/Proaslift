<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyScopeMiddleware
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

        // Super admin her şeyi görebilir
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Kullanıcının company_id'sini session'a kaydet
        // Bu, Global Scope'larda kullanılacak
        session(['user_company_id' => $user->company_id]);

        return $next($request);
    }
}
