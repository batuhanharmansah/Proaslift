<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * "Check" ismi PHP'de/Laravel'de reserved-benzeri kullanımlarla karışabileceği için
 * (Gate::check, validation vb. ile karışmasın diye) model adı CompanyCheck, tablo adı 'checks'.
 */
class CompanyCheck extends Model
{
    protected $table = 'checks';

    protected $fillable = [
        'company_id', 'type', 'direction', 'counterparty_name', 'serial_number',
        'bank_name', 'amount', 'due_date', 'status', 'building_id', 'notes', 'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const STATUSES = [
        'bekliyor' => 'Bekliyor',
        'tahsil_edildi' => 'Tahsil Edildi',
        'odendi' => 'Ödendi',
        'karsiliksiz' => 'Karşılıksız',
        'iade' => 'İade',
        'ciro_edildi' => 'Ciro Edildi',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
