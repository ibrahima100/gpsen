<x-app-layout :title="__('Mon espace Expéditeur')">
    <div class="space-y-8">
        <!-- Bannière d'accueil -->
        <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gp-sand flex items-center justify-center text-3xl shrink-0">
                    📦
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-extrabold text-gp-ink">Bonjour, {{ auth()->user()->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gp-sand text-gp-ink">Expéditeur</span>
                    </div>
                    <p class="text-sm text-gp-warm mt-1">Trouvez un groupeur vérifié pour expédier vos colis en toute sérénité.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('/trajets') }}" class="px-5 py-2.5 rounded-gp bg-gp-orange text-white font-bold text-sm hover:bg-gp-orange-dark transition shadow-sm text-center">
                    🔍 Rechercher un trajet
                </a>
            </div>
        </div>

        <!-- Chiffres clés / Résumé rapide -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Demandes actives</span>
                    <span class="text-xl">⏳</span>
                </div>
                <p class="text-3xl font-extrabold text-gp-ink mt-3">0</p>
                <p class="text-xs text-gp-warm mt-1">En attente ou en cours</p>
            </div>

            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Colis livrés</span>
                    <span class="text-xl">✅</span>
                </div>
                <p class="text-3xl font-extrabold text-gp-ink mt-3">0</p>
                <p class="text-xs text-gp-warm mt-1">Livraisons réussies</p>
            </div>

            <div class="bg-white p-5 rounded-gp border border-gp-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Total expédié</span>
                    <span class="text-xl">⚖️</span>
                </div>
                <p class="text-3xl font-extrabold text-gp-ink mt-3">0 <span class="text-base font-semibold text-gp-warm">kg</span></p>
                <p class="text-xs text-gp-warm mt-1">Poids total acheminé</p>
            </div>
        </div>

        <!-- Section Mes envois / Demandes récentes -->
        <div class="bg-white rounded-gp border border-gp-border shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gp-ink">Mes envois récents</h2>
                <a href="{{ url('/trajets') }}" class="text-xs font-bold text-gp-orange-dark hover:underline">Trouver un départ &rarr;</a>
            </div>

            <!-- État vide convivial -->
            <div class="text-center py-10 px-4 border-2 border-dashed border-gp-border rounded-gp bg-gp-sand/20">
                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-2xl mx-auto shadow-sm">
                    🛫
                </div>
                <h3 class="text-base font-bold text-gp-ink mt-3">Vous n'avez pas encore de demande d'envoi</h3>
                <p class="text-xs text-gp-warm max-w-md mx-auto mt-1">
                    Consultez les trajets disponibles publiés par nos groupeurs certifiés et réservez vos kilos en quelques minutes.
                </p>
                <div class="mt-5">
                    <a href="{{ url('/trajets') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-gp bg-gp-ink text-white text-xs font-bold hover:bg-gp-orange transition">
                        Voir les trajets disponibles
                    </a>
                </div>
            </div>
        </div>

        <!-- Astuces et sécurité pour l'expéditeur -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="p-4 rounded-gp bg-gp-sand/40 border border-gp-border">
                <div class="font-bold text-sm text-gp-ink flex items-center gap-2">
                    <span>🛡️</span> Groupeurs vérifiés
                </div>
                <p class="text-xs text-gp-warm mt-1.5 leading-relaxed">
                    Chaque voyageur fournit une pièce d'identité officielle validée par l'administration avant de pouvoir publier.
                </p>
            </div>

            <div class="p-4 rounded-gp bg-gp-sand/40 border border-gp-border">
                <div class="font-bold text-sm text-gp-ink flex items-center gap-2">
                    <span>📱</span> WhatsApp direct
                </div>
                <p class="text-xs text-gp-warm mt-1.5 leading-relaxed">
                    Dès que le groupeur accepte votre colis, vous pouvez échanger directement pour convenir du lieu de dépôt.
                </p>
            </div>

            <div class="p-4 rounded-gp bg-gp-sand/40 border border-gp-border">
                <div class="font-bold text-sm text-gp-ink flex items-center gap-2">
                    <span>📍</span> Suivi étape par étape
                </div>
                <p class="text-xs text-gp-warm mt-1.5 leading-relaxed">
                    Suivez la prise en charge de votre colis : de la collecte à la remise au destinataire.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
