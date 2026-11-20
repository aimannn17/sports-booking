<?php

use App\Actions\Slots\GenerateTimeSlots;
use App\Actions\Slots\SetSlotBlocked;
use App\Livewire\Forms\GenerateSlotsForm;
use App\Models\Facility;
use App\Models\TimeSlot;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-A4: list slots by facility and date, generate them from the schedule, block and unblock.
 */
new #[Title('Time slots')] class extends Component {
    use WithPagination;

    public GenerateSlotsForm $generate;

    #[Url(except: '')]
    public string $facility = '';

    #[Url]
    public string $date = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', TimeSlot::class);

        if ($this->date === '') {
            $this->date = today()->toDateString();
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['facility', 'date', 'status'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function facilities()
    {
        return Facility::orderBy('name')->get(['id', 'name', 'is_active']);
    }

    #[Computed]
    public function timeSlots()
    {
        return TimeSlot::query()
            ->with(['facility', 'activeBooking.user'])
            ->when($this->facility !== '', fn ($q) => $q->where('facility_id', $this->facility))
            ->when($this->date !== '', fn ($q) => $q->forDate($this->date))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->orderBy('slot_date')->orderBy('facility_id')->orderBy('start_time')
            ->paginate(20);
    }

    public function openGenerate(): void
    {
        $this->authorize('generate', TimeSlot::class);
        $this->generate->reset();
        $this->resetValidation();
        $this->generate->facility_id = $this->facility !== '' ? (int) $this->facility : null;
        $this->generate->from = today()->toDateString();
        $this->generate->to = today()->addDays(6)->toDateString();
        Flux::modal('generate-slots')->show();
    }

    public function runGenerate(GenerateTimeSlots $generator): void
    {
        $this->authorize('generate', TimeSlot::class);
        $data = $this->generate->validate();

        $facility = Facility::findOrFail($data['facility_id']);

        if ($facility->schedules()->doesntExist()) {
            $this->addError('generate.facility_id', __('Set opening hours for this facility first.'));

            return;
        }

        $count = $generator->handle($facility, $data['from'], $data['to']);

        Flux::modal('generate-slots')->close();
        Flux::toast(variant: 'success', text: trans_choice('{0} No new slots. They already exist.|{1} 1 slot created.|[2,*] :count slots created.', $count));

        $this->facility = (string) $facility->id;
        $this->date = $data['from'];
        $this->resetPage();
    }

    public function setBlocked(int $slotId, bool $blocked, SetSlotBlocked $action): void
    {
        $slot = TimeSlot::findOrFail($slotId);
        $this->authorize('block', $slot);

        try {
            $action->handle($slot, $blocked);
            Flux::toast(text: $blocked ? __('Slot blocked.') : __('Slot unblocked.'));
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', text: $e->validator->errors()->first());
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Time slots') }}</flux:heading>
            <flux:subheading>{{ __('Generate slots from opening hours, or block one for repairs or events.') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="sparkles" wire:click="openGenerate">{{ __('Generate slots') }}</flux:button>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:select wire:model.live="facility" class="sm:max-w-56">
            <flux:select.option value="">{{ __('All facilities') }}</flux:select.option>
            @foreach ($this->facilities as $f)
                <flux:select.option :value="$f->id">{{ $f->name }}{{ $f->is_active ? '' : ' ('.__('inactive').')' }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:input type="date" wire:model.live="date" class="sm:max-w-44" />
        <flux:select wire:model.live="status" class="sm:max-w-40">
            <flux:select.option value="">{{ __('Any status') }}</flux:select.option>
            @foreach (\App\Enums\SlotStatus::cases() as $s)
                <flux:select.option :value="$s->value">{{ $s->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table :paginate="$this->timeSlots">
        <flux:table.columns>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Time') }}</flux:table.column>
            <flux:table.column>{{ __('Facility') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Booked by') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->timeSlots as $slot)
                <flux:table.row :key="$slot->id">
                    <flux:table.cell>{{ $slot->slot_date->format('D, j M') }}</flux:table.cell>
                    <flux:table.cell>{{ $slot->timeRange() }}</flux:table.cell>
                    <flux:table.cell>{{ $slot->facility->name }}</flux:table.cell>
                    <flux:table.cell><flux:badge size="sm" :color="$slot->status->color()">{{ $slot->status->label() }}</flux:badge></flux:table.cell>
                    <flux:table.cell>
                        @if ($slot->activeBooking)
                            <flux:link :href="route('admin.bookings.show', $slot->activeBooking)" wire:navigate>{{ $slot->activeBooking->user->name }}</flux:link>
                        @else
                            —
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        @if ($slot->status === \App\Enums\SlotStatus::Available)
                            <flux:button size="sm" variant="ghost" icon="lock-closed" wire:click="setBlocked({{ $slot->id }}, true)">{{ __('Block') }}</flux:button>
                        @elseif ($slot->status === \App\Enums\SlotStatus::Blocked)
                            <flux:button size="sm" variant="ghost" icon="lock-open" wire:click="setBlocked({{ $slot->id }}, false)">{{ __('Unblock') }}</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6">{{ __('No slots match. Try another date, or generate slots.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="generate-slots" class="md:w-96">
        <form wire:submit="runGenerate" class="flex flex-col gap-5">
            <div>
                <flux:heading size="lg">{{ __('Generate slots') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Creates slots from the opening hours. Existing slots are kept.') }}</flux:text>
            </div>
            <flux:select wire:model="generate.facility_id" :label="__('Facility')" :placeholder="__('Choose a facility')">
                @foreach ($this->facilities->where('is_active', true) as $f)
                    <flux:select.option :value="$f->id">{{ $f->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="generate.from" type="date" :label="__('From')" required />
                <flux:input wire:model="generate.to" type="date" :label="__('To')" required />
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Generate') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
