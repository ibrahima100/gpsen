@props(['status'])

@php
    $enum = $status instanceof \BackedEnum ? $status : null;
@endphp

@if ($enum)
    <span {{ $attributes->merge(['class' => 'badge '.$enum->badgeClass()]) }}>
        {{ $enum->label() }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'badge badge-delivered']) }}>{{ $status }}</span>
@endif
