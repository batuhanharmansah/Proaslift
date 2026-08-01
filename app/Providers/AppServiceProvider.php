<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\SystemEvent;
use App\Observers\EmployeeObserver;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Throwable;

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

        Queue::failing(function (JobFailed $event) {
            try {
                SystemEvent::log(
                    source: 'web',
                    type: 'queue_failed',
                    severity: 'critical',
                    message: $event->exception->getMessage() ?: get_class($event->exception),
                    stackTrace: $event->exception->getTraceAsString(),
                    context: [
                        'connection' => $event->connectionName,
                        'job' => $event->job->resolveName(),
                    ]
                );
            } catch (Throwable $loggingFailure) {
                // izleme asla kuyruk işleyicisini bozmamalı
            }
        });
    }
}
