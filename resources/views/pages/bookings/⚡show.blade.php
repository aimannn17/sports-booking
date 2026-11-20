<?php

use App\Actions\Bookings\CancelBooking;
use App\Models\Booking;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

/*
 * Booking details and payment status, owner (or admin) only (FR-E5).
 */
new #[Title('Booking details')] class extends Component {
    #[Locked]
    public Booking $booking;

    #[Validate('nullable|string|max:200')]
    public string $reason = '';

    public function mount(Booking $booking): void
    {
        $this->authorize('view', $booking);

        $this->booking = $booking->load(['timeSlot.facility.facilityType', 'payment', 'user']);
    }

    public function cancel(CancelBooking $cancelBooking): void
    {
        $this->authorize('cancel', $this->booking);
        $this->validate();

        try {
            $cancelBooking->handle($this->booking, $this->reason);
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', text: $e->validator->errors()->first());

            return;
        }

        Flux::modal('cancel-booking')->close();
        Flux::toast(variant: 'success', text: __('Booking cancelled.'));
        $this->booking->refresh();
    }
}; ?>

<div class="flex max-w-3xl flex-col gap-6">
    @php($ts = $booking->timeSlot)
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('bookings.index')" wire:navigate>{{ __('My bookings') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $booking->booking_code }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    @if (session('booking-created'))
        <flux:callout variant="success" icon="check-circle" data-test="booking-created"
            :heading="$booking->status === \App\Enums\BookingStatus::Confirmed ? __('You are booked in!') : __('Slot reserved. Payment needed.')"
            :text="$booking->status === \App\Enums\BookingStatus::Confirmed
                ? __('Show your booking code at the counter.')
                : __('Pay RM:amount at the sports centre counter or by bank transfer before :deadline.', ['amount' => number_format($booking->amount_due, 2), 'deadline' => $booking->payment_deadline?->format('j M Y, H:i')])" />
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <flux:heading size="xl" level="1" class="font-mono">{{ $booking->booking_code }}</flux:heading>
        <flux:badge :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <flux:card class="flex flex-col gap-3">
            <flux:heading>{{ __('Slot') }}</flux:heading>
            <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                <dt class="text-zinc-500">{{ __('Facility') }}</dt>
                <dd><flux:link :href="route('facilities.show', $ts->facility)" wire:navigate>{{ $ts->facility->name }}</flux:link> ({{ $ts->facility->facilityType->name }})</dd>
                <dt class="text-zinc-500">{{ __('Location') }}</dt><dd>{{ $ts->facility->location ?? '—' }}</dd>
                <dt class="text-zinc-500">{{ __('Date') }}</dt><dd>{{ $ts->slot_date->format('l, j F Y') }}</dd>
                <dt class="text-zinc-500">{{ __('Time') }}</dt><dd>{{ $ts->timeRange() }}</dd>
                <dt class="text-zinc-500">{{ __('Booked on') }}</dt><dd>{{ $booking->created_at->format('j M Y, H:i') }}</dd>
                @if ($booking->cancelled_at)
                    <dt class="text-zinc-500">{{ __('Cancelled') }}</dt><dd>{{ $booking->cancelled_at->format('j M Y, H:i') }}</dd>
                    <dt class="text-zinc-500">{{ __('Reason') }}</dt><dd>{{ $booking->cancel_reason ?? '—' }}</dd>
                @endif
            </dl>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>{{ __('Payment') }}</flux:heading>
            @if (! $booking->needsPayment())
                <flux:text>{{ __('Free booking. No payment needed.') }}</flux:text>
            @else
                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                    <dt class="text-zinc-500">{{ __('Amount due') }}</dt><dd class="font-medium">RM{{ number_format($booking->amount_due, 2) }}</dd>
                    <dt class="text-zinc-500">{{ __('Status') }}</dt>
                    <dd>
                        <flux:badge size="sm" :color="$booking->isPaid() ? 'green' : 'amber'" data-test="payment-status">
                            {{ $booking->isPaid() ? __('Paid') : __('Unpaid') }}
                        </flux:badge>
                    </dd>
                    @if ($booking->isPaid())
                        <dt class="text-zinc-500">{{ __('Receipt') }}</dt><dd class="font-mono">{{ $booking->payment->receipt_no }}</dd>
                        <dt class="text-zinc-500">{{ __('Paid on') }}</dt><dd>{{ $booking->payment->paid_at->format('j M Y, H:i') }}</dd>
                    @elseif ($booking->payment_deadline && $booking->isActive())
                        <dt class="text-zinc-500">{{ __('Pay by') }}</dt><dd class="text-amber-600">{{ $booking->payment_deadline->format('j M Y, H:i') }}</dd>
                    @endif
                </dl>
                @if ($booking->isPaid())
                    <flux:button size="sm" icon="receipt-percent" :href="route('payments.receipt', $booking->payment)" wire:navigate class="self-start">{{ __('View receipt') }}</flux:button>
                @elseif ($booking->isActive())
                    <flux:text class="text-sm">{{ __('Pay at the sports centre counter (cash) or by bank transfer, quoting your booking code.') }}</flux:text>
                @endif
            @endif
        </flux:card>
    </div>

    @can('cancel', $booking)
        <div>
            <flux:modal.trigger name="cancel-booking">
                <flux:button variant="danger" icon="x-mark" data-test="cancel-button">{{ __('Cancel booking') }}</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:modal name="cancel-booking" class="md:w-96">
            <form wire:submit="cancel" class="flex flex-col gap-5">
                <div>
                    <flux:heading size="lg">{{ __('Cancel this booking?') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('The slot will be released for others to book. This cannot be undone.') }}</flux:text>
                </div>
                <flux:textarea wire:model="reason" :label="__('Reason (optional)')" rows="2" maxlength="200" />
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close><flux:button variant="ghost">{{ __('Keep booking') }}</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="danger">{{ __('Cancel booking') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endcan
</div>
