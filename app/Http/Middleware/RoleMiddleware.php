<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin her yere erişebilir
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Belirtilen rollerden birine sahip mi kontrol et
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Yetkisiz erişim
        abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
    }
}
