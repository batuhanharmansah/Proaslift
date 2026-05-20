<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            // Bearer token varsa token'a göre, yoksa IP'ye göre sınırla
            // auth:sanctum route middleware'den önce çalıştığı için $request->user() null olabilir
            $key = $request->bearerToken() ?: $request->ip();
            return Limit::perMinute(300)->by($key);
        });

        // Route model binding for AccountingEntry with company scope
        Route::bind('muhasebe', function ($value) {
            return \App\Models\AccountingEntry::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        // Route model binding for Product with company scope (Depo)
        Route::bind('product', function ($value) {
            return \App\Models\Product::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });
        // Eski URL uyumluluğu
        Route::bind('urun_katalogu', function ($value) {
            return \App\Models\Product::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        // Route model binding for Employee with company scope
        Route::bind('personeller', function ($value) {
            return \App\Models\Employee::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        // Route model binding for Building with company scope
        Route::bind('binalar', function ($value) {
            return \App\Models\Building::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        // Route model binding for IssueReport with company scope
        Route::bind('ariza_bildirimi', function ($value) {
            return \App\Models\IssueReport::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        // Route model binding for MaintenanceSchedule with company scope
        Route::bind('bakim_takibi', function ($value) {
            return \App\Models\MaintenanceSchedule::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        // Route model binding for Employee MaintenanceSchedule with company scope
        Route::bind('maintenance', function ($value) {
            return \App\Models\MaintenanceSchedule::where('id', $value)
                ->where('company_id', auth()->user()->company_id)
                ->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Mobile API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/mobile-api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
