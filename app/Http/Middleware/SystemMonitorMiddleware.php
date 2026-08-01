<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sistem sağlığı izleme sayfası herkese açık bir super_admin özelliği DEĞİLDİR.
 * Sadece .env'deki SYSTEM_MONITOR_EMAILS listesinde e-postası bulunan kullanıcılar erişebilir.
 */
class SystemMonitorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedEmails = array_filter(array_map(
            'trim',
            explode(',', config('app.system_monitor_emails', 'superadmin@harmansah.com'))
        ));

        if (!in_array($user->email, $allowedEmails, true)) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
