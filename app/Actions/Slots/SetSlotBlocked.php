<?php

namespace App\Actions\Slots;

use App\Enums\SlotStatus;
use App\Models\TimeSlot;
use Illuminate\Validation\ValidationException;

/**
 * Blocks a slot (for example for repairs) or unblocks it (FR-A4).
 */
class SetSlotBlocked
{
    public function handle(TimeSlot $slot, bool $blocked): TimeSlot
    {
        if ($blocked && $slot->status !== SlotStatus::Available) {
            throw ValidationException::withMessages(['slot' => 'Only available slots can be blocked. Cancel the booking first.']);
        }

        if (! $blocked && $slot->status !== SlotStatus::Blocked) {
            throw ValidationException::withMessages(['slot' => 'This slot is not blocked.']);
        }

        $slot->update(['status' => $blocked ? SlotStatus::Blocked : SlotStatus::Available]);

        return $slot;
    }
}
