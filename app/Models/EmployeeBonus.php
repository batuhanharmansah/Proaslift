<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBonus extends Model
{
    protected $fillable = ['company_id', 'employee_id', 'bonus_date', 'type', 'amount', 'description', 'created_by'];

    protected $casts = ['bonus_date' => 'date', 'amount' => 'decimal:2'];

    public const TYPES = ['prim' => 'Prim', 'mesai' => 'Mesai Ücreti', 'ikramiye' => 'İkramiye', 'diger' => 'Diğer'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
