<?php

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Lets an admin edit a user's details and role (FR-A8).
 */
class UpdateUser
{
    /**
     * @param  array{name: string, email: string, phone?: string|null, student_id?: string|null, role: string|Role}  $data
     */
    public function handle(User $actor, User $user, array $data): User
    {
        $role = $data['role'] instanceof Role ? $data['role'] : Role::from($data['role']);

        if ($actor->is($user) && $role !== Role::Admin) {
            throw ValidationException::withMessages(['role' => 'You cannot remove your own admin role.']);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'student_id' => $role === Role::Student ? ($data['student_id'] ?? null) : null,
            'role' => $role,
        ])->save();

        return $user;
    }
}
