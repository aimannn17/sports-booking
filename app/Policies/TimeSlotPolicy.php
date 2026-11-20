<?php

namespace App\Policies;

use App\Models\TimeSlot;
use App\Models\User;

class TimeSlotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function generate(User $user): bool
    {
        return $user->isAdmin();
    }

    public function block(User $user, TimeSlot $slot): bool
    {
        return $user->isAdmin();
    }
}
