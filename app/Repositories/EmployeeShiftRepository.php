<?php

namespace App\Repositories;

use App\Enums\ShiftParticipantStatusEnum;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\User;

class EmployeeShiftRepository extends BaseRepository
{
    protected string $modelClass = EmployeeShift::class;

    protected function defaultFilters(): array
    {
        return array_merge(parent::defaultFilters(), []);
    }

    public function overlapExists(Shift $shift, $employee_id)
    {
        return $this->modelClass::query()
            ->with('shift', 'employee')
            ->whereBelongsTo($shift)
            ->where('employee_id', '=', $employee_id)
            ->whereIn('status', [
                ShiftParticipantStatusEnum::PENDING->value,
                ShiftParticipantStatusEnum::APPROVED->value
            ])
            ->where(function ($query) use ($shift) {
                $query->whereHas('shift', function ($query) use ($shift) {
                    $query->whereBetween('start_time', [$shift->start_time, $shift->end_time])
                        ->orWhereBetween('end_time', [$shift->start_time, $shift->end_time]);
                });
            })
            ->exists();
    }
}
