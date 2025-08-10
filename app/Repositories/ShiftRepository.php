<?php

namespace App\Repositories;


use App\Models\Shift;

class ShiftRepository extends BaseRepository
{
    protected string $modelClass = Shift::class;

    protected function defaultFilters(): array
    {
        return array_merge(parent::defaultFilters(), []);
    }
}
