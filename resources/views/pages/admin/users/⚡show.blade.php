<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-A8: a user's profile and booking history.
 */
new #[Title('User')] class extends Component {
    use WithPagination;

    #[Locked]
    public User $user;

    public function mount(User $user): void
    {
        $this->authorize('view', $user);
        $this->authorize('viewAny', User::class);
        $this->user = $user;
    }

    #[Computed]
    public function bookings()
    {
        return $this->user->bookings()
            ->with(['timeSlot.facility', 'payment'])
            ->latest()
            ->paginate(10);
    }
}; ?>

<div class="flex max-w-4xl flex-col gap-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('admin.users.index')" wire:navigate>{{ __('Users') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $user->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-wrap items-center gap-3">
        <flux:avatar :name="$user->name" :initials="$user->initials()" />
        <flux:heading size="xl" level="1">{{ $user->name }}</flux:heading>
        <flux:badge :color="$user->role->color()">{{ $user->role->label() }}</flux:badge>
    </div>

    <dl class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-sm">
        <dt class="text-zinc-500">{{ __('Email') }}</dt><dd>{{ $user->email }}</dd>
        <dt class="text-zinc-500">{{ __('Phone') }}</dt><dd>{{ $user->phone ?? '—' }}</dd>
        @if ($user->student_id)
            <dt class="text-zinc-500">{{ __('Student ID') }}</dt><dd>{{ $user->student_id }}</dd>
        @endif
        <dt class="text-zinc-500">{{ __('Joined') }}</dt><dd>{{ $user->created_at->format('j M Y') }}</dd>
    </dl>

    <flux:heading size="lg">{{ __('Booking history') }}</flux:heading>
    <flux:table :paginate="$this->bookings">
        <flux:table.columns>
            <flux:table.column>{{ __('Code') }}</flux:table.column>
            <flux:table.column>{{ __('Facility') }}</flux:table.column>
            <flux:table.column>{{ __('Slot') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Amount') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->bookings as $booking)
                <flux:table.row :key="$booking->id">
                    <flux:table.cell><flux:link :href="route('admin.bookings.show', $booking)" wire:navigate class="font-mono text-sm">{{ $booking->booking_code }}</flux:link></flux:table.cell>
                    <flux:table.cell>{{ $booking->timeSlot->facility->name }}</flux:table.cell>
                    <flux:table.cell>{{ $booking->timeSlot->slot_date->format('j M Y') }}, {{ $booking->timeSlot->timeRange() }}</flux:table.cell>
                    <flux:table.cell><flux:badge size="sm" :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge></flux:table.cell>
                    <flux:table.cell>{{ $booking->needsPayment() ? 'RM'.number_format($booking->amount_due, 2) : __('Free') }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="5">{{ __('No bookings yet.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
