<x-app-layout :title="__('Traitement de la demande')">
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showRejectModal: false }">
        <div class="flex items-center justify-between">
            <a href="{{ route('groupeur.demandes.index') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">
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

        @if($errors->has('weight'))
            <div class="p-4 rounded-gp bg-red-50 border border-red-200 text-red-800 text-xs font-bold">
                {{ $errors->first('weight') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Colonne gauche : Informations colis et expéditeur -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gp-border">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Trajet concerné</span>
                            <h1 class="text-lg font-extrabold text-gp-ink mt-0.5">
                                {{ $trip->origin_city }} &rarr; {{ $trip->destination_city }}
                            </h1>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Capacité restante</span>
                            <p class="text-sm font-bold text-gp-ink">{{ $remainingCapacity }} kg disponibles</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="p-3 bg-gp-sand/30 rounded-gp border border-gp-border/60">
                            <span class="font-bold text-gp-ink block text-sm mb-1">Détails du colis :</span>
                            <p><strong>Poids demandé :</strong> {{ $request->weight_kg }} kg</p>
                            <p><strong>Catégorie :</strong> {{ $request->category }}</p>
                            <p><strong>Description :</strong> {{ $request->description }}</p>
                            @if($request->declared_value)
                                <p><strong>Valeur déclarée :</strong> {{ number_format($request->declared_value, 0, ',', ' ') }} FCFA</p>
                            @endif
                        </div>

                        <div class="p-3 bg-white rounded-gp border border-gp-border">
                            <span class="font-bold text-gp-ink block text-sm mb-1">Destinataire :</span>
                            <p><strong>Nom :</strong> {{ $request->receiver_name }}</p>
                            <p><strong>Téléphone :</strong> {{ $request->receiver_phone }}</p>
                            <p><strong>Ville de remise :</strong> {{ $request->receiver_city }}</p>
                        </div>
                    </div>

                    @if($request->decision_note)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-gp text-xs text-red-900">
                            <strong>Motif communiqué :</strong> {{ $request->decision_note }}
                        </div>
                    @endif
                </div>

                <!-- Chronologie historique -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-3">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Historique des statuts
                    </h2>
                    <ul class="space-y-2 text-xs">
                        @foreach($request->statusHistories as $h)
                            <li class="flex items-center justify-between py-1 border-b border-gp-border/40">
                                <span><strong class="uppercase text-gp-ink">{{ $h->status }}</strong> {{ $h->note ? "— {$h->note}" : '' }}</span>
                                <span class="text-gp-warm text-[11px]">{{ $h->created_at->translatedFormat('d/m/Y H:i') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Avis et Évaluation (Étape 6) -->
                @include('partials.shipment-review-section', ['request' => $request])
            </div>

            <!-- Colonne droite : Actions de gestion et contact WhatsApp direct -->
            <div class="space-y-6">
                <!-- Bloc Expéditeur et Contact direct -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gp-warm">Expéditeur</h3>

                    <div>
                        <h4 class="font-bold text-base text-gp-ink">{{ $request->sender->name }}</h4>
                        <p class="text-xs text-gp-warm mt-0.5">{{ $request->sender->email }}</p>
                    </div>

                    @php
                        $isAccepted = in_array($request->status?->value ?? (string)$request->status, ['acceptee', 'collectee', 'en_transit', 'livree']);
                        $cleanPhone = preg_replace('/[^0-9]/', '', $request->sender->phone);
                        $waText = urlencode("Bonjour {$request->sender->name}, je suis {$trip->groupeur->name}, votre groupeur GPsen pour votre colis de {$request->weight_kg} kg vers {$trip->destination_city}.");
                    @endphp

                    @if($isAccepted)
                        <div>
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="w-full py-3 px-4 rounded-gp bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2 text-center">
                                <span>📱</span> Échanger sur WhatsApp
                            </a>
                            <p class="text-[11px] text-gp-warm text-center mt-1.5">
                                Téléphone : <strong>{{ $request->sender->phone }}</strong>
                            </p>
                        </div>
                    @else
                        <div class="p-3 bg-gp-sand/40 border border-gp-border rounded-gp text-[11px] text-gp-warm text-center">
                            Acceptez la demande pour débloquer l'échange WhatsApp direct.
                        </div>
                    @endif
                </div>

                <!-- Décisions et avancement de statut (Parcours F) -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gp-ink">Actions sur la demande</h3>

                    @php
                        $currentStatus = $request->status?->value ?? (string)$request->status;
                    @endphp

                    @if($currentStatus === 'en_attente')
                        <!-- Accepter ou Refuser -->
                        <div class="space-y-3">
                            <form method="POST" action="{{ route('groupeur.demandes.accept', $request) }}">
                                @csrf
                                <button type="submit" class="w-full py-3 rounded-gp bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                                    ✓ Accepter ce colis ({{ $request->weight_kg }} kg)
                                </button>
                            </form>

                            <button type="button" @click="showRejectModal = true" class="w-full py-2.5 rounded-gp border border-red-600 text-red-600 hover:bg-red-50 font-bold text-xs uppercase tracking-wider transition">
                                ✕ Refuser la demande
                            </button>
                        </div>
                    @elseif($currentStatus === 'acceptee')
                        <!-- Passer à collectée -->
                        <form method="POST" action="{{ route('groupeur.demandes.updateStatus', $request) }}">
                            @csrf
                            <input type="hidden" name="status" value="collectee">
                            <button type="submit" class="w-full py-3 rounded-gp bg-gp-ink hover:bg-gp-orange text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                                📦 Marquer comme Collecté (colis reçu)
                            </button>
                        </form>
                    @elseif($currentStatus === 'collectee')
                        <!-- Passer à en_transit -->
                        <form method="POST" action="{{ route('groupeur.demandes.updateStatus', $request) }}">
                            @csrf
                            <input type="hidden" name="status" value="en_transit">
                            <button type="submit" class="w-full py-3 rounded-gp bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                                🛫 Marquer En Transit (départ confirmé)
                            </button>
                        </form>
                    @elseif($currentStatus === 'en_transit')
                        <!-- Passer à livree -->
                        <form method="POST" action="{{ route('groupeur.demandes.updateStatus', $request) }}">
                            @csrf
                            <input type="hidden" name="status" value="livree">
                            <button type="submit" class="w-full py-3 rounded-gp bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                                ✅ Marquer comme Livré (remis au destinataire)
                            </button>
                        </form>
                    @elseif($currentStatus === 'livree')
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-gp text-xs font-bold text-emerald-800 text-center">
                            Ce colis a été remis et livré avec succès.
                        </div>
                    @else
                        <div class="p-3 bg-gp-sand/30 border border-gp-border rounded-gp text-xs text-gp-warm text-center">
                            Demande clôturée ({{ $currentStatus }}).
                        </div>
                    @endif
                </div>

                <!-- Modale de refus groupeur -->
                <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
                    <div @click.away="showRejectModal = false" class="bg-white rounded-gp p-6 max-w-md w-full shadow-xl border border-gp-border text-left">
                        <h4 class="text-base font-bold text-gp-ink">Refuser la demande</h4>
                        <p class="text-xs text-gp-warm mt-1">Indiquez à l'expéditeur pourquoi vous ne pouvez pas prendre son colis.</p>

                        <form method="POST" action="{{ route('groupeur.demandes.reject', $request) }}" class="mt-4">
                            @csrf
                            <textarea name="decision_note" rows="3" required placeholder="Ex. Pas assez d'espace dans les valises / Nature de colis non acceptée..." class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange"></textarea>

                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-gp border border-gp-border text-xs font-bold text-gp-warm hover:bg-gp-sand">
                                    Annuler
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-gp bg-red-600 hover:bg-red-700 text-white text-xs font-bold">
                                    Confirmer le refus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
