<?php

namespace App\Observers;

use App\Models\MaintenanceSchedule;
use App\Services\LocationCheckService;
use App\Services\NotificationService;

class MaintenanceScheduleObserver
{
    protected $locationCheckService;
    protected $notificationService;

    public function __construct(
        LocationCheckService $locationCheckService,
        NotificationService $notificationService
    ) {
        $this->locationCheckService = $locationCheckService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the MaintenanceSchedule "created" event.
     */
    public function created(MaintenanceSchedule $maintenanceSchedule): void
    {
        // Create location checks if employee is assigned
        if ($maintenanceSchedule->assigned_employee_id) {
            $this->locationCheckService->createLocationChecks($maintenanceSchedule);
        }

        // Create notification
        $this->notificationService->notifyMaintenanceScheduled($maintenanceSchedule);
    }

    /**
     * Handle the MaintenanceSchedule "updated" event.
     */
    public function updated(MaintenanceSchedule $maintenanceSchedule): void
    {
        // If assigned_employee_id was just set, create location checks
        if ($maintenanceSchedule->wasChanged('assigned_employee_id') && $maintenanceSchedule->assigned_employee_id) {
            $this->locationCheckService->createLocationChecks($maintenanceSchedule);
        }

        // If scheduled_time, scheduled_date, or estimated_duration changed, update location checks
        if ($maintenanceSchedule->wasChanged(['scheduled_date', 'scheduled_time', 'estimated_duration']) && 
            $maintenanceSchedule->assigned_employee_id) {
            $this->locationCheckService->createLocationChecks($maintenanceSchedule);
        }

        // If status changed to completed, notify
        if ($maintenanceSchedule->wasChanged('status') && $maintenanceSchedule->status === 'tamamlandi') {
            $this->notificationService->notifyMaintenanceCompleted($maintenanceSchedule);
        }
    }

    /**
     * Handle the MaintenanceSchedule "deleted" event.
     */
    public function deleted(MaintenanceSchedule $maintenanceSchedule): void
    {
        // Location checks will be automatically deleted due to foreign key constraint
    }

    /**
     * Handle the MaintenanceSchedule "restored" event.
     */
    public function restored(MaintenanceSchedule $maintenanceSchedule): void
    {
        //
    }

    /**
     * Handle the MaintenanceSchedule "force deleted" event.
     */
    public function forceDeleted(MaintenanceSchedule $maintenanceSchedule): void
    {
        //
    }
}
