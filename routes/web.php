<?php

use App\Http\Controllers\Admin\FacilityTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public (guest) routes: design doc section 7.1
|--------------------------------------------------------------------------
| The slot list (facilities.slots.index in the design) lives on the facility
| page itself, filtered by ?date=, so guests see free slots without logging in.
*/
Route::livewire('/', 'pages::home')->name('home');
Route::livewire('facilities', 'pages::facilities.index')->name('facilities.index');
Route::livewire('facilities/{facility}', 'pages::facilities.show')->name('facilities.show');

/*
|--------------------------------------------------------------------------
| Logged-in users: design doc section 7.2
|--------------------------------------------------------------------------
| Creating and cancelling a booking are Livewire actions on these pages
| (bookings.store / bookings.cancel in the design), guarded by BookingPolicy.
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('bookings', 'pages::bookings.index')->name('bookings.index');
    Route::livewire('bookings/{booking}', 'pages::bookings.show')->name('bookings.show');
    Route::livewire('payments/{payment}/receipt', 'pages::payments.receipt')->name('payments.receipt');
});

/*
|--------------------------------------------------------------------------
| Admin: design doc sections 7.3 to 7.8
|--------------------------------------------------------------------------
| Every admin route is guarded by the 'access-admin' Gate (AppServiceProvider).
|
| Facility types use a classic Resource Controller (7 RESTful actions,
| Form Requests, Blade forms with @csrf / @method), as in Topics 6 and 8.1.
| The other admin pages are Livewire full-page components that do their own
| create / edit / delete in Flux modals, calling the same kind of Action classes.
*/
Route::middleware(['auth', 'verified', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('facility-types', FacilityTypeController::class);
    Route::livewire('facilities', 'pages::admin.facilities.index')->name('facilities.index');
    Route::livewire('facilities/{facility}/schedules', 'pages::admin.facilities.schedules')->name('facilities.schedules.index');
    Route::livewire('slots', 'pages::admin.slots.index')->name('slots.index');
    Route::livewire('bookings', 'pages::admin.bookings.index')->name('bookings.index');
    Route::livewire('bookings/{booking}', 'pages::admin.bookings.show')->name('bookings.show');
    Route::livewire('payments', 'pages::admin.payments.index')->name('payments.index');
    Route::livewire('users', 'pages::admin.users.index')->name('users.index');
    Route::livewire('users/{user}', 'pages::admin.users.show')->name('users.show');
});

require __DIR__.'/settings.php';

/*
| Any URL that matches nothing above gets the branded 404 page (Topic 4).
*/
Route::fallback(fn () => response()->view('errors.404', [], 404));
