<?php

namespace App\Enums;

enum DispatchStatus: string
{
    case Scheduled = 'scheduled';
    case Departed = 'departed';
    case OnRoute = 'on_route';
    case Delayed = 'delayed';
    case Cancelled = 'cancelled';
    case Arrived = 'arrived';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Departed => 'Departed',
            self::OnRoute => 'On Route',
            self::Delayed => 'Delayed',
            self::Cancelled => 'Cancelled',
            self::Arrived => 'Arrived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'gray',
            self::Departed => 'blue',
            self::OnRoute => 'indigo',
            self::Delayed => 'orange',
            self::Cancelled => 'red',
            self::Arrived => 'green',
        };
    }
}
