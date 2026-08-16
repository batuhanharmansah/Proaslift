<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomChecklistItem extends Model
{
    protected $fillable = [
        'company_id',
        'section_id',
        'item_key',
        'title',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const SECTIONS = [
        'machine_room' => 'Makine Dairesi Kontrolü',
        'floors' => 'Katlar',
        'cabin_interior_top' => 'Kabin İç ve Kabin Üstü Kontrolü',
        'shaft_interior' => 'Kuyu İçi',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
