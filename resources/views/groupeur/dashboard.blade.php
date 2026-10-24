<x-app-layout :title="__('Mon espace Groupeur')">
    <div class="space-y-6">
        <!-- Bannière d'accueil groupeur validé -->
        <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        ✓ Groupeur vérifié
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-gp-ink tracking-tight">Bonjour, {{ $user->name }}</h1>
                <p class="text-xs text-gp-warm mt-1">Publiez vos prochains départs et rentabilisez vos kilos disponibles.</p>
            </div>

            <div>
                <a href="{{ route('groupeur.trips.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-gp bg-gp-orange text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange-dark transition shadow-sm">
                    + Publier un trajet
                </a>
            </div>
        </div>

        <!-- Compteurs clés groupeur selon UI/UX §5.8 -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Trajets actifs</span>
                <p class="text-3xl font-extrabold text-gp-ink mt-2">{{ $activeTripsCount }}</p>
                <p class="text-xs text-gp-warm mt-1">Ouverts aux réservations de colis</p>
            </div>

            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Demandes à traiter</span>
                <p class="text-3xl font-extrabold text-gp-ink mt-2">0</p>
                <p class="text-xs text-gp-warm mt-1">En attente de votre réponse</p>
            </div>

            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Note moyenne</span>
                <div class="flex items-center gap-2 mt-2">
                    <p class="text-3xl font-extrabold text-gp-ink">
                        {{ number_format($user->groupeurProfile?->average_rating ?? 5.0, 1) }}
                    </p>
                    <span class="text-amber-500 font-bold text-lg">★</span>
                </div>
                <p class="text-xs text-gp-warm mt-1">{{ $user->groupeurProfile?->ratings_count ?? 0 }} avis d'expéditeurs</p>
            </div>
        </div>

        <!-- Section d'accès rapide -->
        <div class="bg-white rounded-gp border border-gp-border shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gp-border/60">
                <h2 class="text-base font-bold text-gp-ink">Gestion de vos trajets</h2>
                <a href="{{ route('groupeur.trips.index') }}" class="text-xs font-bold text-gp-orange-dark hover:underline">
                    Voir tous mes trajets &rarr;
                </a>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row items-center justify-between p-4 bg-gp-sand/30 rounded-gp border border-gp-border/60 gap-4">
                <div>
                    <h3 class="text-sm font-bold text-gp-ink">Vous préparez un voyage prochainement ?</h3>
                    <p class="text-xs text-gp-warm mt-0.5">Indiquez votre itinéraire et votre capacité en kg pour recevoir les réservations d'envoi.</p>
                </div>
                <a href="{{ route('groupeur.trips.create') }}" class="px-4 py-2 rounded-gp bg-gp-ink text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange transition shrink-0">
                    Nouveau trajet
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
