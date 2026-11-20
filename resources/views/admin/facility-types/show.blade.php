@extends('layouts.master')

@section('title', $type->name)

@section('content')
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('admin.facility-types.index')">Facility types</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $type->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <flux:heading size="xl" level="1">{{ $type->name }}</flux:heading>
        <flux:button icon="pencil-square" :href="route('admin.facility-types.edit', $type)">Edit</flux:button>
    </div>

    <flux:heading size="lg">Facilities of this type</flux:heading>

    @if ($type->facilities->isEmpty())
        <flux:text>No facilities use this type yet, so it can be deleted.</flux:text>
    @else
        <ul class="flex flex-col divide-y divide-zinc-200 rounded-xl border border-zinc-200 dark:divide-zinc-700 dark:border-zinc-700">
            @foreach ($type->facilities as $facility)
                <li class="flex items-center justify-between px-4 py-3 text-sm">
                    <span>
                        @if ($loop->first)
                            <flux:badge size="sm" class="me-2">First</flux:badge>
                        @endif
                        {{ $facility->name }}
                        <span class="text-zinc-500">· {{ $facility->location ?? 'No location' }}</span>
                    </span>
                    <flux:badge size="sm" :color="$facility->is_active ? 'green' : 'zinc'">{{ $facility->is_active ? 'Active' : 'Inactive' }}</flux:badge>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
