<?php

use App\Actions\Bookings\CancelBooking;
use App\Actions\Bookings\CreateBooking;
use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    $this->student = User::factory()->student()->create();
    $this->slot = TimeSlot::factory()->create();
    $this->booking = app(CreateBooking::class)->handle($this->student, $this->slot);
});

test('cancelling frees the slot and records when and why', function () {
    app(CancelBooking::class)->handle($this->booking, 'Exam clash');

    $this->booking->refresh();

    expect($this->booking->status)->toBe(BookingStatus::Cancelled)
        ->and($this->booking->cancel_reason)->toBe('Exam clash')
        ->and($this->booking->cancelled_at)->not->toBeNull()
        ->and($this->slot->fresh()->status)->toBe(SlotStatus::Available);
});

test('a freed slot can be booked again and keeps the old booking as history', function () {
    app(CancelBooking::class)->handle($this->booking);

    $other = User::factory()->student()->create();
    app(CreateBooking::class)->handle($other, $this->slot->fresh());

    expect($this->slot->bookings()->count())->toBe(2)
        ->and($this->slot->activeBooking->user_id)->toBe($other->id);
});

test('a cancelled booking cannot be cancelled twice', function () {
    app(CancelBooking::class)->handle($this->booking);
    app(CancelBooking::class)->handle($this->booking->fresh());
})->throws(ValidationException::class);

test('a booking that has started cannot be cancelled', function () {
    $this->travelTo($this->slot->startsAt()->addMinutes(5));

    app(CancelBooking::class)->handle($this->booking->fresh());
})->throws(ValidationException::class, 'already started');

test('the owner cancels from the my bookings page with a reason', function () {
    Livewire::actingAs($this->student)
        ->test('pages::bookings.index')
        ->set('cancelForm.booking_id', $this->booking->id)
        ->set('cancelForm.reason', 'Rain')
        ->call('cancel')
        ->assertHasNoErrors();

    expect($this->booking->fresh()->status)->toBe(BookingStatus::Cancelled)
        ->and($this->booking->fresh()->cancel_reason)->toBe('Rain');
});

test('nobody can cancel another user\'s booking', function () {
    $intruder = User::factory()->student()->create();

    Livewire::actingAs($intruder)
        ->test('pages::bookings.index')
        ->set('cancelForm.booking_id', $this->booking->id)
        ->call('cancel')
        ->assertNotFound();

    expect($this->booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});

test('my bookings lists only my own bookings', function () {
    $mine = $this->booking;
    $theirs = Booking::factory()->create();

    $this->actingAs($this->student)
        ->get(route('bookings.index'))
        ->assertOk()
        ->assertSee($mine->booking_code)
        ->assertDontSee($theirs->booking_code);
});

test('booking details are visible to the owner and admins only', function () {
    $this->actingAs($this->student)->get(route('bookings.show', $this->booking))->assertOk();
    $this->actingAs(User::factory()->admin()->create())->get(route('bookings.show', $this->booking))->assertOk();
    $this->actingAs(User::factory()->student()->create())->get(route('bookings.show', $this->booking))->assertForbidden();
});
