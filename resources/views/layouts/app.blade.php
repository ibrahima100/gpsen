<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('app.dashboard') }} — GPsen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">
    @php
        $user = auth()->user();
        $links = [];
        if ($user?->isGroupeur()) {
            $links = [
                ['groupeur.dashboard', 'Mon espace Groupeur'],
            ];
        } elseif ($user?->isExpediteur()) {
            $links = [
                ['expediteur.dashboard', 'Mon espace Expéditeur'],
            ];
        }
    @endphp

    <header x-data="{ open: false }" class="sticky top-0 z-40 bg-gp-cream/90 backdrop-blur border-b border-gp-border">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-logo />
            </a>

            <nav class="hidden md:flex items-center gap-6" aria-label="Navigation">
                @foreach ($links as [$route, $label])
                    <a href="{{ route($route) }}" class="gp-link {{ request()->routeIs($route) ? '!text-gp-orange' : '' }}">{{ $label }}</a>
                @endforeach
                <a href="{{ route('profile.edit') }}" class="gp-link">{{ __('app.profile') }}</a>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                <x-language-switcher />
                <span class="text-sm text-gp-warm">{{ $user?->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="secondary" id="logout-button">{{ __('app.logout') }}</x-button>
                </form>
            </div>

            <button @click="open = !open" class="md:hidden p-2 rounded-gp hover:bg-gp-sand" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div x-show="open" x-cloak x-transition class="md:hidden border-t border-gp-border px-4 py-4 space-y-3 bg-gp-cream">
            @foreach ($links as [$route, $label])
                <a href="{{ route($route) }}" class="block gp-link">{{ $label }}</a>
            @endforeach
            <a href="{{ route('profile.edit') }}" class="block gp-link">{{ __('app.profile') }}</a>
            <x-language-switcher />
            <form method="POST" action="{{ route('logout') }}">@csrf
                <x-button type="submit" variant="secondary">{{ __('app.logout') }}</x-button>
            </form>
        </div>
    </header>

    @isset($header)
        <div class="bg-gp-sand border-b border-gp-border">
            <div class="max-w-6xl mx-auto px-4 py-6">{{ $header }}</div>
        </div>
    @endisset

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    @include('partials.footer')
</body>
</html>
