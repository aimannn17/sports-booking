<?php

namespace App\Enums;

enum SlotStatus: string
{
    case Available = 'available';
    case Booked = 'booked';
    case Blocked = 'blocked';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Booked => 'zinc',
            self::Blocked => 'red',
        };
    }
}
