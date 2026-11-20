<?php

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\User;

/*
 * NFR-2: only logged-in users book, only admins open admin pages.
 */

dataset('admin pages', [
    'facility types' => fn () => route('admin.facility-types.index'),
    'facility type create' => fn () => route('admin.facility-types.create'),
    'facility type show' => fn () => route('admin.facility-types.show', FacilityType::factory()->create()),
    'facility type edit' => fn () => route('admin.facility-types.edit', FacilityType::factory()->create()),
    'facilities' => fn () => route('admin.facilities.index'),
    'schedules' => fn () => route('admin.facilities.schedules.index', Facility::factory()->create()),
    'slots' => fn () => route('admin.slots.index'),
    'bookings' => fn () => route('admin.bookings.index'),
    'booking' => fn () => route('admin.bookings.show', Booking::factory()->create()),
    'payments' => fn () => route('admin.payments.index'),
    'users' => fn () => route('admin.users.index'),
    'user' => fn () => route('admin.users.show', User::factory()->create()),
]);

test('admins can open every admin page', function (string $url) {
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
})->with('admin pages');

// The access-admin Gate uses denyAsNotFound(), so non-admins get a 404
// and cannot even tell the admin area exists (Topic 7.2).
test('students cannot open admin pages', function (string $url) {
    $this->actingAs(User::factory()->student()->create())->get($url)->assertNotFound();
})->with('admin pages');

test('external users cannot open admin pages', function (string $url) {
    $this->actingAs(User::factory()->external()->create())->get($url)->assertNotFound();
})->with('admin pages');

test('the access-admin gate only lets admins through', function () {
    expect(Gate::forUser(User::factory()->admin()->create())->allows('access-admin'))->toBeTrue()
        ->and(Gate::forUser(User::factory()->student()->create())->allows('access-admin'))->toBeFalse()
        ->and(Gate::forUser(User::factory()->external()->create())->allows('access-admin'))->toBeFalse();
});

test('unknown URLs show the branded 404 page', function () {
    $this->get('/no-such-page')->assertNotFound()->assertSee('Page not found');
});

test('guests are sent to log in from admin pages', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with('admin pages');

test('guests can browse facilities but not bookings', function () {
    $facility = Facility::factory()->create(['name' => 'Court Zeta']);

    $this->get(route('home'))->assertOk();
    $this->get(route('facilities.index'))->assertOk()->assertSee('Court Zeta');
    $this->get(route('facilities.show', $facility))->assertOk();
    $this->get(route('bookings.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('inactive facilities are hidden from the public', function () {
    $facility = Facility::factory()->inactive()->create(['name' => 'Court Closed']);

    $this->get(route('facilities.index'))->assertDontSee('Court Closed');
    $this->get(route('facilities.show', $facility))->assertForbidden();
    $this->actingAs(User::factory()->admin()->create())->get(route('facilities.show', $facility))->assertOk();
});

test('every role sees a working dashboard', function (string $role) {
    $this->actingAs(User::factory()->{$role}()->create())->get(route('dashboard'))->assertOk();
})->with(['student', 'external', 'admin']);
