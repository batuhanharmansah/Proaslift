<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'account_number',
        'bank_name',
        'branch_name',
        'initial_balance',
        'current_balance',
        'type',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getTypeLabelAttribute()
    {
        $types = [
            'kasa' => 'Kasa',
            'banka' => 'Banka Hesabı',
            'nakit' => 'Nakit',
            'pos' => 'POS Cihazı'
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getFormattedCurrentBalanceAttribute()
    {
        return '₺' . number_format($this->current_balance, 2);
    }

    public function getFormattedInitialBalanceAttribute()
    {
        return '₺' . number_format($this->initial_balance, 2);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class, 'account_type_id');
    }

    // Bakiye güncelleme metodu
    public function updateBalance($amount, $operation = 'add')
    {
        // Negatif tutar kontrolü
        if ($amount < 0) {
            throw new \InvalidArgumentException('Tutar negatif olamaz.');
        }

        if ($operation === 'add') {
            $this->current_balance += $amount;
        } elseif ($operation === 'subtract') {
            // Yetersiz bakiye kontrolü (isteğe bağlı)
            if ($this->current_balance < $amount) {
                \Log::warning("Yetersiz bakiye uyarısı", [
                    'account_id' => $this->id,
                    'current_balance' => $this->current_balance,
                    'requested_amount' => $amount
                ]);
            }
            $this->current_balance -= $amount;
        } else {
            throw new \InvalidArgumentException('Geçersiz işlem. Sadece "add" veya "subtract" kullanılabilir.');
        }

        $this->save();
    }
}
