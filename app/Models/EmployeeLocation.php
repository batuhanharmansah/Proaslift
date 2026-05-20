<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'company_id',
        'maintenance_schedule_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if location is near building (within radius in meters)
     */
    public function isNearBuilding($buildingLat, $buildingLon, $radiusMeters = 100)
    {
        $distance = self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $buildingLat,
            $buildingLon
        );

        return $distance <= $radiusMeters;
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    // Scopes
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForMaintenance($query, $maintenanceScheduleId)
    {
        return $query->where('maintenance_schedule_id', $maintenanceScheduleId);
    }

    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('recorded_at', '>=', Carbon::now()->subMinutes($minutes));
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }
}
