<?php

namespace App\Policies;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * A receipt is visible to the person who paid and to admins, once it is paid.
     */
    public function viewReceipt(User $user, Payment $payment): bool
    {
        return $payment->status === PaymentStatus::Paid
            && ($user->isAdmin() || $payment->booking->user_id === $user->id);
    }

    /**
     * Only admins record manual payments, and only for bookings that still owe money.
     */
    public function record(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->needsPayment() && ! $booking->isPaid();
    }
}
