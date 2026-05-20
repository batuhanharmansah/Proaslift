<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildingApprovalToken extends Model
{
    protected $fillable = [
        'company_id',
        'building_id',
        'token',
        'expires_at',
        'last_sms_sent_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_sms_sent_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
