<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceDocument extends Model
{
    protected $fillable = [
        'company_id', 'building_id', 'document_type', 'event_date',
        'inspector_or_technician_name', 'description', 'status', 'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public const TYPES = [
        'dtr' => 'Durum Tespit Raporu',
        'kurtarma' => 'Kurtarma Formu',
    ];

    public const STATUSES = [
        'taslak' => 'Taslak',
        'tamamlandi' => 'Tamamlandı',
        'imzalandi' => 'İmzalandı',
        'paylasildi' => 'Paylaşıldı',
        'onaylandi' => 'Onaylandı',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
