<x-public-layout>
    <x-slot name="title">Accueil</x-slot>

    <section class="relative overflow-hidden bg-gradient-to-br from-gp-sand-light via-gp-sand to-gp-orange/30">
        <div class="max-w-6xl mx-auto px-4 py-20 md:py-28 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <span class="badge badge-pending">Sénégal &harr; Monde</span>
                <h1 class="mt-4 text-4xl md:text-5xl font-extrabold leading-tight">
                    Envoyez vos colis avec des <span class="text-gp-orange">voyageurs de confiance</span>
                </h1>
                <p class="mt-4 text-lg text-gp-warm">GPsen met en relation expéditeurs et groupeurs vérifiés : plus simple, moins cher, plus humain.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-button href="{{ route('register') }}" id="cta-send">{{ __('app.send_package') }}</x-button>
                    <x-button href="{{ route('register') }}" variant="secondary" id="cta-groupeur">{{ __('app.become_groupeur') }}</x-button>
                </div>
            </div>
            <div class="gp-card p-6 shadow-gp">
                <p class="text-sm font-semibold text-gp-warm mb-3">Exemple de trajet</p>
                <h2 class="text-2xl font-extrabold">Dakar <span class="text-gp-orange">&rarr;</span> Paris</h2>
                <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                    <div><p class="text-gp-warm">Départ</p><p class="font-bold">15/12</p></div>
                    <div><p class="text-gp-warm">Capacité</p><p class="font-bold">23 kg</p></div>
                    <div><p class="text-gp-warm">Prix/kg</p><p class="font-bold text-gp-orange-dark">6 500 FCFA</p></div>
                </div>
                <span class="badge badge-accepted mt-4">{{ __('app.verified_groupeur') }}</span>
            </div>
        </div>
    </section>

    <section id="comment-ca-marche" class="max-w-6xl mx-auto px-4 py-16">
        <h2 class="text-3xl font-extrabold text-center">{{ __('app.how_it_works') }}</h2>
        <div class="mt-10 grid md:grid-cols-3 gap-6">
            @foreach ([['1','Trouvez un trajet','Parcourez les trajets publiés par des groupeurs validés.'],['2','Faites votre demande','Décrivez votre colis, le groupeur accepte ou refuse.'],['3','Suivez la livraison','Suivez chaque étape jusqu\'à la remise du colis.']] as [$n,$t,$d])
                <div class="gp-card p-6 hover:-translate-y-1 hover:shadow-gp transition duration-200">
                    <span class="grid place-items-center w-10 h-10 rounded-full bg-gp-orange text-white font-extrabold">{{ $n }}</span>
                    <h3 class="mt-4 text-lg font-bold">{{ $t }}</h3>
                    <p class="mt-2 text-gp-warm">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="trajets" class="max-w-6xl mx-auto px-4 pb-8">
        <h2 class="text-3xl font-extrabold text-center">{{ __('app.trips') }}</h2>
        <p class="mt-3 text-center text-gp-warm">Les prochains trajets s'afficheront ici (étape 4).</p>
    </section>
</x-public-layout>
