<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingFinancialRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'building_id',
        'contract_amount',
        'monthly_amount',
        'total_received',
        'total_remaining',
        'contract_start_date',
        'contract_end_date',
        'payment_frequency',
        'status',
        'notes'
    ];

    protected $casts = [
        'contract_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'total_received' => 'decimal:2',
        'total_remaining' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    public function getPaymentFrequencyLabelAttribute()
    {
        $frequencies = [
            'aylik' => 'Aylık',
            'uc_aylik' => '3 Aylık',
            'alti_aylik' => '6 Aylık',
            'yillik' => 'Yıllık'
        ];
        return $frequencies[$this->payment_frequency] ?? $this->payment_frequency;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'pasif' => 'Pasif',
            'iptal' => 'İptal'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getFormattedContractAmountAttribute()
    {
        return '₺' . number_format($this->contract_amount, 2);
    }

    public function getFormattedMonthlyAmountAttribute()
    {
        return '₺' . number_format($this->monthly_amount, 2);
    }

    public function getFormattedTotalReceivedAttribute()
    {
        return '₺' . number_format($this->total_received, 2);
    }

    public function getFormattedTotalRemainingAttribute()
    {
        return '₺' . number_format($this->total_remaining, 2);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->contract_amount > 0) {
            return round(($this->total_received / $this->contract_amount) * 100, 2);
        }
        return 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            // Kalan tutarı hesapla
            $record->total_remaining = $record->contract_amount - $record->total_received;
        });

        static::updating(function ($record) {
            // Kalan tutarı hesapla
            $record->total_remaining = $record->contract_amount - $record->total_received;
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    // Ödeme alma metodu
    public function receivePayment($amount)
    {
        $this->total_received += $amount;
        $this->total_remaining = $this->contract_amount - $this->total_received;
        $this->save();
    }
}
