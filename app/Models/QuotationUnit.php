<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'building_id',
        'elevator_code',
        'location',
        'elevator_type',
        'brand',
        'model',
        'capacity_kg',
        'capacity_person',
        'speed',
        'stop_count',
        'door_type',
        'drive_type',
        'machine_room',
        'current_label_color',
        'last_control_date',
    ];

    protected $casts = [
        'last_control_date' => 'date',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
