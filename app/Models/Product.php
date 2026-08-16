<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'unit',
        'unit_price',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'min_stock_level',
        'supplier',
        'location',
        'is_active',
        'notes',
        'company_id'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getCategoryLabelAttribute()
    {
        $categories = [
            'yedek_parca' => 'Yedek Parça',
            'arac_gerec' => 'Araç Gereç',
            'kimyasal' => 'Kimyasal',
            'elektronik' => 'Elektronik',
            'mekanik' => 'Mekanik'
        ];
        return $categories[$this->category] ?? $this->category;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return (($this->sale_price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->min_stock_level) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusLabelAttribute()
    {
        $statuses = [
            'out_of_stock' => 'Stokta Yok',
            'low_stock' => 'Az Stok',
            'in_stock' => 'Stokta Var'
        ];
        return $statuses[$this->stock_status] ?? 'Bilinmiyor';
    }
}
