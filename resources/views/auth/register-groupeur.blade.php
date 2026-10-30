<x-guest-layout title="Inscription Groupeur">
    <div class="mb-3">
        <a href="{{ route('register') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">&larr; Changer de profil</a>
    </div>

    <div>
        <h2 class="text-xl font-extrabold text-gp-ink tracking-tight">Inscription Groupeur</h2>
        <p class="text-[11px] text-gp-warm mt-0.5">Rentabilisez vos trajets en transportant des colis vérifiés.</p>
    </div>

    <!-- Encadré sécurité compact -->
    <div class="mt-3 p-3 bg-orange-50/70 border-l-4 border-gp-orange rounded-r-gp text-[11px] text-gp-ink leading-snug">
        <p class="font-bold text-gp-ink">Contrôle de conformité</p>
        <p class="text-gp-warm">Votre compte sera vérifié avant publication de trajets.</p>
    </div>

    <form method="POST" action="{{ route('register.groupeur') }}" enctype="multipart/form-data" class="mt-4 space-y-3" x-data="{ show: false }">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Nom complet -->
            <div>
                <x-input-label for="name" value="Nom complet (comme sur CNI)" class="font-bold text-xs uppercase tracking-wider" />
                <x-text-input id="name" class="block mt-1 w-full text-xs" type="text" name="name" :value="old('name')" placeholder="Ex. Ibrahima Diallo" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" value="Adresse email" class="font-bold text-xs uppercase tracking-wider" />
                <x-text-input id="email" class="block mt-1 w-full text-xs" type="email" name="email" :value="old('email')" placeholder="ibrahima@exemple.com" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Téléphone WhatsApp -->
            <div>
                <x-input-label for="phone" value="Numéro WhatsApp" class="font-bold text-xs uppercase tracking-wider" />
                <x-text-input id="phone" class="block mt-1 w-full text-xs" type="tel" name="phone" :value="old('phone', '+221 ')" placeholder="+221 77 000 00 00" required autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>

            <!-- Trajets habituels -->
            <div>
                <x-input-label for="usual_routes" value="Lignes habituelles" class="font-bold text-xs uppercase tracking-wider" />
                <x-text-input id="usual_routes" class="block mt-1 w-full text-xs" type="text" name="usual_routes" :value="old('usual_routes')" placeholder="Ex. Dakar ⇄ Paris" />
                <x-input-error :messages="$errors->get('usual_routes')" class="mt-1" />
            </div>
        </div>

        <!-- Bio / Présentation -->
        <div>
            <x-input-label for="bio" value="Présentation courte" class="font-bold text-xs uppercase tracking-wider" />
            <textarea id="bio" name="bio" rows="2" required placeholder="Voyageur régulier, ponctuel et disponible..." class="block mt-1 w-full rounded-gp border-gp-border bg-white text-xs focus:border-gp-orange focus:ring-gp-orange shadow-sm">{{ old('bio') }}</textarea>
            <x-input-error :messages="$errors->get('bio')" class="mt-1" />
        </div>

        <!-- Pièce d'identité avec aperçu -->
        <div class="p-3 bg-white border border-dashed border-gp-border rounded-gp" x-data="{ fileName: '' }">
            <x-input-label for="id_document" value="Dépôt CNI ou Passeport (PDF, JPG, PNG)" class="font-bold text-xs uppercase tracking-wider text-gp-ink" />
            <input id="id_document" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png" required @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" class="block mt-1.5 w-full text-[11px] text-gp-warm file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-gp-ink file:text-white hover:file:bg-gp-orange file:cursor-pointer transition" />
            
            <template x-if="fileName">
                <p class="mt-1 text-xs font-semibold text-gp-orange-dark">
                    Fichier : <span x-text="fileName"></span>
                </p>
            </template>
            <x-input-error :messages="$errors->get('id_document')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Mot de passe -->
            <div>
                <x-input-label for="password" value="Mot de passe" class="font-bold text-xs uppercase tracking-wider" />
                <div class="relative">
                    <x-text-input id="password" class="block mt-1 w-full pr-9 text-xs" x-bind:type="show ? 'text' : 'password'" type="password" name="password" placeholder="Min. 8 car." required autocomplete="new-password" />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-2.5 flex items-center text-gp-warm hover:text-gp-orange" aria-label="Afficher">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirmation Mot de passe -->
            <div>
                <x-input-label for="password_confirmation" value="Confirmation" class="font-bold text-xs uppercase tracking-wider" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full text-xs" x-bind:type="show ? 'text' : 'password'" type="password" name="password_confirmation" placeholder="Confirmer" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <!-- Case acceptation des conditions -->
        <div class="flex items-start gap-2 pt-1">
            <input id="terms" name="terms" type="checkbox" required class="mt-0.5 rounded border-gp-border text-gp-orange focus:ring-gp-orange">
            <label for="terms" class="text-[11px] text-gp-warm leading-tight">
                J'atteste l'authenticité de ma pièce d'identité et j'accepte les conditions GPsen.
            </label>
        </div>

        <button type="submit" class="mt-3 w-full py-2.5 rounded-gp bg-gp-orange text-white font-bold text-xs uppercase tracking-wider hover:bg-gp-orange-dark active:scale-[.98] transition shadow-md focus:outline-none focus:ring-2 focus:ring-gp-orange focus:ring-offset-2">
            Créer mon compte Groupeur
        </button>
    </form>

    <p class="mt-4 text-center text-xs">
        Déjà un compte ? <a href="{{ route('login') }}" class="text-gp-orange-dark underline font-semibold">Se connecter</a>
    </p>
</x-guest-layout>
