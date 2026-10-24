<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404 — Page introuvable · {{ config('app.name', 'GPsen') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gp-sand text-gp-ink min-h-screen flex items-center justify-center p-5 font-sans">
        <div class="max-w-md w-full text-center bg-white p-8 rounded-gp shadow-lg border border-gp-border">
            <div class="w-16 h-16 bg-gp-sand text-gp-ink rounded-full flex items-center justify-center text-3xl mx-auto mb-4 font-extrabold">
                🔍
            </div>
            <h1 class="text-4xl font-extrabold text-gp-ink">404</h1>
            <h2 class="text-xl font-bold mt-2 text-gp-ink">Page introuvable</h2>
            <p class="text-sm text-gp-warm mt-3 leading-relaxed">
                Le lien que vous avez suivi est incorrect ou la page a été déplacée.
            </p>
            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="px-5 py-2.5 rounded-gp border border-gp-border font-bold text-sm text-gp-ink hover:bg-gp-sand transition">
                    &larr; Retour
                </a>
                <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-sm transition">
                    Accueil GPsen
                </a>
            </div>
        </div>
    </body>
</html>
