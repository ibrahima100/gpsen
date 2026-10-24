@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-gp font-semibold text-sm transition duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-gp-orange focus:ring-offset-2';
    $variants = [
        'primary'   => 'bg-gp-orange text-white shadow-gp hover:bg-gp-orange-dark',
        'secondary' => 'bg-gp-cream text-gp-ink border border-gp-border hover:bg-gp-sand',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700',
        'ghost'     => 'text-gp-warm hover:bg-gp-sand',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
