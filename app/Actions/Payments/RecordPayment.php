<?php

namespace App\Actions\Payments;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Records a manual payment, issues a receipt number and confirms the booking (FR-A6).
 */
class RecordPayment
{
    /**
     * @param  array{amount: float|string, method: string|PaymentMethod, reference_no?: string|null}  $data
     */
    public function handle(Booking $booking, User $admin, array $data): Payment
    {
        if (! $booking->needsPayment()) {
            throw ValidationException::withMessages(['booking' => 'This booking is free and needs no payment.']);
        }

        if ($booking->status === BookingStatus::Cancelled) {
            throw ValidationException::withMessages(['booking' => 'A cancelled booking cannot be paid.']);
        }

        if ($booking->isPaid()) {
            throw ValidationException::withMessages(['booking' => 'This booking has already been paid.']);
        }

        return DB::transaction(function () use ($booking, $admin, $data) {
            $payment = $booking->payment ?? new Payment(['booking_id' => $booking->id]);

            $payment->fill([
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference_no' => $data['reference_no'] ?? null,
            ]);
            $payment->status = PaymentStatus::Paid;
            $payment->paid_at = now();
            $payment->recorded_by = $admin->id;
            $payment->receipt_no = $this->nextReceiptNumber();
            $payment->save();

            if ($booking->status === BookingStatus::Pending) {
                $booking->update(['status' => BookingStatus::Confirmed]);
            }

            return $payment;
        });
    }

    /**
     * RCP-YYYYMMDD-NNNN, numbered from 0001 each day.
     */
    public function nextReceiptNumber(): string
    {
        $prefix = 'RCP-'.now()->format('Ymd').'-';

        $last = Payment::query()
            ->where('receipt_no', 'like', $prefix.'%')
            ->lockForUpdate()
            ->max('receipt_no');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
