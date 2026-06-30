<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'quotation_unit_id',
        'category',
        'item_code',
        'description',
        'brand',
        'model',
        'quantity',
        'unit',
        'unit_price',
        'discount_rate',
        'vat_rate',
        'line_subtotal',
        'discount_amount',
        'vat_amount',
        'line_total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function unit()
    {
        return $this->belongsTo(QuotationUnit::class, 'quotation_unit_id');
    }

    public function getCategoryLabelAttribute()
    {
        return [
            'service' => 'Hizmet',
            'material' => 'Malzeme',
            'labor' => 'İşçilik',
            'inspection' => 'Kontrol',
            'discount' => 'İndirim',
        ][$this->category] ?? $this->category;
    }

    public static function totalsFor($quantity, $unitPrice, $discountRate, $vatRate)
    {
        $quantity = (float) $quantity;
        $unitPrice = (float) $unitPrice;
        $discountRate = (float) $discountRate;
        $vatRate = (float) $vatRate;

        $lineSubtotal = round($quantity * $unitPrice, 2);
        $discountAmount = round($lineSubtotal * ($discountRate / 100), 2);
        $taxable = $lineSubtotal - $discountAmount;
        $vatAmount = round($taxable * ($vatRate / 100), 2);

        return [
            'line_subtotal' => $lineSubtotal,
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'line_total' => $taxable + $vatAmount,
        ];
    }
}
