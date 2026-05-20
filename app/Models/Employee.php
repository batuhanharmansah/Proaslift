<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'position',
        'salary',
        'hire_date',
        'is_active',
        'notes',
        'company_id',
        'user_id'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPositionLabelAttribute()
    {
        $positions = [
            'teknisyen' => 'Teknisyen',
            'usta' => 'Usta',
            'muhendis' => 'Mühendis',
            'yonetici' => 'Yönetici',
            'muhasebe' => 'Muhasebe'
        ];

        return $positions[$this->position] ?? $this->position;
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class, 'assigned_employee_id');
    }

    public function maintenanceReports()
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function locations()
    {
        return $this->hasMany(EmployeeLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(EmployeeLocation::class)->latestOfMany('recorded_at');
    }

    public function locationChecks()
    {
        return $this->hasMany(LocationCheck::class);
    }
}
