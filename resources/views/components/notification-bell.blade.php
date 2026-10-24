@php
    $unreadCount = auth()->user()?->unreadNotifications->count() ?? 0;
    $recentNotifications = auth()->user()?->notifications()->take(5)->get() ?? collect();
@endphp

<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button @click="open = !open" 
            type="button" 
            class="relative p-2 rounded-gp text-gp-warm hover:text-gp-ink hover:bg-gp-sand/60 transition focus:outline-none"
            aria-label="Notifications">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-gp-orange text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 rounded-gp bg-white shadow-xl border border-gp-border z-50 overflow-hidden divide-y divide-gp-border">
        
        <div class="px-4 py-3 bg-gp-sand/30 flex items-center justify-between">
            <span class="text-xs font-bold text-gp-ink uppercase tracking-wider">Notifications</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="text-[11px] font-bold text-gp-orange hover:text-gp-orange-dark transition">
                        Tout marquer lu
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-72 overflow-y-auto divide-y divide-gp-border">
            @forelse($recentNotifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $title = $notification->data['title'] ?? 'Notification';
                    $message = $notification->data['message'] ?? '';
                    $actionUrl = $notification->data['action_url'] ?? null;
                @endphp
                <div class="p-3.5 hover:bg-gp-cream/50 transition flex items-start gap-3 {{ $isUnread ? 'bg-gp-sand/20' : '' }}">
                    <div class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $isUnread ? 'bg-gp-orange' : 'bg-transparent' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gp-ink truncate">{{ $title }}</p>
                        <p class="text-[11px] text-gp-warm line-clamp-2 mt-0.5 leading-relaxed">{{ $message }}</p>
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-[10px] text-gp-warm/70">{{ $notification->created_at->diffForHumans() }}</span>
                            @if($isUnread)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-bold text-gp-orange hover:underline">Consulter</button>
                                </form>
                            @elseif($actionUrl)
                                <a href="{{ $actionUrl }}" class="text-[10px] font-bold text-gp-ink hover:underline">Voir</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-gp-warm">
                    Aucune notification pour le moment.
                </div>
            @endforelse
        </div>

        <div class="p-2.5 bg-gp-sand/20 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-gp-ink hover:text-gp-orange transition block">
                Voir toutes les notifications →
            </a>
        </div>
    </div>
</div>
