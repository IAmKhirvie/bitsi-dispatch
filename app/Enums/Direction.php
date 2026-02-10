<?php

namespace App\Enums;

enum Direction: string
{
    case SB = 'SB';
    case NB = 'NB';

    public function label(): string
    {
        return match ($this) {
            self::SB => 'Southbound',
            self::NB => 'Northbound',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SB => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            self::NB => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
        };
    }
}
