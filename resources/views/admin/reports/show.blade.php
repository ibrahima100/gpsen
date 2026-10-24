<x-admin-layout>
    <x-slot name="header">Détail du signalement #{{ $report->id }}</x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-gp-warm hover:text-gp-ink transition">
                ← Retour à la liste des signalements
            </a>

            @php
                $statusStyles = [
                    'ouvert' => 'bg-red-100 text-red-800 border-red-200',
                    'en_cours' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'traite' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'rejete' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $statusStyles[$report->status] ?? 'bg-gp-sand text-gp-ink' }}">
                Statut : {{ $report->status }}
            </span>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-green-50 border border-green-200 text-green-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche : Détails du signalement -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Motif et Description -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Faits signalés
                    </h2>

                    <div>
                        <span class="text-xs font-bold text-gp-warm uppercase tracking-wider block">Motif invoqué :</span>
                        <p class="text-base font-bold text-gp-ink mt-0.5">
                            @php
                                $reasons = [
                                    'colis_non_conforme' => 'Colis non conforme aux déclarations',
                                    'retard_excessif' => 'Retard excessif ou non-respect des engagements',
                                    'comportement_inapproprie' => 'Comportement inapproprié ou incivilité',
                                    'suspicion_fraude' => 'Suspicion de fraude ou fausse annonce',
                                    'autre' => 'Autre problème',
                                ];
                            @endphp
                            {{ $reasons[$report->reason] ?? $report->reason }}
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-gp-warm uppercase tracking-wider block">Description détaillée :</span>
                        <div class="mt-1 p-4 bg-gp-sand/30 rounded-gp border border-gp-border text-xs text-gp-ink leading-relaxed whitespace-pre-line">
                            {{ $report->description }}
                        </div>
                    </div>

                    <div class="text-[11px] text-gp-warm pt-2 border-t border-gp-border/60">
                        Signalé le {{ $report->created_at->translatedFormat('d F Y à H:i') }} (il y a {{ $report->created_at->diffForHumans() }})
                    </div>
                </div>

                <!-- Élément et Utilisateur mis en cause -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Élément et Partie mise en cause
                    </h2>

                    @if($report->targetUser)
                        <div class="p-4 bg-red-50/50 border border-red-200 rounded-gp flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-red-900 uppercase tracking-wider">Utilisateur mis en cause :</span>
                                <h3 class="text-base font-bold text-gp-ink">{{ $report->targetUser->name }}</h3>
                                <p class="text-xs text-gp-warm">{{ $report->targetUser->email }} • {{ $report->targetUser->phone }}</p>
                                <p class="text-xs text-gp-warm mt-1">Rôle : <span class="font-bold text-gp-ink capitalize">{{ $report->targetUser->role?->value ?? $report->targetUser->role }}</span> | Statut actuel : <span class="font-bold uppercase {{ $report->targetUser->status === 'actif' ? 'text-emerald-700' : 'text-red-700' }}">{{ $report->targetUser->status }}</span></p>
                            </div>

                            <form method="POST" action="{{ route('admin.users.toggleStatus', $report->targetUser) }}" onsubmit="return confirm('Confirmez-vous le changement de statut de cet utilisateur ?')">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 rounded-gp text-xs font-bold uppercase tracking-wider transition {{ $report->targetUser->status === 'actif' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                                    {{ $report->targetUser->status === 'actif' ? 'Suspendre cet utilisateur' : 'Réactiver l\'utilisateur' }}
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($report->trip)
                        <div class="p-4 bg-gp-sand/30 border border-gp-border rounded-gp text-xs space-y-1">
                            <span class="font-bold text-gp-ink uppercase tracking-wider">Trajet concerné :</span>
                            <p class="text-sm font-bold text-gp-ink">{{ $report->trip->origin_city }} → {{ $report->trip->destination_city }}</p>
                            <p class="text-gp-warm">Départ le : {{ \Carbon\Carbon::parse($report->trip->departure_date)->translatedFormat('d M Y') }} | Tarif : {{ number_format($report->trip->price_per_kg, 0, ',', ' ') }} FCFA/kg</p>
                            <div class="pt-1">
                                <a href="{{ route('trajets.show', $report->trip) }}" target="_blank" class="text-xs font-bold text-gp-orange hover:underline">
                                    Voir la fiche publique du trajet ↗
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($report->shipmentRequest)
                        <div class="p-4 bg-gp-sand/30 border border-gp-border rounded-gp text-xs space-y-1">
                            <span class="font-bold text-gp-ink uppercase tracking-wider">Demande d'envoi liée :</span>
                            <p class="text-sm font-bold text-gp-ink">Colis #{{ $report->shipmentRequest->id }} — {{ $report->shipmentRequest->weight_kg }} kg</p>
                            <p class="text-gp-warm">Statut de la demande : <strong>{{ $report->shipmentRequest->status }}</strong></p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Colonne droite : Déclarant et Outils de modération -->
            <div class="space-y-6">
                <!-- Déclarant -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gp-warm">Auteur du signalement</h3>
                    <div>
                        <h4 class="font-bold text-sm text-gp-ink">{{ $report->reporter->name }}</h4>
                        <p class="text-xs text-gp-warm">{{ $report->reporter->email }}</p>
                        <p class="text-xs text-gp-warm">{{ $report->reporter->phone }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold bg-gp-sand text-gp-ink uppercase">
                            {{ $report->reporter->role?->value ?? $report->reporter->role }}
                        </span>
                    </div>
                </div>

                <!-- Prise en charge administrative -->
                <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Action de modération
                    </h3>

                    <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="status" class="block text-xs font-bold text-gp-ink uppercase tracking-wider mb-1">
                                Statut du traitement
                            </label>
                            <select id="status" name="status" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                                <option value="ouvert" {{ $report->status === 'ouvert' ? 'selected' : '' }}>Ouvert (à traiter)</option>
                                <option value="en_cours" {{ $report->status === 'en_cours' ? 'selected' : '' }}>En cours d'examen</option>
                                <option value="traite" {{ $report->status === 'traite' ? 'selected' : '' }}>Traité (action prise / résolu)</option>
                                <option value="rejete" {{ $report->status === 'rejete' ? 'selected' : '' }}>Rejeté (non fondé)</option>
                            </select>
                        </div>

                        <div>
                            <label for="admin_note" class="block text-xs font-bold text-gp-ink uppercase tracking-wider mb-1">
                                Note interne admin
                            </label>
                            <textarea id="admin_note" 
                                      name="admin_note" 
                                      rows="4" 
                                      placeholder="Commentaire interne pour l'équipe (ex. avertissement envoyé par téléphone, compte suspendu pour fraude...)" 
                                      class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">{{ old('admin_note', $report->admin_note) }}</textarea>
                        </div>

                        @if($report->handler)
                            <p class="text-[11px] text-gp-warm">
                                Dernier traitement par : <strong>{{ $report->handler->name }}</strong>
                            </p>
                        @endif

                        <button type="submit" class="w-full py-2.5 px-4 rounded-gp bg-gp-ink hover:bg-gp-orange text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                            Enregistrer la décision
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
