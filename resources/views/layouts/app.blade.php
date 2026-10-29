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
            </nav>

            <div class="hidden md:flex items-center gap-3">
                <x-notification-bell />

                <!-- Menu Utilisateur (Avatar + Nom + Paramètres / Déconnexion) -->
                <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                    <button @click="userMenu = !userMenu" 
                            type="button" 
                            class="flex items-center gap-2 p-1.5 rounded-gp hover:bg-gp-sand transition focus:outline-none"
                            title="Mon compte">
                        <!-- Avatar / Initiale -->
                        <span class="w-8 h-8 rounded-full bg-gp-ink text-white font-extrabold text-xs flex items-center justify-center border-2 border-gp-orange shadow-sm">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="text-xs font-bold text-gp-ink">{{ $user?->name }}</span>
                        <!-- Icône Paramètres / Menu -->
                        <svg class="w-4 h-4 text-gp-warm" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="userMenu" 
                         x-cloak
                         x-transition
                         class="absolute right-0 mt-2 w-52 rounded-gp bg-white shadow-xl border border-gp-border py-1 z-50 divide-y divide-gp-border text-xs">
                        <div class="px-4 py-2 bg-gp-sand/30">
                            <p class="font-bold text-gp-ink">{{ $user?->name }}</p>
                            <p class="text-[11px] text-gp-warm truncate">{{ $user?->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-gp-orange/10 text-gp-orange-dark uppercase">
                                {{ $user?->role?->label() ?? 'Utilisateur' }}
                            </span>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-gp-ink hover:bg-gp-sand transition font-medium">
                                <svg class="w-4 h-4 text-gp-orange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('app.profile') }}
                            </a>
                        </div>
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" id="logout-button" class="w-full text-left flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 transition font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('app.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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
            <a href="{{ route('notifications.index') }}" class="block gp-link flex items-center justify-between">
                <span>Notifications</span>
                @if(auth()->user()?->unreadNotifications->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gp-orange text-white">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
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
