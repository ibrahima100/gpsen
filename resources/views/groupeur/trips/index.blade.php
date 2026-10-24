<x-app-layout :title="__('Mes trajets')">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gp-ink">Mes trajets publiés</h1>
                <p class="text-xs text-gp-warm mt-1">Gérez vos départs, suivez la capacité restante et recevez les demandes d'envoi.</p>
            </div>
            <a href="{{ route('groupeur.trips.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm self-start">
                + Publier un trajet
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-gp bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold">
                {{ session('warning') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse($trips as $trip)
                @php
                    $remaining = $capacityService->getRemainingCapacity($trip);
                    $percentage = $trip->capacity_kg > 0 ? round((($trip->capacity_kg - $remaining) / $trip->capacity_kg) * 100) : 0;
                @endphp
                <div class="bg-white rounded-gp border border-gp-border p-5 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gp-border/60">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-black text-gp-ink">
                                {{ $trip->origin_city }} &rarr; {{ $trip->destination_city }}
                            </span>
                            <x-status-badge :status="$trip->status" />
                        </div>
                        <div class="text-xs text-gp-warm">
                            Départ : <strong class="text-gp-ink">{{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d F Y') }}</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-gp-warm">Tarif par kg</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ number_format($trip->price_per_kg, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between text-gp-warm mb-1">
                                <span>Capacité : <strong>{{ $remaining }} kg</strong> restants sur {{ $trip->capacity_kg }} kg</span>
                                <span>{{ $percentage }}% réservé</span>
                            </div>
                            <div class="w-full bg-gp-sand/50 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-gp-orange h-full rounded-full transition-all duration-300" style="width: {{ min(100, $percentage) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gp-border/60 flex items-center justify-between">
                        <span class="text-[11px] text-gp-warm">
                            Créé le {{ $trip->created_at->translatedFormat('d/m/Y') }}
                        </span>
                        <div class="flex items-center gap-2">
                            @if($trip->status === \App\Enums\TripStatus::ouvert)
                                <a href="{{ route('groupeur.trips.edit', $trip) }}" class="px-3 py-1.5 rounded-gp border border-gp-border text-xs font-bold text-gp-ink hover:bg-gp-sand transition">
                                    Modifier
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 border border-dashed border-gp-border rounded-gp bg-white">
                    <h3 class="text-sm font-bold text-gp-ink">Vous n'avez aucun trajet publié</h3>
                    <p class="text-xs text-gp-warm max-w-sm mx-auto mt-1">
                        Publiez vos prochaines dates de voyage pour vendre vos kilos disponibles aux expéditeurs.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('groupeur.trips.create') }}" class="inline-flex items-center px-4 py-2 rounded-gp bg-gp-orange text-white text-xs font-bold uppercase tracking-wider hover:bg-gp-orange-dark transition shadow-sm">
                            + Publier mon premier trajet
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div>
            {{ $trips->links() }}
        </div>
    </div>
</x-app-layout>
