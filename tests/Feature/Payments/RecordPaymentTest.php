<?php

use App\Actions\Bookings\CreateBooking;
use App\Actions\Payments\RecordPayment;
use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Facility;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->external = User::factory()->external()->create();
    $facility = Facility::factory()->create(['external_fee' => 40]);
    $this->booking = app(CreateBooking::class)->handle($this->external, TimeSlot::factory()->for($facility)->create());
});

test('recording a payment issues a receipt and confirms the booking', function () {
    $this->travelTo(now()->setDate(2026, 10, 1));

    $payment = app(RecordPayment::class)->handle($this->booking, $this->admin, ['amount' => 40, 'method' => 'cash']);

    expect($payment->status)->toBe(PaymentStatus::Paid)
        ->and($payment->receipt_no)->toBe('RCP-20261001-0001')
        ->and($payment->recorded_by)->toBe($this->admin->id)
        ->and($payment->paid_at)->not->toBeNull()
        ->and($this->booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});

test('receipt numbers count up within a day', function () {
    $action = app(RecordPayment::class);
    $action->handle($this->booking, $this->admin, ['amount' => 40, 'method' => 'cash']);

    $second = app(CreateBooking::class)->handle($this->external, TimeSlot::factory()->for($this->booking->timeSlot->facility)->create());
    $payment = $action->handle($second, $this->admin, ['amount' => 40, 'method' => PaymentMethod::BankTransfer, 'reference_no' => 'X1']);

    expect($payment->receipt_no)->toEndWith('-0002');
});

test('a booking cannot be paid twice', function () {
    $action = app(RecordPayment::class);
    $action->handle($this->booking, $this->admin, ['amount' => 40, 'method' => 'cash']);
    $action->handle($this->booking->fresh(), $this->admin, ['amount' => 40, 'method' => 'cash']);
})->throws(ValidationException::class, 'already been paid');

test('free student bookings need no payment', function () {
    $free = app(CreateBooking::class)->handle(User::factory()->student()->create(), TimeSlot::factory()->create());

    app(RecordPayment::class)->handle($free, $this->admin, ['amount' => 10, 'method' => 'cash']);
})->throws(ValidationException::class, 'free');

test('admin records a bank transfer from the payments page', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.payments.index')
        ->assertSee($this->booking->booking_code)
        ->call('startPayment', $this->booking->id)
        ->assertSet('paymentForm.amount', '40.00')
        ->set('paymentForm.method', 'bank_transfer')
        ->call('recordPayment')
        ->assertHasErrors(['paymentForm.reference_no' => 'required'])
        ->set('paymentForm.reference_no', 'MBB123')
        ->call('recordPayment')
        ->assertHasNoErrors();

    expect($this->booking->fresh()->payment->reference_no)->toBe('MBB123');
});

test('the receipt is visible to the payer and admins but not other users', function () {
    $payment = app(RecordPayment::class)->handle($this->booking, $this->admin, ['amount' => 40, 'method' => 'cash']);

    $this->actingAs($this->external)->get(route('payments.receipt', $payment))->assertOk()->assertSee($payment->receipt_no);
    $this->actingAs($this->admin)->get(route('payments.receipt', $payment))->assertOk();
    $this->actingAs(User::factory()->external()->create())->get(route('payments.receipt', $payment))->assertForbidden();
});
