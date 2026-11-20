<?php

use App\Models\Facility;
use App\Models\FacilityType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public')] #[Title('Welcome')] class extends Component {
    #[Computed]
    public function types()
    {
        return FacilityType::query()
            ->whereHas('facilities', fn ($q) => $q->active())
            ->withCount(['facilities' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function facilityCount(): int
    {
        return Facility::active()->count();
    }
}; ?>

<div class="mx-auto flex max-w-4xl flex-col gap-10 py-6">
    <section class="flex flex-col gap-4">
        <flux:heading size="xl" level="1">{{ __('Book a court in seconds') }}</flux:heading>
        <flux:text class="max-w-2xl text-base">
            {{ __('See which courts are free right now and book online. No more WhatsApp messages, no wasted trips. GMI students play for free; alumni and the public pay a small fee per slot.') }}
        </flux:text>
        <div class="flex flex-wrap gap-3">
            <flux:button variant="primary" icon="magnifying-glass" :href="route('facilities.index')" wire:navigate>
                {{ __('Browse :count facilities', ['count' => $this->facilityCount]) }}
            </flux:button>
            @guest
                <flux:button :href="route('register')" wire:navigate>{{ __('Create an account') }}</flux:button>
            @endguest
        </div>
    </section>

    @if ($this->types->isNotEmpty())
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->types as $type)
                <a href="{{ route('facilities.index', ['type' => $type->id]) }}" wire:navigate wire:key="type-{{ $type->id }}"
                   class="rounded-xl border border-zinc-200 p-5 transition hover:border-zinc-400 dark:border-zinc-700 dark:hover:border-zinc-500">
                    <flux:heading size="lg">{{ $type->name }}</flux:heading>
                    <flux:text>{{ trans_choice(':count facility|:count facilities', $type->facilities_count) }}</flux:text>
                </a>
            @endforeach
        </section>
    @endif

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-zinc-50 p-5 dark:bg-zinc-900">
            <flux:heading>{{ __('1. Pick a court and date') }}</flux:heading>
            <flux:text>{{ __('Green slots are free to book.') }}</flux:text>
        </div>
        <div class="rounded-xl bg-zinc-50 p-5 dark:bg-zinc-900">
            <flux:heading>{{ __('2. Confirm your booking') }}</flux:heading>
            <flux:text>{{ __('Students get it confirmed at once. Others pay at the counter or by transfer.') }}</flux:text>
        </div>
        <div class="rounded-xl bg-zinc-50 p-5 dark:bg-zinc-900">
            <flux:heading>{{ __('3. Turn up and play') }}</flux:heading>
            <flux:text>{{ __('Plans changed? Cancel any time before the slot starts.') }}</flux:text>
        </div>
    </section>
</div>
