<?php

namespace App\Livewire\Forms;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $student_id = '';

    public string $role = 'student';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::enum(Role::class)],
            'student_id' => [
                Rule::requiredIf($this->role === Role::Student->value),
                'nullable', 'string', 'max:20',
                Rule::unique('users', 'student_id')->ignore($this->user?->id),
            ],
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = (string) $user->phone;
        $this->student_id = (string) $user->student_id;
        $this->role = $user->role->value;
    }
}
