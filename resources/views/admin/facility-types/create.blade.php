@extends('layouts.master')

@section('title', 'Add facility type')

@section('content')
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('admin.facility-types.index')">Facility types</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Add</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl" level="1">Add facility type</flux:heading>

    <form method="POST" action="{{ route('admin.facility-types.store') }}" class="flex max-w-md flex-col gap-5">
        @csrf

        @include('admin.facility-types._form')

        <div class="flex gap-2">
            <flux:button type="submit" variant="primary" data-test="save-type">Save</flux:button>
            <flux:button :href="route('admin.facility-types.index')" variant="ghost">Cancel</flux:button>
        </div>
    </form>
@endsection
