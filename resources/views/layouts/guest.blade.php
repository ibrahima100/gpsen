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
    <body class="font-sans text-gp-ink antialiased bg-gp-sand min-h-screen">
        <div class="relative min-h-screen w-full bg-gp-sand flex flex-col justify-between overflow-x-hidden">
            {{-- Photo de fond fixe --}}
            <div class="fixed inset-0 bg-cover bg-center pointer-events-none" style="background-image:url('{{ asset('images/fond.jpg') }}')"></div>
            <div class="fixed inset-0 bg-gp-sand/90 backdrop-blur-[2px] pointer-events-none"></div>

            {{-- Formes diagonales orange / noir stylisées --}}
            <div class="hidden sm:block fixed -bottom-24 -right-20 w-[500px] h-[250px] bg-gp-orange/80 -rotate-[14deg] pointer-events-none"></div>
            <div class="hidden sm:block fixed -bottom-40 -right-24 w-[420px] h-[230px] bg-gp-ink/90 -rotate-[14deg] pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl w-full mx-auto px-4 py-4 sm:py-6 flex flex-col justify-between items-center min-h-screen">
                {{-- Barre supérieure avec logo et bouton retour accueil --}}
                <div class="w-full flex items-center justify-between shrink-0 pb-3">
                    <a href="/" aria-label="GPsen accueil" class="inline-flex items-center">
                        <x-logo :scale="1.1" />
                    </a>
                    <a href="/" class="group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gp-ink text-white font-bold text-xs shadow-md hover:bg-gp-orange transition-all duration-200 border border-gp-ink hover:border-gp-orange active:scale-95">
                        <svg class="w-3.5 h-3.5 text-gp-orange group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Retour à l'accueil</span>
                    </a>
                </div>

                {{-- Carte de connexion / inscription fluide et complète --}}
                <div class="w-full my-auto py-2">
                    <div class="bg-gp-cream border-2 border-gp-ink rounded-2xl p-5 sm:p-7 shadow-[8px_8px_0_0_#F26B1D] sm:shadow-[10px_10px_0_0_#F26B1D]">
                        {{ $slot }}
                    </div>
                </div>

                {{-- Pied de page discret --}}
                <p class="text-[11px] text-gp-warm text-center shrink-0 pt-3">
                    GPsen &copy; {{ date('Y') }} — Plateforme de groupage participatif
                </p>
            </div>
        </div>
    </body>
</html>
