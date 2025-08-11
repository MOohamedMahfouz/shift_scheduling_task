<?php

namespace App\Repositories;

use App\Enums\EmployeeShiftStatusEnum;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShiftRepository extends BaseRepository
{
    protected string $modelClass = Shift::class;

    protected function defaultFilters(): array
    {
        return array_merge(parent::defaultFilters(), []);
    }
}
