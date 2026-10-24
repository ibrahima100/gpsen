<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold">{{ __('app.dashboard') }}</h1>
    </x-slot>

    <div class="gp-card p-6">
        <p class="font-semibold">{{ __('app.welcome') }}, {{ auth()->user()->name }} !</p>
        <p class="text-gp-warm mt-1">{{ auth()->user()->role?->label() }}</p>
    </div>
</x-app-layout>
