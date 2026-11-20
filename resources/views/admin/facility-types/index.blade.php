@extends('layouts.master')

@section('title', 'Facility types')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Facility types</flux:heading>
            <flux:subheading>Group facilities by sport, for example Badminton or Futsal.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.facility-types.create')" data-test="add-type">Add type</flux:button>
    </div>

    {{-- A failed delete (type still in use) comes back as a validation error --}}
    @error('type')
        <flux:callout variant="danger" icon="x-circle" :heading="$message" data-test="delete-error" />
    @enderror

    {{-- Plain GET form: the search term goes into the URL (?search=...) --}}
    <form method="GET" action="{{ route('admin.facility-types.index') }}" class="flex max-w-sm gap-2">
        <flux:input name="search" :value="request('search')" icon="magnifying-glass" placeholder="Search types" />
        <flux:button type="submit">Search</flux:button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 text-zinc-500 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-3 font-medium">#</th>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Facilities</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($types as $type)
                    {{-- $loop->even stripes alternate rows (Topic 4) --}}
                    <tr @class([
                        'border-b border-zinc-100 last:border-0 dark:border-zinc-800',
                        'bg-zinc-50 dark:bg-zinc-900/60' => $loop->even,
                    ])>
                        <td class="px-4 py-3 text-zinc-500">{{ $types->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 font-medium text-zinc-800 dark:text-white">
                            <a href="{{ route('admin.facility-types.show', $type) }}" class="hover:underline">{{ $type->name }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $type->facilities_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                @can('update', $type)
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" :href="route('admin.facility-types.edit', $type)">Edit</flux:button>
                                @endcan
                                @can('delete', $type)
                                    {{-- HTML forms only send GET/POST, so @method spoofs DELETE (Topic 6) --}}
                                    <form method="POST" action="{{ route('admin.facility-types.destroy', $type) }}"
                                        x-data
                                        x-on:submit="if (! confirm('Delete {{ addslashes($type->name) }}? This cannot be undone.')) $event.preventDefault()">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button size="sm" variant="ghost" icon="trash" type="submit"
                                            :disabled="$type->facilities_count > 0"
                                            :title="$type->facilities_count > 0 ? 'In use by a facility' : null">Delete</flux:button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-zinc-500">
                            No facility types yet. <a href="{{ route('admin.facility-types.create') }}" class="underline">Add the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $types->links() }}
@endsection
