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
}
