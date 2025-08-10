<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ShiftData extends Data
{
    public function __construct(
        protected string|Optional $name,
        protected string|Optional $start_time,
        protected string|Optional $end_time,

        protected int|Optional $max_resources,
        protected int|Optional $max_employees,

        protected int|Optional $department_id,
    ) {}
}
