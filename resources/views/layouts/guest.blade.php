<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="GPsen — un colis, une destination, un bon groupeur.">

        <title>{{ $title ?? 'Connexion' }} — {{ config('app.name', 'GPsen') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gp-ink antialiased">
        <div class="relative min-h-screen lg:h-screen lg:overflow-hidden bg-gp-sand flex flex-col justify-between">
            {{-- Photo de fond (côté droit) + dégradé sable à gauche --}}
            <div class="absolute inset-0 bg-cover bg-center lg:bg-[position:80%_center]" style="background-image:url('{{ asset('images/fond.jpg') }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-gp-sand via-gp-sand/95 to-gp-sand/40 lg:to-transparent"></div>

            {{-- Formes diagonales orange / noir --}}
            <div class="hidden lg:block absolute -bottom-24 -right-20 w-[620px] h-[300px] bg-gp-orange -rotate-[14deg]"></div>
            <div class="hidden lg:block absolute -bottom-44 -right-24 w-[520px] h-[280px] bg-gp-ink -rotate-[14deg]"></div>

            <div class="relative z-10 max-w-6xl w-full mx-auto px-5 py-4 lg:py-6 h-full flex flex-col flex-1">
                <div class="flex items-center justify-end shrink-0">
                    <a href="/" class="text-sm font-semibold text-gp-warm hover:text-gp-orange transition">&larr; Accueil</a>
                </div>

                <div class="flex-1 grid lg:grid-cols-2 gap-8 lg:gap-10 items-start lg:items-center py-4 min-h-0">
                    {{-- Colonne gauche : logo et phrases alignés vers le haut (non décollés vers le bas) --}}
                    <section class="hidden lg:flex flex-col justify-start self-start pt-2">
                        <a href="/" aria-label="GPsen accueil" class="inline-block">
                            <x-logo :scale="1.3" tagline="Le bon trajet. Le bon groupeur." />
                        </a>
                        <h1 class="mt-6 text-4xl xl:text-5xl font-extrabold leading-[1.05] tracking-tight">
                            Un colis, une destination, <span class="text-gp-orange-dark">un bon groupeur.</span>
                        </h1>
                        <p class="mt-4 text-base xl:text-lg text-gp-warm max-w-md">Envoyez vos colis, trouvez un groupeur ou proposez vos trajets, au même endroit.</p>

                        <ul class="mt-6 max-w-md border-t-2 border-gp-ink">
                            @foreach ([['01','Échanges sécurisés','Vos transactions sont protégées.'],['02','Groupeurs vérifiés','Profils contrôlés par GPsen.'],['03','Suivi en temps réel','Restez informé à chaque étape.']] as [$n,$t,$d])
                                <li class="flex gap-4 py-2.5 border-b border-gp-ink/30">
                                    <span class="text-sm font-bold text-gp-orange-dark pt-0.5">{{ $n }}</span>
                                    <span><strong class="block text-sm leading-tight">{{ $t }}</strong><span class="text-xs xl:text-sm text-gp-warm">{{ $d }}</span></span>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    {{-- Colonne droite : carte de formulaire avec défilement interne propre si nécessaire (empêche le balayage de toute la page) --}}
                    <div class="w-full max-w-md mx-auto lg:ml-auto lg:max-h-[calc(100vh-6.5rem)] flex flex-col justify-center">
                        <a href="/" class="lg:hidden flex justify-center mb-4" aria-label="GPsen accueil">
                            <x-logo :scale="1.2" />
                        </a>
                        <div class="bg-gp-cream border-2 border-gp-ink rounded-2xl p-6 xl:p-7 shadow-[10px_10px_0_0_#F26B1D] overflow-y-auto max-h-[85vh] lg:max-h-[calc(100vh-7.5rem)]">
                            {{ $slot }}
                        </div>
                    </div>
                </div>

                <p class="text-xs xl:text-sm text-gp-warm lg:max-w-sm shrink-0 pt-2">GPsen — Une plateforme fiable et sécurisée.</p>
            </div>
        </div>
    </body>
</html>
