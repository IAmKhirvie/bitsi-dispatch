<?php

namespace App\Enums;

enum PmsUnit: string
{
    case Kilometers = 'kilometers';
    case Trips = 'trips';

    public function label(): string
    {
        return match ($this) {
            self::Kilometers => 'Kilometers',
            self::Trips => 'Trips',
        };
    }
}
