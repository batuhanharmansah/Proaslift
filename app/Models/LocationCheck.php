<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LocationCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id',
        'employee_id',
        'building_id',
        'company_id',
        'check_type',
        'scheduled_time',
        'actual_time',
        'employee_latitude',
        'employee_longitude',
        'building_latitude',
        'building_longitude',
        'distance_from_building',
        'is_on_time',
        'time_difference_minutes',
        'status',
        'tolerance_minutes',
        'notes',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'actual_time' => 'datetime',
        'employee_latitude' => 'decimal:8',
        'employee_longitude' => 'decimal:8',
        'building_latitude' => 'decimal:8',
        'building_longitude' => 'decimal:8',
        'distance_from_building' => 'decimal:2',
        'is_on_time' => 'boolean',
        'time_difference_minutes' => 'integer',
        'tolerance_minutes' => 'integer',
    ];

    /**
     * Check if employee arrived/left on time (±tolerance minutes)
     */
    public function checkOnTime()
    {
        if (!$this->actual_time) {
            $this->status = 'pending';
            return false;
        }

        $scheduled = Carbon::parse($this->scheduled_time);
        $actual = Carbon::parse($this->actual_time);
        $tolerance = $this->tolerance_minutes ?? 15;

        $diffMinutes = abs($scheduled->diffInMinutes($actual));

        $this->time_difference_minutes = $actual->greaterThan($scheduled) 
            ? $diffMinutes 
            : -$diffMinutes;

        $this->is_on_time = $diffMinutes <= $tolerance;

        if ($this->is_on_time) {
            $this->status = 'on_time';
        } elseif ($diffMinutes > $tolerance) {
            $this->status = $actual->greaterThan($scheduled) ? 'late' : 'early';
        } else {
            $this->status = 'missed';
        }

        return $this->is_on_time;
    }

    /**
     * Check if employee is at building location
     */
    public function checkLocationProximity($radiusMeters = 100)
    {
        if (!$this->employee_latitude || !$this->employee_longitude) {
            return false;
        }

        $distance = EmployeeLocation::calculateDistance(
            $this->employee_latitude,
            $this->employee_longitude,
            $this->building_latitude,
            $this->building_longitude
        );

        $this->distance_from_building = $distance;

        return $distance <= $radiusMeters;
    }

    // Relationships
    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeArrival($query)
    {
        return $query->where('check_type', 'arrival');
    }

    public function scopeDeparture($query)
    {
        return $query->where('check_type', 'departure');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOnTime($query)
    {
        return $query->where('status', 'on_time');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeForMaintenance($query, $maintenanceScheduleId)
    {
        return $query->where('maintenance_schedule_id', $maintenanceScheduleId);
    }
}
