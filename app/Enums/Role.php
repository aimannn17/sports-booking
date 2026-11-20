<?php

namespace App\Enums;

enum Role: string
{
    case Student = 'student';
    case External = 'external';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::External => 'External user',
            self::Admin => 'Admin',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Student => 'blue',
            self::External => 'purple',
            self::Admin => 'amber',
        };
    }
}
