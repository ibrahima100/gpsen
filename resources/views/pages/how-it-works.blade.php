<x-public-layout :title="__('Comment ça marche — GPsen')">
    <div class="max-w-4xl mx-auto px-4 py-12 space-y-12">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold uppercase tracking-wider text-gp-orange-dark">Guide pratique</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gp-ink mt-2">Le groupage participatif en toute simplicité</h1>
            <p class="text-xs text-gp-warm mt-3 leading-relaxed">
                GPsen met en relation les expéditeurs ayant un colis à envoyer et les voyageurs se déplaçant avec de l'espace disponible dans leurs bagages.
            </p>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm flex gap-4">
                <span class="w-10 h-10 rounded-full bg-gp-sand flex items-center justify-center font-black text-gp-ink shrink-0">1</span>
                <div>
                    <h2 class="text-base font-bold text-gp-ink">Trouvez un voyageur vérifié</h2>
                    <p class="text-xs text-gp-warm mt-1 leading-relaxed">
                        Parcourez les départs confirmés. Chaque profil groupeur est authentifié par une pièce d'identité officielle contrôlée par l'administration GPsen.
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm flex gap-4">
                <span class="w-10 h-10 rounded-full bg-gp-sand flex items-center justify-center font-black text-gp-ink shrink-0">2</span>
                <div>
                    <h2 class="text-base font-bold text-gp-ink">Réservez vos kilos & contactez sur WhatsApp</h2>
                    <p class="text-xs text-gp-warm mt-1 leading-relaxed">
                        Décrivez le contenu de votre colis et son poids. Une fois votre demande acceptée, le contact WhatsApp direct est activé pour fixer le rendez-vous de remise.
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm flex gap-4">
                <span class="w-10 h-10 rounded-full bg-gp-sand flex items-center justify-center font-black text-gp-ink shrink-0">3</span>
                <div>
                    <h2 class="text-base font-bold text-gp-ink">Suivez l'acheminement jusqu'à la livraison</h2>
                    <p class="text-xs text-gp-warm mt-1 leading-relaxed">
                        Chaque étape est enregistrée (colis collecté, départ en transit, livraison au destinataire). Vous recevez un récapitulatif transparent et pouvez laisser un avis d'évaluation.
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('trajets.index') }}" class="px-6 py-3 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                Consulter les départs disponibles
            </a>
        </div>
    </div>
</x-public-layout>
