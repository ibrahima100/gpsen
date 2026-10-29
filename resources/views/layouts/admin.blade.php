<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Administration' }} — GPsen Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen" x-data="{ side: false }">
    @php
        $pendingReportsCount = \App\Models\Report::where('status', 'ouvert')->count();
        $nav = [
            ['admin.dashboard', 'Tableau de bord', 0],
            ['admin.groupeurs.index', 'Validation groupeurs', \App\Models\GroupeurProfile::where('validation_status', 'en_attente')->count()],
            ['admin.reports.index', 'Signalements', $pendingReportsCount],
            ['admin.users.index', 'Gestion des utilisateurs', 0],
        ];
    @endphp

    <div class="flex min-h-screen">
        <aside :class="side ? 'translate-x-0' : '-translate-x-full'"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-gp-ink text-gp-sand flex flex-col transition-transform md:translate-x-0">
            <div class="h-16 px-5 flex items-center gap-2 border-b border-white/10">
                <span class="bg-gp-sand rounded-gp px-2 py-1"><x-logo :text="false" /></span><span class="text-xs font-semibold text-gp-border">Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-1" aria-label="Administration">
                @foreach ($nav as [$route, $label, $badge])
                    <a href="{{ route($route) }}"
                       class="flex items-center justify-between px-3 py-2 rounded-gp text-sm font-semibold transition {{ request()->routeIs($route.'*') ? 'bg-gp-orange text-white' : 'hover:bg-white/10' }}">
                        <span>{{ $label }}</span>
                        @if($badge > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ request()->routeIs($route.'*') ? 'bg-white text-gp-orange' : 'bg-gp-orange text-white' }}">
                                {{ $badge }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="p-4 border-t border-white/10">
                @csrf
                <button class="w-full px-3 py-2 rounded-gp text-sm font-semibold bg-white/10 hover:bg-white/20 transition">{{ __('app.logout') }}</button>
            </form>
        </aside>

        <div class="flex-1 min-w-0 flex flex-col">
            <header class="h-16 bg-gp-cream border-b border-gp-border px-4 flex items-center justify-between">
                <button @click="side = !side" class="md:hidden p-2 rounded-gp hover:bg-gp-sand" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="font-bold text-lg">{{ $header ?? 'Administration' }}</h1>
                <div class="flex items-center gap-3">
                    <x-notification-bell />
                    
                    <!-- Menu Paramètres / Profil Admin -->
                    <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                        <button @click="userMenu = !userMenu" 
                                type="button" 
                                class="flex items-center gap-2 p-1.5 rounded-gp hover:bg-gp-sand transition focus:outline-none"
                                title="Paramètres du compte administrateur">
                            <!-- Avatar / Initiale -->
                            <span class="w-8 h-8 rounded-full bg-gp-ink text-white font-extrabold text-xs flex items-center justify-center border-2 border-gp-orange shadow-sm">
                                {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                            </span>
                            <span class="hidden sm:inline text-xs font-bold text-gp-ink">{{ auth()->user()?->name }}</span>
                            <!-- Icône Paramètres (engrenage) -->
                            <svg class="w-4 h-4 text-gp-warm" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>

                        <div x-show="userMenu" 
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-52 rounded-gp bg-white shadow-xl border border-gp-border py-1 z-50 divide-y divide-gp-border text-xs">
                            <div class="px-4 py-2 bg-gp-sand/30">
                                <p class="font-bold text-gp-ink">{{ auth()->user()?->name }}</p>
                                <p class="text-[11px] text-gp-warm truncate">{{ auth()->user()?->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-gp-ink hover:bg-gp-sand transition font-medium">
                                    <svg class="w-4 h-4 text-gp-orange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Paramètres du profil
                                </a>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 transition font-medium">
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
            </header>
            <main class="flex-1 p-6">{{ $slot }}</main>
        </div>
    </div>
</body>
</html>
