<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'building_id',
        'reported_by',
        'issue_type',
        'priority',
        'description',
        'location_details',
        'contact_name',
        'contact_phone',
        'status',
        'assigned_employee_id',
        'assigned_at',
        'estimated_completion_time',
        'actual_completion_time',
        'customer_notes',
        'photos',
        'is_urgent',
        'requires_immediate_attention'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'estimated_completion_time' => 'datetime',
        'actual_completion_time' => 'datetime',
        'photos' => 'array',
        'is_urgent' => 'boolean',
        'requires_immediate_attention' => 'boolean',
    ];

    // İlişkiler
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

    public function maintenanceSchedule()
    {
        return $this->hasOne(MaintenanceSchedule::class, 'issue_report_id');
    }

    public function maintenanceReport()
    {
        return $this->hasOneThrough(MaintenanceReport::class, MaintenanceSchedule::class);
    }

    // Accessor'lar
    public function getIssueTypeLabelAttribute()
    {
        return match($this->issue_type) {
            'elektrik_arizasi' => 'Elektrik Arızası',
            'mekanik_ariza' => 'Mekanik Arıza',
            'kapı_arizasi' => 'Kapı Arızası',
            'ses_sistemi' => 'Ses Sistemi',
            'acil_durum' => 'Acil Durum',
            'diger' => 'Diğer',
            default => 'Bilinmiyor'
        };
    }

    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'dusuk' => 'Düşük',
            'orta' => 'Orta',
            'yuksek' => 'Yüksek',
            'acil' => 'Acil',
            default => 'Bilinmiyor'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'bildirildi' => 'Bildirildi',
            'inceleniyor' => 'İnceleniyor',
            'ekip_atandi' => 'Ekip Atandı',
            'calisma_basladi' => 'Çalışma Başladı',
            'tamamlandi' => 'Tamamlandı',
            'iptal_edildi' => 'İptal Edildi',
            default => 'Bilinmiyor'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'dusuk' => 'bg-green-100 text-green-800',
            'orta' => 'bg-yellow-100 text-yellow-800',
            'yuksek' => 'bg-orange-100 text-orange-800',
            'acil' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'bildirildi' => 'bg-blue-100 text-blue-800',
            'inceleniyor' => 'bg-yellow-100 text-yellow-800',
            'ekip_atandi' => 'bg-purple-100 text-purple-800',
            'calisma_basladi' => 'bg-orange-100 text-orange-800',
            'tamamlandi' => 'bg-green-100 text-green-800',
            'iptal_edildi' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Scope'lar
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('assigned_employee_id', $employeeId);
    }

    // Metodlar
    public function assignEmployee($employeeId)
    {
        $this->update([
            'assigned_employee_id' => $employeeId,
            'assigned_at' => now(),
            'status' => 'ekip_atandi'
        ]);
    }

    public function startWork()
    {
        $this->update([
            'status' => 'calisma_basladi'
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'tamamlandi',
            'actual_completion_time' => now()
        ]);
    }

    public function isCompleted()
    {
        return $this->status === 'tamamlandi';
    }

    public function isAssigned()
    {
        return !is_null($this->assigned_employee_id);
    }

    public function isInProgress()
    {
        return in_array($this->status, ['ekip_atandi', 'calisma_basladi']);
    }
}
