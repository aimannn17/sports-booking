<?php

use App\Actions\Facilities\SaveFacilitySchedule;
use App\Livewire\Forms\ScheduleForm;
use App\Models\Facility;
use App\Models\FacilitySchedule;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/*
 * FR-A3: a facility's opening days, hours and slot length. One row per weekday.
 */
new #[Title('Opening hours')] class extends Component {
    #[Locked]
    public Facility $facility;

    public ScheduleForm $form;

    public function mount(Facility $facility): void
    {
        $this->authorize('manageSchedule', $facility);
        $this->facility = $facility;
    }

    #[Computed]
    public function schedules()
    {
        return $this->facility->schedules()->get()->keyBy('day_of_week');
    }

    public function edit(int $day): void
    {
        $this->resetValidation();
        $schedule = $this->schedules->get($day);

        if ($schedule) {
            $this->form->setSchedule($schedule);
        } else {
            $this->form->reset();
            $this->form->day_of_week = $day;
        }

        Flux::modal('schedule-form')->show();
    }

    public function save(SaveFacilitySchedule $save): void
    {
        $this->authorize('manageSchedule', $this->facility);

        $save->handle($this->facility, $this->form->validate());

        unset($this->schedules);
        Flux::modal('schedule-form')->close();
        Flux::toast(variant: 'success', text: __(':day saved.', ['day' => FacilitySchedule::DAYS[$this->form->day_of_week]]));
    }

    public function close(int $day): void
    {
        $schedule = $this->schedules->get($day);
        abort_if($schedule === null, 404);
        $this->authorize('delete', $schedule);

        $schedule->delete();

        unset($this->schedules);
        Flux::toast(text: __(':day marked as closed. Existing slots are not removed.', ['day' => FacilitySchedule::DAYS[$day]]));
    }
}; ?>

<div class="flex max-w-3xl flex-col gap-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('admin.facilities.index')" wire:navigate>{{ __('Facilities') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $facility->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Opening hours') }}</flux:heading>
            <flux:subheading>{{ __('Slots are generated from these hours. Change them, then generate slots for the coming days.') }}</flux:subheading>
        </div>
        <flux:button icon="calendar-days" :href="route('admin.slots.index', ['facility' => $facility->id])" wire:navigate>{{ __('Time slots') }}</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Day') }}</flux:table.column>
            <flux:table.column>{{ __('Hours') }}</flux:table.column>
            <flux:table.column>{{ __('Slot length') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach (\App\Models\FacilitySchedule::DAYS as $day => $dayName)
                @php($schedule = $this->schedules->get($day))
                <flux:table.row :key="'day-'.$day">
                    <flux:table.cell variant="strong">{{ $dayName }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($schedule)
                            {{ substr($schedule->open_time, 0, 5) }} – {{ substr($schedule->close_time, 0, 5) }}
                        @else
                            <flux:badge size="sm" color="zinc">{{ __('Closed') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $schedule ? $schedule->slot_minutes.' min' : '—' }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $day }})">{{ $schedule ? __('Edit') : __('Open') }}</flux:button>
                        @if ($schedule)
                            <flux:button size="sm" variant="ghost" icon="no-symbol" wire:click="close({{ $day }})"
                                wire:confirm="{{ __('Mark :day as closed?', ['day' => $dayName]) }}">{{ __('Close') }}</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="schedule-form" class="md:w-96">
        <form wire:submit="save" class="flex flex-col gap-5">
            <flux:heading size="lg">{{ \App\Models\FacilitySchedule::DAYS[$form->day_of_week ?? 0] }}</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.open_time" type="time" :label="__('Opens')" required />
                <flux:input wire:model="form.close_time" type="time" :label="__('Closes')" required />
            </div>
            <flux:select wire:model="form.slot_minutes" :label="__('Slot length')">
                @foreach ([30, 60, 90, 120] as $m)
                    <flux:select.option :value="$m">{{ $m }} {{ __('minutes') }}</flux:select.option>
                @endforeach
            </flux:select>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
