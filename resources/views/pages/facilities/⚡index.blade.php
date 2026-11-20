<?php

use App\Models\Facility;
use App\Models\FacilityType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-S2 / FR-E2: anyone can browse active facilities and their fees.
 */
new #[Layout('layouts::public')] #[Title('Facilities')] class extends Component {
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $type = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type'], true)) {
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
            ->active()
            ->with('facilityType')
            ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('location', 'like', "%{$this->search}%")))
            ->when($this->type !== '', fn ($q) => $q->where('facility_type_id', $this->type))
            ->orderBy('facility_type_id')
            ->orderBy('name')
            ->paginate(9);
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Facilities') }}</flux:heading>
        <flux:subheading>{{ __('Pick a facility to see its free slots.') }}</flux:subheading>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by name or location')" class="sm:max-w-xs" />
        <flux:select wire:model.live="type" class="sm:max-w-48">
            <flux:select.option value="">{{ __('All types') }}</flux:select.option>
            @foreach ($this->types as $t)
                <flux:select.option :value="$t->id">{{ $t->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    @if ($this->facilities->isEmpty())
        <flux:callout icon="magnifying-glass" :heading="__('No facilities match your search.')" />
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->facilities as $facility)
                <a href="{{ route('facilities.show', $facility) }}" wire:navigate wire:key="facility-{{ $facility->id }}"
                   class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-5 transition hover:border-zinc-400 dark:border-zinc-700 dark:hover:border-zinc-500"
                   data-test="facility-card">
                    <div class="flex items-start justify-between gap-2">
                        <flux:heading size="lg">{{ $facility->name }}</flux:heading>
                        <flux:badge size="sm">{{ $facility->facilityType->name }}</flux:badge>
                    </div>
                    @if ($facility->location)
                        <flux:text class="flex items-center gap-1"><flux:icon.map-pin variant="micro" /> {{ $facility->location }}</flux:text>
                    @endif
                    <flux:text class="text-sm">
                        {{ __('Free for students') }} ·
                        {{ (float) $facility->external_fee > 0 ? 'RM'.number_format($facility->external_fee, 2).' '.__('per slot for others') : __('free for everyone') }}
                    </flux:text>
                </a>
            @endforeach
        </div>

        {{ $this->facilities->links() }}
    @endif
</div>
