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

    public function badgeClass(): string
    {
        return match ($this) {
            self::OK => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            self::UR => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            self::PMS => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            self::InTransit => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            self::Lutaw => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        };
    }
}
