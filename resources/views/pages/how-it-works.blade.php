<x-public-layout :title="__('Comment ça marche — GPsen')">
    <div class="relative bg-gp-ink text-gp-sand min-h-[calc(100vh-4rem)] py-16 md:py-24 overflow-hidden">
        {{-- Effets de lumière d'arrière-plan en dégradé néo-moderne 2026 --}}
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-gp-orange/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-gp-orange/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-white/[0.03] rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 space-y-16">
            {{-- En-tête de section --}}
            <div class="text-center max-w-2xl mx-auto space-y-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black tracking-widest uppercase bg-white/10 text-gp-orange border border-white/10 shadow-sm backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-gp-orange animate-ping"></span>
                    Protocole GPsen
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Comment fonctionne <span class="text-gp-orange">GPsen</span> ?
                </h1>
                <p class="text-sm md:text-base text-gp-border max-w-xl mx-auto leading-relaxed">
                    Une chaîne collaborative transparente, sécurisée et fluide reliant les expéditeurs et les voyageurs certifiés du monde entier.
                </p>
            </div>

            {{-- Les 3 Grilles avec cartes immersives sombres & micro-animations --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Étape 01 --}}
                <div class="group relative bg-white/[0.04] hover:bg-white/[0.07] border border-white/10 hover:border-gp-orange/50 rounded-2xl p-8 backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_15px_30px_-10px_rgba(242,107,29,0.25)] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-6 border-b border-white/10">
                            <span class="text-4xl font-black text-gp-orange/80 group-hover:text-gp-orange group-hover:scale-110 transition-transform duration-300 inline-block">01</span>
                            <span class="p-3 rounded-xl bg-white/5 text-gp-orange border border-white/10 group-hover:bg-gp-orange group-hover:text-white transition-colors duration-300 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-white mt-6 group-hover:text-gp-orange transition-colors">
                            Trouver ou publier un trajet
                        </h2>
                        <p class="text-xs text-gp-border/90 mt-3 leading-relaxed">
                            Les voyageurs certifiés par pièce d'identité publient leur itinéraire, date et capacité en bagages. Les expéditeurs sélectionnent le trajet adapté en toute transparence.
                        </p>
                    </div>
                    <div class="mt-8 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-gp-border font-medium">
                        <span>Recherche par ville & pays</span>
                        <span class="text-gp-orange font-bold">&rarr;</span>
                    </div>
                </div>

                {{-- Étape 02 --}}
                <div class="group relative bg-white/[0.04] hover:bg-white/[0.07] border border-white/10 hover:border-gp-orange/50 rounded-2xl p-8 backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_15px_30px_-10px_rgba(242,107,29,0.25)] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-6 border-b border-white/10">
                            <span class="text-4xl font-black text-gp-orange/80 group-hover:text-gp-orange group-hover:scale-110 transition-transform duration-300 inline-block">02</span>
                            <span class="p-3 rounded-xl bg-white/5 text-gp-orange border border-white/10 group-hover:bg-gp-orange group-hover:text-white transition-colors duration-300 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-white mt-6 group-hover:text-gp-orange transition-colors">
                            Réservation & WhatsApp direct
                        </h2>
                        <p class="text-xs text-gp-border/90 mt-3 leading-relaxed">
                            L'expéditeur décrit son colis. Dès que le groupeur confirme la demande, le lien direct WhatsApp est débloqué pour convenir de l'horaire et du lieu de collecte du paquet.
                        </p>
                    </div>
                    <div class="mt-8 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-gp-border font-medium">
                        <span>Échanges directs sécurisés</span>
                        <span class="text-gp-orange font-bold">&rarr;</span>
                    </div>
                </div>

                {{-- Étape 03 --}}
                <div class="group relative bg-white/[0.04] hover:bg-white/[0.07] border border-white/10 hover:border-gp-orange/50 rounded-2xl p-8 backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_15px_30px_-10px_rgba(242,107,29,0.25)] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-6 border-b border-white/10">
                            <span class="text-4xl font-black text-gp-orange/80 group-hover:text-gp-orange group-hover:scale-110 transition-transform duration-300 inline-block">03</span>
                            <span class="p-3 rounded-xl bg-white/5 text-gp-orange border border-white/10 group-hover:bg-gp-orange group-hover:text-white transition-colors duration-300 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-white mt-6 group-hover:text-gp-orange transition-colors">
                            Code OTP & Livraison vérifiée
                        </h2>
                        <p class="text-xs text-gp-border/90 mt-3 leading-relaxed">
                            L'acheminement est tracé pas à pas. À destination, un code confidentiel de remise à 4 chiffres transmis au destinataire garantit la validation infalsifiable de la remise physique.
                        </p>
                    </div>
                    <div class="mt-8 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-gp-border font-medium">
                        <span>Code de remise OTP & Avis</span>
                        <span class="text-gp-orange font-bold">&rarr;</span>
                    </div>
                </div>
            </div>

            {{-- Bouton d'action --}}
            <div class="text-center pt-8">
                <a href="{{ route('trajets.index') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-gp-orange hover:bg-gp-orange-dark text-white font-extrabold text-xs uppercase tracking-widest transition-all duration-300 shadow-[0_10px_20px_-5px_rgba(242,107,29,0.5)] hover:scale-105 active:scale-95">
                    <span>Explorer les départs disponibles</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
