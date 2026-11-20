<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-A5: every booking, searchable and filterable by status.
 */
new #[Title('All bookings')] class extends Component {
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $date = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Booking::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'date'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function bookings()
    {
        return Booking::query()
            ->with(['user', 'timeSlot.facility', 'payment'])
            ->join('time_slots', 'time_slots.id', '=', 'bookings.time_slot_id')
            ->select('bookings.*')
            ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('bookings.booking_code', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('student_id', 'like', "%{$this->search}%"))))
            ->when($this->status !== '', fn ($q) => $q->where('bookings.status', $this->status))
            ->when($this->date !== '', fn ($q) => $q->whereDate('time_slots.slot_date', $this->date))
            ->orderByDesc('time_slots.slot_date')->orderBy('time_slots.start_time')
            ->paginate(15);
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Bookings') }}</flux:heading>
        <flux:subheading>{{ __('Every booking, newest slot first.') }}</flux:subheading>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Code, name, email or student ID')" class="sm:max-w-xs" />
        <flux:select wire:model.live="status" class="sm:max-w-40">
            <flux:select.option value="">{{ __('Any status') }}</flux:select.option>
            @foreach (\App\Enums\BookingStatus::cases() as $s)
                <flux:select.option :value="$s->value">{{ $s->label() }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:input type="date" wire:model.live="date" class="sm:max-w-44" />
    </div>

    <flux:table :paginate="$this->bookings">
        <flux:table.columns>
            <flux:table.column>{{ __('Code') }}</flux:table.column>
            <flux:table.column>{{ __('User') }}</flux:table.column>
            <flux:table.column>{{ __('Facility') }}</flux:table.column>
            <flux:table.column>{{ __('Slot') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Payment') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->bookings as $booking)
                <flux:table.row :key="$booking->id">
                    <flux:table.cell><flux:link :href="route('admin.bookings.show', $booking)" wire:navigate class="font-mono text-sm">{{ $booking->booking_code }}</flux:link></flux:table.cell>
                    <flux:table.cell>
                        {{ $booking->user->name }}
                        <div class="text-xs text-zinc-500">{{ $booking->user->role->label() }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $booking->timeSlot->facility->name }}</flux:table.cell>
                    <flux:table.cell>{{ $booking->timeSlot->slot_date->format('j M Y') }}<div class="text-xs text-zinc-500">{{ $booking->timeSlot->timeRange() }}</div></flux:table.cell>
                    <flux:table.cell><flux:badge size="sm" :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge></flux:table.cell>
                    <flux:table.cell>
                        @if (! $booking->needsPayment())
                            <span class="text-zinc-500">{{ __('Free') }}</span>
                        @else
                            RM{{ number_format($booking->amount_due, 2) }}
                            <flux:badge size="sm" :color="$booking->isPaid() ? 'green' : 'amber'" class="ms-1">{{ $booking->isPaid() ? __('Paid') : __('Unpaid') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6">{{ __('No bookings found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
