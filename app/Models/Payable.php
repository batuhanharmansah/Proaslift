<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'title', 'description', 'total_amount', 'paid_amount',
        'remaining_amount', 'due_date', 'status', 'category', 'priority',
        'invoice_number', 'supplier_name', 'notes', 'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    // İlişkiler
    public function company()
    {
        return $this->belongsTo(Company::class);
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

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'elektrik' => 'Elektrik',
            'su' => 'Su',
            'dogalgaz' => 'Doğalgaz',
            'internet' => 'İnternet',
            'telefon' => 'Telefon',
            'maas' => 'Maaş',
            'vergi' => 'Vergi',
            'sigorta' => 'Sigorta',
            'kira' => 'Kira',
            'diger' => 'Diğer'
        ];
        return $labels[$this->category] ?? $this->category;
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

    public function getFormattedPaidAmountAttribute()
    {
        return '₺' . number_format($this->paid_amount, 2);
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return '₺' . number_format($this->remaining_amount, 2);
    }

    // Metodlar
    public function makePayment($amount)
    {
        $this->paid_amount += $amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;

        // Durumu güncelle
        if ($this->remaining_amount <= 0) {
            $this->status = 'tamamlandi';
        } elseif ($this->paid_amount > 0) {
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

        static::creating(function ($payable) {
            $payable->remaining_amount = $payable->total_amount;
        });
    }
}
