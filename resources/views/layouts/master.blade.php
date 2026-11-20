{{--
    Master layout for classic controller views (Topic 4: layout inheritance).
    Child views use @extends('layouts.master') and fill @section('title')
    and @section('content'). It reuses the starter kit's sidebar layout, so
    controller pages and Livewire pages look and navigate the same.
--}}
<x-layouts::app :title="trim($__env->yieldContent('title'))">
    <div class="flex flex-col gap-6">
        @include('partials.flash')

        @yield('content')
    </div>
</x-layouts::app>
