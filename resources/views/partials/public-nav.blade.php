<header x-data="{ open: false }" class="sticky top-0 z-40 bg-gp-cream/90 backdrop-blur border-b border-gp-border">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2" aria-label="GPsen accueil">
            <x-logo />
        </a>

        <nav class="hidden md:flex items-center gap-6" aria-label="Navigation principale">
            <a href="{{ route('home') }}" class="gp-link">{{ __('app.home') }}</a>
            <a href="{{ route('home') }}#comment-ca-marche" class="gp-link">{{ __('app.how_it_works') }}</a>
            <a href="{{ route('trajets.index') }}" class="gp-link">{{ __('app.trips') }}</a>
        </nav>

        <div class="hidden md:flex items-center gap-3">
            <x-language-switcher />
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

    <div x-show="open" x-transition x-cloak class="md:hidden border-t border-gp-border px-4 py-4 space-y-3 bg-gp-cream">
        <a href="{{ route('home') }}" class="block gp-link">{{ __('app.home') }}</a>
        <a href="{{ route('home') }}#comment-ca-marche" class="block gp-link">{{ __('app.how_it_works') }}</a>
        <a href="{{ route('trajets.index') }}" class="block gp-link">{{ __('app.trips') }}</a>
        <div class="flex items-center gap-3 pt-2">
            <x-language-switcher />
            @auth
                <x-button href="{{ route('dashboard') }}">{{ __('app.dashboard') }}</x-button>
            @else
                <a href="{{ route('login') }}" class="gp-link">{{ __('app.login') }}</a>
                <x-button href="{{ route('register') }}">{{ __('app.register') }}</x-button>
            @endauth
        </div>
    </div>
</header>
