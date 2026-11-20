<?php

use App\Enums\Role;
use App\Models\User;

test('registering with a student ID makes you a student', function () {
    $this->post(route('register.store'), [
        'name' => 'Ali',
        'email' => 'ali@example.com',
        'is_student' => '1',
        'student_id' => 'GMI2024123',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors();

    $user = User::where('email', 'ali@example.com')->first();

    expect($user->role)->toBe(Role::Student)
        ->and($user->student_id)->toBe('GMI2024123');
});

test('students must give a student ID', function () {
    $this->post(route('register.store'), [
        'name' => 'Ali',
        'email' => 'ali@example.com',
        'is_student' => '1',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('student_id');
});

test('registering without the student box makes you an external user', function () {
    $this->post(route('register.store'), [
        'name' => 'Public Person',
        'email' => 'public@example.com',
        'student_id' => 'IGNORED',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors();

    $user = User::where('email', 'public@example.com')->first();

    expect($user->role)->toBe(Role::External)
        ->and($user->student_id)->toBeNull();
});

test('nobody can register as an admin', function () {
    $this->post(route('register.store'), [
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'role' => 'admin',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(User::where('email', 'sneaky@example.com')->first()->isAdmin())->toBeFalse();
});
