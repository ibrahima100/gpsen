<x-app-layout :title="__('Demandes reçues')">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gp-ink">Demandes de colis reçues</h1>
                <p class="text-xs text-gp-warm mt-1">Gérez les demandes des expéditeurs pour vos trajets de fret.</p>
            </div>
            <a href="{{ route('groupeur.trips.index') }}" class="text-xs font-bold text-gp-orange-dark hover:underline">
                Voir mes trajets &rarr;
            </a>
        </div>

        <!-- Onglets / Filtres rapides par statut selon UI/UX §5.8 -->
        <div class="flex flex-wrap gap-2 text-xs font-bold border-b border-gp-border pb-3">
            <a href="{{ route('groupeur.demandes.index') }}" class="px-3 py-1.5 rounded-gp transition {{ empty($status) ? 'bg-gp-ink text-white' : 'bg-white border border-gp-border text-gp-ink hover:bg-gp-sand' }}">
                Toutes ({{ array_sum($counts) }})
            </a>
            <a href="{{ route('groupeur.demandes.index', ['statut' => 'en_attente']) }}" class="px-3 py-1.5 rounded-gp transition {{ $status === 'en_attente' ? 'bg-gp-orange text-white' : 'bg-white border border-gp-border text-gp-ink hover:bg-gp-sand' }}">
                À traiter ({{ $counts['en_attente'] }})
            </a>
            <a href="{{ route('groupeur.demandes.index', ['statut' => 'acceptee']) }}" class="px-3 py-1.5 rounded-gp transition {{ $status === 'acceptee' ? 'bg-emerald-700 text-white' : 'bg-white border border-gp-border text-gp-ink hover:bg-gp-sand' }}">
                Acceptées ({{ $counts['acceptee'] }})
            </a>
            <a href="{{ route('groupeur.demandes.index', ['statut' => 'collectee']) }}" class="px-3 py-1.5 rounded-gp transition {{ $status === 'collectee' ? 'bg-gp-ink text-white' : 'bg-white border border-gp-border text-gp-ink hover:bg-gp-sand' }}">
                Collectées ({{ $counts['collectee'] }})
            </a>
            <a href="{{ route('groupeur.demandes.index', ['statut' => 'en_transit']) }}" class="px-3 py-1.5 rounded-gp transition {{ $status === 'en_transit' ? 'bg-orange-700 text-white' : 'bg-white border border-gp-border text-gp-ink hover:bg-gp-sand' }}">
                En transit ({{ $counts['en_transit'] }})
            </a>
            <a href="{{ route('groupeur.demandes.index', ['statut' => 'livree']) }}" class="px-3 py-1.5 rounded-gp transition {{ $status === 'livree' ? 'bg-emerald-900 text-white' : 'bg-white border border-gp-border text-gp-ink hover:bg-gp-sand' }}">
                Livrées ({{ $counts['livree'] }})
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

        <!-- Liste des demandes -->
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
                            Date départ trajet : <strong class="text-gp-ink">{{ \Carbon\Carbon::parse($req->trip->departure_date)->translatedFormat('d F Y') }}</strong>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-gp-warm">Expéditeur</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ $req->sender->name }}</p>
                            <p class="text-[11px] text-gp-warm mt-0.5">{{ $req->sender->email }}</p>
                        </div>
                        <div>
                            <span class="text-gp-warm">Colis proposé</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ $req->weight_kg }} kg ({{ $req->category }})</p>
                            <p class="text-[11px] text-gp-warm truncate mt-0.5">{{ $req->description }}</p>
                        </div>
                        <div>
                            <span class="text-gp-warm">Destinataire</span>
                            <p class="text-sm font-bold text-gp-ink mt-0.5">{{ $req->receiver_name }}</p>
                            <p class="text-[11px] text-gp-warm mt-0.5">{{ $req->receiver_city }} ({{ $req->receiver_phone }})</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gp-border/60 flex items-center justify-between">
                        <span class="text-[11px] text-gp-warm">
                            Reçue le {{ $req->created_at->translatedFormat('d/m/Y à H:i') }}
                        </span>
                        <a href="{{ route('groupeur.demandes.show', $req) }}" class="inline-flex items-center px-4 py-2 rounded-gp bg-gp-ink text-white font-bold text-xs hover:bg-gp-orange transition shadow-sm">
                            Traiter la demande &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 border border-dashed border-gp-border rounded-gp bg-white">
                    <h3 class="text-sm font-bold text-gp-ink">Aucune demande trouvée pour ce statut</h3>
                    <p class="text-xs text-gp-warm max-w-sm mx-auto mt-1">
                        Les réservations soumises par les expéditeurs apparaîtront directement ici.
                    </p>
                </div>
            @endforelse
        </div>

        <div>
            {{ $requests->links() }}
        </div>
    </div>
</x-app-layout>
