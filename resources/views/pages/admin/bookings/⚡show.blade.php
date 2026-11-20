<?php

use App\Actions\Bookings\UpdateBookingStatus;
use App\Actions\Payments\RecordPayment;
use App\Enums\BookingStatus;
use App\Livewire\Forms\PaymentForm;
use App\Models\Booking;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/*
 * FR-A5 / FR-A6: booking details, change status, record a manual payment.
 */
new #[Title('Booking')] class extends Component {
    #[Locked]
    public Booking $booking;

    public string $status = '';

    public PaymentForm $paymentForm;

    public function mount(Booking $booking): void
    {
        $this->authorize('view', $booking);
        $this->booking = $booking;
        $this->status = $booking->status->value;
        $this->paymentForm->forBooking($booking);
    }

    public function updateStatus(UpdateBookingStatus $action): void
    {
        $this->authorize('updateStatus', $this->booking);
        $this->validate(['status' => ['required', Rule::enum(BookingStatus::class)]]);

        try {
            $action->handle($this->booking, BookingStatus::from($this->status));
            Flux::toast(variant: 'success', text: __('Status changed to :status.', ['status' => BookingStatus::from($this->status)->label()]));
        } catch (ValidationException $e) {
            $this->status = $this->booking->status->value;
            Flux::toast(variant: 'danger', text: $e->validator->errors()->first());
        }

        $this->booking->refresh();
    }

    public function recordPayment(RecordPayment $action): void
    {
        $this->authorize('record', [\App\Models\Payment::class, $this->booking]);
        $data = $this->paymentForm->validate();

        try {
            $payment = $action->handle($this->booking, auth()->user(), $data);
        } catch (ValidationException $e) {
            $this->addError('paymentForm.amount', $e->validator->errors()->first());

            return;
        }

        Flux::modal('record-payment')->close();
        Flux::toast(variant: 'success', text: __('Payment recorded. Receipt :no issued.', ['no' => $payment->receipt_no]));
        $this->booking->refresh();
        $this->status = $this->booking->status->value;
    }
}; ?>

<div class="flex max-w-4xl flex-col gap-6">
    @php($booking->loadMissing(['user', 'timeSlot.facility', 'payment.recorder']))
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('admin.bookings.index')" wire:navigate>{{ __('Bookings') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $booking->booking_code }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-wrap items-center gap-3">
        <flux:heading size="xl" level="1" class="font-mono">{{ $booking->booking_code }}</flux:heading>
        <flux:badge :color="$booking->status->color()" data-test="booking-status">{{ $booking->status->label() }}</flux:badge>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <flux:card class="flex flex-col gap-3">
            <flux:heading>{{ __('Booking') }}</flux:heading>
            <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                <dt class="text-zinc-500">{{ __('User') }}</dt>
                <dd><flux:link :href="route('admin.users.show', $booking->user)" wire:navigate>{{ $booking->user->name }}</flux:link> · {{ $booking->user->role->label() }}</dd>
                <dt class="text-zinc-500">{{ __('Email') }}</dt><dd>{{ $booking->user->email }}</dd>
                <dt class="text-zinc-500">{{ __('Phone') }}</dt><dd>{{ $booking->user->phone ?? '—' }}</dd>
                <dt class="text-zinc-500">{{ __('Facility') }}</dt><dd>{{ $booking->timeSlot->facility->name }}</dd>
                <dt class="text-zinc-500">{{ __('Slot') }}</dt><dd>{{ $booking->timeSlot->slot_date->format('D, j M Y') }}, {{ $booking->timeSlot->timeRange() }}</dd>
                <dt class="text-zinc-500">{{ __('Booked on') }}</dt><dd>{{ $booking->created_at->format('j M Y, H:i') }}</dd>
                @if ($booking->cancelled_at)
                    <dt class="text-zinc-500">{{ __('Cancelled') }}</dt><dd>{{ $booking->cancelled_at->format('j M Y, H:i') }} · {{ $booking->cancel_reason ?? '—' }}</dd>
                @endif
            </dl>

            <form wire:submit="updateStatus" class="mt-2 flex items-end gap-2">
                <flux:select wire:model="status" :label="__('Change status')" class="max-w-48">
                    @foreach (\App\Enums\BookingStatus::cases() as $s)
                        <flux:select.option :value="$s->value">{{ $s->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:button type="submit">{{ __('Update') }}</flux:button>
            </form>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>{{ __('Payment') }}</flux:heading>
            @if (! $booking->needsPayment())
                <flux:text>{{ __('Free booking (student).') }}</flux:text>
            @elseif ($booking->isPaid())
                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                    <dt class="text-zinc-500">{{ __('Amount') }}</dt><dd>RM{{ number_format($booking->payment->amount, 2) }}</dd>
                    <dt class="text-zinc-500">{{ __('Method') }}</dt><dd>{{ $booking->payment->method->label() }}</dd>
                    @if ($booking->payment->reference_no)
                        <dt class="text-zinc-500">{{ __('Reference') }}</dt><dd class="font-mono">{{ $booking->payment->reference_no }}</dd>
                    @endif
                    <dt class="text-zinc-500">{{ __('Receipt') }}</dt><dd class="font-mono">{{ $booking->payment->receipt_no }}</dd>
                    <dt class="text-zinc-500">{{ __('Recorded by') }}</dt><dd>{{ $booking->payment->recorder?->name ?? '—' }}</dd>
                </dl>
                <flux:button size="sm" icon="receipt-percent" :href="route('payments.receipt', $booking->payment)" wire:navigate class="self-start">{{ __('View receipt') }}</flux:button>
            @else
                <flux:text>
                    {{ __('Amount due') }}: <span class="font-medium">RM{{ number_format($booking->amount_due, 2) }}</span>
                    @if ($booking->payment_deadline)
                        <br>{{ __('Deadline') }}: {{ $booking->payment_deadline->format('j M Y, H:i') }}
                    @endif
                </flux:text>
                @can('record', [\App\Models\Payment::class, $booking])
                    @if ($booking->status !== \App\Enums\BookingStatus::Cancelled)
                        <flux:modal.trigger name="record-payment">
                            <flux:button variant="primary" icon="banknotes" class="self-start">{{ __('Record payment') }}</flux:button>
                        </flux:modal.trigger>
                    @endif
                @endcan
            @endif
        </flux:card>
    </div>

    @include('partials.record-payment-modal')
</div>
