<?php

namespace App\Enums;

enum RoleEnum: string
{
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';

    public static function values(): array
    {
        return array_map(
            fn($item) => $item->value,
            self::cases()
        );
    }
}
