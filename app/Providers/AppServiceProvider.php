<?php

namespace App\Providers;

use App\Models\Employee;
use App\Observers\EmployeeObserver;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Employee::observe(EmployeeObserver::class);

        // Carbon'u Türkçe olarak ayarla
        Carbon::setLocale('tr');

        // Varsayılan timezone'u ayarla
        date_default_timezone_set('Europe/Istanbul');
    }
}
