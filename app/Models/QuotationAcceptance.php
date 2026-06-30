<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'action',
        'accepted_by_name',
        'accepted_by_phone',
        'accepted_by_email',
        'accepted_ip',
        'user_agent',
        'customer_note',
        'terms_snapshot',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
