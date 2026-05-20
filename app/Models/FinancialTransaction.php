<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'transaction_type',
        'category',
        'description',
        'amount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'transaction_date',
        'transaction_time',
        'from_account_id',
        'to_account_id',
        'building_id',
        'employee_id',
        'payment_method',
        'invoice_number',
        'receipt_number',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'transaction_date' => 'date',
        'transaction_time' => 'datetime',
    ];

    public function getTransactionTypeLabelAttribute()
    {
        $types = [
            'gelir' => 'Gelir',
            'gider' => 'Gider',
            'transfer' => 'Transfer',
            'bakiye_aktarimi' => 'Bakiye Aktarımı'
        ];
        return $types[$this->transaction_type] ?? $this->transaction_type;
    }

    public function getCategoryLabelAttribute()
    {
        $categories = [
            'bakim_geliri' => 'Bakım Geliri',
            'yedek_parca_gideri' => 'Yedek Parça Gideri',
            'personel_maasi' => 'Personel Maaşı',
            'vergi' => 'Vergi',
            'sigorta' => 'Sigorta',
            'kasa_giris' => 'Kasa Girişi',
            'kasa_cikis' => 'Kasa Çıkışı',
            'banka_transfer' => 'Banka Transferi',
            'pos_geliri' => 'POS Geliri',
            'nakit_geliri' => 'Nakit Geliri',
            'dukkan_gideri' => 'Dükkan Gideri',
            'genel_gider' => 'Genel Gider',
            'bina_geliri' => 'Bina Geliri',
            'diger' => 'Diğer'
        ];
        return $categories[$this->category] ?? $this->category;
    }

    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'nakit' => 'Nakit',
            'banka_havalesi' => 'Banka Havalesi',
            'kredi_karti' => 'Kredi Kartı',
            'cek' => 'Çek',
            'pos' => 'POS',
            'transfer' => 'Transfer'
        ];
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'beklemede' => 'Beklemede',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getFormattedAmountAttribute()
    {
        return '₺' . number_format($this->amount, 2);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return '₺' . number_format($this->total_amount, 2);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // KDV hesaplama
            if ($transaction->vat_rate > 0) {
                $transaction->vat_amount = ($transaction->amount * $transaction->vat_rate) / 100;
            } else {
                $transaction->vat_amount = 0;
            }
            $transaction->total_amount = $transaction->amount + $transaction->vat_amount;

            // Varsayılan değerler
            if (!$transaction->transaction_time) {
                $transaction->transaction_time = now();
            }
            if (!$transaction->created_by) {
                $transaction->created_by = auth()->id();
            }
        });

        static::updating(function ($transaction) {
            // KDV hesaplama
            if ($transaction->vat_rate > 0) {
                $transaction->vat_amount = ($transaction->amount * $transaction->vat_rate) / 100;
            } else {
                $transaction->vat_amount = 0;
            }
            $transaction->total_amount = $transaction->amount + $transaction->vat_amount;
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(AccountType::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(AccountType::class, 'to_account_id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
