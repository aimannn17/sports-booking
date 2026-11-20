<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    /**
     * Statuses that hold a slot.
     *
     * @return list<string>
     */
    public static function active(): array
    {
        return [self::Pending->value, self::Confirmed->value];
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Confirmed => 'green',
            self::Cancelled => 'red',
            self::Completed => 'zinc',
        };
    }
}
