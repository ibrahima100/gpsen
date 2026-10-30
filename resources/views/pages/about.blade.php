<x-public-layout :title="__('À propos de nous — GPsen')">
    {{-- Bannière Hero "À propos" avec fond sombre 2026, halo lumineux et badge dynamique --}}
    <section class="relative bg-gp-ink text-gp-sand py-24 overflow-hidden border-b border-white/10">
        <div class="absolute -top-24 right-1/4 w-96 h-96 bg-gp-orange/20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-20 left-10 w-80 h-80 bg-gp-orange/15 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-4 text-center space-y-5">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase bg-white/5 text-gp-orange border border-white/10 shadow-inner backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-gp-orange animate-pulse"></span>
                Plateforme de Groupage Participatif
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight">
                Qui sommes-nous ? Bienvenue sur <span class="text-transparent bg-clip-text bg-gradient-to-r from-gp-orange via-orange-400 to-amber-300">GPsen</span>
            </h1>
            <p class="text-base md:text-lg text-gp-border max-w-2xl mx-auto leading-relaxed font-normal">
                La solution digitale nouvelle génération qui réinvente le transport de colis entre le Sénégal et le reste du monde, en combinant simplicité, sécurité et rapidité.
            </p>
        </div>
    </section>

    {{-- Corps de la page --}}
    <div class="max-w-5xl mx-auto px-4 py-16 space-y-20">
        
        {{-- Section 1 : Origine & Concept du GP --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <div class="space-y-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-orange-100 text-gp-orange-dark font-bold text-xs uppercase tracking-wider">
                    Origine & Vision
                </div>
                <h2 class="text-2xl md:text-4xl font-extrabold text-gp-ink leading-tight">
                    Digitaliser et professionnaliser la tradition du <span class="text-gp-orange">« GP »</span>
                </h2>
                <p class="text-sm md:text-base text-gp-warm leading-relaxed">
                    Au Sénégal et au sein de la diaspora, confier un colis à un voyageur (le fameux <em>« GP »</em>) a toujours été le moyen le plus rapide et chaleureux pour faire voyager des denrées, des documents ou des effets personnels entre Dakar, l'Europe, l'Amérique et l'Afrique.
                </p>
                <p class="text-sm md:text-base text-gp-warm leading-relaxed">
                    Auparavant basé sur l'informel et les réseaux sociaux sans garantie, <strong>GPsen apporte un cadre fiable et transparent</strong> avec suivi en direct, vérification systématique des identités et confirmation de livraison sécurisée.
                </p>
            </div>

            {{-- Carte vitrine concept --}}
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-gp-orange to-amber-500 rounded-3xl blur opacity-30 group-hover:opacity-60 transition duration-500"></div>
                <div class="relative p-8 rounded-3xl bg-white border border-gp-border/80 shadow-xl space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-gp-orange/20 flex items-center justify-center text-gp-orange text-2xl shadow-sm">
                            ⚡
                        </div>
                        <div>
                            <h3 class="font-bold text-gp-ink text-base">Le principe gagnant-gagnant</h3>
                            <p class="text-xs text-gp-warm">Une synergie directe entre voyageurs et expéditeurs</p>
                        </div>
                    </div>
                    <p class="text-sm text-gp-warm leading-relaxed">
                        Chaque jour, des centaines de voyageurs ont des kilos de bagages non utilisés, pendant que des expéditeurs cherchent une solution abordable et express. GPsen connecte ces deux besoins en toute confiance.
                    </p>
                    <div class="pt-4 border-t border-gp-border/60 flex items-center justify-between text-xs font-semibold text-gp-ink">
                        <span class="flex items-center gap-1.5 text-emerald-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Traçabilité totale
                        </span>
                        <span class="flex items-center gap-1.5 text-gp-orange font-bold">
                            Livraison en main propre
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2 : Pourquoi choisir GPsen ? (Une seule grande carte unifiée avec bordure animée gradient moderne) --}}
        <div class="space-y-8">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-orange">Nos Engagements</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gp-ink">Pourquoi choisir GPsen ?</h2>
                <p class="text-xs md:text-sm text-gp-warm">La combinaison idéale entre sécurité, économie et simplicité.</p>
            </div>

            {{-- Carte unifiée avec bordure animée (Rotating conic gradient / border glow) --}}
            <div class="relative p-[2px] rounded-3xl overflow-hidden shadow-2xl group">
                {{-- L'animation rotative continue sur la bordure --}}
                <div class="absolute inset-[-150%] animate-[spin_6s_linear_infinite] bg-[conic-gradient(from_0deg,#F26B1D,#F8F2E6,#F26B1D,#141414,#F26B1D)] pointer-events-none"></div>

                {{-- Contenu intérieur sur fond sombre ultra-moderne --}}
                <div class="relative rounded-[22px] bg-gp-ink text-gp-sand p-8 md:p-12 overflow-hidden">
                    <div class="absolute top-0 right-0 w-80 h-80 bg-gp-orange/10 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10 md:divide-x md:divide-white/10">
                        {{-- Engagement 1 --}}
                        <div class="space-y-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-gp-orange text-2xl shadow-inner">
                                🛡️
                            </div>
                            <h3 class="text-lg font-bold text-white tracking-tight">Identités Contrôlées</h3>
                            <p class="text-xs md:text-sm text-gp-border leading-relaxed">
                                Chaque profil de groupeur est rigoureusement audité et validé via une pièce d'identité officielle (CNI ou Passeport) avant d'être autorisé à publier un trajet.
                            </p>
                            <div class="text-[11px] font-semibold text-gp-orange flex items-center gap-1 pt-1">
                                <span>&check; Zéro profil anonyme</span>
                            </div>
                        </div>

                        {{-- Engagement 2 --}}
                        <div class="space-y-3.5 md:pl-8">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-gp-orange text-2xl shadow-inner">
                                💰
                            </div>
                            <h3 class="text-lg font-bold text-white tracking-tight">Économique & Rentable</h3>
                            <p class="text-xs md:text-sm text-gp-border leading-relaxed">
                                Les expéditeurs réduisent leurs frais jusqu'à 60% par rapport aux frets conventionnels, tandis que les voyageurs rentabilisent intelligemment le coût de leurs billets d'avion.
                            </p>
                            <div class="text-[11px] font-semibold text-gp-orange flex items-center gap-1 pt-1">
                                <span>&check; Tarification libre au kilo</span>
                            </div>
                        </div>

                        {{-- Engagement 3 --}}
                        <div class="space-y-3.5 md:pl-8">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-gp-orange text-2xl shadow-inner">
                                🔑
                            </div>
                            <h3 class="text-lg font-bold text-white tracking-tight">Code de Remise OTP</h3>
                            <p class="text-xs md:text-sm text-gp-border leading-relaxed">
                                Un code secret à 4 chiffres généré sur la plateforme garantit que le colis n'est marqué comme livré que lorsque la remise physique en main propre est constatée.
                            </p>
                            <div class="text-[11px] font-semibold text-gp-orange flex items-center gap-1 pt-1">
                                <span>&check; Preuve de livraison infalsifiable</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3 : Profil Développeur / Concepteur (Design épuré, professionnel, Fullstack & Réseau) --}}
        <div class="relative rounded-3xl p-8 md:p-12 bg-white border border-gp-border shadow-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center gap-8 md:gap-10">
                {{-- Avatar monogramme avec halo --}}
                <div class="relative shrink-0">
                    <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl bg-gradient-to-tr from-gp-ink via-zinc-800 to-gp-orange p-1 shadow-xl">
                        <div class="w-full h-full rounded-[14px] bg-gp-ink flex items-center justify-center text-white text-3xl font-black tracking-wider">
                            IT
                        </div>
                    </div>
                    <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gp-orange text-white shadow-md">
                        Lead
                    </span>
                </div>

                {{-- Texte descriptif --}}
                <div class="space-y-3 text-center md:text-left flex-1">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-gp-orange">Conception & Ingénierie</span>
                        <h3 class="text-2xl md:text-3xl font-black text-gp-ink tracking-tight">Ibrahima Tall</h3>
                        <p class="text-xs md:text-sm font-semibold text-gp-warm">
                            Développeur Fullstack & Administrateur Systèmes & Réseaux
                        </p>
                    </div>
                    <p class="text-xs md:text-sm text-gp-warm leading-relaxed">
                        Passionné par l'architecture logicielle, les infrastructures réseau et la conception de solutions digitales à fort impact, j'ai conçu et bâti la plateforme <strong>GPsen</strong> pour apporter aux utilisateurs une expérience fluide, réactive et hautement sécurisée.
                    </p>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 pt-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gp-sand text-gp-ink border border-gp-border/60">
                            Fullstack Web Architecture
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gp-sand text-gp-ink border border-gp-border/60">
                            Systèmes & Réseaux
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gp-sand text-gp-ink border border-gp-border/60">
                            Sécurité & Performance
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4 : Contact direct & Call to Action --}}
        <div class="text-center pt-4 space-y-6">
            <div class="space-y-1">
                <p class="text-sm font-bold text-gp-ink uppercase tracking-wider">Une question ou besoin d'assistance ?</p>
                <p class="text-xs text-gp-warm">Notre équipe est joignable à tout moment pour vous orienter.</p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="tel:+221778744844" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-full bg-gp-ink text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange hover:shadow-lg hover:shadow-gp-orange/20 transition-all duration-300">
                    <svg class="w-4 h-4 text-gp-orange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>+221 77 874 48 44</span>
                </a>
                <a href="{{ route('trajets.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-gp-orange text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange-dark hover:shadow-lg hover:shadow-gp-orange/30 transition-all duration-300">
                    <span>Explorer les trajets</span>
                    <span class="text-sm">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
