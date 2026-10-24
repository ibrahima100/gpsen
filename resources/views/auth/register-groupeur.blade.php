<x-guest-layout title="Inscription Groupeur">
    <div class="mb-4">
        <a href="{{ route('register') }}" class="text-xs font-bold text-gp-warm hover:text-gp-orange transition">&larr; Changer de profil</a>
    </div>

    <div>
        <h2 class="text-2xl font-extrabold text-gp-ink tracking-tight">Inscription Groupeur</h2>
        <p class="text-xs text-gp-warm mt-1">Rentabilisez vos trajets en transportant des colis vérifiés.</p>
    </div>

    <!-- Encadré sécurité selon document UI/UX §5.3 -->
    <div class="mt-4 p-4 bg-orange-50/70 border-l-4 border-gp-orange rounded-r-gp text-xs text-gp-ink leading-relaxed">
        <p class="font-bold text-gp-ink">Contrôle de conformité</p>
        <p class="text-gp-warm mt-0.5">Votre compte sera vérifié par notre équipe avant de pouvoir publier un trajet.</p>
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

        <!-- Téléphone WhatsApp avec indicatif +221 par défaut selon §5.3 -->
        <div>
            <x-input-label for="phone" value="Numéro WhatsApp" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', '+221 ')" placeholder="+221 77 000 00 00" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        <!-- Trajets habituels -->
        <div>
            <x-input-label for="usual_routes" value="Lignes ou trajets habituels (optionnel)" class="font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="usual_routes" class="block mt-1 w-full" type="text" name="usual_routes" :value="old('usual_routes')" placeholder="Ex. Dakar ⇄ Paris, Dakar ⇄ Milan" />
            <x-input-error :messages="$errors->get('usual_routes')" class="mt-1" />
        </div>

        <!-- Bio / Présentation -->
        <div>
            <x-input-label for="bio" value="Présentation courte" class="font-bold text-xs uppercase tracking-wider" />
            <textarea id="bio" name="bio" rows="3" required placeholder="Voyageur régulier entre la France et le Sénégal, ponctuel et disponible..." class="block mt-1 w-full rounded-gp border-gp-border bg-white text-sm focus:border-gp-orange focus:ring-gp-orange shadow-sm">{{ old('bio') }}</textarea>
            <p class="text-[11px] text-gp-warm mt-1">Au moins 20 caractères.</p>
            <x-input-error :messages="$errors->get('bio')" class="mt-1" />
        </div>

        <!-- Pièce d'identité avec aperçu selon §5.3 -->
        <div class="p-4 bg-white border-2 border-dashed border-gp-border rounded-gp" x-data="{ fileName: '' }">
            <x-input-label for="id_document" value="Dépôt de la pièce d'identité (CNI ou Passeport)" class="font-bold text-xs uppercase tracking-wider text-gp-ink" />
            <input id="id_document" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png" required @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" class="block mt-2 w-full text-xs text-gp-warm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gp-ink file:text-white hover:file:bg-gp-orange file:cursor-pointer transition" />
            
            <template x-if="fileName">
                <p class="mt-2 text-xs font-semibold text-gp-orange-dark">
                    Fichier sélectionné : <span x-text="fileName"></span>
                </p>
            </template>
            <p class="text-[11px] text-gp-warm mt-1">Format PDF, JPG ou PNG (max 5 Mo). Stockage privé sécurisé.</p>
            <x-input-error :messages="$errors->get('id_document')" class="mt-1" />
        </div>

        <!-- Case acceptation des conditions selon §5.3 -->
        <div class="flex items-start gap-2.5 pt-1">
            <input id="terms" name="terms" type="checkbox" required class="mt-1 rounded border-gp-border text-gp-orange focus:ring-gp-orange">
            <label for="terms" class="text-xs text-gp-warm leading-snug">
                J'atteste sur l'honneur l'authenticité de la pièce d'identité fournie et j'accepte les conditions de transport de colis de GPsen.
            </label>
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
            Créer mon compte Groupeur
        </button>
    </form>

    <p class="mt-5 text-center text-sm">
        Déjà un compte ? <a href="{{ route('login') }}" class="text-gp-orange-dark underline font-semibold">Se connecter</a>
    </p>
</x-guest-layout>
