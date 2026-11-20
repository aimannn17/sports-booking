{{--
    Branded "not found" page. Used by Route::fallback() for unknown URLs and by
    Laravel for any 404, including the access-admin Gate's denyAsNotFound().
--}}
<x-layouts::public :title="__('Page not found')">
    <div class="mx-auto flex max-w-md flex-col items-center gap-4 py-16 text-center">
        <div class="text-6xl font-semibold text-zinc-300 dark:text-zinc-600">404</div>
        <flux:heading size="xl" level="1">{{ __('Page not found') }}</flux:heading>
        <flux:text>{{ __('The page you are looking for does not exist or has moved.') }}</flux:text>
        <div class="flex gap-2">
            <flux:button variant="primary" :href="route('facilities.index')">{{ __('Browse facilities') }}</flux:button>
            <flux:button :href="route('home')">{{ __('Home') }}</flux:button>
        </div>
    </div>
</x-layouts::public>
