<x-app-layout :title="__('Mon espace Expéditeur')">
    <div class="space-y-6">
        <!-- En-tête sobre et clair -->
        <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-gp-sand text-gp-ink mb-2">
                    Espace Expéditeur
                </span>
                <h1 class="text-2xl font-extrabold text-gp-ink tracking-tight">Bonjour, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-gp-warm mt-1">Gérez vos envois de colis et réservez des kilos auprès de groupeurs vérifiés.</p>
            </div>

            <div>
                <a href="{{ url('/trajets') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-gp bg-gp-orange text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange-dark transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Rechercher un trajet
                </a>
            </div>
        </div>

        <!-- Compteurs d'activité selon les conventions GPsen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Demandes en cours</span>
                <p class="text-3xl font-extrabold text-gp-ink mt-2">0</p>
                <p class="text-xs text-gp-warm mt-1">En attente d'acceptation ou en transit</p>
            </div>

            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Colis livrés</span>
                <p class="text-3xl font-extrabold text-gp-ink mt-2">0</p>
                <p class="text-xs text-gp-warm mt-1">Acheminés à destination</p>
            </div>

            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Poids total expédié</span>
                <p class="text-3xl font-extrabold text-gp-ink mt-2">0 <span class="text-sm font-semibold text-gp-warm">kg</span></p>
                <p class="text-xs text-gp-warm mt-1">Sur l'ensemble de vos envois</p>
            </div>
        </div>

        <!-- Section Suivi des demandes récentes avec état vide concret -->
        <div class="bg-white rounded-gp border border-gp-border shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gp-border/60">
                <h2 class="text-base font-bold text-gp-ink">Mes demandes d'envoi</h2>
                <a href="{{ url('/trajets') }}" class="text-xs font-bold text-gp-orange-dark hover:underline">Trouver un trajet disponible &rarr;</a>
            </div>

            <!-- État vide concret et non générique -->
            <div class="text-center py-12 px-4 border border-dashed border-gp-border rounded-gp bg-gp-sand/20 mt-4">
                <div class="w-12 h-12 rounded-xl bg-white border border-gp-border flex items-center justify-center text-gp-ink text-xl font-bold mx-auto shadow-sm">
                    <svg class="w-6 h-6 text-gp-orange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gp-ink mt-3">Aucune demande d'envoi pour le moment</h3>
                <p class="text-xs text-gp-warm max-w-sm mx-auto mt-1 leading-relaxed">
                    Parcourez les trajets Dakar ⇄ Paris, Dakar ⇄ Milan ou régionaux et envoyez votre première demande de groupage.
                </p>
                <div class="mt-4">
                    <a href="{{ url('/trajets') }}" class="inline-flex items-center px-4 py-2 rounded-gp bg-gp-ink text-white text-xs font-bold hover:bg-gp-orange transition shadow-sm">
                        Consulter les départs prévus
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
