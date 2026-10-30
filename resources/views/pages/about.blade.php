<x-public-layout :title="__('À propos de nous — GPsen')">
    {{-- Bannière Hero "À propos" avec fond sombre 2026 et halos --}}
    <section class="relative bg-gp-ink text-gp-sand py-20 overflow-hidden border-b border-gp-border">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-gp-orange/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-10 w-80 h-80 bg-gp-orange/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-4 text-center space-y-4">
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black tracking-widest uppercase bg-white/10 text-gp-orange border border-white/10 shadow-sm backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-gp-orange animate-ping"></span>
                Notre Histoire & Notre Mission
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Qui sommes-nous ? Bienvenue chez <span class="text-gp-orange">GPsen</span>
            </h1>
            <p class="text-sm md:text-base text-gp-border max-w-2xl mx-auto leading-relaxed">
                La plateforme sénégalaise et internationale de groupage participatif, réinventant le transport de colis entre particuliers en alliant économie, sécurité et solidarité.
            </p>
        </div>
    </section>

    {{-- Corps de la page --}}
    <div class="max-w-5xl mx-auto px-4 py-16 space-y-16">
        
        {{-- Section 1 : D'où vient GPsen ? (Le concept du GP) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <div class="space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-orange">Origine & Raison d'être</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gp-ink leading-snug">
                    Moderniser et sécuriser la tradition du « GP »
                </h2>
                <p class="text-sm text-gp-warm leading-relaxed">
                    Dans la culture sénégalaise et au sein de la diaspora, le <strong>« GP » (Gratuité Partagée / Groupage de Particulier)</strong> a toujours été le moyen privilégié pour acheminer des effets personnels, des denrées locales, des documents ou des cadeaux entre Dakar, Paris, Milan, New York et le reste du monde.
                </p>
                <p class="text-sm text-gp-warm leading-relaxed">
                    Longtemps cantonné aux groupes informels sur les réseaux sociaux et au bouche-à-oreille non vérifié, le groupage souffrait d'un manque de sécurité, de visibilité et de traçabilité. <strong>GPsen est né pour structurer, numériser et sécuriser</strong> cette pratique historique.
                </p>
            </div>
            <div class="p-8 rounded-2xl bg-white border border-gp-border shadow-md space-y-4">
                <div class="flex items-center gap-3 pb-4 border-b border-gp-border/60">
                    <span class="w-10 h-10 rounded-xl bg-orange-100 text-gp-orange-dark flex items-center justify-center font-black text-lg">💡</span>
                    <div>
                        <h3 class="font-bold text-gp-ink text-sm">Le constat de départ</h3>
                        <p class="text-[11px] text-gp-warm">Des milliers de kilos inexploités chaque jour</p>
                    </div>
                </div>
                <p class="text-xs text-gp-warm leading-relaxed">
                    Des voyageurs prennent l'avion ou la route avec des valises à moitié vides, pendant que des familles et commerçants cherchent désespérément à faire parvenir un paquet à moindre coût. GPsen crée ce pont de confiance mutuelle.
                </p>
            </div>
        </div>

        {{-- Section 2 : Les 3 Piliers Fondamentaux de GPsen --}}
        <div class="space-y-8">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-orange">Nos Engagements</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gp-ink">Pourquoi choisir GPsen ?</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Pilule 1 --}}
                <div class="p-6 rounded-2xl bg-white border border-gp-border shadow-sm hover:shadow-md transition space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-gp-sand flex items-center justify-center text-gp-orange text-xl font-bold">
                        🛡️
                    </div>
                    <h3 class="text-base font-bold text-gp-ink">Identités Vérifiées</h3>
                    <p class="text-xs text-gp-warm leading-relaxed">
                        Chaque profil voyageur est contrôlé manuellement par notre équipe via une pièce d'identité officielle (CNI ou Passeport) avant toute publication de trajet.
                    </p>
                </div>

                {{-- Pilule 2 --}}
                <div class="p-6 rounded-2xl bg-white border border-gp-border shadow-sm hover:shadow-md transition space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-gp-sand flex items-center justify-center text-gp-orange text-xl font-bold">
                        💰
                    </div>
                    <h3 class="text-base font-bold text-gp-ink">Économique & Rentable</h3>
                    <p class="text-xs text-gp-warm leading-relaxed">
                        Les expéditeurs paient jusqu'à 3 fois moins cher que le fret aérien classique, tandis que les voyageurs rentabilisent une partie conséquente de leur billet d'avion.
                    </p>
                </div>

                {{-- Pilule 3 --}}
                <div class="p-6 rounded-2xl bg-white border border-gp-border shadow-sm hover:shadow-md transition space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-gp-sand flex items-center justify-center text-gp-orange text-xl font-bold">
                        🔑
                    </div>
                    <h3 class="text-base font-bold text-gp-ink">Code de Remise OTP</h3>
                    <p class="text-xs text-gp-warm leading-relaxed">
                        Un protocole de remise sécurisé avec un code confidentiel à 4 chiffres garantit que le colis n'est validé comme livré que lors de la remise physique en main propre.
                    </p>
                </div>
            </div>
        </div>

        {{-- Section 3 : Qui a conçu la plateforme ? --}}
        <div class="p-8 md:p-10 rounded-2xl bg-gp-cream border-2 border-gp-ink shadow-[8px_8px_0_0_#F26B1D] flex flex-col md:flex-row items-center gap-8">
            <div class="w-24 h-24 rounded-full bg-gp-ink text-white flex items-center justify-center text-3xl font-black shrink-0 border-4 border-gp-orange shadow-md">
                IT
            </div>
            <div class="space-y-2 flex-1 text-center md:text-left">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-orange-dark">Projet & Conception</span>
                <h3 class="text-xl md:text-2xl font-extrabold text-gp-ink">Conçu et Développé par Ibrahima Tall</h3>
                <p class="text-xs md:text-sm text-gp-warm leading-relaxed">
                    Étudiant en <strong>Licence 3 Génie Logiciel à l'ESTM Dakar (École Supérieure de Technologie et de Management)</strong>. GPsen a été conçu comme projet académique et professionnel rigoureux, répondant aux standards modernes d'ingénierie logicielle (architecture Laravel monolithique robuste, conformité à la loi sénégalaise 2008-12 sur les données personnelles, et ergonomie mobile-first).
                </p>
            </div>
        </div>

        {{-- Section 4 : Contact direct & Appel à l'action --}}
        <div class="text-center pt-6 space-y-5">
            <p class="text-sm font-semibold text-gp-ink">
                Une question, une suggestion ou besoin d'assistance ?
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="tel:+221778744844" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gp-ink text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange transition shadow-sm">
                    📞 +221 77 874 48 44
                </a>
                <a href="{{ route('trajets.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gp-orange text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange-dark transition shadow-sm">
                    Découvrir les trajets disponibles &rarr;
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
