<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingPortalAccount extends Model
{
    protected $fillable = ['building_id', 'phone', 'password', 'is_active', 'last_login_at'];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
