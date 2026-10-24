<div class="space-y-6">
    <div class="bg-white rounded-gp border border-gp-border p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gp-ink tracking-tight">Notifications</h1>
            <p class="text-xs text-gp-warm mt-1">Consultez l'historique de vos notifications et les mises à jour de vos colis.</p>
        </div>

        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-gp bg-gp-sand hover:bg-gp-sand/80 text-gp-ink font-semibold text-xs transition">
                    Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="p-4 rounded-gp bg-green-50 border border-green-200 text-green-800 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-gp border border-gp-border divide-y divide-gp-border shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            @php
                $isUnread = is_null($notification->read_at);
                $title = $notification->data['title'] ?? 'Notification GPsen';
                $message = $notification->data['message'] ?? '';
                $actionUrl = $notification->data['action_url'] ?? null;
            @endphp
            <div class="p-5 flex items-start justify-between gap-4 transition hover:bg-gp-cream/40 {{ $isUnread ? 'bg-gp-sand/30' : '' }}">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $isUnread ? 'bg-gp-orange text-white' : 'bg-gp-sand text-gp-warm' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-gp-ink">{{ $title }}</h3>
                            @if($isUnread)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gp-orange/20 text-gp-orange-dark">Nouveau</span>
                            @endif
                        </div>
                        <p class="text-xs text-gp-warm mt-1 leading-relaxed">{{ $message }}</p>
                        <span class="text-[11px] text-gp-warm/70 mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($isUnread)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-gp bg-white border border-gp-border text-xs font-semibold text-gp-ink hover:bg-gp-sand transition">
                                Ouvrir
                            </button>
                        </form>
                    @elseif($actionUrl)
                        <a href="{{ $actionUrl }}" class="px-3 py-1.5 rounded-gp bg-white border border-gp-border text-xs font-semibold text-gp-ink hover:bg-gp-sand transition">
                            Voir
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gp-sand flex items-center justify-center text-gp-warm mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gp-ink">Aucune notification pour le moment</h3>
                <p class="text-xs text-gp-warm mt-1">Vous serez notifié dès qu'une action ou mise à jour interviendra sur vos colis.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>
            {{ $notifications->links() }}
        </div>
    @endif
</div>
