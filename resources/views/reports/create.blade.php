<x-app-layout :title="'Signaler un problème'">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- En-tête -->
        <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 mb-2">
                Sécurité & Confiance
            </span>
            <h1 class="text-2xl font-extrabold text-gp-ink tracking-tight">Signaler une anomalie</h1>
            <p class="text-xs text-gp-warm mt-1">
                Aidez-nous à préserver la sécurité de la communauté GPsen en nous signalant tout manquement, comportement suspect ou non-respect de nos règles.
            </p>
        </div>

        <!-- Contexte de l'élément signalé -->
        @if($targetUser || $trip || $shipmentRequest)
            <div class="p-4 bg-gp-sand/30 rounded-gp border border-gp-border text-xs space-y-1">
                <span class="font-bold text-gp-ink uppercase tracking-wider block text-[11px]">Élément concerné :</span>
                @if($targetUser)
                    <p class="text-gp-ink"><strong>Utilisateur :</strong> {{ $targetUser->name }} ({{ $targetUser->phone }})</p>
                @endif
                @if($trip)
                    <p class="text-gp-ink"><strong>Trajet :</strong> {{ $trip->origin_city }} → {{ $trip->destination_city }} (Départ : {{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d M Y') }})</p>
                @endif
                @if($shipmentRequest)
                    <p class="text-gp-ink"><strong>Colis n° :</strong> #{{ $shipmentRequest->id }} ({{ $shipmentRequest->weight_kg }} kg)</p>
                @endif
            </div>
        @endif

        <!-- Formulaire de signalement -->
        <form method="POST" action="{{ route('reports.store') }}" class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-5">
            @csrf
            @if($targetUser)
                <input type="hidden" name="target_user_id" value="{{ $targetUser->id }}">
            @endif
            @if($trip)
                <input type="hidden" name="trip_id" value="{{ $trip->id }}">
            @endif
            @if($shipmentRequest)
                <input type="hidden" name="shipment_request_id" value="{{ $shipmentRequest->id }}">
            @endif

            <div>
                <label for="reason" class="block text-xs font-bold text-gp-ink uppercase tracking-wider mb-1.5">
                    Motif du signalement <span class="text-red-600">*</span>
                </label>
                <select id="reason" name="reason" required class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                    <option value="">Sélectionnez un motif...</option>
                    <option value="colis_non_conforme" {{ old('reason') === 'colis_non_conforme' ? 'selected' : '' }}>Colis non conforme aux déclarations</option>
                    <option value="retard_excessif" {{ old('reason') === 'retard_excessif' ? 'selected' : '' }}>Retard excessif ou non-respect des engagements</option>
                    <option value="comportement_inapproprie" {{ old('reason') === 'comportement_inapproprie' ? 'selected' : '' }}>Comportement inapproprié ou incivilité</option>
                    <option value="suspicion_fraude" {{ old('reason') === 'suspicion_fraude' ? 'selected' : '' }}>Suspicion de fraude ou fausse annonce</option>
                    <option value="autre" {{ old('reason') === 'autre' ? 'selected' : '' }}>Autre problème</option>
                </select>
                @error('reason')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-gp-ink uppercase tracking-wider mb-1.5">
                    Description détaillée des faits <span class="text-red-600">*</span>
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="5" 
                          required 
                          minlength="10" 
                          maxlength="1000" 
                          placeholder="Expliquez clairement la situation, les dates, les échanges ou les incidents constatés..." 
                          class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">{{ old('description') }}</textarea>
                <p class="text-[11px] text-gp-warm mt-1">Minimum 10 caractères, maximum 1000 caractères.</p>
                @error('description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-3.5 bg-gp-sand/30 rounded-gp text-[11px] text-gp-warm flex items-start gap-2">
                <svg class="w-4 h-4 text-gp-ink shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Votre signalement est confidentiel. L'utilisateur mis en cause ne saura pas qui est à l'origine du rapport. Notre équipe étudie chaque dossier avec diligence.</span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ url()->previous() }}" class="px-4 py-2.5 rounded-gp border border-gp-border text-xs font-bold text-gp-warm hover:bg-gp-sand transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-gp bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                    Envoyer le signalement
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
