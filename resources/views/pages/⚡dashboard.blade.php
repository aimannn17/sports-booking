<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

/*
 * Students and external users see their upcoming bookings;
 * admins see today's activity and what needs attention.
 */
new #[Title('Dashboard')] class extends Component {
    #[Computed]
    public function upcoming()
    {
        return auth()->user()->bookings()
            ->active()->upcoming()
            ->with(['timeSlot.facility', 'payment'])
            ->join('time_slots', 'time_slots.id', '=', 'bookings.time_slot_id')
            ->select('bookings.*')
            ->orderBy('time_slots.slot_date')->orderBy('time_slots.start_time')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function unpaidCount(): int
    {
        return auth()->user()->bookings()
            ->where('status', BookingStatus::Pending)
            ->where('amount_due', '>', 0)
            ->count();
    }

    #[Computed]
    public function adminStats(): array
    {
        return [
            'today' => Booking::active()->whereHas('timeSlot', fn ($q) => $q->whereDate('slot_date', today()))->count(),
            'unpaid' => Booking::where('status', BookingStatus::Pending)->where('amount_due', '>', 0)->count(),
            'revenue' => (float) Payment::where('status', PaymentStatus::Paid)->where('paid_at', '>=', now()->startOfMonth())->sum('amount'),
            'facilities' => Facility::active()->count(),
        ];
    }

    #[Computed]
    public function todaysBookings()
    {
        return Booking::active()
            ->with(['timeSlot.facility', 'user'])
            ->join('time_slots', 'time_slots.id', '=', 'bookings.time_slot_id')
            ->select('bookings.*')
            ->whereDate('time_slots.slot_date', today())
            ->orderBy('time_slots.start_time')
            ->limit(10)
            ->get();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Hi, :name', ['name' => auth()->user()->name]) }}</flux:heading>
        <flux:subheading>
            <flux:badge size="sm" :color="auth()->user()->role->color()">{{ auth()->user()->role->label() }}</flux:badge>
        </flux:subheading>
    </div>

    @if (auth()->user()->isAdmin())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['label' => __('Active bookings today'), 'value' => $this->adminStats['today'], 'href' => route('admin.bookings.index')],
                ['label' => __('Unpaid external bookings'), 'value' => $this->adminStats['unpaid'], 'href' => route('admin.payments.index')],
                ['label' => __('Collected this month'), 'value' => 'RM'.number_format($this->adminStats['revenue'], 2), 'href' => route('admin.payments.index', ['filter' => 'paid'])],
                ['label' => __('Active facilities'), 'value' => $this->adminStats['facilities'], 'href' => route('admin.facilities.index')],
            ] as $stat)
                <a href="{{ $stat['href'] }}" wire:navigate class="rounded-xl border border-zinc-200 p-5 transition hover:border-zinc-400 dark:border-zinc-700 dark:hover:border-zinc-500">
                    <flux:text>{{ $stat['label'] }}</flux:text>
                    <div class="mt-1 text-2xl font-semibold">{{ $stat['value'] }}</div>
                </a>
            @endforeach
        </div>

        <section class="flex flex-col gap-3">
            <flux:heading size="lg">{{ __("Today's bookings") }}</flux:heading>
            @if ($this->todaysBookings->isEmpty())
                <flux:callout icon="calendar" :heading="__('Nothing booked for today.')" />
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Time') }}</flux:table.column>
                        <flux:table.column>{{ __('Facility') }}</flux:table.column>
                        <flux:table.column>{{ __('User') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->todaysBookings as $booking)
                            <flux:table.row :key="$booking->id">
                                <flux:table.cell>{{ $booking->timeSlot->timeRange() }}</flux:table.cell>
                                <flux:table.cell>{{ $booking->timeSlot->facility->name }}</flux:table.cell>
                                <flux:table.cell><flux:link :href="route('admin.bookings.show', $booking)" wire:navigate>{{ $booking->user->name }}</flux:link></flux:table.cell>
                                <flux:table.cell><flux:badge size="sm" :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge></flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </section>
    @else
        @if ($this->unpaidCount > 0)
            <flux:callout icon="banknotes" color="amber" data-test="unpaid-callout"
                :heading="trans_choice(':count booking is waiting for payment.|:count bookings are waiting for payment.', $this->unpaidCount)"
                :text="__('Pay at the sports centre counter or by bank transfer before the deadline.')" />
        @endif

        <section class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Your next bookings') }}</flux:heading>
                <flux:button size="sm" variant="primary" icon="plus" :href="route('facilities.index')" wire:navigate>{{ __('Book a slot') }}</flux:button>
            </div>

            @if ($this->upcoming->isEmpty())
                <flux:callout icon="calendar" :heading="__('No upcoming bookings.')" :text="__('Find a free court and book it in a few taps.')" />
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->upcoming as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" wire:navigate wire:key="upcoming-{{ $booking->id }}"
                           class="flex flex-col gap-1 rounded-xl border border-zinc-200 p-4 transition hover:border-zinc-400 dark:border-zinc-700 dark:hover:border-zinc-500">
                            <div class="flex items-center justify-between">
                                <flux:heading>{{ $booking->timeSlot->facility->name }}</flux:heading>
                                <flux:badge size="sm" :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge>
                            </div>
                            <flux:text>{{ $booking->timeSlot->slot_date->format('D, j M') }} · {{ $booking->timeSlot->timeRange() }}</flux:text>
                            <flux:text class="font-mono text-xs">{{ $booking->booking_code }}</flux:text>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
