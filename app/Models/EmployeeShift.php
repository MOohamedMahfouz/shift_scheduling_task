<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeShift extends Pivot
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
