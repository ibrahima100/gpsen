@if(auth()->user()->isAdmin())
    <x-admin-layout>
        <x-slot name="header">Centre de notifications</x-slot>
        @include('notifications.partials.list')
    </x-admin-layout>
@else
    <x-app-layout :title="'Mes notifications'">
        @include('notifications.partials.list')
    </x-app-layout>
@endif
