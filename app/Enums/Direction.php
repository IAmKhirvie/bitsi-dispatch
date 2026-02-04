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
}
