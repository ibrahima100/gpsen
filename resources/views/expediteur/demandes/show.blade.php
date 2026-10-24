<x-app-layout :title="__('Suivi de mon colis')">
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showCancelModal: false }">
        <div class="flex items-center justify-between">
            <a href="{{ route('expediteur.demandes.index') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">
                &larr; Retour à mes demandes
            </a>
            <x-status-badge :status="$request->status" />
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-gp bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Colonne gauche : Suivi et Chronologie datée (§5.7) -->
            <div class="md:col-span-2 space-y-6">
                <!-- En-tête de la demande -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gp-border">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Acheminement</span>
                            <h1 class="text-xl font-extrabold text-gp-ink mt-0.5">
                                {{ $trip->origin_city }} &rarr; {{ $trip->destination_city }}
                            </h1>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Colis</span>
                            <p class="text-lg font-black text-gp-ink">{{ $request->weight_kg }} kg</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <p><strong>Description :</strong> {{ $request->description }}</p>
                        <p><strong>Catégorie :</strong> {{ $request->category }}</p>
                        @if($request->declared_value)
                            <p><strong>Valeur déclarée :</strong> {{ number_format($request->declared_value, 0, ',', ' ') }} FCFA</p>
                        @endif
                        <p><strong>Destinataire :</strong> {{ $request->receiver_name }} ({{ $request->receiver_phone }}) à {{ $request->receiver_city }}</p>
                    </div>

                    @if($request->decision_note)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-gp text-xs text-red-900">
                            <strong>Motif communiqué :</strong> {{ $request->decision_note }}
                        </div>
                    @endif
                </div>

                <!-- Chronologie datée selon UI/UX §5.7 -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Chronologie du suivi
                    </h2>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gp-border">
                        @forelse($histories as $history)
                            <div class="relative">
                                <div class="absolute -left-[27px] top-1 w-3 h-3 rounded-full bg-gp-orange ring-4 ring-white"></div>
                                <div>
                                    <span class="text-xs font-bold text-gp-ink uppercase tracking-wider">
                                        {{ $history->status }}
                                    </span>
                                    <span class="text-[11px] text-gp-warm ml-2">
                                        {{ $history->created_at->translatedFormat('d M Y à H:i') }}
                                    </span>
                                    @if($history->note)
                                        <p class="text-xs text-gp-warm mt-1 bg-gp-sand/30 p-2 rounded-gp border border-gp-border/60">
                                            {{ $history->note }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gp-warm">Historique en cours de constitution.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Groupeur et Contact WhatsApp conditionnel (§5.7) -->
            <div class="space-y-6">
                <!-- Bloc Groupeur avec WhatsApp -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gp-warm">Votre groupeur</h3>

                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-base text-gp-ink">{{ $groupeur->name }}</h4>
                            <span class="text-emerald-700 font-bold" title="Vérifié par GPsen">✓</span>
                        </div>
                        <p class="text-xs text-gp-warm mt-0.5">Note : {{ number_format($groupeur->groupeurProfile?->average_rating ?? 5.0, 1) }} ★</p>
                    </div>

                    @php
                        $isAccepted = in_array($request->status?->value ?? (string)$request->status, ['acceptee', 'collectee', 'en_transit', 'livree']);
                        $cleanPhone = preg_replace('/[^0-9]/', '', $groupeur->phone);
                        $waText = urlencode("Bonjour {$groupeur->name}, je vous contacte depuis GPsen concernant mon colis de {$request->weight_kg} kg sur votre trajet {$trip->origin_city} -> {$trip->destination_city}.");
                    @endphp

                    @if($isAccepted)
                        <!-- Bouton WhatsApp débloqué selon §5.7 -->
                        <div class="pt-2">
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="w-full py-3 px-4 rounded-gp bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2 text-center">
                                <span>📱</span> Contacter sur WhatsApp
                            </a>
                            <p class="text-[11px] text-gp-warm text-center mt-1.5">
                                Numéro direct : <strong>{{ $groupeur->phone }}</strong>
                            </p>
                        </div>
                    @else
                        <!-- Contact masqué avant acceptation -->
                        <div class="p-4 bg-orange-50 border border-orange-200 rounded-gp text-xs text-gp-ink text-center">
                            <span class="font-bold block">🔒 Coordonnées protégées</span>
                            <p class="text-gp-warm mt-1 text-[11px]">
                                Le numéro WhatsApp direct du voyageur sera activé dès que votre colis aura été accepté.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Actions de gestion (Annulation par l'expéditeur si en attente ou acceptée) -->
                @if(in_array($request->status?->value ?? (string)$request->status, ['en_attente', 'acceptee']))
                    <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm text-center">
                        <button type="button" @click="showCancelModal = true" class="text-xs font-bold text-red-600 hover:underline">
                            Annuler cette demande d'envoi
                        </button>
                    </div>

                    <!-- Modale annulation expéditeur -->
                    <div x-show="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
                        <div @click.away="showCancelModal = false" class="bg-white rounded-gp p-6 max-w-md w-full shadow-xl border border-gp-border text-left">
                            <h4 class="text-base font-bold text-gp-ink">Annulation de la demande</h4>
                            <p class="text-xs text-gp-warm mt-1">Veuillez indiquer le motif d'annulation pour en avertir le groupeur.</p>

                            <form method="POST" action="{{ route('expediteur.demandes.cancel', $request) }}" class="mt-4">
                                @csrf
                                <textarea name="cancel_reason" rows="3" required placeholder="Ex. Le destinataire n'a plus besoin du colis / Changement de plan..." class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange"></textarea>

                                <div class="mt-4 flex justify-end gap-2">
                                    <button type="button" @click="showCancelModal = false" class="px-4 py-2 rounded-gp border border-gp-border text-xs font-bold text-gp-warm hover:bg-gp-sand">
                                        Fermer
                                    </button>
                                    <button type="submit" class="px-4 py-2 rounded-gp bg-red-600 hover:bg-red-700 text-white text-xs font-bold">
                                        Confirmer l'annulation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
