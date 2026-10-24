<x-guest-layout title="Inscription Groupeur">
    <div class="mb-4">
        <a href="{{ route('register') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">&larr; Changer de profil</a>
    </div>

    <div class="flex items-center gap-3">
        <span class="text-2xl">✈️</span>
        <div>
            <h2 class="text-2xl font-extrabold text-gp-ink">Compte Groupeur</h2>
            <p class="text-xs text-gp-warm">Publiez vos trajets et rentabilisez vos bagages.</p>
        </div>
    </div>

    <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-gp text-xs text-gp-ink flex gap-2">
        <span class="text-base shrink-0">🛡️</span>
        <p>
            <strong>Contrôle de sécurité :</strong> Pour protéger les expéditeurs, votre profil sera activé par un administrateur GPsen après vérification de votre pièce d'identité.
        </p>
    </div>

    <form method="POST" action="{{ route('register.groupeur') }}" enctype="multipart/form-data" class="mt-5 space-y-4" x-data="{ show: false }">
        @csrf

        <!-- Nom complet -->
        <div>
            <x-input-label for="name" value="Nom complet (comme sur votre pièce)" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Ex. Ibrahima Diallo" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Adresse email" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="ibrahima@exemple.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Téléphone WhatsApp -->
        <div>
            <x-input-label for="phone" value="Numéro WhatsApp actif" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" placeholder="+221 77 000 00 00" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        <!-- Trajets habituels -->
        <div>
            <x-input-label for="usual_routes" value="Lignes ou trajets habituels (optionnel)" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="usual_routes" class="block mt-1 w-full" type="text" name="usual_routes" :value="old('usual_routes')" placeholder="Ex. Paris ⇄ Dakar, Milan ⇄ Dakar" />
            <x-input-error :messages="$errors->get('usual_routes')" class="mt-1" />
        </div>

        <!-- Bio / Présentation -->
        <div>
            <x-input-label for="bio" value="Présentation de votre activité de groupeur" class="font-bold text-xs uppercase tracking-wider" />
            <textarea id="bio" name="bio" rows="3" required placeholder="Voyageur régulier depuis 5 ans, sérieux et ponctuel. Spécialisé sur la ligne France-Sénégal..." class="block mt-1 w-full rounded-gp border-gp-border bg-white text-sm focus:border-gp-orange focus:ring-gp-orange shadow-sm">{{ old('bio') }}</textarea>
            <p class="text-[11px] text-gp-warm mt-1">Au moins 20 caractères. Cette bio sera visible sur vos annonces de trajets.</p>
            <x-input-error :messages="$errors->get('bio')" class="mt-1" />
        </div>

        <!-- Pièce d'identité -->
        <div class="p-3 bg-gp-sand/40 border-2 border-dashed border-gp-border rounded-gp">
            <x-input-label for="id_document" value="Pièce d'identité (Passeport ou CNI)" class="font-bold text-xs uppercase tracking-wider text-gp-ink" />
            <input id="id_document" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="block mt-2 w-full text-xs text-gp-warm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gp-ink file:text-white hover:file:bg-gp-orange file:cursor-pointer transition" />
            <p class="text-[11px] text-gp-warm mt-1.5">Format PDF, JPG ou PNG (max 5 Mo). Stocké sur disque privé sécurisé, visible uniquement par l'administrateur.</p>
            <x-input-error :messages="$errors->get('id_document')" class="mt-1" />
        </div>

        <!-- Mot de passe -->
        <div>
            <x-input-label for="password" value="Mot de passe" class="font-bold text-xs uppercase tracking-wider" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-11" x-bind:type="show ? 'text' : 'password'" type="password" name="password" placeholder="Au moins 8 caractères" required autocomplete="new-password" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gp-warm hover:text-gp-orange" aria-label="Afficher le mot de passe">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirmation Mot de passe -->
        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" x-bind:type="show ? 'text' : 'password'" type="password" name="password_confirmation" placeholder="Retapez le mot de passe" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="mt-5 w-full py-3 rounded-gp bg-gp-orange text-white font-bold hover:bg-gp-orange-dark active:scale-[.98] transition shadow-md focus:outline-none focus:ring-2 focus:ring-gp-orange focus:ring-offset-2">
            Soumettre mon dossier Groupeur
        </button>
    </form>

    <p class="mt-5 text-center text-sm">
        Déjà un compte ? <a href="{{ route('login') }}" class="text-gp-orange-dark underline font-semibold">Se connecter</a>
    </p>
</x-guest-layout>
