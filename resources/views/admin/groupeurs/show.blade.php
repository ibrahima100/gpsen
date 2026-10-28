<x-app-layout :title="__('Dossier Groupeur')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.groupeurs.index') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">
                &larr; Retour à la file des groupeurs
            </a>
            <x-status-badge :status="$profile->validation_status" />
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-gp bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Disposition 2 colonnes selon UI/UX §5.9 : Pièce à gauche, informations à droite -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Colonne gauche : Pièce d'identité sécurisée -->
            <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink">Justificatif d'identité</h2>
                    <a href="{{ route('admin.groupeurs.document', $profile) }}" target="_blank" class="text-xs font-bold text-gp-orange-dark hover:underline">
                        Ouvrir en plein écran &nearr;
                    </a>
                </div>

                <div class="border border-gp-border rounded-gp bg-gp-sand/20 overflow-hidden min-h-[360px] flex items-center justify-center p-2">
                    @php
                        $ext = strtolower(pathinfo($profile->id_document_path, PATHINFO_EXTENSION));
                    @endphp

                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                        <img src="{{ route('admin.groupeurs.document', $profile) }}" alt="Pièce d'identité" class="max-h-[460px] w-auto object-contain rounded" />
                    @else
                        <iframe src="{{ route('admin.groupeurs.document', $profile) }}" class="w-full h-[460px] border-0 rounded"></iframe>
                    @endif
                </div>
                <p class="text-[11px] text-gp-warm mt-2">
                    Fichier privé : <code class="text-gp-ink">{{ $profile->id_document_path }}</code>
                </p>
            </div>

            <!-- Colonne droite : Informations et Décision -->
            <div class="space-y-6">
                <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                        Informations du candidat
                    </h2>

                    <div>
                        <span class="text-xs text-gp-warm">Nom complet</span>
                        <p class="text-base font-bold text-gp-ink">{{ $profile->user->name }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gp-warm">Adresse email</span>
                            <p class="text-sm font-semibold text-gp-ink">{{ $profile->user->email }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gp-warm">Téléphone WhatsApp</span>
                            <p class="text-sm font-semibold text-gp-ink">{{ $profile->user->phone }}</p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gp-warm">Lignes ou trajets habituels</span>
                        <p class="text-sm text-gp-ink font-medium">{{ $profile->usual_routes ?: 'Non renseigné' }}</p>
                    </div>

                    <div>
                        <span class="text-xs text-gp-warm">Présentation / Bio</span>
                        <p class="text-xs text-gp-ink bg-gp-sand/30 p-3 rounded-gp border border-gp-border/60 leading-relaxed mt-1">
                            {{ $profile->bio }}
                        </p>
                    </div>

                    @if($profile->rejection_reason)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-gp text-xs">
                            <span class="font-bold text-red-800">Dernier motif de refus :</span>
                            <p class="text-red-900 mt-0.5">{{ $profile->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                <!-- Décision Administrateur (Valider / Refuser avec modale/motif) -->
                <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm space-y-4" x-data="{ showRejectModal: false }">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gp-ink">Décision de conformité</h3>

                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('admin.groupeurs.validate', $profile) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-3 rounded-gp bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                                ✓ Valider le groupeur
                            </button>
                        </form>

                        <button type="button" @click="showRejectModal = true" class="flex-1 py-3 rounded-gp border-2 border-red-600 text-red-600 hover:bg-red-50 font-bold text-xs uppercase tracking-wider transition">
                            ✕ Refuser le dossier
                        </button>
                    </div>

                    <!-- Bouton Avertissement avant fermeture -->
                    <div class="mt-4 pt-4 border-t border-gp-border">
                        <button type="button" @click="showWarnModal = true" class="w-full py-2.5 rounded-gp bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Envoyer un avertissement avant fermeture
                        </button>
                    </div>

                    <!-- Modale d'avertissement -->
                    <div x-show="showWarnModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
                        <div @click.away="showWarnModal = false" class="bg-white rounded-gp p-6 max-w-lg w-full shadow-xl border border-gp-border">
                            <h4 class="text-lg font-bold text-gp-ink flex items-center gap-2">
                                <span class="text-amber-500">⚠</span> Avertissement avant fermeture de compte
                            </h4>
                            <p class="text-xs text-gp-warm mt-1">
                                Le groupeur recevra un courriel officiel et une notification sur la plateforme détaillant le motif, le délai de régularisation et la marche à suivre.
                            </p>

                            <form method="POST" action="{{ route('admin.groupeurs.warn', $profile) }}" class="mt-4 space-y-4" x-data="{ reasonPreset: 'Non-respect des engagements de livraison' }">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gp-ink mb-1">Motif principal</label>
                                    <select name="reason_preset" x-model="reasonPreset" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                                        <option value="Non-respect des engagements de livraison">Non-respect des engagements de livraison</option>
                                        <option value="Signalements répétés pour retards injustifiés">Signalements répétés pour retards injustifiés</option>
                                        <option value="Comportement ou communication inappropriée">Comportement ou communication inappropriée</option>
                                        <option value="Suspicion de transport d'articles non déclarés ou non conformes">Suspicion de transport d'articles non déclarés ou non conformes</option>
                                        <option value="Incohérence sur la pièce d'identité ou profil obsolète">Incohérence sur la pièce d'identité ou profil obsolète</option>
                                        <option value="autre">Autre motif personnalisé...</option>
                                    </select>
                                </div>

                                <div x-show="reasonPreset === 'autre'">
                                    <label class="block text-xs font-bold text-gp-ink mb-1">Précisez le motif</label>
                                    <input type="text" name="custom_reason" placeholder="Ex: Dégradation de colis constatée..." class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gp-ink mb-1">Délai avant décision administrative (en jours)</label>
                                    <input type="number" name="delay_days" value="7" min="1" max="30" required class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                                    <p class="text-[11px] text-gp-warm mt-1">Délai accordé pour régulariser sa situation (ex. 7 jours).</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gp-ink mb-1">Détails ou consignes spécifiques (optionnel)</label>
                                    <textarea name="details" rows="3" placeholder="Indiquez les actions requises du groupeur avant expiration du délai..." class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange"></textarea>
                                </div>

                                <div class="mt-4 flex justify-end gap-2 pt-2 border-t border-gp-border">
                                    <button type="button" @click="showWarnModal = false" class="px-4 py-2 rounded-gp border border-gp-border text-xs font-bold text-gp-warm hover:bg-gp-sand">
                                        Annuler
                                    </button>
                                    <button type="submit" class="px-4 py-2 rounded-gp bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-sm">
                                        Envoyer l'avertissement officiel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modale de refus avec motif obligatoire -->
                    <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
                        <div @click.away="showRejectModal = false" class="bg-white rounded-gp p-6 max-w-md w-full shadow-xl border border-gp-border">
                            <h4 class="text-lg font-bold text-gp-ink">Motif du refus</h4>
                            <p class="text-xs text-gp-warm mt-1">Expliquez au groupeur pourquoi son dossier est rejeté (ex: document flou, non conforme).</p>

                            <form method="POST" action="{{ route('admin.groupeurs.reject', $profile) }}" class="mt-4">
                                @csrf
                                <textarea name="rejection_reason" rows="3" required placeholder="La pièce d'identité est floue et illisible. Veuillez soumettre une photo nette..." class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange"></textarea>

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

        <!-- Historique des avertissements envoyés au groupeur -->
        <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm">
            <h3 class="text-base font-extrabold text-gp-ink flex items-center justify-between">
                <span>Historique des avertissements notifiés ({{ $warnings->count() }})</span>
                @if($warnings->count() > 0)
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800">
                        Compte sous surveillance
                    </span>
                @endif
            </h3>

            @if($warnings->isEmpty())
                <p class="text-xs text-gp-warm mt-2">Aucun avertissement n'a été émis à ce jour pour ce groupeur.</p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach($warnings as $w)
                        <div class="p-4 rounded-gp bg-gp-sand/30 border border-gp-border/60 text-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gp-ink">{{ $w->reason }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Délai : {{ $w->delay_days }} jours
                                    </span>
                                </div>
                                @if($w->details)
                                    <p class="text-gp-warm text-[11px]">{{ $w->details }}</p>
                                @endif
                                <p class="text-[10px] text-gp-warm">
                                    Émis par <span class="font-semibold">{{ $w->admin->name }}</span> le {{ $w->sent_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-[11px] font-semibold text-gp-warm">
                                    Échéance indicative : {{ $w->sent_at->copy()->addDays($w->delay_days)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
