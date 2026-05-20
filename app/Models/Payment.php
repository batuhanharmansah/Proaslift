<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'amount',
        'payment_date',
        'period_start',
        'period_end',
        'status',
        'payment_method',
        'invoice_number',
        'notes',
        'recorded_at',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'recorded_at' => 'datetime',
    ];

    // İlişkiler
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Accessor'lar
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'paid' => 'Ödendi',
            'pending' => 'Bekliyor',
            'overdue' => 'Gecikmiş',
            'cancelled' => 'İptal Edildi',
            default => 'Bilinmiyor'
        };
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'nakit' => 'Nakit',
            'banka_havalesi' => 'Banka Havalesi',
            'kredi_karti' => 'Kredi Kartı',
            'cek' => 'Çek',
            default => 'Bilinmiyor'
        };
    }

    public function getFormattedAmountAttribute()
    {
        return '₺' . number_format($this->amount, 2);
    }

    public function getPeriodLabelAttribute()
    {
        return $this->period_start->format('d.m.Y') . ' - ' . $this->period_end->format('d.m.Y');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('payment_date', [$start, $end]);
    }

    // Helper metodlar
    public function markAsPaid($recordedBy = null)
    {
        $this->update([
            'status' => 'paid',
            'recorded_at' => now(),
            'recorded_by' => $recordedBy ?? auth()->id(),
        ]);
    }

    public function markAsOverdue()
    {
        $this->update(['status' => 'overdue']);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue';
    }
}
