<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gp-ink leading-tight">
            Validation de votre profil Groupeur
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-gp border border-gp-border p-8 text-center">
                @if ($profile && $profile->validation_status === \App\Enums\GroupeurStatus::refuse)
                    <!-- Dossier refusé -->
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                        ✕
                    </div>
                    <h3 class="text-2xl font-extrabold text-gp-ink">Dossier non validé</h3>
                    <p class="mt-2 text-sm text-gp-warm">
                        Votre dossier n'a pas pu être validé par notre équipe pour le motif suivant :
                    </p>

                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-gp text-left">
                        <p class="text-xs font-bold text-red-800 uppercase tracking-wide">Motif du refus</p>
                        <p class="text-sm text-red-900 mt-1 font-medium">
                            {{ $profile->rejection_reason ?? 'Pièce d\'identité non conforme ou illisible. Veuillez contacter le support.' }}
                        </p>
                    </div>

                    <div class="mt-6 p-5 bg-white border border-gp-border rounded-gp text-left">
                        <h4 class="font-bold text-sm text-gp-ink mb-2">Mettre à jour mon justificatif d'identité</h4>
                        <p class="text-xs text-gp-warm mb-3">Téléversez un document lisible et conforme (PDF, JPG, PNG) pour soumettre à nouveau votre dossier.</p>

                        <form method="POST" action="{{ route('groupeur.resubmit') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="file" name="id_document" required accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-xs text-gp-warm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gp-ink file:text-white hover:file:bg-gp-orange file:cursor-pointer transition" />
                            <button type="submit" class="px-5 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white text-xs font-bold uppercase tracking-wider transition shadow-sm">
                                Soumettre à nouveau pour examen
                            </button>
                        </form>
                    </div>
                @else
                    <!-- En attente -->
                    <div class="w-16 h-16 bg-orange-100 text-gp-orange rounded-full flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                        ⏳
                    </div>
                    <h3 class="text-2xl font-extrabold text-gp-ink">Votre dossier est en cours d'examen</h3>
                    <p class="mt-3 text-sm text-gp-warm max-w-lg mx-auto leading-relaxed">
                        Merci d'avoir soumis votre inscription en tant que groupeur GPsen. Par mesure de sécurité pour nos expéditeurs, chaque pièce d'identité est vérifiée manuellement par notre équipe avant activation.
                    </p>

                    <div class="mt-8 grid sm:grid-cols-3 gap-4 text-left">
                        <div class="p-4 bg-gp-sand/40 rounded-gp border border-gp-border">
                            <span class="text-xs font-bold text-gp-orange-dark">Étape 1</span>
                            <h4 class="font-bold text-sm text-gp-ink mt-1">Dossier reçu</h4>
                            <p class="text-xs text-gp-warm mt-1">Vos coordonnées et votre pièce sont bien enregistrées.</p>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-gp border border-gp-orange/40">
                            <span class="text-xs font-bold text-gp-orange">Étape 2 (En cours)</span>
                            <h4 class="font-bold text-sm text-gp-ink mt-1">Vérification admin</h4>
                            <p class="text-xs text-gp-warm mt-1">Contrôle de conformité de votre pièce d'identité.</p>
                        </div>
                        <div class="p-4 bg-gp-sand/40 rounded-gp border border-gp-border opacity-70">
                            <span class="text-xs font-bold text-gp-warm">Étape 3</span>
                            <h4 class="font-bold text-sm text-gp-ink mt-1">Publication ouverte</h4>
                            <p class="text-xs text-gp-warm mt-1">Vous pourrez publier vos trajets et recevoir des colis.</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gp-border flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-gp-warm">
                            Délai moyen de validation : <strong>moins de 24h</strong>.
                        </p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-bold text-gp-warm hover:text-gp-orange transition">
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
