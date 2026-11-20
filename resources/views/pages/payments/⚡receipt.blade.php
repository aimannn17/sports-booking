<?php

use App\Models\Payment;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/*
 * Printable receipt, visible to the payer and admins (FR-E5).
 */
new #[Title('Receipt')] class extends Component {
    #[Locked]
    public Payment $payment;

    public function mount(Payment $payment): void
    {
        $this->authorize('viewReceipt', $payment);

        $this->payment = $payment->load(['booking.timeSlot.facility', 'booking.user', 'recorder']);
    }
}; ?>

<div class="flex max-w-xl flex-col gap-6">
    @php($booking = $payment->booking)
    <div class="flex items-center justify-between print:hidden">
        <flux:button variant="ghost" icon="arrow-left"
            :href="auth()->user()->isAdmin() ? route('admin.bookings.show', $booking) : route('bookings.show', $booking)" wire:navigate>
            {{ __('Back to booking') }}
        </flux:button>
        <flux:button icon="printer" x-data x-on:click="window.print()">{{ __('Print') }}</flux:button>
    </div>

    <flux:card class="flex flex-col gap-5">
        <div class="flex items-start justify-between">
            <div>
                <flux:heading size="lg">{{ config('app.name') }}</flux:heading>
                <flux:text>{{ __('Official receipt') }}</flux:text>
            </div>
            <div class="text-end">
                <div class="font-mono text-lg font-semibold" data-test="receipt-no">{{ $payment->receipt_no }}</div>
                <flux:text>{{ $payment->paid_at->format('j M Y, H:i') }}</flux:text>
            </div>
        </div>

        <flux:separator />

        <dl class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 text-sm">
            <dt class="text-zinc-500">{{ __('Received from') }}</dt><dd>{{ $booking->user->name }}</dd>
            <dt class="text-zinc-500">{{ __('Booking') }}</dt><dd class="font-mono">{{ $booking->booking_code }}</dd>
            <dt class="text-zinc-500">{{ __('Facility') }}</dt><dd>{{ $booking->timeSlot->facility->name }}</dd>
            <dt class="text-zinc-500">{{ __('Slot') }}</dt><dd>{{ $booking->timeSlot->slot_date->format('j M Y') }}, {{ $booking->timeSlot->timeRange() }}</dd>
            <dt class="text-zinc-500">{{ __('Method') }}</dt><dd>{{ $payment->method->label() }}</dd>
            @if ($payment->reference_no)
                <dt class="text-zinc-500">{{ __('Reference') }}</dt><dd class="font-mono">{{ $payment->reference_no }}</dd>
            @endif
            <dt class="text-zinc-500">{{ __('Recorded by') }}</dt><dd>{{ $payment->recorder?->name ?? '—' }}</dd>
        </dl>

        <flux:separator />

        <div class="flex items-center justify-between">
            <flux:heading>{{ __('Amount paid') }}</flux:heading>
            <div class="text-2xl font-semibold">RM{{ number_format($payment->amount, 2) }}</div>
        </div>
    </flux:card>
</div>
