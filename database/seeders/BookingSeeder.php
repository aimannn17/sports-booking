<?php

namespace Database\Seeders;

use App\Actions\Bookings\CancelBooking;
use App\Actions\Bookings\CreateBooking;
use App\Actions\Payments\RecordPayment;
use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * A mix of confirmed, pending, cancelled and completed bookings, with payments.
 * Future bookings go through the real Actions so the data follows every business rule.
 */
class BookingSeeder extends Seeder
{
    public function run(CreateBooking $create, CancelBooking $cancel, RecordPayment $pay): void
    {
        $admin = User::where('email', 'admin@gmi.test')->firstOrFail();
        $faris = User::where('email', 'student@gmi.test')->firstOrFail();
        $aina = User::where('email', 'aina@gmi.test')->firstOrFail();
        $weijie = User::where('email', 'weijie@gmi.test')->firstOrFail();
        $siti = User::where('email', 'external@gmi.test')->firstOrFail();
        $rajesh = User::where('email', 'rajesh@gmi.test')->firstOrFail();

        $courtA = Facility::where('name', 'Badminton Court A')->firstOrFail();
        $courtB = Facility::where('name', 'Badminton Court B')->firstOrFail();
        $futsal = Facility::where('name', 'Futsal Court')->firstOrFail();
        $tennis = Facility::where('name', 'Tennis Court 1')->firstOrFail();

        $slot = fn (Facility $f, int $days, string $start) => TimeSlot::where('facility_id', $f->id)
            ->whereDate('slot_date', today()->addDays($days))
            ->where('start_time', $start)
            ->firstOrFail();

        // Students: free and confirmed at once. Faris is at his 2-booking limit.
        $create->handle($faris, $slot($courtA, 1, '20:00:00'));
        $create->handle($faris, $slot($futsal, 3, '17:00:00'));
        $create->handle($aina, $slot($courtB, 1, '20:00:00'));

        // A student cancellation.
        $cancel->handle($create->handle($weijie, $slot($courtA, 2, '18:00:00')), 'Exam clash');

        // External users: pending until paid.
        $create->handle($siti, $slot($tennis, 2, '19:00:00')); // unpaid
        $paidCash = $create->handle($siti, $slot($courtA, 3, '21:00:00'));
        $pay->handle($paidCash, $admin, ['amount' => $paidCash->amount_due, 'method' => 'cash']);

        $paidTransfer = $create->handle($rajesh, $slot($futsal, 4, '20:00:00'));
        $pay->handle($paidTransfer, $admin, ['amount' => $paidTransfer->amount_due, 'method' => 'bank_transfer', 'reference_no' => 'MBB88231907']);

        $create->handle($rajesh, $slot($courtB, 5, '19:00:00')); // unpaid

        // History: past bookings, written directly because the Actions refuse past slots.
        $this->past($faris, $courtA, 7, '18:00:00', BookingStatus::Completed);
        $this->past($aina, $courtB, 3, '19:00:00', BookingStatus::Completed);
        $this->past($weijie, $courtB, 7, '19:00:00', BookingStatus::Cancelled);
        $paidPast = $this->past($siti, $tennis, 3, '18:00:00', BookingStatus::Completed, 40);
        $pay->handle($paidPast, $admin, ['amount' => 40, 'method' => 'cash']);
    }

    protected function past(User $user, Facility $facility, int $daysAgo, string $start, BookingStatus $status, float $amount = 0): Booking
    {
        $slot = TimeSlot::where('facility_id', $facility->id)
            ->whereDate('slot_date', today()->subDays($daysAgo))
            ->where('start_time', $start)
            ->firstOrFail();

        $booking = new Booking([
            'user_id' => $user->id,
            'time_slot_id' => $slot->id,
            'amount_due' => $amount,
            'status' => $status,
            'cancel_reason' => $status === BookingStatus::Cancelled ? 'Feeling unwell' : null,
        ]);
        $booking->booking_code = 'BK-'.Str::upper(Str::random(8));
        $booking->cancelled_at = $status === BookingStatus::Cancelled ? $slot->startsAt()->subDay() : null;
        $booking->created_at = $slot->startsAt()->subDays(2);
        $booking->save();

        if ($status !== BookingStatus::Cancelled) {
            $slot->update(['status' => SlotStatus::Booked]);
        }

        return $booking;
    }
}
