<?php

namespace App\Enums;

enum BusType: string
{
    case Regular = 'regular';
    case Deluxe = 'deluxe';
    case SuperDeluxe = 'super_deluxe';
    case Elite = 'elite';
    case Sleeper = 'sleeper';
    case SingleSeater = 'single_seater';
    case SkyBus = 'skybus';

    public function label(): string
    {
        return match ($this) {
            self::Regular => 'Regular',
            self::Deluxe => 'Deluxe',
            self::SuperDeluxe => 'Super Deluxe',
            self::Elite => 'Elite',
            self::Sleeper => 'Sleeper',
            self::SingleSeater => 'Single Seater',
            self::SkyBus => 'SkyBus',
        };
    }
}
