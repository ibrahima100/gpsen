<x-guest-layout>
    <h2 class="text-2xl font-extrabold">Bienvenue</h2>
    <p class="text-sm text-gp-warm mt-1">Connectez-vous pour accéder à votre espace.</p>

    <div class="mt-5 grid grid-cols-2 p-1 rounded-gp bg-gp-sand text-sm font-bold" role="tablist">
        <span class="text-center py-2 rounded-lg bg-gp-ink text-white">Connexion</span>
        <a href="{{ route('register') }}" class="text-center py-2 rounded-lg text-gp-ink hover:text-gp-orange-dark transition">Inscription</a>
    </div>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-5" x-data="{ show: false }">
        @csrf

        <div>
            <x-input-label for="email" value="Email" class="font-bold" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="nom@exemple.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" class="font-bold" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-11" x-bind:type="show ? 'text' : 'password'" type="password" name="password" placeholder="Votre mot de passe" required autocomplete="current-password" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gp-warm hover:text-gp-orange" aria-label="Afficher le mot de passe">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 text-sm">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-gp-border text-gp-orange focus:ring-gp-orange" name="remember">
                <span>Se souvenir de moi</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-gp-orange-dark underline hover:text-gp-orange" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        <button type="submit" id="login-submit" class="mt-5 w-full py-3 rounded-gp bg-gp-orange-dark text-white font-bold hover:bg-gp-orange active:scale-[.98] transition focus:outline-none focus:ring-2 focus:ring-gp-orange focus:ring-offset-2">
            Se connecter
        </button>
    </form>

    <p class="mt-5 text-center text-sm">Pas encore de compte ? <a href="{{ route('register') }}" class="text-gp-orange-dark underline font-semibold">Créer un compte</a></p>
</x-guest-layout>
