<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ShiftData extends Data
{
    public function __construct(
        public string|Optional $start_time,
        public string|Optional $end_time,

        public int|Optional $max_resources,
        public int|Optional $max_employees,

        public int|Optional $department_id,
    ) {}
}
