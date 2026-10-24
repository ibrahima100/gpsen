<x-app-layout :title="__('Mes demandes d\'envoi')">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gp-ink">Mes demandes d'envoi</h1>
                <p class="text-xs text-gp-warm mt-1">Suivez en temps réel la prise en charge et l'acheminement de vos colis.</p>
            </div>
            <a href="{{ route('trajets.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm self-start">
                + Nouveau colis à envoyer
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-gp bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold">
                {{ session('warning') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse($requests as $req)
                <div class="bg-white rounded-gp border border-gp-border p-5 shadow-sm space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gp-border/60">
                        <div class="flex items-center gap-3">
                            <span class="text-base font-black text-gp-ink">
                                {{ $req->trip->origin_city }} &rarr; {{ $req->trip->destination_city }}
                            </span>
                            <x-status-badge :status="$req->status" />
                        </div>
                        <span class="text-xs text-gp-warm">
                            Départ prévu : <strong class="text-gp-ink">{{ \Carbon\Carbon::parse($req->trip->departure_date)->translatedFormat('d F Y') }}</strong>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-gp-warm">Colis</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ $req->weight_kg }} kg ({{ $req->category }})</p>
                            <p class="text-[11px] text-gp-warm truncate mt-0.5">{{ $req->description }}</p>
                        </div>
                        <div>
                            <span class="text-gp-warm">Destinataire</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ $req->receiver_name }}</p>
                            <p class="text-[11px] text-gp-warm mt-0.5">{{ $req->receiver_city }} ({{ $req->receiver_phone }})</p>
                        </div>
                        <div>
                            <span class="text-gp-warm">Voyageur (Groupeur)</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ $req->trip->groupeur->name }}</p>
                            @if(in_array($req->status?->value ?? (string)$req->status, ['acceptee', 'collectee', 'en_transit', 'livree']))
                                <span class="text-[11px] font-bold text-emerald-700">WhatsApp activé ✓</span>
                            @else
                                <span class="text-[11px] text-gp-warm">Contact masqué (en attente)</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gp-border/60 flex items-center justify-between">
                        <span class="text-[11px] text-gp-warm">
                            Envoyée le {{ $req->created_at->translatedFormat('d/m/Y à H:i') }}
                        </span>
                        <a href="{{ route('expediteur.demandes.show', $req) }}" class="inline-flex items-center px-4 py-2 rounded-gp bg-gp-ink text-white font-bold text-xs hover:bg-gp-orange transition shadow-sm">
                            Suivre mon colis &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 border border-dashed border-gp-border rounded-gp bg-white">
                    <h3 class="text-sm font-bold text-gp-ink">Aucune demande d'envoi en cours</h3>
                    <p class="text-xs text-gp-warm max-w-sm mx-auto mt-1">
                        Trouvez un départ prochain pour acheminer vos bagages ou colis au meilleur prix.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('trajets.index') }}" class="inline-flex items-center px-4 py-2 rounded-gp bg-gp-orange text-white text-xs font-bold uppercase tracking-wider hover:bg-gp-orange-dark transition shadow-sm">
                            Rechercher un trajet
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div>
            {{ $requests->links() }}
        </div>
    </div>
</x-app-layout>
