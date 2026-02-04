<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case OK = 'OK';
    case UR = 'UR';
    case PMS = 'PMS';
    case InTransit = 'In Transit';
    case Lutaw = 'Lutaw';

    public function label(): string
    {
        return match ($this) {
            self::OK => 'OK (Available)',
            self::UR => 'Under Repair',
            self::PMS => 'For Maintenance',
            self::InTransit => 'In Transit',
            self::Lutaw => 'Lutaw (Idle)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OK => 'green',
            self::UR => 'red',
            self::PMS => 'orange',
            self::InTransit => 'blue',
            self::Lutaw => 'yellow',
        };
    }
}
