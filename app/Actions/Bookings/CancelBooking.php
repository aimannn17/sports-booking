<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Cancels a booking and frees its slot (FR-S5, FR-E4).
 */
class CancelBooking
{
    public function handle(Booking $booking, ?string $reason = null, bool $allowStarted = false): Booking
    {
        if (! $booking->isActive()) {
            throw ValidationException::withMessages(['booking' => 'Only pending or confirmed bookings can be cancelled.']);
        }

        if (! $allowStarted && $booking->timeSlot->hasStarted()) {
            throw ValidationException::withMessages(['booking' => 'This booking has already started and can no longer be cancelled.']);
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->status = BookingStatus::Cancelled;
            $booking->cancelled_at = now();
            $booking->cancel_reason = filled($reason) ? $reason : null;
            $booking->save();

            $slot = TimeSlot::query()->lockForUpdate()->find($booking->time_slot_id);

            if ($slot && $slot->status === SlotStatus::Booked) {
                $slot->update(['status' => SlotStatus::Available]);
            }

            return $booking;
        });
    }
}
