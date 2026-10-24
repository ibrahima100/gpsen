@props(['trip', 'remaining' => null])

<article {{ $attributes->merge(['class' => 'gp-card p-5 hover:shadow-gp hover:-translate-y-0.5 transition duration-200']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold">
                {{ $trip->origin_city }} <span class="text-gp-orange">&rarr;</span> {{ $trip->destination_city }}
            </h3>
            <p class="text-sm text-gp-warm">{{ $trip->origin_country }} &middot; {{ $trip->destination_country }}</p>
        </div>
        <x-status-badge :status="$trip->status instanceof \BackedEnum ? $trip->status : \App\Enums\TripStatus::tryFrom((string) $trip->status)" />
    </div>

    <dl class="mt-4 grid grid-cols-3 gap-3 text-sm">
        <div>
            <dt class="text-gp-warm">{{ __('app.departure_date') }}</dt>
            <dd class="font-semibold">{{ $trip->departure_date?->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-gp-warm">{{ __('app.capacity_remaining') }}</dt>
            <dd class="font-semibold">{{ $remaining ?? $trip->capacity_kg }} kg</dd>
        </div>
        <div>
            <dt class="text-gp-warm">{{ __('app.price_per_kg') }}</dt>
            <dd class="font-semibold text-gp-orange-dark">{{ number_format($trip->price_per_kg, 0, ',', ' ') }} FCFA</dd>
        </div>
    </dl>

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-gp-border">{{ $footer }}</div>
    @endisset
</article>
