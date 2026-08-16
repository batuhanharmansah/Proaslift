<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyVehicle extends Model
{
    protected $fillable = [
        'company_id', 'plate', 'brand_model', 'driver_employee_id',
        'inspection_due_date', 'insurance_due_date', 'notes',
    ];

    protected $casts = ['inspection_due_date' => 'date', 'insurance_due_date' => 'date'];

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_employee_id');
    }
}
