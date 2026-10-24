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
    </div>
</x-app-layout>
