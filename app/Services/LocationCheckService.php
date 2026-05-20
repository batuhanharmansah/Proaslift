<?php

namespace App\Services;

use App\Models\LocationCheck;
use App\Models\MaintenanceSchedule;
use App\Models\EmployeeLocation;
use App\Models\Building;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LocationCheckService
{
    /**
     * Create location check records for a maintenance schedule
     */
    public function createLocationChecks(MaintenanceSchedule $maintenanceSchedule): void
    {
        if (!$maintenanceSchedule->assigned_employee_id || !$maintenanceSchedule->building_id) {
            return;
        }

        $building = $maintenanceSchedule->building;
        if (!$building->latitude || !$building->longitude) {
            return;
        }

        // Arrival check (scheduled_time ± 15 minutes)
        LocationCheck::updateOrCreate(
            [
                'maintenance_schedule_id' => $maintenanceSchedule->id,
                'check_type' => 'arrival',
            ],
            [
                'employee_id' => $maintenanceSchedule->assigned_employee_id,
                'building_id' => $building->id,
                'company_id' => $maintenanceSchedule->company_id,
                'scheduled_time' => Carbon::parse($maintenanceSchedule->scheduled_date->format('Y-m-d') . ' ' . $maintenanceSchedule->scheduled_time),
                'building_latitude' => $building->latitude,
                'building_longitude' => $building->longitude,
                'tolerance_minutes' => 15,
                'status' => 'pending',
            ]
        );

        // Departure check (estimated_end_time ± 15 minutes)
        if ($maintenanceSchedule->estimated_duration) {
            $scheduledDateTime = Carbon::parse($maintenanceSchedule->scheduled_date->format('Y-m-d') . ' ' . $maintenanceSchedule->scheduled_time);
            $estimatedEndTime = $scheduledDateTime->copy()->addMinutes($maintenanceSchedule->estimated_duration);

            LocationCheck::updateOrCreate(
                [
                    'maintenance_schedule_id' => $maintenanceSchedule->id,
                    'check_type' => 'departure',
                ],
                [
                    'employee_id' => $maintenanceSchedule->assigned_employee_id,
                    'building_id' => $building->id,
                    'company_id' => $maintenanceSchedule->company_id,
                    'scheduled_time' => $estimatedEndTime,
                    'building_latitude' => $building->latitude,
                    'building_longitude' => $building->longitude,
                    'tolerance_minutes' => 15,
                    'status' => 'pending',
                ]
            );
        }
    }

    /**
     * Check if employee arrived at building location on time
     */
    public function checkArrival(MaintenanceSchedule $maintenanceSchedule, EmployeeLocation $employeeLocation): ?LocationCheck
    {
        $arrivalCheck = $maintenanceSchedule->arrivalCheck;

        if (!$arrivalCheck || $arrivalCheck->status !== 'pending') {
            return null;
        }

        // Check if employee is near building (within 100 meters)
        $isNearBuilding = $employeeLocation->isNearBuilding(
            $arrivalCheck->building_latitude,
            $arrivalCheck->building_longitude,
            100
        );

        if (!$isNearBuilding) {
            return null;
        }

        // Update check with actual time and location
        $arrivalCheck->actual_time = $employeeLocation->recorded_at;
        $arrivalCheck->employee_latitude = $employeeLocation->latitude;
        $arrivalCheck->employee_longitude = $employeeLocation->longitude;
        $arrivalCheck->distance_from_building = EmployeeLocation::calculateDistance(
            $employeeLocation->latitude,
            $employeeLocation->longitude,
            $arrivalCheck->building_latitude,
            $arrivalCheck->building_longitude
        );

        // Check if on time
        $arrivalCheck->checkOnTime();
        $arrivalCheck->save();

        return $arrivalCheck;
    }

    /**
     * Check if employee left building location on time
     */
    public function checkDeparture(MaintenanceSchedule $maintenanceSchedule, EmployeeLocation $employeeLocation): ?LocationCheck
    {
        $departureCheck = $maintenanceSchedule->departureCheck;

        if (!$departureCheck || $departureCheck->status !== 'pending') {
            return null;
        }

        // Check if employee is away from building (more than 100 meters)
        $isAwayFromBuilding = !$employeeLocation->isNearBuilding(
            $departureCheck->building_latitude,
            $departureCheck->building_longitude,
            100
        );

        if (!$isAwayFromBuilding) {
            return null;
        }

        // Update check with actual time and location
        $departureCheck->actual_time = $employeeLocation->recorded_at;
        $departureCheck->employee_latitude = $employeeLocation->latitude;
        $departureCheck->employee_longitude = $employeeLocation->longitude;
        $departureCheck->distance_from_building = EmployeeLocation::calculateDistance(
            $employeeLocation->latitude,
            $employeeLocation->longitude,
            $departureCheck->building_latitude,
            $departureCheck->building_longitude
        );

        // Check if on time
        $departureCheck->checkOnTime();
        $departureCheck->save();

        return $departureCheck;
    }

    /**
     * Process employee location update and check if it matches any pending location checks
     */
    public function processLocationUpdate(EmployeeLocation $employeeLocation): array
    {
        $results = [];

        if (!$employeeLocation->maintenance_schedule_id) {
            return $results;
        }

        $maintenanceSchedule = $employeeLocation->maintenanceSchedule;

        if (!$maintenanceSchedule) {
            return $results;
        }

        // Check arrival
        $arrivalCheck = $this->checkArrival($maintenanceSchedule, $employeeLocation);
        if ($arrivalCheck) {
            $results['arrival'] = $arrivalCheck;
        }

        // Check departure
        $departureCheck = $this->checkDeparture($maintenanceSchedule, $employeeLocation);
        if ($departureCheck) {
            $results['departure'] = $departureCheck;
        }

        return $results;
    }

    /**
     * Get location check summary for a maintenance schedule
     */
    public function getLocationCheckSummary(MaintenanceSchedule $maintenanceSchedule): array
    {
        $arrivalCheck = $maintenanceSchedule->arrivalCheck;
        $departureCheck = $maintenanceSchedule->departureCheck;

        return [
            'arrival' => [
                'exists' => $arrivalCheck !== null,
                'status' => $arrivalCheck?->status ?? 'not_created',
                'scheduled_time' => $arrivalCheck?->scheduled_time,
                'actual_time' => $arrivalCheck?->actual_time,
                'is_on_time' => $arrivalCheck?->is_on_time ?? null,
                'time_difference_minutes' => $arrivalCheck?->time_difference_minutes,
            ],
            'departure' => [
                'exists' => $departureCheck !== null,
                'status' => $departureCheck?->status ?? 'not_created',
                'scheduled_time' => $departureCheck?->scheduled_time,
                'actual_time' => $departureCheck?->actual_time,
                'is_on_time' => $departureCheck?->is_on_time ?? null,
                'time_difference_minutes' => $departureCheck?->time_difference_minutes,
            ],
        ];
    }
}
