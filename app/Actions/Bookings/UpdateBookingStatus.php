<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Lets an admin move a booking between pending, confirmed, cancelled and completed (FR-A5),
 * keeping the slot status in step.
 */
class UpdateBookingStatus
{
    public function __construct(protected CancelBooking $cancelBooking) {}

    public function handle(Booking $booking, BookingStatus $status, ?string $reason = null): Booking
    {
        if ($booking->status === $status) {
            return $booking;
        }

        if ($status === BookingStatus::Cancelled) {
            return $this->cancelBooking->handle($booking, $reason ?? 'Cancelled by admin', allowStarted: true);
        }

        return DB::transaction(function () use ($booking, $status) {
            // Re-activating a cancelled booking needs the slot back.
            if ($booking->status === BookingStatus::Cancelled) {
                $slot = TimeSlot::query()->lockForUpdate()->findOrFail($booking->time_slot_id);

                if ($slot->status !== SlotStatus::Available) {
                    throw ValidationException::withMessages(['status' => 'The slot is no longer available, so this booking cannot be restored.']);
                }

                $slot->update(['status' => SlotStatus::Booked]);
                $booking->cancelled_at = null;
                $booking->cancel_reason = null;
            }

            $booking->status = $status;
            $booking->save();

            return $booking;
        });
    }
}
