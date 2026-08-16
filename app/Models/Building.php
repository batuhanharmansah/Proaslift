<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'district',
        'city',
        'latitude',
        'longitude',
        'floor_count',
        'elevator_count',
        'elevator_type',
        'elevator_brand',
        'elevator_model',
        'installation_year',
        'contract_type',
        'monthly_fee',
        'fee_per_elevator',
        'contract_start_date',
        'contract_end_date',
        'status',
        'notes',
        'company_id',
        // Yeni asansör alanları
        'elevator_code',
        'capacity_kg',
        'capacity_person',
        'manufacturer',
        'model',
        'serial_number',
        'responsible_person',
        'responsible_phone',
        'responsible_email',
        'operational_status',
        'elevator_notes',
        'default_employee_id',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'fee_per_elevator' => 'decimal:2',
        'floor_count' => 'integer',
        'elevator_count' => 'integer',
        'installation_year' => 'integer',
    ];

    public function getElevatorTypeLabelAttribute()
    {
        $types = [
            'yolcu' => 'Yolcu Asansörü',
            'yuk' => 'Yük Asansörü',
            'hasta' => 'Hasta Asansörü',
            'karma' => 'Karma Asansör'
        ];

        return $types[$this->elevator_type] ?? $this->elevator_type;
    }

    public function getContractTypeLabelAttribute()
    {
        $types = [
            'bakim' => 'Bakım Sözleşmesi',
            'onarim' => 'Onarım Sözleşmesi',
            'modernizasyon' => 'Modernizasyon Sözleşmesi'
        ];

        return $types[$this->contract_type] ?? $this->contract_type;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'pasif' => 'Pasif',
            'beklemede' => 'Beklemede'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getContractRemainingDaysAttribute()
    {
        return $this->contract_end_date ? now()->diffInDays($this->contract_end_date, false) : null;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function defaultEmployee()
    {
        return $this->belongsTo(Employee::class, 'default_employee_id');
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function contacts()
    {
        return $this->hasMany(BuildingContact::class);
    }

    public function primaryContact()
    {
        return $this->hasOne(BuildingContact::class)->where('is_primary', true);
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class);
    }

    public function documents()
    {
        return $this->hasMany(BuildingDocument::class);
    }

    // Yeni ilişkiler
    public function elevatorLabels()
    {
        return $this->hasMany(ElevatorLabel::class);
    }

    public function activeLabel()
    {
        return $this->hasOne(ElevatorLabel::class)->where('status', 'aktif');
    }

    public function elevatorNotifications()
    {
        return $this->hasMany(ElevatorNotification::class);
    }

    public function elevatorEscalations()
    {
        return $this->hasMany(ElevatorEscalation::class);
    }

    public function locationChecks()
    {
        return $this->hasMany(LocationCheck::class);
    }

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class, 'building_id', 'id');
    }

    // Yeni accessor metodları
    public function getOperationalStatusLabelAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'bakim' => 'Bakımda',
            'arizali' => 'Arızalı',
            'muhurlendi' => 'Mühürlendi',
            'devre_disi' => 'Devre Dışı'
        ];

        return $statuses[$this->operational_status] ?? $this->operational_status;
    }

    public function getCurrentLabelAttribute()
    {
        return $this->activeLabel;
    }

    public function getCurrentLabelColorAttribute()
    {
        return $this->activeLabel?->label_color;
    }

    public function getCurrentLabelStatusAttribute()
    {
        return $this->activeLabel?->label_color_text;
    }

    public function getRemainingDaysAttribute()
    {
        return $this->activeLabel?->getRemainingDays();
    }

    public function getIsOverdueAttribute()
    {
        return $this->activeLabel?->isOverdue() ?? false;
    }

    public function getIsDueSoonAttribute()
    {
        return $this->activeLabel?->isDueSoon() ?? false;
    }

    public function getCanBeSealedAttribute()
    {
        return $this->activeLabel?->canBeSealed() ?? false;
    }

    public function getRiskLevelAttribute()
    {
        return $this->activeLabel?->getRiskLevel() ?? 'unknown';
    }

    // Scope metodları
    public function scopeWithActiveLabel($query)
    {
        return $query->with('activeLabel');
    }

    public function scopeByLabelColor($query, $color)
    {
        return $query->whereHas('activeLabel', function ($q) use ($color) {
            $q->where('label_color', $color);
        });
    }

    public function scopeDueSoon($query, $days = 30)
    {
        return $query->whereHas('activeLabel', function ($q) use ($days) {
            $q->dueSoon($days);
        });
    }

    public function scopeOverdue($query)
    {
        return $query->whereHas('activeLabel', function ($q) {
            $q->overdue();
        });
    }

    public function scopeSealingCandidates($query)
    {
        return $query->whereHas('activeLabel', function ($q) {
            $q->sealingCandidates();
        });
    }

    // Yardımcı metodlar
    public function createLabel($labelColor, $controlDate, $description = null, $userId = null)
    {
        return ElevatorLabel::create([
            'building_id' => $this->id,
            'label_color' => $labelColor,
            'control_date' => $controlDate,
            'description' => $description,
            'created_by' => $userId,
        ]);
    }

    public function getLabelHistory()
    {
        return $this->elevatorLabels()
                   ->orderBy('control_date', 'desc')
                   ->get();
    }

    public function getNotificationHistory()
    {
        return $this->elevatorNotifications()
                   ->with('elevatorLabel')
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    public function getEscalationHistory()
    {
        return $this->elevatorEscalations()
                   ->with('elevatorLabel')
                   ->orderBy('triggered_at', 'desc')
                   ->get();
    }

    public function hasActiveCriticalLabel()
    {
        return $this->activeLabel &&
               in_array($this->activeLabel->label_number, ['sari', 'kirmizi']);
    }

    public function needsImmediateAttention()
    {
        return $this->activeLabel &&
               $this->activeLabel->label_number === 'kirmizi' &&
               $this->activeLabel->isOverdue();
    }

    /**
     * Cache invalidation metodları
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($building) {
            Cache::forget('buildings_list_' . $building->company_id . '_*');
            Cache::forget('elevator_labels_stats_' . $building->company_id);
            Cache::forget('dashboard_data_' . $building->company_id);
        });

        static::deleted(function ($building) {
            Cache::forget('buildings_list_' . $building->company_id . '_*');
            Cache::forget('elevator_labels_stats_' . $building->company_id);
            Cache::forget('dashboard_data_' . $building->company_id);
        });
    }
}
