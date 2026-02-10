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

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            self::Dispatched => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            self::OnRoute => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            self::OnLeave => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
        };
    }

    public function buttonActiveClass(): string
    {
        return match ($this) {
            self::Available => 'bg-green-600 text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600',
            self::Dispatched => 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600',
            self::OnRoute => 'bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600',
            self::OnLeave => 'bg-orange-600 text-white hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-600',
        };
    }
}
