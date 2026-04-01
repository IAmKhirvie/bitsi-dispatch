<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case OperationsManager = 'operations_manager';
    case Dispatcher = 'dispatcher';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::OperationsManager => 'Operations Manager',
            self::Dispatcher => 'Dispatcher',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            self::OperationsManager => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            self::Dispatcher => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        };
    }
}
