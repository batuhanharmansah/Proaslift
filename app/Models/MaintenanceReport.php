<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'building_id',
        'maintenance_schedule_id',
        'employee_id',
        'title',
        'start_time',
        'end_time',
        'work_description',
        'used_products',
        'total_cost',
        'problems_found',
        'recommendations',
        'completion_status',
        'customer_signature',
        'customer_name',
        'customer_notes',
        'photos',
        'routine_maintenance_checklist',
        'completion_percentage',
        'approval_status',
        'approved_by_name',
        'approved_at',
        'approval_ip',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'used_products' => 'array',
        'total_cost' => 'decimal:2',
        'customer_signature' => 'boolean',
        'photos' => 'array',
        'routine_maintenance_checklist' => 'array',
        'completion_percentage' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function getCompletionStatusLabelAttribute()
    {
        $statuses = [
            'tamamlandi' => 'Tamamlandı',
            'kismi_tamamlandi' => 'Kısmi Tamamlandı',
            'ertelendi' => 'Ertelendi'
        ];
        return $statuses[$this->completion_status] ?? $this->completion_status;
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        $statuses = [
            'onay_bekliyor' => 'Onay Bekleniyor',
            'onaylandi' => 'Onaylandı',
        ];

        return $statuses[$this->approval_status] ?? (string) $this->approval_status;
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class);
    }
}
