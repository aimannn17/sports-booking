<?php

use App\Actions\Bookings\CreateBooking;
use App\Actions\Slots\GenerateTimeSlots;
use App\Enums\BookingStatus;
use App\Enums\Role;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\TimeSlot;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

test('admin adds a facility with validation', function () {
    $type = FacilityType::factory()->create();

    Livewire::test('pages::admin.facilities.index')
        ->call('create')
        ->set('form.name', '')
        ->set('form.external_fee', '-5')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.facility_type_id', 'form.external_fee']);

    Livewire::test('pages::admin.facilities.index')
        ->call('create')
        ->set('form.facility_type_id', $type->id)
        ->set('form.name', 'Court Z')
        ->set('form.external_fee', '25.50')
        ->call('save')
        ->assertHasNoErrors();

    expect(Facility::where('name', 'Court Z')->first()->external_fee)->toBe('25.50');
});

test('deleting a facility only deactivates it', function () {
    $facility = Facility::factory()->create();
    Booking::factory()->create(['time_slot_id' => TimeSlot::factory()->for($facility)->booked()]);

    Livewire::test('pages::admin.facilities.index')
        ->call('confirmDeactivate', $facility->id)
        ->call('deactivate');

    expect($facility->fresh()->is_active)->toBeFalse()
        ->and($facility->bookings()->count())->toBe(1);
});

test('admin sets opening hours, and closing must be after opening', function () {
    $facility = Facility::factory()->create();

    Livewire::test('pages::admin.facilities.schedules', ['facility' => $facility])
        ->call('edit', 1)
        ->set('form.open_time', '10:00')
        ->set('form.close_time', '09:00')
        ->call('save')
        ->assertHasErrors(['form.close_time'])
        ->set('form.close_time', '12:00')
        ->call('save')
        ->assertHasNoErrors();

    // Saving the same day again updates it instead of adding a row.
    Livewire::test('pages::admin.facilities.schedules', ['facility' => $facility])
        ->call('edit', 1)
        ->set('form.close_time', '14:00')
        ->call('save');

    expect($facility->schedules()->count())->toBe(1)
        ->and($facility->schedules()->first()->close_time)->toStartWith('14:00');
});

test('slots are generated from the schedule without duplicates', function () {
    $facility = Facility::factory()->create();
    $monday = today()->next('Monday');
    $facility->schedules()->create(['day_of_week' => 1, 'open_time' => '08:00', 'close_time' => '12:00', 'slot_minutes' => 60]);

    $created = app(GenerateTimeSlots::class)->handle($facility, $monday, $monday->addDays(6));
    $again = app(GenerateTimeSlots::class)->handle($facility, $monday, $monday->addDays(6));

    expect($created)->toBe(4)
        ->and($again)->toBe(0)
        ->and($facility->timeSlots()->pluck('start_time')->all())->toBe(['08:00:00', '09:00:00', '10:00:00', '11:00:00']);
});

test('admin generates slots from the slots page', function () {
    $facility = Facility::factory()->create();
    foreach (range(0, 6) as $day) {
        $facility->schedules()->create(['day_of_week' => $day, 'open_time' => '08:00', 'close_time' => '10:00', 'slot_minutes' => 60]);
    }

    Livewire::test('pages::admin.slots.index')
        ->call('openGenerate')
        ->set('generate.facility_id', $facility->id)
        ->set('generate.from', today()->addDay()->toDateString())
        ->set('generate.to', today()->addDays(2)->toDateString())
        ->call('runGenerate')
        ->assertHasNoErrors();

    expect($facility->timeSlots()->count())->toBe(4);
});

test('admin blocks and unblocks an available slot but not a booked one', function () {
    $slot = TimeSlot::factory()->create();
    $booked = TimeSlot::factory()->booked()->create();

    Livewire::test('pages::admin.slots.index')->call('setBlocked', $slot->id, true);
    expect($slot->fresh()->status)->toBe(SlotStatus::Blocked);

    Livewire::test('pages::admin.slots.index')->call('setBlocked', $slot->id, false);
    expect($slot->fresh()->status)->toBe(SlotStatus::Available);

    Livewire::test('pages::admin.slots.index')->call('setBlocked', $booked->id, true);
    expect($booked->fresh()->status)->toBe(SlotStatus::Booked);
});

test('admin changes a booking status and cancelling frees the slot', function () {
    $slot = TimeSlot::factory()->create();
    $booking = app(CreateBooking::class)->handle(User::factory()->student()->create(), $slot);

    Livewire::test('pages::admin.bookings.show', ['booking' => $booking])
        ->set('status', 'cancelled')
        ->call('updateStatus');

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled)
        ->and($slot->fresh()->status)->toBe(SlotStatus::Available);

    Livewire::test('pages::admin.bookings.show', ['booking' => $booking->fresh()])
        ->set('status', 'confirmed')
        ->call('updateStatus');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed)
        ->and($slot->fresh()->status)->toBe(SlotStatus::Booked);
});

test('admin searches bookings by code', function () {
    $wanted = Booking::factory()->create();
    $other = Booking::factory()->create();

    Livewire::test('pages::admin.bookings.index')
        ->set('search', $wanted->booking_code)
        ->assertSee($wanted->booking_code)
        ->assertDontSee($other->booking_code);
});

test('admin edits a user role and the student ID follows it', function () {
    $user = User::factory()->external()->create();

    Livewire::test('pages::admin.users.index')
        ->call('edit', $user->id)
        ->set('form.role', 'student')
        ->call('save')
        ->assertHasErrors(['form.student_id' => 'required'])
        ->set('form.student_id', 'GMI2099999')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->role)->toBe(Role::Student)
        ->and($user->fresh()->student_id)->toBe('GMI2099999');
});

test('admins cannot remove their own admin role', function () {
    Livewire::test('pages::admin.users.index')
        ->call('edit', $this->admin->id)
        ->set('form.role', 'student')
        ->set('form.student_id', 'GMI2000001')
        ->call('save')
        ->assertHasErrors('form.role');

    expect($this->admin->fresh()->isAdmin())->toBeTrue();
});

test('users with bookings are kept; users without can be deleted', function () {
    $withBookings = Booking::factory()->create()->user;
    $without = User::factory()->create();

    Livewire::test('pages::admin.users.index')->call('confirmDelete', $withBookings->id)->call('delete');
    Livewire::test('pages::admin.users.index')->call('confirmDelete', $without->id)->call('delete');

    expect(User::find($withBookings->id))->not->toBeNull()
        ->and(User::find($without->id))->toBeNull();
});
