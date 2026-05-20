<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'building_id',
        'issue_report_id',
        'assigned_employee_id',
        'maintenance_type',
        'title',
        'scheduled_date',
        'scheduled_time',
        'priority',
        'status',
        'description',
        'notes',
        'estimated_duration',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function getMaintenanceTypeLabelAttribute()
    {
        $types = [
            'rutin_bakim' => 'Rutin Bakım',
            'ariza_onarim' => 'Arıza Onarım',
            'periyodik_kontrol' => 'Periyodik Kontrol',
            'modernizasyon' => 'Modernizasyon'
        ];
        return $types[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function getPriorityLabelAttribute()
    {
        $priorities = [
            'dusuk' => 'Düşük',
            'normal' => 'Normal',
            'yuksek' => 'Yüksek',
            'acil' => 'Acil'
        ];
        return $priorities[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'planli' => 'Planlı',
            'atandi' => 'Atandı',
            'baslandi' => 'Başlandı',
            'tamamlandi' => 'Tamamlandı',
            'ertelendi' => 'Ertelendi',
            'iptal' => 'İptal'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function issueReport()
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function maintenanceReports()
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function maintenanceReport()
    {
        return $this->hasOne(MaintenanceReport::class);
    }

    public function detailedMaintenanceReport()
    {
        return $this->hasOne(DetailedMaintenanceReport::class);
    }

    public function employeeLocations()
    {
        return $this->hasMany(EmployeeLocation::class);
    }

    public function locationChecks()
    {
        return $this->hasMany(LocationCheck::class);
    }

    public function arrivalCheck()
    {
        return $this->hasOne(LocationCheck::class)->where('check_type', 'arrival');
    }

    public function departureCheck()
    {
        return $this->hasOne(LocationCheck::class)->where('check_type', 'departure');
    }

    /**
     * Get estimated end time based on scheduled time and duration
     */
    public function getEstimatedEndTimeAttribute()
    {
        if (!$this->scheduled_date || !$this->scheduled_time || !$this->estimated_duration) {
            return null;
        }

        $scheduledDateTime = Carbon::parse($this->scheduled_date->format('Y-m-d') . ' ' . $this->scheduled_time);
        return $scheduledDateTime->addMinutes($this->estimated_duration);
    }
}
