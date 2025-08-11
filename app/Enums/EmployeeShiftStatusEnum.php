<?php

namespace App\Enums;

enum EmployeeShiftStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public static function values(): array
    {
        return array_map(
            fn($item) => $item->value,
            self::cases()
        );
    }
}
