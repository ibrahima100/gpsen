<x-app-layout :title="__('Modifier le trajet')">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('groupeur.trips.index') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">
                    &larr; Mes trajets
                </a>
                <h1 class="text-2xl font-extrabold text-gp-ink mt-1">Modifier le trajet</h1>
                <p class="text-xs text-gp-warm">Ajustez les informations de votre départ.</p>
            </div>
            <x-status-badge :status="$trip->status" />
        </div>

        <form method="POST" action="{{ route('groupeur.trips.update', $trip) }}" class="bg-white rounded-gp border border-gp-border p-6 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <!-- Itinéraire -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                    1. Itinéraire
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="origin_country" value="Pays de départ" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="origin_country" name="origin_country" type="text" class="block mt-1 w-full" :value="old('origin_country', $trip->origin_country)" required />
                        <x-input-error :messages="$errors->get('origin_country')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="origin_city" value="Ville de départ" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="origin_city" name="origin_city" type="text" class="block mt-1 w-full" :value="old('origin_city', $trip->origin_city)" required />
                        <x-input-error :messages="$errors->get('origin_city')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="destination_country" value="Pays d'arrivée" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="destination_country" name="destination_country" type="text" class="block mt-1 w-full" :value="old('destination_country', $trip->destination_country)" required />
                        <x-input-error :messages="$errors->get('destination_country')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="destination_city" value="Ville d'arrivée" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="destination_city" name="destination_city" type="text" class="block mt-1 w-full" :value="old('destination_city', $trip->destination_city)" required />
                        <x-input-error :messages="$errors->get('destination_city')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Dates et capacité -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                    2. Dates, capacité et tarif
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="departure_date" value="Date de départ" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="departure_date" name="departure_date" type="date" class="block mt-1 w-full" :value="old('departure_date', $trip->departure_date->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('departure_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="arrival_date" value="Date d'arrivée estimée" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="arrival_date" name="arrival_date" type="date" class="block mt-1 w-full" :value="old('arrival_date', $trip->arrival_date?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('arrival_date')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="capacity_kg" value="Kilos totaux (kg)" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="capacity_kg" name="capacity_kg" type="number" step="0.5" min="1" max="1000" class="block mt-1 w-full" :value="old('capacity_kg', $trip->capacity_kg)" required />
                        <x-input-error :messages="$errors->get('capacity_kg')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="price_per_kg" value="Tarif indicatif par kg (FCFA)" class="font-bold text-xs uppercase tracking-wider" />
                        <x-text-input id="price_per_kg" name="price_per_kg" type="number" min="500" step="100" class="block mt-1 w-full" :value="old('price_per_kg', $trip->price_per_kg)" required />
                        <x-input-error :messages="$errors->get('price_per_kg')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Conditions de transport -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gp-ink pb-2 border-b border-gp-border">
                    3. Conditions de prise en charge
                </h2>

                <div>
                    <x-input-label for="pickup_info" value="Lieu et créneau de collecte des colis" class="font-bold text-xs uppercase tracking-wider" />
                    <x-text-input id="pickup_info" name="pickup_info" type="text" class="block mt-1 w-full" :value="old('pickup_info', $trip->pickup_info)" />
                    <x-input-error :messages="$errors->get('pickup_info')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="accepted_items" value="Objets acceptés" class="font-bold text-xs uppercase tracking-wider" />
                        <textarea id="accepted_items" name="accepted_items" rows="2" class="block mt-1 w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">{{ old('accepted_items', $trip->accepted_items) }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="prohibited_items" value="Objets interdits" class="font-bold text-xs uppercase tracking-wider" />
                        <textarea id="prohibited_items" name="prohibited_items" rows="2" class="block mt-1 w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">{{ old('prohibited_items', $trip->prohibited_items) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gp-border flex items-center justify-between">
                <!-- Annulation avec motif -->
                <button type="button" @click="showCancelModal = true" class="text-xs font-bold text-red-600 hover:underline">
                    Annuler définitivement ce trajet
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('groupeur.trips.index') }}" class="px-5 py-2.5 rounded-gp border border-gp-border text-xs font-bold text-gp-ink hover:bg-gp-sand transition">
                        Retour
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white text-xs font-bold uppercase tracking-wider transition shadow-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
