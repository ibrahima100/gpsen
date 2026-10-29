<x-public-layout :title="__('Trajets disponibles — Groupage')">
    <div class="max-w-6xl mx-auto px-4 py-10 space-y-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gp-ink tracking-tight">Trajets disponibles</h1>
            <p class="text-xs text-gp-warm mt-1">Trouvez un voyageur vérifié pour acheminer votre colis vers votre destination.</p>
        </div>

        <!-- Filtres selon UI/UX §5.4 avec listes déroulantes de pays et villes -->
        <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
            @php
                $availableCountries = \App\Models\Country::with(['cities' => fn($q) => $q->orderBy('name')])->orderBy('name')->get();
            @endphp
            <form method="GET" action="{{ route('trajets.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="origin" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Ville ou pays de départ</label>
                    <select id="origin" name="origin" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                        <option value="">Tous les départs</option>
                        @foreach($availableCountries as $country)
                            <optgroup label="📍 {{ $country->name }}">
                                <option value="{{ $country->name }}" {{ ($filters['origin'] ?? '') === $country->name ? 'selected' : '' }}>
                                    Tout le pays ({{ $country->name }})
                                </option>
                                @foreach($country->cities as $city)
                                    <option value="{{ $city->name }}" {{ ($filters['origin'] ?? '') === $city->name ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="destination" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Destination</label>
                    <select id="destination" name="destination" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                        <option value="">Toutes les destinations</option>
                        @foreach($availableCountries as $country)
                            <optgroup label="🎯 {{ $country->name }}">
                                <option value="{{ $country->name }}" {{ ($filters['destination'] ?? '') === $country->name ? 'selected' : '' }}>
                                    Tout le pays ({{ $country->name }})
                                </option>
                                @foreach($country->cities as $city)
                                    <option value="{{ $city->name }}" {{ ($filters['destination'] ?? '') === $city->name ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Départ à partir du</label>
                    <input type="date" id="date" name="date" value="{{ $filters['date'] ?? '' }}" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                        Filtrer
                    </button>
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('trajets.index') }}" class="px-3 py-2.5 rounded-gp border border-gp-border text-xs font-bold text-gp-warm hover:bg-gp-sand transition" title="Réinitialiser les filtres">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste des trajets -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($trips as $trip)
                @php
                    $remaining = $capacityService->getRemainingCapacity($trip);
                    $percentage = $trip->capacity_kg > 0 ? round((($trip->capacity_kg - $remaining) / $trip->capacity_kg) * 100) : 0;
                @endphp
                <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gp-border/60">
                            <span class="text-base font-black text-gp-ink">
                                {{ $trip->origin_city }} &rarr; {{ $trip->destination_city }}
                            </span>
                            <span class="text-xs font-bold text-gp-orange-dark bg-orange-50 px-2.5 py-0.5 rounded-full border border-orange-200">
                                {{ number_format($trip->price_per_kg, 0, ',', ' ') }} FCFA/kg
                            </span>
                        </div>

                        <div class="mt-4 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-gp-warm">
                                <span>Départ le :</span>
                                <strong class="text-gp-ink">{{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d F Y') }}</strong>
                            </div>

                            <div class="flex items-center justify-between text-gp-warm">
                                <span>Groupeur certifié :</span>
                                <span class="font-bold text-gp-ink flex items-center gap-1">
                                    {{ $trip->groupeur->name }}
                                    <span class="text-emerald-700 font-bold" title="Vérifié par l'administration GPsen">✓</span>
                                </span>
                            </div>

                            <div class="pt-2">
                                <div class="flex items-center justify-between text-[11px] text-gp-warm mb-1">
                                    <span>Capacité restante :</span>
                                    <strong class="text-gp-ink">{{ $remaining }} kg sur {{ $trip->capacity_kg }} kg</strong>
                                </div>
                                <div class="w-full bg-gp-sand/60 h-2 rounded-full overflow-hidden">
                                    <div class="bg-gp-orange h-full rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gp-border/60 flex items-center justify-between">
                        <span class="text-[11px] text-gp-warm">Collecte : {{ $trip->pickup_info ?: 'Dakar centre' }}</span>
                        <a href="{{ route('trajets.show', $trip) }}" class="px-4 py-2 rounded-gp bg-gp-ink text-white font-bold text-xs hover:bg-gp-orange transition shadow-sm">
                            Voir le détail
                        </a>
                    </div>
                </div>
            @empty
                <!-- État vide officiel selon UI/UX §5.4 -->
                <div class="col-span-3 text-center py-16 px-4 border border-dashed border-gp-border rounded-gp bg-white">
                    <p class="text-base font-bold text-gp-ink">Aucun trajet ne correspond.</p>
                    <p class="text-xs text-gp-warm mt-1">Essayez d'autres dates ou villes pour élargir votre recherche.</p>
                    <div class="mt-4">
                        <a href="{{ route('trajets.index') }}" class="inline-flex items-center px-4 py-2 rounded-gp bg-gp-orange text-white text-xs font-bold uppercase tracking-wider hover:bg-gp-orange-dark transition shadow-sm">
                            Réinitialiser les filtres
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div>
            {{ $trips->links() }}
        </div>
    </div>
</x-public-layout>
