<?php

use App\Actions\Facilities\DeactivateFacility;
use App\Actions\Facilities\SaveFacility;
use App\Livewire\Forms\FacilityForm;
use App\Models\Facility;
use App\Models\FacilityType;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-A2: add, edit and deactivate facilities. The list includes inactive ones.
 */
new #[Title('Manage facilities')] class extends Component {
    use WithPagination;

    public FacilityForm $form;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Locked]
    public ?int $deactivatingId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Facility::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function types()
    {
        return FacilityType::orderBy('name')->get();
    }

    #[Computed]
    public function facilities()
    {
        return Facility::query()
            ->with('facilityType')
            ->withCount('schedules')
            ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('location', 'like', "%{$this->search}%")))
            ->when($this->status !== '', fn ($q) => $q->where('is_active', $this->status === 'active'))
            ->orderByDesc('is_active')->orderBy('name')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->authorize('create', Facility::class);
        $this->form->reset();
        $this->resetValidation();
        Flux::modal('facility-form')->show();
    }

    public function edit(int $id): void
    {
        $facility = Facility::findOrFail($id);
        $this->authorize('update', $facility);
        $this->resetValidation();
        $this->form->setFacility($facility);
        Flux::modal('facility-form')->show();
    }

    public function save(SaveFacility $save): void
    {
        $this->form->facility
            ? $this->authorize('update', $this->form->facility)
            : $this->authorize('create', Facility::class);

        $isNew = $this->form->facility === null;
        $save->handle($this->form->payload(), $this->form->facility);

        Flux::modal('facility-form')->close();
        Flux::toast(variant: 'success', text: $isNew ? __('Facility added. Set its opening hours next.') : __('Facility updated.'));
        $this->form->reset();
    }

    public function confirmDeactivate(int $id): void
    {
        $this->deactivatingId = $id;
        Flux::modal('deactivate-facility')->show();
    }

    public function deactivate(DeactivateFacility $deactivate): void
    {
        $facility = Facility::findOrFail($this->deactivatingId);
        $this->authorize('delete', $facility);

        $deactivate->handle($facility);

        $this->deactivatingId = null;
        Flux::modal('deactivate-facility')->close();
        Flux::toast(variant: 'success', text: __(':name deactivated. It can no longer be booked.', ['name' => $facility->name]));
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Facilities') }}</flux:heading>
            <flux:subheading>{{ __('Courts and halls that can be booked.') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">{{ __('Add facility') }}</flux:button>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search name or location')" class="sm:max-w-xs" />
        <flux:select wire:model.live="status" class="sm:max-w-40">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
            <flux:select.option value="inactive">{{ __('Inactive') }}</flux:select.option>
        </flux:select>
    </div>

    <flux:table :paginate="$this->facilities">
        <flux:table.columns>
            <flux:table.column>{{ __('Facility') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column>{{ __('External fee') }}</flux:table.column>
            <flux:table.column>{{ __('Opening days') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->facilities as $facility)
                <flux:table.row :key="$facility->id">
                    <flux:table.cell>
                        <div class="font-medium text-zinc-800 dark:text-white">{{ $facility->name }}</div>
                        <div class="text-xs text-zinc-500">{{ $facility->location }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $facility->facilityType->name }}</flux:table.cell>
                    <flux:table.cell>RM{{ number_format($facility->external_fee, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $facility->schedules_count }}/7</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$facility->is_active ? 'green' : 'zinc'">{{ $facility->is_active ? __('Active') : __('Inactive') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown position="bottom" align="end">
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" :aria-label="__('Actions')" />
                            <flux:menu>
                                <flux:menu.item icon="pencil-square" wire:click="edit({{ $facility->id }})">{{ __('Edit') }}</flux:menu.item>
                                <flux:menu.item icon="clock" :href="route('admin.facilities.schedules.index', $facility)" wire:navigate>{{ __('Opening hours') }}</flux:menu.item>
                                <flux:menu.item icon="calendar-days" :href="route('admin.slots.index', ['facility' => $facility->id])" wire:navigate>{{ __('Time slots') }}</flux:menu.item>
                                @if ($facility->is_active)
                                    <flux:menu.separator />
                                    <flux:menu.item icon="no-symbol" variant="danger" wire:click="confirmDeactivate({{ $facility->id }})">{{ __('Deactivate') }}</flux:menu.item>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6">{{ __('No facilities found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="facility-form" class="md:w-[32rem]">
        <form wire:submit="save" class="flex flex-col gap-5">
            <flux:heading size="lg">{{ $form->facility ? __('Edit facility') : __('Add facility') }}</flux:heading>

            <flux:select wire:model="form.facility_type_id" :label="__('Type')" :placeholder="__('Choose a type')">
                @foreach ($this->types as $type)
                    <flux:select.option :value="$type->id">{{ $type->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="form.name" :label="__('Name')" placeholder="Court A" required />
                <flux:input wire:model="form.external_fee" :label="__('External fee (RM per slot)')" type="number" step="0.01" min="0" required />
            </div>

            <flux:input wire:model="form.location" :label="__('Location')" placeholder="Sports Hall, Level 1" />
            <flux:textarea wire:model="form.description" :label="__('Description')" rows="3" />
            <flux:switch wire:model="form.is_active" :label="__('Open for booking')" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="deactivate-facility" class="md:w-96">
        <div class="flex flex-col gap-5">
            <div>
                <flux:heading size="lg">{{ __('Deactivate this facility?') }}</flux:heading>
                <flux:text class="mt-1">{{ __('It will disappear from the public list and cannot be booked. Existing bookings and history are kept. You can turn it back on from Edit.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deactivate">{{ __('Deactivate') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
