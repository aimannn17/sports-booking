<?php

use App\Enums\BookingStatus;
use App\Enums\Role;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\CarbonImmutable;

/*
 * Pure rules that need no database rows.
 */

test('only pending and confirmed bookings hold a slot', function () {
    expect(BookingStatus::active())->toBe(['pending', 'confirmed']);
});

test('role helpers match the role', function (Role $role, string $method) {
    $user = new User(['role' => $role]);

    expect($user->{$method}())->toBeTrue();
})->with([
    [Role::Admin, 'isAdmin'],
    [Role::Student, 'isStudent'],
    [Role::External, 'isExternal'],
]);

test('a slot knows when it starts and formats its time range', function () {
    $slot = new TimeSlot(['slot_date' => '2026-12-01', 'start_time' => '20:00:00', 'end_time' => '21:00:00']);

    expect($slot->startsAt())->toEqual(CarbonImmutable::parse('2026-12-01 20:00:00'))
        ->and($slot->timeRange())->toBe('20:00 – 21:00');
});

test('a slot has started once its start time passes', function () {
    $slot = new TimeSlot(['slot_date' => '2026-12-01', 'start_time' => '20:00:00', 'end_time' => '21:00:00']);

    $this->travelTo(CarbonImmutable::parse('2026-12-01 19:59'));
    expect($slot->hasStarted())->toBeFalse();

    $this->travelTo(CarbonImmutable::parse('2026-12-01 20:01'));
    expect($slot->hasStarted())->toBeTrue();
});

test('a booking needs payment only when money is due', function () {
    expect((new Booking(['amount_due' => 0]))->needsPayment())->toBeFalse()
        ->and((new Booking(['amount_due' => 40]))->needsPayment())->toBeTrue();
});

test('every status has a badge colour', function () {
    foreach ([...BookingStatus::cases(), ...SlotStatus::cases(), ...Role::cases()] as $case) {
        expect($case->color())->toBeString()->not->toBeEmpty();
    }
});
