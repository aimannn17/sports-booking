<?php

use App\Actions\Bookings\CreateBooking;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\TimeSlot;
use Flux\Flux;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/*
 * Facility details, opening hours and the slot grid for a date (FR-S2, FR-E2),
 * plus the confirm-booking modal (FR-S3, FR-E3).
 */
new #[Layout('layouts::public')] #[Title('Facility')] class extends Component {
    #[Locked]
    public Facility $facility;

    #[Url]
    public string $date = '';

    #[Locked]
    public ?int $selectedSlotId = null;

    public function mount(Facility $facility): void
    {
        $this->authorize('view', $facility);

        $this->facility = $facility->load('facilityType', 'schedules');
        $this->date = $this->validDate($this->date)->toDateString();
    }

    public function updatedDate(): void
    {
        $this->date = $this->validDate($this->date)->toDateString();
    }

    public function shiftDate(int $days): void
    {
        $this->date = $this->validDate(Carbon::parse($this->date)->addDays($days)->toDateString())->toDateString();
    }

    protected function validDate(string $value): CarbonInterface
    {
        try {
            $date = $value !== '' ? Carbon::parse($value)->startOfDay() : today();
        } catch (\Throwable) {
            $date = today();
        }

        return $date->max(today());
    }

    #[Computed]
    public function timeSlots()
    {
        return $this->facility->timeSlots()
            ->forDate($this->date)
            ->orderBy('start_time')
            ->get();
    }

    #[Computed]
    public function selectedSlot(): ?TimeSlot
    {
        return $this->selectedSlotId
            ? $this->facility->timeSlots()->find($this->selectedSlotId)
            : null;
    }

    public function selectSlot(int $slotId): void
    {
        if (! auth()->check()) {
            session()->put('url.intended', route('facilities.show', ['facility' => $this->facility, 'date' => $this->date]));
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->authorize('create', Booking::class);

        $slot = $this->facility->timeSlots()->findOrFail($slotId);

        if ($slot->status !== SlotStatus::Available || $slot->hasStarted()) {
            Flux::toast(variant: 'warning', text: __('That slot is no longer available.'));

            return;
        }

        $this->resetErrorBag();
        $this->selectedSlotId = $slot->id;
        unset($this->selectedSlot);

        Flux::modal('confirm-booking')->show();
    }

    public function book(CreateBooking $createBooking): void
    {
        $this->authorize('create', Booking::class);

        $slot = $this->selectedSlot;
        abort_if($slot === null, 404);

        try {
            $booking = $createBooking->handle(auth()->user(), $slot);
        } catch (ValidationException $e) {
            $this->addError('slot', $e->validator->errors()->first());
            unset($this->timeSlots);

            return;
        }

        session()->flash('booking-created', true);
        $this->redirectRoute('bookings.show', $booking, navigate: true);
    }
}; ?>

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('facilities.index')" wire:navigate>{{ __('Facilities') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $facility->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-wrap items-center gap-3">
            <flux:heading size="xl" level="1">{{ $facility->name }}</flux:heading>
            <flux:badge>{{ $facility->facilityType->name }}</flux:badge>
            @unless ($facility->is_active)
                <flux:badge color="red">{{ __('Inactive') }}</flux:badge>
            @endunless
        </div>

        <flux:text>
            @if ($facility->location) {{ $facility->location }} · @endif
            {{ __('Free for students') }} ·
            <span class="font-medium" data-test="external-fee">RM{{ number_format($facility->external_fee, 2) }} {{ __('for external users') }}</span>
        </flux:text>

        @if ($facility->description)
            <flux:text class="max-w-2xl">{{ $facility->description }}</flux:text>
        @endif
    </div>

    <div class="grid gap-8 lg:grid-cols-[1fr_16rem]">
        {{-- Slot picker --}}
        <section class="flex flex-col gap-4">
            <div class="flex flex-wrap items-end gap-2">
                <flux:button icon="chevron-left" wire:click="shiftDate(-1)" :disabled="$date === today()->toDateString()" :aria-label="__('Previous day')" />
                <flux:input type="date" wire:model.live="date" :min="today()->toDateString()" class="max-w-44" :aria-label="__('Date')" />
                <flux:button icon="chevron-right" wire:click="shiftDate(1)" :aria-label="__('Next day')" />
                <flux:text class="ms-2 pb-2">{{ \Illuminate\Support\Carbon::parse($date)->format('l, j F Y') }}</flux:text>
            </div>

            <div class="flex flex-wrap gap-4 text-sm">
                <span class="flex items-center gap-1"><span class="size-3 rounded-sm bg-green-500"></span> {{ __('Available') }}</span>
                <span class="flex items-center gap-1"><span class="size-3 rounded-sm bg-zinc-400"></span> {{ __('Booked') }}</span>
                <span class="flex items-center gap-1"><span class="size-3 rounded-sm bg-red-500"></span> {{ __('Blocked') }}</span>
            </div>

            @if ($this->timeSlots->isEmpty())
                <flux:callout icon="calendar" :heading="__('No slots on this day.')" :text="__('The facility may be closed, or slots have not been opened yet. Try another date.')" />
            @else
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4" wire:loading.class="opacity-60">
                    @foreach ($this->timeSlots as $slot)
                        @php($bookable = $slot->status === \App\Enums\SlotStatus::Available && ! $slot->hasStarted())
                        <button type="button"
                            wire:key="slot-{{ $slot->id }}"
                            @if ($bookable) wire:click="selectSlot({{ $slot->id }})" @else disabled @endif
                            data-test="slot-{{ $slot->status->value }}"
                            @class([
                                'rounded-lg border px-3 py-3 text-start text-sm transition',
                                'border-green-600 bg-green-50 text-green-900 hover:bg-green-100 dark:bg-green-950 dark:text-green-100 dark:hover:bg-green-900 cursor-pointer' => $bookable,
                                'border-zinc-300 bg-zinc-100 text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 cursor-not-allowed' => ! $bookable && $slot->status !== \App\Enums\SlotStatus::Blocked,
                                'border-red-300 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300 cursor-not-allowed' => $slot->status === \App\Enums\SlotStatus::Blocked,
                            ])>
                            <span class="block font-semibold">{{ $slot->timeRange() }}</span>
                            <span class="block text-xs">{{ $slot->hasStarted() && $slot->status === \App\Enums\SlotStatus::Available ? __('Passed') : $slot->status->label() }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Opening hours --}}
        <aside class="flex flex-col gap-2">
            <flux:heading>{{ __('Opening hours') }}</flux:heading>
            @forelse ($facility->schedules as $schedule)
                <div class="flex justify-between text-sm" wire:key="schedule-{{ $schedule->id }}">
                    <span>{{ $schedule->dayName() }}</span>
                    <span class="text-zinc-500">{{ substr($schedule->open_time, 0, 5) }} – {{ substr($schedule->close_time, 0, 5) }}</span>
                </div>
            @empty
                <flux:text>{{ __('Not set yet.') }}</flux:text>
            @endforelse
        </aside>
    </div>

    {{-- Confirm booking modal --}}
    <flux:modal name="confirm-booking" class="md:w-96">
        @if ($this->selectedSlot)
            <div class="flex flex-col gap-5">
                <div>
                    <flux:heading size="lg">{{ __('Confirm booking') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Check the details before you book.') }}</flux:text>
                </div>

                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                    <dt class="text-zinc-500">{{ __('Facility') }}</dt><dd>{{ $facility->name }}</dd>
                    <dt class="text-zinc-500">{{ __('Date') }}</dt><dd>{{ $this->selectedSlot->slot_date->format('D, j M Y') }}</dd>
                    <dt class="text-zinc-500">{{ __('Time') }}</dt><dd>{{ $this->selectedSlot->timeRange() }}</dd>
                    <dt class="text-zinc-500">{{ __('Fee') }}</dt>
                    <dd class="font-medium">
                        @if (auth()->user()?->isExternal() && (float) $facility->external_fee > 0)
                            RM{{ number_format($facility->external_fee, 2) }}
                        @else
                            {{ __('Free') }}
                        @endif
                    </dd>
                </dl>

                @if (auth()->user()?->isExternal() && (float) $facility->external_fee > 0)
                    <flux:callout icon="information-circle" color="amber"
                        :text="__('Your booking stays pending until you pay at the sports centre counter or by bank transfer. Unpaid bookings are released after the payment deadline.')" />
                @endif

                @error('slot')
                    <flux:callout variant="danger" icon="x-circle" :heading="$message" data-test="booking-error" />
                @enderror

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Back') }}</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" wire:click="book" data-test="confirm-booking-button">{{ __('Book now') }}</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
