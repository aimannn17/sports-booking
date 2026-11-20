<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * The admin "all bookings" list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->user_id === $user->id;
    }

    /**
     * Students and external users book; admins manage.
     */
    public function create(User $user): bool
    {
        return $user->isStudent() || $user->isExternal();
    }

    /**
     * Owners cancel their own active booking before it starts.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id
            && $booking->isActive()
            && ! $booking->timeSlot->hasStarted();
    }

    public function updateStatus(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }
}
