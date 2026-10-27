<x-public-layout :title="__('Accueil — Groupage participatif de colis')">
    <!-- Hero section avec photo de fond visible et masque contrasté (UI/UX §2.5 & §5.1) -->
    <section class="relative bg-gp-sand overflow-hidden py-16 md:py-24 border-b border-gp-border">
        <div class="max-w-6xl mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white text-gp-orange-dark border border-gp-border mb-4">
                    Plateforme certifiée de groupage participatif
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gp-ink leading-[1.1] tracking-tight">
                    Envoyez vos colis moins cher avec des <span class="text-gp-orange-dark">voyageurs vérifiés.</span>
                </h1>
                <p class="text-base md:text-lg text-gp-warm mt-4 leading-relaxed">
                    Partagez les kilos disponibles de voyageurs entre le Sénégal, la France, l'Italie et la sous-région en toute confiance.
                </p>
            </div>

            <!-- Formulaire de recherche rapide (§5.1) -->
            <div class="mt-8 bg-white p-5 md:p-6 rounded-gp border border-gp-border shadow-md max-w-4xl">
                <form method="GET" action="{{ route('trajets.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="origin" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Ville de départ</label>
                        <input type="text" id="origin" name="origin" value="{{ request('origin') }}" placeholder="Ex. Dakar, Paris" class="w-full text-sm rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange" />
                    </div>
                    <div>
                        <label for="destination" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Destination</label>
                        <input type="text" id="destination" name="destination" value="{{ request('destination') }}" placeholder="Ex. Paris, Milan" class="w-full text-sm rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange" />
                    </div>
                    <div>
                        <label for="date" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Date à partir du</label>
                        <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full text-sm rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange" />
                    </div>
                    <div>
                        <button type="submit" class="w-full py-3 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-sm transition uppercase tracking-wider shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Trouver un trajet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Trajets récents disponibles (§5.1 : 3 à 6 cartes) -->
    <section id="trajets" class="max-w-6xl mx-auto px-4 py-16 scroll-mt-20">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-gp-ink tracking-tight">Prochains départs confirmés</h2>
                <p class="text-xs text-gp-warm mt-1">Voyageurs avec kilos de bagages actuellement disponibles.</p>
            </div>
            <a href="{{ route('trajets.index') }}" class="text-xs font-bold text-gp-orange-dark hover:underline">
                Voir tous les trajets &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($recentTrips as $trip)
                @php
                    $remaining = $capacityService->getRemainingCapacity($trip);
                    $percentage = $trip->capacity_kg > 0 ? round((($trip->capacity_kg - $remaining) / $trip->capacity_kg) * 100) : 0;
                @endphp
                <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gp-border/60">
                            <span class="text-lg font-black text-gp-ink">
                                {{ $trip->origin_city }} &rarr; {{ $trip->destination_city }}
                            </span>
                            <span class="text-xs font-bold text-gp-orange-dark bg-orange-50 px-2 py-0.5 rounded-full border border-orange-200">
                                {{ number_format($trip->price_per_kg, 0, ',', ' ') }} FCFA/kg
                            </span>
                        </div>

                        <div class="mt-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between text-gp-warm">
                                <span>Départ le :</span>
                                <strong class="text-gp-ink">{{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d F Y') }}</strong>
                            </div>

                            <div class="flex items-center justify-between text-gp-warm">
                                <span>Groupeur :</span>
                                <span class="font-bold text-gp-ink flex items-center gap-1">
                                    {{ $trip->groupeur->name }}
                                    <span class="text-emerald-700 font-bold" title="Groupeur vérifié par CNI">✓</span>
                                </span>
                            </div>

                            <div class="pt-2">
                                <div class="flex items-center justify-between text-[11px] text-gp-warm mb-1">
                                    <span>Capacité disponible :</span>
                                    <strong class="text-gp-ink">{{ $remaining }} kg sur {{ $trip->capacity_kg }} kg</strong>
                                </div>
                                <div class="w-full bg-gp-sand/60 h-2 rounded-full overflow-hidden">
                                    <div class="bg-gp-orange h-full rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gp-border/60 flex items-center justify-between">
                        <span class="text-xs text-gp-warm">Collecte : {{ $trip->pickup_info ?: 'À convenir' }}</span>
                        <a href="{{ route('trajets.show', $trip) }}" class="px-4 py-2 rounded-gp bg-gp-ink text-white font-bold text-xs hover:bg-gp-orange transition shadow-sm">
                            Voir le trajet
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 px-4 border border-dashed border-gp-border rounded-gp bg-white">
                    <p class="text-sm font-bold text-gp-ink">Aucun départ prévu pour l'instant</p>
                    <p class="text-xs text-gp-warm mt-1">Revenez bientôt ou publiez votre propre voyage si vous êtes groupeur.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Comment ça marche en 3 étapes asymétriques (UI/UX §5.1) -->
    <section id="comment-ca-marche" class="bg-gp-sand/40 border-y border-gp-border py-16 scroll-mt-20">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-orange-dark">Simplicité & Sécurité</span>
                <h2 class="text-3xl font-extrabold text-gp-ink mt-1">Comment fonctionne GPsen ?</h2>
                <p class="text-xs text-gp-warm mt-2 leading-relaxed">Une chaîne logistique collaborative humaine et transparente en trois temps.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Étape 1 -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm">
                    <span class="text-2xl font-black text-gp-orange-dark">01</span>
                    <h3 class="text-base font-bold text-gp-ink mt-3">Rechercher ou publier</h3>
                    <p class="text-xs text-gp-warm mt-2 leading-relaxed">
                        Les voyageurs certifiés publient leurs dates et kilos disponibles. Les expéditeurs sélectionnent le départ qui correspond à leur besoin.
                    </p>
                </div>

                <!-- Étape 2 -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm md:translate-y-4">
                    <span class="text-2xl font-black text-gp-orange-dark">02</span>
                    <h3 class="text-base font-bold text-gp-ink mt-3">Validation & WhatsApp direct</h3>
                    <p class="text-xs text-gp-warm mt-2 leading-relaxed">
                        L'expéditeur détaille son colis. Dès que le groupeur accepte, le contact WhatsApp direct est débloqué pour organiser la remise.
                    </p>
                </div>

                <!-- Étape 3 -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm md:translate-y-8">
                    <span class="text-2xl font-black text-gp-orange-dark">03</span>
                    <h3 class="text-base font-bold text-gp-ink mt-3">Suivi et remise en main propre</h3>
                    <p class="text-xs text-gp-warm mt-2 leading-relaxed">
                        Le colis est suivi à chaque étape (collecté, en transit, livré). À l'arrivée, l'expéditeur et le destinataire valident la livraison.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bloc de réassurance de la charte (§5.1) -->
    <section class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gp-ink rounded-gp p-8 md:p-12 text-white flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-orange">Confiance & Rigueur</span>
                <h2 class="text-2xl md:text-3xl font-extrabold mt-2 tracking-tight">Vous voyagez bientôt ? Rentabilisez votre valise.</h2>
                <p class="text-xs text-gp-border mt-3 leading-relaxed">
                    Rejoignez les groupeurs agréés de la diaspora. Chaque voyageur est vérifié par pièce d'identité officielle avant de pouvoir publier.
                </p>
            </div>
            <div>
                <a href="{{ route('register.groupeur') }}" class="px-6 py-3.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-md whitespace-nowrap block text-center">
                    Devenir groupeur GPsen
                </a>
            </div>
        </div>
    </section>
</x-public-layout>
