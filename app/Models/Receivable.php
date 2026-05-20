<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'building_id', 'title', 'description', 'total_amount',
        'received_amount', 'remaining_amount', 'due_date', 'status',
        'payment_type', 'installment_count', 'paid_installments',
        'installment_amount', 'priority', 'notes', 'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor'lar
    public function getStatusLabelAttribute()
    {
        $labels = [
            'beklemede' => 'Beklemede',
            'kismi_odendi' => 'Kısmi Ödendi',
            'tamamlandi' => 'Tamamlandı',
            'gecikti' => 'Gecikti'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getPaymentTypeLabelAttribute()
    {
        $labels = [
            'tek_sefer' => 'Tek Sefer',
            'taksitli' => 'Taksitli'
        ];
        return $labels[$this->payment_type] ?? $this->payment_type;
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'dusuk' => 'Düşük',
            'orta' => 'Orta',
            'yuksek' => 'Yüksek'
        ];
        return $labels[$this->priority] ?? $this->priority;
    }

    public function getFormattedTotalAmountAttribute()
    {
        return '₺' . number_format($this->total_amount, 2);
    }

    public function getFormattedReceivedAmountAttribute()
    {
        return '₺' . number_format($this->received_amount, 2);
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return '₺' . number_format($this->remaining_amount, 2);
    }

    public function getFormattedInstallmentAmountAttribute()
    {
        return '₺' . number_format($this->installment_amount, 2);
    }

    // Metodlar
    public function receivePayment($amount)
    {
        $this->received_amount += $amount;
        $this->remaining_amount = $this->total_amount - $this->received_amount;

        if ($this->payment_type === 'taksitli' && $this->installment_amount > 0) {
            $this->paid_installments = floor($this->received_amount / $this->installment_amount);
        }

        // Durumu güncelle
        if ($this->remaining_amount <= 0) {
            $this->status = 'tamamlandi';
        } elseif ($this->received_amount > 0) {
            $this->status = 'kismi_odendi';
        }

        // Vade geçmiş mi kontrol et
        if ($this->due_date < now() && $this->status !== 'tamamlandi') {
            $this->status = 'gecikti';
        }

        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receivable) {
            if ($receivable->payment_type === 'taksitli' && $receivable->installment_count > 0) {
                $receivable->installment_amount = $receivable->total_amount / $receivable->installment_count;
            } else {
                $receivable->installment_amount = $receivable->total_amount;
            }
            $receivable->remaining_amount = $receivable->total_amount;
        });
    }
}
