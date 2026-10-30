<x-guest-layout title="Inscription">
    <div class="mb-4">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gp-ink tracking-tight">Rejoindre GPsen</h2>
        <p class="text-xs sm:text-sm text-gp-warm mt-1">
            Choisissez votre profil pour continuer sur la plateforme de groupage participatif.
        </p>
    </div>

    <div class="grid grid-cols-2 p-1 rounded-gp bg-gp-sand text-xs sm:text-sm font-bold mb-4" role="tablist">
        <a href="{{ route('login') }}" class="text-center py-2 rounded-lg text-gp-ink hover:text-gp-orange-dark transition">Connexion</a>
        <span class="text-center py-2 rounded-lg bg-gp-ink text-white shadow-sm">Inscription</span>
    </div>

    <!-- Deux grandes cartes côte à côte : Expéditeur & Groupeur -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Carte Expéditeur -->
        <a href="{{ route('register.expediteur') }}" class="group flex flex-col justify-between p-4 sm:p-5 rounded-gp border-2 border-gp-border hover:border-gp-orange bg-white hover:bg-orange-50/20 transition-all shadow-sm">
            <div>
                <div class="w-10 h-10 rounded-xl bg-gp-sand flex items-center justify-center text-gp-ink text-lg font-black mb-3 group-hover:bg-gp-orange group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-gp-ink group-hover:text-gp-orange-dark tracking-tight leading-snug">
                    « J'envoie mes colis moins cher »
                </h3>
                <p class="text-xs text-gp-warm mt-1.5 leading-relaxed">
                    Partagez l'espace d'un voyageur vérifié et suivez votre colis en temps réel.
                </p>
            </div>

            <div class="mt-4 pt-3 border-t border-gp-border/60 flex items-center justify-between">
                <span class="text-xs font-bold text-gp-orange-dark">Je suis expéditeur</span>
                <span class="text-gp-orange font-black group-hover:translate-x-1 transition">&rarr;</span>
            </div>
        </a>

        <!-- Carte Groupeur -->
        <a href="{{ route('register.groupeur') }}" class="group flex flex-col justify-between p-4 sm:p-5 rounded-gp border-2 border-gp-border hover:border-gp-orange bg-white hover:bg-orange-50/20 transition-all shadow-sm">
            <div>
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-gp-orange-dark text-lg font-black mb-3 group-hover:bg-gp-orange group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-gp-ink group-hover:text-gp-orange-dark tracking-tight leading-snug">
                    « Mes kilos libres deviennent des revenus »
                </h3>
                <p class="text-xs text-gp-warm mt-1.5 leading-relaxed">
                    Publiez votre trajet, fixez vos conditions et rentabilisez vos kilos de bagages.
                </p>
            </div>

            <div class="mt-4 pt-3 border-t border-gp-border/60 flex items-center justify-between">
                <span class="text-xs font-bold text-gp-orange-dark">Je suis groupeur</span>
                <span class="text-gp-orange font-black group-hover:translate-x-1 transition">&rarr;</span>
            </div>
        </a>
    </div>

    <div class="mt-5 text-center text-xs text-gp-warm">
        Déjà inscrit ? <a href="{{ route('login') }}" class="text-gp-orange-dark underline font-bold hover:text-gp-orange">Se connecter</a>
    </div>
</x-guest-layout>
