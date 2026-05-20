<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'name',
        'title',
        'phone',
        'email',
        'apartment_no',
        'is_primary',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getTitleLabelAttribute()
    {
        $titles = [
            'yonetici' => 'Yönetici',
            'sahip' => 'Bina Sahibi',
            'komsu' => 'Komşu',
            'diger' => 'Diğer'
        ];

        return $titles[$this->title] ?? $this->title;
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
