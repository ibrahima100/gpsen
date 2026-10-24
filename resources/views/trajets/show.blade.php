<x-public-layout :title="__('Détail du trajet')">
    <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">
        <div>
            <a href="{{ route('trajets.index') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">
                &larr; Retour à la liste des trajets
            </a>
            <div class="mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-3xl font-extrabold text-gp-ink tracking-tight">
                    {{ $trip->origin_city }} ({{ $trip->origin_country }}) &rarr; {{ $trip->destination_city }} ({{ $trip->destination_country }})
                </h1>
                <span class="text-sm font-extrabold text-gp-orange-dark bg-orange-50 px-3 py-1 rounded-full border border-orange-200 self-start">
                    {{ number_format($trip->price_per_kg, 0, ',', ' ') }} FCFA / kg
                </span>
            </div>
        </div>

        @if(session('warning'))
            <div class="p-4 rounded-gp bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Colonne principale : Détails du trajet et conditions -->
            <div class="md:col-span-2 space-y-6">
                <!-- Bloc Dates et capacité en barre -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Disponibilités et calendrier
                    </h2>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gp-warm">Date de départ</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">
                                {{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('l d F Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-gp-warm">Arrivée estimée</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">
                                {{ $trip->arrival_date ? \Carbon\Carbon::parse($trip->arrival_date)->translatedFormat('l d F Y') : 'Non précisée' }}
                            </p>
                        </div>
                    </div>

                    @php
                        $percentage = $trip->capacity_kg > 0 ? round((($trip->capacity_kg - $remainingCapacity) / $trip->capacity_kg) * 100) : 0;
                    @endphp
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs text-gp-warm mb-1.5">
                            <span>Espace restant : <strong class="text-gp-ink">{{ $remainingCapacity }} kg</strong> sur {{ $trip->capacity_kg }} kg</span>
                            <span>{{ $percentage }}% réservé</span>
                        </div>
                        <div class="w-full bg-gp-sand/60 h-3 rounded-full overflow-hidden">
                            <div class="bg-gp-orange h-full rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Conditions de transport et collecte selon UI/UX §5.5 -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Conditions de transport
                    </h2>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="font-bold text-gp-ink">Lieu et modalités de collecte :</span>
                            <p class="text-gp-warm mt-0.5">{{ $trip->pickup_info ?: 'À convenir directement avec le groupeur après acceptation.' }}</p>
                        </div>

                        <div>
                            <span class="font-bold text-emerald-800">Objets acceptés :</span>
                            <p class="text-gp-warm mt-0.5">{{ $trip->accepted_items ?: 'Bagages ordinaires, vêtements, documents, colis secs fermés.' }}</p>
                        </div>

                        <div>
                            <span class="font-bold text-red-800">Objets formellement interdits :</span>
                            <p class="text-gp-warm mt-0.5">{{ $trip->prohibited_items ?: 'Produits inflammables, stupéfiants, liquides non étanches, denrées périssables fraîches.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Avis récents sur le groupeur (§5.5 & Étape 6) -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-gp-border">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink flex items-center gap-2">
                            <span>★</span> Avis des expéditeurs sur {{ $trip->groupeur->name }}
                        </h2>
                        <span class="text-xs text-gp-warm font-semibold">
                            {{ $trip->groupeur->groupeurProfile?->ratings_count ?? 0 }} avis
                        </span>
                    </div>

                    @if(isset($recentReviews) && $recentReviews->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($recentReviews as $rev)
                                <div class="p-3.5 bg-gp-sand/20 rounded-gp border border-gp-border/60">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gp-ink">{{ $rev->author->name }}</span>
                                            <span class="text-[11px] text-gp-warm/70">{{ $rev->created_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <span class="text-xs font-bold text-gp-orange">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $rev->rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                    </div>
                                    @if($rev->comment)
                                        <p class="text-xs text-gp-warm mt-1.5 italic">"{{ $rev->comment }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 bg-gp-sand/20 rounded-gp border border-gp-border/50 text-center">
                            <p class="text-xs text-gp-warm">Ce groupeur n'a pas encore reçu d'avis public. Ses voyages précédents ont été vérifiés par l'équipe GPsen.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Colonne latérale : Bloc Groupeur vérifié et Action (§5.5) -->
            <div class="space-y-6">
                <!-- Bloc Groupeur avec CNI validée et note -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gp-warm">À propos du groupeur</h3>

                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-base text-gp-ink">{{ $trip->groupeur->name }}</h4>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                                ✓ Vérifié
                            </span>
                        </div>
                        <p class="text-xs text-gp-warm mt-0.5">{{ $trip->groupeur->groupeurProfile?->usual_routes ?: 'Voyageur régulier' }}</p>
                    </div>

                    <div class="p-3 bg-gp-sand/30 rounded-gp border border-gp-border/60 text-xs text-gp-warm leading-relaxed">
                        {{ $trip->groupeur->groupeurProfile?->bio ?: 'Voyageur certifié sur la plateforme GPsen.' }}
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-gp-border/60">
                        <span class="text-gp-warm">Note moyenne :</span>
                        <span class="font-bold text-gp-ink flex items-center gap-1">
                            {{ number_format($trip->groupeur->groupeurProfile?->average_rating ?? 5.0, 1) }}
                            <span class="text-amber-500 font-bold">★</span>
                        </span>
                    </div>

                    <div class="text-[11px] text-gp-warm p-3 bg-orange-50 border border-orange-200 rounded-gp">
                        🔒 Le numéro direct WhatsApp du groupeur sera débloqué automatiquement dès que votre demande aura été acceptée.
                    </div>

                    @auth
                        <div class="pt-2 text-center border-t border-gp-border/50">
                            <a href="{{ route('reports.create', ['user_id' => $trip->groupeur_id, 'trip_id' => $trip->id]) }}" class="text-[11px] text-gp-warm hover:text-red-600 transition flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Signaler une anomalie sur ce trajet</span>
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Bouton fixe / Action principale selon AppFlow & §5.5 -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm text-center">
                    @if($remainingCapacity > 0)
                        @auth
                            @if(auth()->user()->isExpediteur())
                                <a href="{{ route('expediteur.requests.create', $trip) }}" class="w-full py-3 px-4 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm block text-center">
                                    Envoyer une demande d'envoi
                                </a>
                            @else
                                <p class="text-xs text-gp-warm">
                                    Vous êtes connecté en tant que groupeur ou administrateur. Seuls les comptes expéditeurs peuvent réserver.
                                </p>
                            @endif
                        @else
                            <a href="{{ route('login') }}?intended={{ urlencode(route('expediteur.requests.create', $trip)) }}" class="w-full py-3 px-4 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm block text-center">
                                Se connecter pour envoyer une demande
                            </a>
                            <p class="text-[11px] text-gp-warm mt-2">
                                Pas encore inscrit ? <a href="{{ route('register.expediteur') }}" class="underline font-bold text-gp-orange-dark">Créer un compte expéditeur</a>
                            </p>
                        @endauth
                    @else
                        <div class="py-2.5 px-3 rounded-gp bg-gp-sand text-gp-warm font-bold text-xs">
                            Ce trajet est complet (0 kg restant)
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
