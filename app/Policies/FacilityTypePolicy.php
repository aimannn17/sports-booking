<?php

namespace App\Policies;

use App\Models\FacilityType;
use App\Models\User;

class FacilityTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, FacilityType $type): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, FacilityType $type): bool
    {
        return $user->isAdmin();
    }
}
