{{--
    Reusable validation message (Topic 6.4, DRY).
    Usage: <x-forms.error name="name" />
--}}
@props(['name'])

@error($name)
    <p {{ $attributes->merge(['class' => 'mt-1 text-sm font-medium text-red-500 dark:text-red-400']) }} data-test="error-{{ $name }}">
        {{ $message }}
    </p>
@enderror
