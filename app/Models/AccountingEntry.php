<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'account_type_id',
        'type',
        'category',
        'description',
        'amount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'transaction_date',
        'invoice_number',
        'building_id',
        'employee_id',
        'maintenance_report_id',
        'payment_method',
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
    ];

    public function getTypeLabelAttribute()
    {
        $types = [
            'gelir' => 'Gelir',
            'gider' => 'Gider',
            'maas' => 'Maaş',
            'vergi' => 'Vergi',
            'sigorta' => 'Sigorta'
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'nakit' => 'Nakit',
            'banka_havalesi' => 'Banka Havalesi',
            'kredi_karti' => 'Kredi Kartı',
            'cek' => 'Çek'
        ];
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'beklemede' => 'Beklemede',
            'odendi' => 'Ödendi',
            'tahsil_edildi' => 'Tahsil Edildi',
            'iptal' => 'İptal'
        ];
        return $statuses[$this->status] ?? $this->status;
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function maintenanceReport()
    {
        return $this->belongsTo(MaintenanceReport::class);
    }
}
