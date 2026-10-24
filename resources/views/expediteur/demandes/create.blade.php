<x-app-layout :title="__('Envoyer une demande d\'envoi')">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('trajets.show', $trip) }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">
                &larr; Retour au trajet
            </a>
            <h1 class="text-2xl font-extrabold text-gp-ink mt-2">Demande d'envoi de colis</h1>
            <p class="text-xs text-gp-warm">Trajet : <strong>{{ $trip->origin_city }} &rarr; {{ $trip->destination_city }}</strong> (Départ le {{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d F Y') }})</p>
        </div>

        <!-- Formulaire court en 3 blocs selon UI/UX §5.6 -->
        <form method="POST" action="{{ route('expediteur.requests.store', $trip) }}" class="bg-white rounded-gp border border-gp-border p-6 shadow-sm space-y-6">
            @csrf

            <!-- Bloc 1 : Le colis (description, catégorie, poids) -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                    1. Le colis
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="category" value="Catégorie d'objet" class="font-bold text-xs uppercase tracking-wider" />
                        <select id="category" name="category" required class="block mt-1 w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                            <option value="vetements" {{ old('category') == 'vetements' ? 'selected' : '' }}>Vêtements / Textiles</option>
                            <option value="documents" {{ old('category') == 'documents' ? 'selected' : '' }}>Documents / Papiers</option>
                            <option value="electronique" {{ old('category') == 'electronique' ? 'selected' : '' }}>Petite électronique (téléphone, accessoires)</option>
                            <option value="cosmetiques" {{ old('category') == 'cosmetiques' ? 'selected' : '' }}>Cosmétiques / Soins</option>
                            <option value="denrees_seches" {{ old('category') == 'denrees_seches' ? 'selected' : '' }}>Denrées sèches emballées</option>
                            <option value="autre" {{ old('category') == 'autre' ? 'selected' : '' }}>Autre objet autorisé</option>
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="weight_kg" value="Poids estimé (en kg)" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="weight_kg" name="weight_kg" type="number" step="0.5" min="0.5" max="100" class="block mt-1 w-full" :value="old('weight_kg', '2')" required />
                        <p class="text-[11px] text-gp-warm mt-1">Capacité disponible sur ce trajet : <strong>{{ $remainingCapacity }} kg</strong>.</p>
                        <x-input-error :messages="$errors->get('weight_kg')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="description" value="Description détaillée du contenu" class="font-bold text-xs uppercase tracking-wider" />
                    <textarea id="description" name="description" rows="3" required placeholder="Ex. 2 boubous traditionnels emballés sous vide et une paire de chaussures..." class="block mt-1 w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="declared_value" value="Valeur estimée du colis en FCFA (optionnel)" class="font-bold text-xs uppercase tracking-wider" />
                    <x-text-input id="declared_value" name="declared_value" type="number" min="0" step="500" class="block mt-1 w-full" :value="old('declared_value')" placeholder="Ex. 50000" />
                    <x-input-error :messages="$errors->get('declared_value')" class="mt-1" />
                </div>
            </div>

            <!-- Bloc 2 : Le destinataire (nom, téléphone, ville) -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                    2. Le destinataire à l'arrivée
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="receiver_name" value="Nom complet" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="receiver_name" name="receiver_name" type="text" class="block mt-1 w-full" :value="old('receiver_name')" required placeholder="Ex. Ousmane Diop" />
                        <x-input-error :messages="$errors->get('receiver_name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="receiver_phone" value="Téléphone / WhatsApp" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="receiver_phone" name="receiver_phone" type="tel" class="block mt-1 w-full" :value="old('receiver_phone')" required placeholder="Ex. +33 6 12 34 56 78" />
                        <x-input-error :messages="$errors->get('receiver_phone')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="receiver_city" value="Ville de remise" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="receiver_city" name="receiver_city" type="text" class="block mt-1 w-full" :value="old('receiver_city', $trip->destination_city)" required />
                        <x-input-error :messages="$errors->get('receiver_city')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Bloc 3 : Récapitulatif et engagement financier (§5.6) -->
            <div class="space-y-3 p-4 bg-gp-sand/40 border border-gp-border rounded-gp text-xs text-gp-ink">
                <h3 class="font-bold uppercase tracking-wider text-gp-ink">3. Récapitulatif</h3>
                <p>
                    Tarif indicatif du groupeur : <strong>{{ number_format($trip->price_per_kg, 0, ',', ' ') }} FCFA / kg</strong>.
                </p>
                <p class="text-gp-warm italic">
                    « Les conditions financières et les modalités de paiement sont convenues directement avec le groupeur. »
                </p>
            </div>

            <div class="pt-4 border-t border-gp-border flex items-center justify-end gap-3">
                <a href="{{ route('trajets.show', $trip) }}" class="px-5 py-2.5 rounded-gp border border-gp-border text-xs font-bold text-gp-ink hover:bg-gp-sand transition">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    Envoyer ma demande d'envoi
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
