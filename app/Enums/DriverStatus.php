<?php

namespace App\Enums;

enum DriverStatus: string
{
    case Available = 'available';
    case Dispatched = 'dispatched';
    case OnRoute = 'on_route';
    case OnLeave = 'on_leave';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Dispatched => 'Dispatched',
            self::OnRoute => 'On Route',
            self::OnLeave => 'On Leave',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Dispatched => 'blue',
            self::OnRoute => 'indigo',
            self::OnLeave => 'orange',
        };
    }
}
