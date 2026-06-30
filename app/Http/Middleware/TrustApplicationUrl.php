<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class TrustApplicationUrl
{
    /**
     * Align generated URLs and session cookie security with the actual request.
     * Prevents 419 errors when APP_URL host/port differs from the browser URL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost()) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        if ($request->isSecure()) {
            config(['session.secure' => true]);
        }

        return $next($request);
    }
}
