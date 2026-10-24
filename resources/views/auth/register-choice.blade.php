<x-guest-layout title="Inscription">
    <h2 class="text-2xl font-extrabold text-gp-ink">Créer un compte</h2>
    <p class="text-sm text-gp-warm mt-1">Choisissez votre profil pour commencer sur GPsen.</p>

    <div class="mt-5 grid grid-cols-2 p-1 rounded-gp bg-gp-sand text-sm font-bold" role="tablist">
        <a href="{{ route('login') }}" class="text-center py-2 rounded-lg text-gp-ink hover:text-gp-orange-dark transition">Connexion</a>
        <span class="text-center py-2 rounded-lg bg-gp-ink text-white">Inscription</span>
    </div>

    <div class="mt-6 space-y-4">
        <!-- Carte Expéditeur -->
        <a href="{{ route('register.expediteur') }}" class="group block p-5 rounded-gp border-2 border-gp-border hover:border-gp-orange bg-white hover:bg-gp-sand/30 transition shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-gp-sand flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition">
                    📦
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-base text-gp-ink group-hover:text-gp-orange-dark">Je suis Expéditeur</h3>
                        <span class="text-gp-orange-dark font-bold text-sm">Choisir &rarr;</span>
                    </div>
                    <p class="text-xs text-gp-warm mt-1 leading-relaxed">
                        J'ai des colis, plis ou bagages à faire acheminer vers le Sénégal ou l'international.
                    </p>
                    <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-semibold text-gp-warm">
                        <span class="px-2 py-0.5 rounded-full bg-gp-sand/80">Recherche de trajets</span>
                        <span class="px-2 py-0.5 rounded-full bg-gp-sand/80">Suivi en direct</span>
                        <span class="px-2 py-0.5 rounded-full bg-gp-sand/80">Sans vérification d'identité</span>
                    </div>
                </div>
            </div>
        </a>

        <!-- Carte Groupeur -->
        <a href="{{ route('register.groupeur') }}" class="group block p-5 rounded-gp border-2 border-gp-border hover:border-gp-orange bg-white hover:bg-gp-sand/30 transition shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition">
                    ✈️
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-base text-gp-ink group-hover:text-gp-orange-dark">Je suis Groupeur</h3>
                        <span class="text-gp-orange-dark font-bold text-sm">Choisir &rarr;</span>
                    </div>
                    <p class="text-xs text-gp-warm mt-1 leading-relaxed">
                        Je voyage ou transporte du fret, je vends mes kilos disponibles pour rentabiliser mes déplacements.
                    </p>
                    <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-semibold text-gp-orange-dark">
                        <span class="px-2 py-0.5 rounded-full bg-orange-50 border border-orange-200">Publication de trajets</span>
                        <span class="px-2 py-0.5 rounded-full bg-orange-50 border border-orange-200">Pièce d'identité requise</span>
                        <span class="px-2 py-0.5 rounded-full bg-orange-50 border border-orange-200">Profil vérifié par l'admin</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <p class="mt-6 text-center text-xs text-gp-warm">
        En vous inscrivant, vous acceptez les règles de sécurité et de modération de GPsen.
    </p>
</x-guest-layout>
