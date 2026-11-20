<?php

use App\Actions\Payments\RecordPayment;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilitySchedule;
use App\Models\FacilityType;
use App\Models\Payment;
use App\Models\TimeSlot;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->student = User::factory()->student()->create();
    $this->external = User::factory()->external()->create();
});

test('only admins manage facility types, facilities, schedules and slots', function () {
    $type = FacilityType::factory()->create();
    $facility = Facility::factory()->for($type)->create();
    $schedule = FacilitySchedule::factory()->for($facility)->create();
    $slot = TimeSlot::factory()->for($facility)->create();

    foreach ([$this->student, $this->external] as $user) {
        expect($user->can('create', FacilityType::class))->toBeFalse()
            ->and($user->can('update', $type))->toBeFalse()
            ->and($user->can('delete', $type))->toBeFalse()
            ->and($user->can('create', Facility::class))->toBeFalse()
            ->and($user->can('update', $facility))->toBeFalse()
            ->and($user->can('manageSchedule', $facility))->toBeFalse()
            ->and($user->can('update', $schedule))->toBeFalse()
            ->and($user->can('generate', TimeSlot::class))->toBeFalse()
            ->and($user->can('block', $slot))->toBeFalse();
    }

    expect($this->admin->can('create', FacilityType::class))->toBeTrue()
        ->and($this->admin->can('update', $facility))->toBeTrue()
        ->and($this->admin->can('manageSchedule', $facility))->toBeTrue()
        ->and($this->admin->can('delete', $schedule))->toBeTrue()
        ->and($this->admin->can('block', $slot))->toBeTrue();
});

test('anyone may view an active facility, only admins an inactive one', function () {
    $active = Facility::factory()->create();
    $inactive = Facility::factory()->inactive()->create();

    expect(Gate::forUser(null)->allows('view', $active))->toBeTrue()
        ->and(Gate::forUser(null)->allows('view', $inactive))->toBeFalse()
        ->and($this->student->can('view', $inactive))->toBeFalse()
        ->and($this->admin->can('view', $inactive))->toBeTrue();
});

test('an already inactive facility cannot be deactivated again', function () {
    expect($this->admin->can('delete', Facility::factory()->inactive()->create()))->toBeFalse();
});

test('students and external users can book; admins cannot', function () {
    expect($this->student->can('create', Booking::class))->toBeTrue()
        ->and($this->external->can('create', Booking::class))->toBeTrue()
        ->and($this->admin->can('create', Booking::class))->toBeFalse();
});

test('a booking is viewed and cancelled by its owner', function () {
    $booking = Booking::factory()->for($this->student)->create();

    expect($this->student->can('view', $booking))->toBeTrue()
        ->and($this->student->can('cancel', $booking))->toBeTrue()
        ->and($this->external->can('view', $booking))->toBeFalse()
        ->and($this->external->can('cancel', $booking))->toBeFalse()
        ->and($this->admin->can('view', $booking))->toBeTrue()
        ->and($this->admin->can('updateStatus', $booking))->toBeTrue()
        ->and($this->student->can('updateStatus', $booking))->toBeFalse();
});

test('cancelled bookings cannot be cancelled', function () {
    $booking = Booking::factory()->for($this->student)->cancelled()->create();

    expect($this->student->can('cancel', $booking))->toBeFalse();
});

test('only admins record payments, and only for unpaid fee bookings', function () {
    $unpaid = Booking::factory()->for($this->external)->pending(40)->create();
    $free = Booking::factory()->for($this->student)->create();

    expect($this->admin->can('record', [Payment::class, $unpaid]))->toBeTrue()
        ->and($this->external->can('record', [Payment::class, $unpaid]))->toBeFalse()
        ->and($this->admin->can('record', [Payment::class, $free]))->toBeFalse();

    app(RecordPayment::class)->handle($unpaid, $this->admin, ['amount' => 40, 'method' => 'cash']);

    expect($this->admin->can('record', [Payment::class, $unpaid->fresh()]))->toBeFalse();
});

test('a receipt is only visible once paid', function () {
    $payment = Payment::factory()->create();

    expect($this->admin->can('viewReceipt', $payment))->toBeFalse();
});

test('admins manage users but cannot delete themselves', function () {
    expect($this->admin->can('update', $this->student))->toBeTrue()
        ->and($this->admin->can('delete', $this->student))->toBeTrue()
        ->and($this->admin->can('delete', $this->admin))->toBeFalse()
        ->and($this->student->can('viewAny', User::class))->toBeFalse()
        ->and($this->student->can('update', $this->external))->toBeFalse();
});
