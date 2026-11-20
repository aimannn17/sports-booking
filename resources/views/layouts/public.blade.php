{{--
    Layout for pages guests can open (facility browsing).
    Logged-in users see the normal sidebar layout instead, so navigation stays the same everywhere.
--}}
@auth
    <x-layouts::app :title="$title ?? null">
        {{ $slot }}
    </x-layouts::app>
@else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <x-app-logo href="{{ route('home') }}" wire:navigate />

            <flux:navbar class="-mb-px ms-4">
                <flux:navbar.item icon="building-office" :href="route('facilities.index')" :current="request()->routeIs('facilities.*')" wire:navigate>
                    {{ __('Facilities') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar>
                <flux:navbar.item :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:navbar.item>
                @if (Route::has('register'))
                    <flux:navbar.item :href="route('register')" wire:navigate>{{ __('Register') }}</flux:navbar.item>
                @endif
            </flux:navbar>
        </flux:header>

        <flux:main container>
            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
@endauth
