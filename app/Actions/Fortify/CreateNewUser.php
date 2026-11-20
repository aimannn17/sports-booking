<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $isStudent = filter_var($input['is_student'] ?? false, FILTER_VALIDATE_BOOLEAN);

        Validator::make($input, [
            ...$this->profileRules(),
            'phone' => ['nullable', 'string', 'max:20'],
            'student_id' => [Rule::requiredIf($isStudent), 'nullable', 'string', 'max:20', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ], [
            'student_id.required' => 'Enter your student ID, or untick "I am a GMI student".',
        ])->validate();

        // The role is decided here on the server, never taken from the form,
        // so nobody can register as an admin.
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'student_id' => $isStudent ? $input['student_id'] : null,
            'role' => $isStudent ? Role::Student : Role::External,
            'password' => $input['password'],
        ]);
    }
}
