<x-admin-layout>
    <x-slot name="header">Tableau de bord administrateur</x-slot>

    <!-- Cartes chiffres clés selon UI/UX §5.9 -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.groupeurs.index') }}" class="gp-card p-5 hover:border-gp-orange transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Groupeurs à valider</span>
                <span class="text-xs font-bold text-gp-orange">Action requise</span>
            </div>
            <p class="mt-2 text-3xl font-extrabold text-gp-orange-dark">{{ $pendingGroupeurs }}</p>
            <p class="text-[11px] text-gp-warm mt-1">Dossiers et pièces d'identité en attente</p>
        </a>

        <div class="gp-card p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Total utilisateurs</span>
            <p class="mt-2 text-3xl font-extrabold text-gp-ink">{{ $totalUsers }}</p>
            <p class="text-[11px] text-gp-warm mt-1">Expéditeurs et groupeurs inscrits</p>
        </div>

        <div class="gp-card p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Trajets ouverts</span>
            <p class="mt-2 text-3xl font-extrabold text-gp-ink">{{ $openTrips }}</p>
            <p class="text-[11px] text-gp-warm mt-1">Actuellement disponibles aux envois</p>
        </div>

        <div class="gp-card p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Signalements</span>
            <p class="mt-2 text-3xl font-extrabold text-gp-ink">0</p>
            <p class="text-[11px] text-gp-warm mt-1">File de modération (Étape 6)</p>
        </div>
    </div>

    <!-- Section d'actions prioritaires selon UI/UX §5.9 -->
    <div class="mt-8 bg-white rounded-gp border border-gp-border p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-gp-border/60">
            <div>
                <h2 class="text-base font-bold text-gp-ink">Dossiers prioritaires à examiner</h2>
                <p class="text-xs text-gp-warm mt-0.5">Vérifiez les pièces d'identité déposées pour autoriser les publications de trajets.</p>
            </div>
            <a href="{{ route('admin.groupeurs.index') }}" class="px-4 py-2 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Voir la file complète ({{ $pendingGroupeurs }})
            </a>
        </div>

        <div class="mt-4">
            @if($pendingGroupeurs > 0)
                <div class="p-4 rounded-gp bg-orange-50 border border-orange-200 text-xs text-gp-ink flex items-center justify-between">
                    <div>
                        <strong class="text-gp-orange-dark">{{ $pendingGroupeurs }} groupeur(s)</strong> attendent l'approbation de leur justificatif d'identité pour pouvoir publier.
                    </div>
                    <a href="{{ route('admin.groupeurs.index') }}" class="font-bold underline text-gp-orange-dark hover:text-gp-orange">
                        Traiter maintenant &rarr;
                    </a>
                </div>
            @else
                <p class="text-xs text-gp-warm py-4 text-center">Aucun dossier en attente pour le moment. Tous les groupeurs ont été traités.</p>
            @endif
        </div>
    </div>
</x-admin-layout>
