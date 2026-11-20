<?php

use App\Actions\Bookings\CancelBooking;
use App\Enums\BookingStatus;
use App\Livewire\Forms\CancelBookingForm;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * Own booking history in Upcoming / Past / Cancelled tabs (FR-S6, FR-E5),
 * with a cancel confirmation modal (FR-S5, FR-E4).
 */
new #[Title('My bookings')] class extends Component {
    use WithPagination;

    public CancelBookingForm $cancelForm;

    protected function baseQuery()
    {
        return auth()->user()->bookings()
            ->with(['timeSlot.facility', 'payment'])
            ->join('time_slots', 'time_slots.id', '=', 'bookings.time_slot_id')
            ->select('bookings.*');
    }

    #[Computed]
    public function upcoming()
    {
        return $this->baseQuery()
            ->whereIn('bookings.status', BookingStatus::active())
            ->whereDate('time_slots.slot_date', '>=', today())
            ->orderBy('time_slots.slot_date')->orderBy('time_slots.start_time')
            ->paginate(10, pageName: 'upcomingPage');
    }

    #[Computed]
    public function past()
    {
        return $this->baseQuery()
            ->where('bookings.status', '!=', BookingStatus::Cancelled->value)
            ->where(fn ($q) => $q
                ->whereDate('time_slots.slot_date', '<', today())
                ->orWhere('bookings.status', BookingStatus::Completed->value))
            ->orderByDesc('time_slots.slot_date')->orderByDesc('time_slots.start_time')
            ->paginate(10, pageName: 'pastPage');
    }

    #[Computed]
    public function cancelled()
    {
        return $this->baseQuery()
            ->where('bookings.status', BookingStatus::Cancelled->value)
            ->orderByDesc('bookings.cancelled_at')
            ->paginate(10, pageName: 'cancelledPage');
    }

    public function cancel(CancelBooking $cancelBooking): void
    {
        $this->cancelForm->validate();

        $booking = auth()->user()->bookings()->with('timeSlot')->findOrFail($this->cancelForm->booking_id);
        $this->authorize('cancel', $booking);

        try {
            $cancelBooking->handle($booking, $this->cancelForm->reason);
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', text: $e->validator->errors()->first());

            return;
        }

        $this->cancelForm->reset();
        Flux::modal('cancel-booking')->close();
        Flux::toast(variant: 'success', text: __('Booking :code cancelled. The slot is free again.', ['code' => $booking->booking_code]));
    }
}; ?>

<div class="flex flex-col gap-6" x-data="{ tab: 'upcoming' }">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('My bookings') }}</flux:heading>
            <flux:subheading>
                @if (auth()->user()->isStudent())
                    {{ __('Students can hold up to :n upcoming bookings at a time.', ['n' => \App\Actions\Bookings\CreateBooking::STUDENT_ACTIVE_LIMIT]) }}
                @else
                    {{ __('Pay for pending bookings before the deadline, or they may be released.') }}
                @endif
            </flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('facilities.index')" wire:navigate>{{ __('Book a slot') }}</flux:button>
    </div>

    {{-- Alpine.js tabs: switching is instant, with no request to the server --}}
    <div class="flex gap-1 border-b border-zinc-200 dark:border-zinc-700" role="tablist">
        @foreach (['upcoming' => __('Upcoming'), 'past' => __('Past'), 'cancelled' => __('Cancelled')] as $key => $label)
            <button type="button" role="tab"
                x-on:click="tab = '{{ $key }}'"
                :aria-selected="tab === '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'border-zinc-800 text-zinc-900 dark:border-white dark:text-white' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                class="-mb-px border-b-2 px-4 py-2 text-sm font-medium"
                data-test="tab-{{ $key }}">
                {{ $label }}
                <flux:badge size="sm" class="ms-1">{{ $this->{$key}->total() }}</flux:badge>
            </button>
        @endforeach
    </div>

    @foreach (['upcoming', 'past', 'cancelled'] as $key)
        <div x-show="tab === '{{ $key }}'" @if ($key !== 'upcoming') x-cloak @endif role="tabpanel">
            @if ($this->{$key}->isEmpty())
                <flux:callout icon="calendar" :heading="match ($key) {
                    'upcoming' => __('No upcoming bookings.'),
                    'past' => __('No past bookings yet.'),
                    default => __('No cancelled bookings.'),
                }" />
            @else
                <flux:table :paginate="$this->{$key}">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Booking') }}</flux:table.column>
                        <flux:table.column>{{ __('Facility') }}</flux:table.column>
                        <flux:table.column>{{ __('Date & time') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                        <flux:table.column>{{ __('Amount') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->{$key} as $booking)
                            <flux:table.row :key="$key.'-'.$booking->id">
                                <flux:table.cell>
                                    <flux:link :href="route('bookings.show', $booking)" wire:navigate class="font-mono text-sm">{{ $booking->booking_code }}</flux:link>
                                </flux:table.cell>
                                <flux:table.cell>{{ $booking->timeSlot->facility->name }}</flux:table.cell>
                                <flux:table.cell>
                                    {{ $booking->timeSlot->slot_date->format('D, j M Y') }}<br>
                                    <span class="text-zinc-500">{{ $booking->timeSlot->timeRange() }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge>
                                    @if ($booking->needsPayment() && $booking->status !== \App\Enums\BookingStatus::Cancelled)
                                        <flux:badge size="sm" :color="$booking->isPaid() ? 'green' : 'amber'" class="ms-1">
                                            {{ $booking->isPaid() ? __('Paid') : __('Unpaid') }}
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $booking->needsPayment() ? 'RM'.number_format($booking->amount_due, 2) : __('Free') }}
                                    @if ($booking->payment_deadline && ! $booking->isPaid() && $booking->isActive())
                                        <br><span class="text-xs text-amber-600">{{ __('Pay by :time', ['time' => $booking->payment_deadline->format('j M, H:i')]) }}</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    @if ($booking->isPaid())
                                        <flux:button size="sm" variant="ghost" icon="receipt-percent" :href="route('payments.receipt', $booking->payment)" wire:navigate>{{ __('Receipt') }}</flux:button>
                                    @endif
                                    @can('cancel', $booking)
                                        {{-- Alpine sets which booking to cancel, then opens the modal without a server round trip --}}
                                        <flux:button size="sm" variant="ghost" icon="x-mark"
                                            x-on:click="$wire.cancelForm.booking_id = {{ $booking->id }}; $wire.cancelForm.reason = ''; $flux.modal('cancel-booking').show()"
                                            data-test="cancel-button">
                                            {{ __('Cancel') }}
                                        </flux:button>
                                    @endcan
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    @endforeach

    <flux:modal name="cancel-booking" class="md:w-96">
        <form wire:submit="cancel" class="flex flex-col gap-5">
            <div>
                <flux:heading size="lg">{{ __('Cancel this booking?') }}</flux:heading>
                <flux:text class="mt-1">{{ __('The slot will be released for others to book. This cannot be undone.') }}</flux:text>
            </div>

            <flux:textarea wire:model="cancelForm.reason" :label="__('Reason (optional)')" rows="2" maxlength="200" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Keep booking') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger" data-test="confirm-cancel-button">{{ __('Cancel booking') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
