@props(['scale' => 1, 'text' => true, 'tagline' => null])

@php
    // Recadrage de l'icône (flèches) dans logo.jpg (639x617). Zone utile ~ x:120 y:165 w:350 h:185.
    $k = 0.1486 * $scale;
    $w = round(350 * $k); $h = round(185 * $k);
    $bg = round(639 * $k);
    $x = round(-120 * $k); $y = round(-165 * $k);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <span role="img" aria-label="Logo GPsen"
          style="display:inline-block;width:{{ $w }}px;height:{{ $h }}px;background:url('{{ asset('images/logo.jpg') }}') {{ $x }}px {{ $y }}px / {{ $bg }}px auto no-repeat;mix-blend-mode:multiply;"></span>
    @if ($text)
        <span class="flex flex-col leading-none">
            <span class="font-extrabold tracking-tight text-gp-ink" style="font-size: {{ round(22 * max($scale,1)) }}px">GPsen</span>
            @if ($tagline)
                <span class="text-[11px] font-medium text-gp-warm mt-1">{{ $tagline }}</span>
            @endif
        </span>
    @endif
</span>
