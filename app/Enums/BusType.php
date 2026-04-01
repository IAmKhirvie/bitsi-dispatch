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
<<<<<<< HEAD
    case Executive = 'executive';
    case Royal = 'royal';
    case RoyalExe = 'royal_exe';
    case Premier = 'premier';
    case Economy = 'economy';
=======
>>>>>>> f3e9eb09b15a2f6335fdb45f7d301596e236cf82

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
<<<<<<< HEAD
            self::Executive => 'Executive',
            self::Royal => 'Royal',
            self::RoyalExe => 'Royal Executive',
            self::Premier => 'Premier',
            self::Economy => 'Economy',
=======
>>>>>>> f3e9eb09b15a2f6335fdb45f7d301596e236cf82
        };
    }
}
