<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Books a slot for a user (FR-S3, FR-S4, FR-E3).
 *
 * The slot row is locked inside a transaction so two people pressing "Book"
 * at the same moment cannot both get it (NFR-4).
 */
class CreateBooking
{
    public const STUDENT_ACTIVE_LIMIT = 2;

    public function handle(User $user, TimeSlot $slot): Booking
    {
        return DB::transaction(function () use ($user, $slot) {
            /** @var TimeSlot $slot */
            $slot = TimeSlot::query()->with('facility')->lockForUpdate()->findOrFail($slot->id);

            $this->ensureCanBook($user, $slot);

            $isExternal = $user->isExternal();
            $fee = $isExternal ? (float) $slot->facility->external_fee : 0.0;
            $needsPayment = $fee > 0;

            $booking = new Booking([
                'user_id' => $user->id,
                'time_slot_id' => $slot->id,
                'amount_due' => $fee,
                'status' => $needsPayment ? BookingStatus::Pending : BookingStatus::Confirmed,
                'payment_deadline' => $needsPayment ? $this->paymentDeadline($slot) : null,
            ]);
            $booking->booking_code = $this->newBookingCode();
            $booking->save();

            $slot->update(['status' => SlotStatus::Booked]);

            return $booking;
        });
    }

    /**
     * 24 hours after booking, or 2 hours before the slot starts if that is sooner.
     */
    public function paymentDeadline(TimeSlot $slot): CarbonInterface
    {
        $deadline = now()->addDay();
        $beforeStart = $slot->startsAt()->subHours(2);

        if ($beforeStart->lessThan($deadline)) {
            $deadline = $beforeStart->isPast() ? $slot->startsAt() : $beforeStart;
        }

        return $deadline;
    }

    protected function ensureCanBook(User $user, TimeSlot $slot): void
    {
        if ($user->isAdmin()) {
            throw ValidationException::withMessages(['slot' => 'Admins cannot make bookings.']);
        }

        if (! $slot->facility->is_active) {
            throw ValidationException::withMessages(['slot' => 'This facility is not open for booking.']);
        }

        if ($slot->status !== SlotStatus::Available) {
            throw ValidationException::withMessages(['slot' => 'Sorry, this slot has just been taken or is blocked.']);
        }

        if ($slot->hasStarted()) {
            throw ValidationException::withMessages(['slot' => 'This slot has already started.']);
        }

        if ($user->isStudent()) {
            $activeCount = $user->bookings()->active()->upcoming()->count();

            if ($activeCount >= self::STUDENT_ACTIVE_LIMIT) {
                throw ValidationException::withMessages([
                    'slot' => 'Students can hold at most '.self::STUDENT_ACTIVE_LIMIT.' upcoming bookings. Cancel one first.',
                ]);
            }
        }
    }

    protected function newBookingCode(): string
    {
        do {
            $code = 'BK-'.Str::upper(Str::random(8));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
