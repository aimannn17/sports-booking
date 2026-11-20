<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    /**
     * Anyone, even a guest, may browse active facilities (FR-S2, FR-E2).
     */
    public function view(?User $user, Facility $facility): bool
    {
        return $facility->is_active || $user?->isAdmin();
    }

    /**
     * The admin list includes inactive facilities.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->isAdmin();
    }

    /**
     * Opening hours are part of a facility, so managing them follows the same rule.
     */
    public function manageSchedule(User $user, Facility $facility): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->isAdmin() && $facility->is_active;
    }
}
