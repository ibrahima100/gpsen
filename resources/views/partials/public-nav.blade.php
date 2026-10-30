<header x-data="{ open: false }" class="sticky top-0 z-40 bg-gp-cream/90 backdrop-blur border-b border-gp-border">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2" aria-label="GPsen accueil">
            <x-logo />
        </a>

        <nav class="hidden md:flex items-center gap-2" aria-label="Navigation principale">
            <a href="{{ route('home') }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('home') ? 'bg-gp-ink text-white shadow-sm' : 'text-gp-ink hover:bg-gp-sand' }}">
                <svg class="w-3.5 h-3.5 {{ request()->routeIs('home') ? 'text-gp-orange' : 'text-gp-warm' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Accueil</span>
            </a>
            <a href="{{ route('home') }}#comment-ca-marche" class="px-3 py-1.5 rounded-full text-xs font-semibold text-gp-ink hover:text-gp-orange hover:bg-gp-sand transition">
                {{ __('app.how_it_works') }}
            </a>
            <a href="{{ route('pages.about') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('pages.about') ? 'bg-gp-orange text-white' : 'text-gp-ink hover:text-gp-orange hover:bg-gp-sand' }}">
                À propos
            </a>
            <a href="{{ route('trajets.index') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('trajets.*') ? 'bg-gp-orange text-white' : 'text-gp-ink hover:text-gp-orange hover:bg-gp-sand' }}">
                {{ __('app.trips') }}
            </a>
        </nav>

        <div class="hidden md:flex items-center gap-3">
            @auth
                <x-notification-bell />
                <x-button href="{{ route('dashboard') }}">{{ __('app.dashboard') }}</x-button>
            @else
                <a href="{{ route('login') }}" class="gp-link">{{ __('app.login') }}</a>
                <x-button href="{{ route('register') }}">{{ __('app.register') }}</x-button>
            @endauth
        </div>

        <button @click="open = !open" class="md:hidden p-2 rounded-gp hover:bg-gp-sand" aria-label="Menu" id="public-menu-toggle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div x-show="open" x-transition x-cloak class="md:hidden border-t border-gp-border px-4 py-4 space-y-2.5 bg-gp-cream">
        <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 rounded-gp text-sm font-bold {{ request()->routeIs('home') ? 'bg-gp-ink text-white' : 'text-gp-ink hover:bg-gp-sand' }}">
            <svg class="w-4 h-4 text-gp-orange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Accueil</span>
        </a>
        <a href="{{ route('home') }}#comment-ca-marche" class="block px-3 py-1.5 rounded-gp text-sm font-semibold text-gp-ink hover:bg-gp-sand">{{ __('app.how_it_works') }}</a>
        <a href="{{ route('pages.about') }}" class="block px-3 py-1.5 rounded-gp text-sm font-semibold {{ request()->routeIs('pages.about') ? 'bg-gp-orange text-white' : 'text-gp-ink hover:bg-gp-sand' }}">À propos</a>
        <a href="{{ route('trajets.index') }}" class="block px-3 py-1.5 rounded-gp text-sm font-semibold {{ request()->routeIs('trajets.*') ? 'bg-gp-orange text-white' : 'text-gp-ink hover:bg-gp-sand' }}">{{ __('app.trips') }}</a>
        <div class="flex items-center gap-3 pt-2">
            @auth
                <x-button href="{{ route('dashboard') }}">{{ __('app.dashboard') }}</x-button>
            @else
                <a href="{{ route('login') }}" class="gp-link">{{ __('app.login') }}</a>
                <x-button href="{{ route('register') }}">{{ __('app.register') }}</x-button>
            @endauth
        </div>
    </div>
</header>
