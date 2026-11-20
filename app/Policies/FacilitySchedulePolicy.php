<?php

namespace App\Policies;

use App\Models\FacilitySchedule;
use App\Models\User;

class FacilitySchedulePolicy
{
    public function update(User $user, FacilitySchedule $schedule): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, FacilitySchedule $schedule): bool
    {
        return $user->isAdmin();
    }
}
