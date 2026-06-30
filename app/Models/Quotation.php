<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'quote_no',
        'type',
        'status',
        'building_id',
        'customer_name',
        'customer_contact_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'issue_report_id',
        'maintenance_schedule_id',
        'elevator_label_id',
        'template_id',
        'valid_until',
        'currency',
        'subtotal',
        'discount_total',
        'vat_total',
        'grand_total',
        'scope_summary',
        'scope_inclusions',
        'scope_exclusions',
        'terms',
        'notes',
        'internal_notes',
        'public_token',
        'created_by',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'rejected_at',
        'converted_at',
        'converted_receivable_id',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'vat_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            if (empty($quotation->public_token)) {
                $quotation->public_token = Str::random(48);
            }

            if (empty($quotation->quote_no)) {
                $quotation->quote_no = self::generateQuoteNo($quotation->company_id);
            }
        });
    }

    public static function generateQuoteNo($companyId)
    {
        $prefix = 'TKL-' . now('Europe/Istanbul')->format('Ymd') . '-';
        $count = self::where('company_id', $companyId)
            ->whereDate('created_at', now('Europe/Istanbul')->toDateString())
            ->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function issueReport()
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function elevatorLabel()
    {
        return $this->belongsTo(ElevatorLabel::class);
    }

    public function template()
    {
        return $this->belongsTo(QuotationTemplate::class, 'template_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function units()
    {
        return $this->hasMany(QuotationUnit::class);
    }

    public function acceptances()
    {
        return $this->hasMany(QuotationAcceptance::class);
    }

    public function convertedReceivable()
    {
        return $this->belongsTo(Receivable::class, 'converted_receivable_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute()
    {
        return [
            'maintenance' => 'Bakım Teklifi',
            'modernization' => 'Revizyon / Modernizasyon',
            'installation' => 'Yeni Montaj',
            'repair' => 'Arıza / Parça',
        ][$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Taslak',
            'sent' => 'Gönderildi',
            'viewed' => 'Görüntülendi',
            'accepted' => 'Kabul Edildi',
            'rejected' => 'Reddedildi',
            'expired' => 'Süresi Doldu',
            'converted' => 'Alacağa Dönüştü',
            'cancelled' => 'İptal Edildi',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'gray',
            'sent' => 'blue',
            'viewed' => 'indigo',
            'accepted' => 'green',
            'rejected' => 'red',
            'expired' => 'orange',
            'converted' => 'emerald',
            'cancelled' => 'gray',
        ][$this->status] ?? 'gray';
    }

    public function recalculateTotals()
    {
        $items = $this->items()->get();
        $subtotal = $items->sum(fn ($item) => (float) $item->line_subtotal);
        $discountTotal = $items->sum(fn ($item) => (float) $item->discount_amount);
        $vatTotal = $items->sum(fn ($item) => (float) $item->vat_amount);

        $this->update([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'vat_total' => $vatTotal,
            'grand_total' => $subtotal - $discountTotal + $vatTotal,
        ]);
    }

    public function publicUrl()
    {
        return route('quotations.public.show', $this->public_token);
    }
}
