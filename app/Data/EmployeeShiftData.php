<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EmployeeShiftData extends Data
{
    public function __construct(
        public int|Optional $shift_id,
        public int|Optional $employee_id,

        public string|Optional $status,

        public string|Optional $reserved_at,
        public string|null|Optional $approved_at,
    ) {}
}
