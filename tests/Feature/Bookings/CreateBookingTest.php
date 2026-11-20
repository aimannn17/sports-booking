<?php

use App\Actions\Bookings\CreateBooking;
use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    $this->facility = Facility::factory()->create(['external_fee' => 40]);
    $this->slot = TimeSlot::factory()->for($this->facility)->create();
});

test('a student books a slot for free and it is confirmed at once', function () {
    $student = User::factory()->student()->create();

    $booking = app(CreateBooking::class)->handle($student, $this->slot);

    expect($booking->status)->toBe(BookingStatus::Confirmed)
        ->and((float) $booking->amount_due)->toBe(0.0)
        ->and($booking->payment_deadline)->toBeNull()
        ->and($booking->booking_code)->toMatch('/^BK-[A-Z0-9]{8}$/')
        ->and($this->slot->fresh()->status)->toBe(SlotStatus::Booked);
});

test('an external user pays the facility fee and the booking stays pending', function () {
    $external = User::factory()->external()->create();

    $booking = app(CreateBooking::class)->handle($external, $this->slot);

    expect($booking->status)->toBe(BookingStatus::Pending)
        ->and((float) $booking->amount_due)->toBe(40.0)
        ->and($booking->payment_deadline)->not->toBeNull();
});

test('the payment deadline is 24 hours, or 2 hours before the slot if sooner', function () {
    $this->travelTo(today()->setTime(9, 0));
    $action = app(CreateBooking::class);

    $farSlot = TimeSlot::factory()->for($this->facility)->create(['slot_date' => today()->addDays(5), 'start_time' => '10:00:00', 'end_time' => '11:00:00']);
    $soonSlot = TimeSlot::factory()->for($this->facility)->create(['slot_date' => today(), 'start_time' => '15:00:00', 'end_time' => '16:00:00']);

    expect($action->paymentDeadline($farSlot)->toDateTimeString())->toBe(now()->addDay()->toDateTimeString())
        ->and($action->paymentDeadline($soonSlot)->format('H:i'))->toBe('13:00');
});

test('a slot that is already booked cannot be booked again', function () {
    $first = User::factory()->student()->create();
    $second = User::factory()->student()->create();

    app(CreateBooking::class)->handle($first, $this->slot);
    app(CreateBooking::class)->handle($second, $this->slot);
})->throws(ValidationException::class, 'just been taken');

test('blocked slots cannot be booked', function () {
    $this->slot->update(['status' => SlotStatus::Blocked]);

    app(CreateBooking::class)->handle(User::factory()->student()->create(), $this->slot);
})->throws(ValidationException::class);

test('slots at an inactive facility cannot be booked', function () {
    $this->facility->update(['is_active' => false]);

    app(CreateBooking::class)->handle(User::factory()->student()->create(), $this->slot);
})->throws(ValidationException::class, 'not open for booking');

test('slots that have already started cannot be booked', function () {
    $past = TimeSlot::factory()->for($this->facility)->past()->create();

    app(CreateBooking::class)->handle(User::factory()->student()->create(), $past);
})->throws(ValidationException::class, 'already started');

test('a student can hold at most two upcoming bookings', function () {
    $student = User::factory()->student()->create();
    [$a, $b, $c] = TimeSlot::factory()->count(3)->for($this->facility)->create();
    $action = app(CreateBooking::class);

    $action->handle($student, $a);
    $action->handle($student, $b);

    expect(fn () => $action->handle($student, $c))->toThrow(ValidationException::class, 'at most 2');
    expect($c->fresh()->status)->toBe(SlotStatus::Available);
});

test('external users have no booking limit', function () {
    $external = User::factory()->external()->create();
    $slots = TimeSlot::factory()->count(3)->for($this->facility)->create();

    $slots->each(fn ($slot) => app(CreateBooking::class)->handle($external, $slot));

    expect($external->bookings()->count())->toBe(3);
});

test('admins cannot make bookings', function () {
    app(CreateBooking::class)->handle(User::factory()->admin()->create(), $this->slot);
})->throws(ValidationException::class);

test('a student can book from the facility page', function () {
    $student = User::factory()->student()->create();

    Livewire::actingAs($student)
        ->test('pages::facilities.show', ['facility' => $this->facility])
        ->set('date', $this->slot->slot_date->toDateString())
        ->call('selectSlot', $this->slot->id)
        ->assertSet('selectedSlotId', $this->slot->id)
        ->call('book')
        ->assertHasNoErrors()
        ->assertRedirect(route('bookings.show', Booking::first()));

    expect(Booking::first()->user_id)->toBe($student->id);
});

test('the booking error is shown in the modal when the slot was taken meanwhile', function () {
    $student = User::factory()->student()->create();

    $component = Livewire::actingAs($student)
        ->test('pages::facilities.show', ['facility' => $this->facility])
        ->call('selectSlot', $this->slot->id);

    app(CreateBooking::class)->handle(User::factory()->student()->create(), $this->slot);

    $component->call('book')->assertHasErrors('slot')->assertNoRedirect();
});

test('guests who pick a slot are sent to log in', function () {
    Livewire::test('pages::facilities.show', ['facility' => $this->facility])
        ->call('selectSlot', $this->slot->id)
        ->assertRedirect(route('login'));

    expect(Booking::count())->toBe(0);
});
