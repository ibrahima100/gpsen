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
        $nav = [
            ['admin.dashboard', 'Tableau de bord'],
            ['admin.groupeurs.index', 'Validation groupeurs'],
        ];
    @endphp

    <div class="flex min-h-screen">
        <aside :class="side ? 'translate-x-0' : '-translate-x-full'"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-gp-ink text-gp-sand flex flex-col transition-transform md:translate-x-0">
            <div class="h-16 px-5 flex items-center gap-2 border-b border-white/10">
                <span class="bg-gp-sand rounded-gp px-2 py-1"><x-logo :text="false" /></span><span class="text-xs font-semibold text-gp-border">Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-1" aria-label="Administration">
                @foreach ($nav as [$route, $label])
                    <a href="{{ route($route) }}"
                       class="block px-3 py-2 rounded-gp text-sm font-semibold transition {{ request()->routeIs($route) ? 'bg-gp-orange text-white' : 'hover:bg-white/10' }}">{{ $label }}</a>
                @endforeach
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-gp text-sm font-semibold hover:bg-white/10 transition">{{ __('app.profile') }}</a>
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
                    <x-language-switcher />
                    <span class="hidden sm:inline text-sm text-gp-warm">{{ auth()->user()?->name }}</span>
                </div>
            </header>
            <main class="flex-1 p-6">{{ $slot }}</main>
        </div>
    </div>
</body>
</html>
