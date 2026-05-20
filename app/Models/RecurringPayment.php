<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecurringPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'title', 'description', 'amount', 'type', 'category', 'frequency',
        'start_date', 'end_date', 'day_of_month', 'account_id', 'is_active',
        'building_id', 'notes', 'created_by', 'next_payment_date', 'last_payment_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_payment_date' => 'date',
        'last_payment_date' => 'date',
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
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

    public function account()
    {
        return $this->belongsTo(AccountType::class, 'account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor'lar
    public function getTypeLabelAttribute()
    {
        $labels = [
            'gelir' => 'Gelir',
            'gider' => 'Gider'
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function getFrequencyLabelAttribute()
    {
        $labels = [
            'gunluk' => 'Günlük',
            'haftalik' => 'Haftalık',
            'aylik' => 'Aylık',
            'uc_aylik' => '3 Aylık',
            'alti_aylik' => '6 Aylık',
            'yillik' => 'Yıllık'
        ];
        return $labels[$this->frequency] ?? $this->frequency;
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
            'bina_geliri' => 'Bina Geliri',
            'diger' => 'Diğer'
        ];
        return $labels[$this->category] ?? $this->category;
    }

    public function getFormattedAmountAttribute()
    {
        return '₺' . number_format($this->amount, 2);
    }

    // Metodlar
    public function shouldCreatePayment()
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now('Europe/Istanbul');

        // Bitiş tarihi geçmiş mi?
        if ($this->end_date && $this->end_date < $today) {
            return false;
        }

        // Başlangıç tarihi gelecek mi?
        if ($this->start_date > $today) {
            return false;
        }

        switch ($this->frequency) {
            case 'gunluk':
                return true;

            case 'haftalik':
                $startWeek = $this->start_date->startOfWeek();
                $currentWeek = $today->startOfWeek();
                $weeksDiff = $startWeek->diffInWeeks($currentWeek);
                return $weeksDiff >= 0;

            case 'aylik':
                // Ayın belirtilen gününde mi?
                if ($today->day == $this->day_of_month) {
                    $startMonth = $this->start_date->startOfMonth();
                    $currentMonth = $today->startOfMonth();
                    $monthsDiff = $startMonth->diffInMonths($currentMonth);
                    return $monthsDiff >= 0;
                }
                return false;

            case 'uc_aylik':
                if ($today->day == $this->day_of_month) {
                    $startQuarter = $this->start_date->startOfQuarter();
                    $currentQuarter = $today->startOfQuarter();
                    $quartersDiff = $startQuarter->diffInMonths($currentQuarter) / 3;
                    return $quartersDiff >= 0;
                }
                return false;

            case 'alti_aylik':
                if ($today->day == $this->day_of_month) {
                    // 6 aylık dönemleri doğru hesapla (Ocak-Haziran, Temmuz-Aralık)
                    $startPeriod = $this->start_date->copy();
                    $currentPeriod = $today->copy();

                    // Başlangıç dönemini belirle
                    if ($startPeriod->month <= 6) {
                        $startPeriod->month(1)->day(1);
                    } else {
                        $startPeriod->month(7)->day(1);
                    }

                    // Mevcut dönemi belirle
                    if ($currentPeriod->month <= 6) {
                        $currentPeriod->month(1)->day(1);
                    } else {
                        $currentPeriod->month(7)->day(1);
                    }

                    $halfYearsDiff = $startPeriod->diffInMonths($currentPeriod) / 6;
                    return $halfYearsDiff >= 0;
                }
                return false;

            case 'yillik':
                if ($today->day == $this->day_of_month && $today->month == $this->start_date->month) {
                    $yearsDiff = $this->start_date->diffInYears($today);
                    return $yearsDiff >= 0;
                }
                return false;

            default:
                return false;
        }

        return false;
    }

    /**
     * Model boot - next_payment_date hesaplama
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->calculateNextPaymentDate();
        });

        static::updating(function ($payment) {
            if ($payment->isDirty(['start_date', 'frequency', 'day_of_month'])) {
                $payment->calculateNextPaymentDate();
            }
        });
    }

    /**
     * Sonraki ödeme tarihini hesapla
     */
    public function calculateNextPaymentDate()
    {
        if (!$this->start_date) {
            return;
        }

        $startDate = $this->start_date;
        $today = now('Europe/Istanbul')->startOfDay();

        switch ($this->frequency) {
            case 'gunluk':
                $this->next_payment_date = $startDate > $today ? $startDate : $today->addDay();
                break;

            case 'haftalik':
                $nextDate = $startDate > $today ? $startDate : $today;
                $targetDayOfWeek = $startDate->dayOfWeek;
                $attempts = 0;
                while ($nextDate->dayOfWeek !== $targetDayOfWeek && $attempts < 7) {
                    $nextDate->addDay();
                    $attempts++;
                }
                $this->next_payment_date = $nextDate;
                break;

            case 'aylik':
                $nextDate = $startDate > $today ? $startDate : $today;
                $nextDate->day($this->day_of_month);
                if ($nextDate <= $today) {
                    $nextDate->addMonth();
                }
                $this->next_payment_date = $nextDate;
                break;

            case 'uc_aylik':
                $nextDate = $startDate > $today ? $startDate : $today;
                $nextDate->day($this->day_of_month);
                while ($nextDate <= $today) {
                    $nextDate->addMonths(3);
                }
                $this->next_payment_date = $nextDate;
                break;

            case 'alti_aylik':
                $nextDate = $startDate > $today ? $startDate : $today;
                $nextDate->day($this->day_of_month);
                while ($nextDate <= $today) {
                    $nextDate->addMonths(6);
                }
                $this->next_payment_date = $nextDate;
                break;

            case 'yillik':
                $nextDate = $startDate > $today ? $startDate : $today;
                $nextDate->day($this->day_of_month);
                while ($nextDate <= $today) {
                    $nextDate->addYear();
                }
                $this->next_payment_date = $nextDate;
                break;

            default:
                $this->next_payment_date = $startDate;
        }
    }

    /**
     * Sonraki ödeme tarihini güncelle
     */
    public function updateNextPaymentDate()
    {
        $this->last_payment_date = now()->toDateString();
        $this->calculateNextPaymentDate();
        $this->save();
    }

    public function createPayment()
    {
        return DB::transaction(function () {
            // Bu ay için ödeme zaten oluşturulmuş mu kontrol et (locking ile)
            $existingPayment = FinancialTransaction::where('company_id', $this->company_id)
                ->where('category', $this->category)
                ->where('description', 'LIKE', '%' . $this->title . '%')
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [now()->format('Y-m')])
                ->lockForUpdate()
                ->first();

            if ($existingPayment) {
                return false; // Zaten oluşturulmuş
            }

            // Yeni finansal işlem oluştur
            $transaction = new FinancialTransaction([
                'company_id' => $this->company_id,
                'transaction_type' => $this->type,
                'category' => $this->category,
                'description' => $this->title . ' (' . now()->format('F Y') . ')',
                'amount' => $this->amount,
                'total_amount' => $this->amount,
                'transaction_date' => now(),
                'from_account_id' => $this->type === 'gider' ? $this->account_id : null,
                'to_account_id' => $this->type === 'gelir' ? $this->account_id : null,
                'building_id' => $this->building_id,
                'created_by' => $this->created_by,
            ]);

            $transaction->save();

            // Hesap bakiyelerini güncelle
            if ($this->account_id) {
                $account = AccountType::lockForUpdate()->find($this->account_id);
                if ($account) {
                    if ($this->type === 'gider') {
                        $account->updateBalance($this->amount, 'subtract');
                    } else {
                        $account->updateBalance($this->amount, 'add');
                    }
                }
            }

            return $transaction;
        });
    }
}
