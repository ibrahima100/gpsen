<x-guest-layout title="Inscription Expéditeur">
    <div class="mb-4">
        <a href="{{ route('register') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">&larr; Changer de profil</a>
    </div>

    <div>
        <h2 class="text-2xl font-extrabold text-gp-ink tracking-tight">Inscription Expéditeur</h2>
        <p class="text-xs text-gp-warm mt-1">Expédiez vos colis facilement avec des voyageurs de confiance.</p>
    </div>

    <form method="POST" action="{{ route('register.expediteur') }}" class="mt-6" x-data="{ show: false }">
        @csrf

        <!-- 4 champs selon §5.3 : nom, e-mail, téléphone WhatsApp indicatif +221, mot de passe -->
        <div>
            <x-input-label for="name" value="Nom complet" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Ex. Fatou Ndiaye" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Adresse email" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="fatou@exemple.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" value="Numéro WhatsApp" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', '+221 ')" placeholder="+221 77 123 45 67" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" class="font-bold text-xs uppercase tracking-wider" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-11" x-bind:type="show ? 'text' : 'password'" type="password" name="password" placeholder="Au moins 8 caractères" required autocomplete="new-password" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gp-warm hover:text-gp-orange" aria-label="Afficher le mot de passe">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" x-bind:type="show ? 'text' : 'password'" type="password" name="password_confirmation" placeholder="Retapez le mot de passe" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="mt-6 w-full py-3 rounded-gp bg-gp-orange text-white font-bold hover:bg-gp-orange-dark active:scale-[.98] transition shadow-md focus:outline-none focus:ring-2 focus:ring-gp-orange focus:ring-offset-2">
            Créer mon compte Expéditeur
        </button>
    </form>

    <p class="mt-5 text-center text-sm">
        Déjà un compte ? <a href="{{ route('login') }}" class="text-gp-orange-dark underline font-semibold">Se connecter</a>
    </p>
</x-guest-layout>
