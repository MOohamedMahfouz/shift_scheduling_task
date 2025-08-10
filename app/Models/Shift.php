<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $guarded = [];

    protected function casts()
    {
        return [
            'max_resources' => 'integer',
            'max_employees' => 'integer',

            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /* Relations */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
