<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Deletes a user who has no bookings (FR-A8). Users with bookings are kept for the audit trail.
 */
class DeleteUser
{
    public function handle(User $actor, User $user): void
    {
        if ($actor->is($user)) {
            throw ValidationException::withMessages(['user' => 'You cannot delete your own account here.']);
        }

        if ($user->bookings()->exists()) {
            throw ValidationException::withMessages(['user' => "{$user->name} has bookings, so the account is kept for the booking history."]);
        }

        $user->delete();
    }
}
