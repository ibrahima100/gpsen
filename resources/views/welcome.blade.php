<x-public-layout :title="__('Accueil — Groupage participatif de colis')">
    <!-- Hero section avec photo de fond visible (images/fond.jpg identique à login/register) et masque contrasté -->
    <section class="relative bg-gp-sand overflow-hidden py-16 md:py-24 border-b border-gp-border">
        {{-- Photo de fond (fond.jpg) avec cadrage soigné et superposition de dégradé protecteur pour la lisibilité --}}
        <div class="absolute inset-0 bg-cover bg-center lg:bg-[position:80%_center]" style="background-image:url('{{ asset('images/fond.jpg') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-gp-sand/98 via-gp-sand/90 to-gp-sand/50"></div>

        {{-- Formes diagonales stylisées signature GPsen --}}
        <div class="hidden lg:block absolute -bottom-24 -right-20 w-[550px] h-[260px] bg-gp-orange/20 -rotate-[14deg] pointer-events-none"></div>
        <div class="hidden lg:block absolute -bottom-40 -right-24 w-[450px] h-[240px] bg-gp-ink/10 -rotate-[14deg] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 relative z-10">
            <div class="max-w-2xl bg-white/60 backdrop-blur-sm p-6 sm:p-8 rounded-2xl border border-white/60 shadow-sm">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white text-gp-orange-dark border border-gp-border mb-4 shadow-2xs">
                    Groupage participatif de colis
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gp-ink leading-[1.1] tracking-tight">
                    Envoyez vos colis moins cher avec des <span class="text-gp-orange-dark">voyageurs vérifiés.</span>
                </h1>
                <p class="text-base md:text-lg text-gp-warm mt-4 leading-relaxed font-medium">
                    Partagez les kilos disponibles de voyageurs entre le Sénégal, la France, l'Italie et la sous-région en toute confiance.
                </p>
            </div>

            <!-- Formulaire de recherche rapide (§5.1) avec sélection des villes et pays GPsen -->
            <div class="mt-8 bg-white/95 backdrop-blur-md p-5 md:p-6 rounded-gp border border-gp-border shadow-lg max-w-4xl">
                @php
                    $availableCountries = \App\Models\Country::with(['cities' => fn($q) => $q->orderBy('name')])->orderBy('name')->get();
                @endphp
                <form method="GET" action="{{ route('trajets.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="origin" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">
                            Ville ou pays de départ
                        </label>
                        <select id="origin" name="origin" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                            <option value="">Tous les départs</option>
                            @foreach($availableCountries as $country)
                                <optgroup label="📍 {{ $country->name }}">
                                    <option value="{{ $country->name }}" {{ request('origin') === $country->name ? 'selected' : '' }}>
                                        Tout le pays ({{ $country->name }})
                                    </option>
                                    @foreach($country->cities as $city)
                                        <option value="{{ $city->name }}" {{ request('origin') === $city->name ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="destination" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">
                            Destination
                        </label>
                        <select id="destination" name="destination" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                            <option value="">Toutes les destinations</option>
                            @foreach($availableCountries as $country)
                                <optgroup label="🎯 {{ $country->name }}">
                                    <option value="{{ $country->name }}" {{ request('destination') === $country->name ? 'selected' : '' }}>
                                        Tout le pays ({{ $country->name }})
                                    </option>
                                    @foreach($country->cities as $city)
                                        <option value="{{ $city->name }}" {{ request('destination') === $city->name ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date" class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">Date à partir du</label>
                        <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange" />
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs transition uppercase tracking-wider shadow-sm flex items-center justify-center gap-2">
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

    <!-- Comment ça marche en 3 étapes néo-modernes 2026 avec fond sombre, grilles centrées et faisceau animé sur bordures -->
    <section id="comment-ca-marche" class="relative bg-gp-ink text-gp-sand border-y border-gp-border py-20 scroll-mt-20 overflow-hidden flex flex-col justify-center items-center">
        {{-- Halos néo-modernes 2026 --}}
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-gp-orange/15 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 relative z-10 w-full flex flex-col items-center">
            <div class="text-center max-w-xl mx-auto mb-16 space-y-3">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-black tracking-widest uppercase bg-white/10 text-gp-orange border border-white/10 shadow-sm backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-gp-orange animate-ping"></span>
                    Simplicité & Sécurité
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white">Comment fonctionne GPsen ?</h2>
                <p class="text-xs md:text-sm text-gp-border leading-relaxed">Une chaîne logistique collaborative humaine et transparente en trois temps.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl justify-center items-stretch">
                <!-- Étape 1 -->
                <div class="group relative rounded-2xl p-[1.5px] overflow-hidden transition-all duration-300 hover:-translate-y-1.5 flex flex-col">
                    <div class="absolute inset-[-150%] bg-[conic-gradient(from_0deg,#F26B1D_0deg,#F26B1D_45deg,transparent_90deg,transparent_360deg)] animate-spin-beam opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 rounded-2xl border border-white/15 pointer-events-none"></div>

                    <div class="relative z-10 bg-gp-ink/90 backdrop-blur-xl rounded-[15px] p-7 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                                <span class="text-3xl font-black text-gp-orange/80 group-hover:text-gp-orange transition-colors">01</span>
                                <span class="p-2.5 rounded-xl bg-white/5 text-gp-orange border border-white/10 group-hover:bg-gp-orange group-hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-white mt-5 group-hover:text-gp-orange transition-colors">Rechercher ou publier</h3>
                            <p class="text-xs text-gp-border/90 mt-2 leading-relaxed">
                                Les voyageurs vérifiés publient leurs dates et kilos disponibles. Les expéditeurs sélectionnent le départ qui correspond à leur besoin.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-white/10 flex items-center justify-between text-[11px] text-gp-border font-medium">
                            <span>Trajets certifiés</span>
                            <span class="text-gp-orange font-bold">&rarr;</span>
                        </div>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="group relative rounded-2xl p-[1.5px] overflow-hidden transition-all duration-300 hover:-translate-y-1.5 flex flex-col">
                    <div class="absolute inset-[-150%] bg-[conic-gradient(from_0deg,#F26B1D_0deg,#F26B1D_45deg,transparent_90deg,transparent_360deg)] animate-spin-beam opacity-40 group-hover:opacity-100 transition-opacity" style="animation-delay: -2s;"></div>
                    <div class="absolute inset-0 rounded-2xl border border-white/15 pointer-events-none"></div>

                    <div class="relative z-10 bg-gp-ink/90 backdrop-blur-xl rounded-[15px] p-7 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                                <span class="text-3xl font-black text-gp-orange/80 group-hover:text-gp-orange transition-colors">02</span>
                                <span class="p-2.5 rounded-xl bg-white/5 text-gp-orange border border-white/10 group-hover:bg-gp-orange group-hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-white mt-5 group-hover:text-gp-orange transition-colors">Validation & WhatsApp direct</h3>
                            <p class="text-xs text-gp-border/90 mt-2 leading-relaxed">
                                L'expéditeur détaille son colis. Dès que le groupeur accepte, le contact WhatsApp direct est débloqué pour organiser la remise.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-white/10 flex items-center justify-between text-[11px] text-gp-border font-medium">
                            <span>Échanges directs sécurisés</span>
                            <span class="text-gp-orange font-bold">&rarr;</span>
                        </div>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="group relative rounded-2xl p-[1.5px] overflow-hidden transition-all duration-300 hover:-translate-y-1.5 flex flex-col">
                    <div class="absolute inset-[-150%] bg-[conic-gradient(from_0deg,#F26B1D_0deg,#F26B1D_45deg,transparent_90deg,transparent_360deg)] animate-spin-beam opacity-40 group-hover:opacity-100 transition-opacity" style="animation-delay: -4s;"></div>
                    <div class="absolute inset-0 rounded-2xl border border-white/15 pointer-events-none"></div>

                    <div class="relative z-10 bg-gp-ink/90 backdrop-blur-xl rounded-[15px] p-7 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                                <span class="text-3xl font-black text-gp-orange/80 group-hover:text-gp-orange transition-colors">03</span>
                                <span class="p-2.5 rounded-xl bg-white/5 text-gp-orange border border-white/10 group-hover:bg-gp-orange group-hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-white mt-5 group-hover:text-gp-orange transition-colors">Code OTP & Remise physique</h3>
                            <p class="text-xs text-gp-border/90 mt-2 leading-relaxed">
                                Le colis est suivi à chaque étape. À l'arrivée, le code de remise confidentiel à 4 chiffres sécurise définitivement la livraison physique.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-white/10 flex items-center justify-between text-[11px] text-gp-border font-medium">
                            <span>Code de livraison OTP</span>
                            <span class="text-gp-orange font-bold">&rarr;</span>
                        </div>
                    </div>
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
