@props(['scale' => 1, 'text' => true, 'tagline' => null])

@php
    $size = round(32 * $scale);
    $fontSize = round(22 * max($scale, 1));
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    <!-- Emblème SVG GPsen : Colis stylisé avec flèche d'acheminement et couleur signature -->
    <svg class="shrink-0" style="width: {{ $size }}px; height: {{ $size }}px;" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Emblème GPsen">
        <rect width="40" height="40" rx="10" fill="#141414"/>
        <!-- Boîte colis -->
        <path d="M12 16L20 12L28 16L20 20L12 16Z" fill="#F26B1D"/>
        <path d="M12 16V25L20 29V20L12 16Z" fill="#D4550C"/>
        <path d="M28 16V25L20 29V20L28 16Z" fill="#F8F2E6"/>
        <!-- Ruban adhésif / étiquette d'expédition -->
        <line x1="20" y1="12" x2="20" y2="20" stroke="#141414" stroke-width="1.5"/>
    </svg>

    @if ($text)
        <span class="flex flex-col leading-none">
            <span class="font-black tracking-tight text-gp-ink" style="font-size: {{ $fontSize }}px">
                GP<span class="text-gp-orange">sen</span>
            </span>
            @if ($tagline)
                <span class="text-[10px] uppercase font-bold tracking-wider text-gp-warm mt-0.5">{{ $tagline }}</span>
            @endif
        </span>
    @endif
</span>
