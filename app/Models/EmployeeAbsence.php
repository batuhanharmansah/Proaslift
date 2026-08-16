<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAbsence extends Model
{
    protected $fillable = ['company_id', 'employee_id', 'start_date', 'end_date', 'type', 'note'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public const TYPES = [
        'izinli' => 'İzinli',
        'izinsiz' => 'İzinsiz',
        'raporlu' => 'Raporlu',
        'yillik_izin' => 'Yıllık İzin',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
