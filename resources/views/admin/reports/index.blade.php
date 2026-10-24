<x-admin-layout>
    <x-slot name="header">Modération des signalements</x-slot>

    <div class="space-y-6">
        <!-- Onglets de statut -->
        <div class="flex flex-wrap items-center gap-2 border-b border-gp-border pb-3">
            @php
                $tabs = [
                    'ouvert' => ['Ouverts', $counts['ouvert']],
                    'en_cours' => ['En cours', $counts['en_cours']],
                    'traite' => ['Traités', $counts['traite']],
                    'rejete' => ['Rejetés', $counts['rejete']],
                    'tous' => ['Tous', $counts['tous']],
                ];
            @endphp

            @foreach($tabs as $tabKey => [$tabLabel, $tabCount])
                <a href="{{ route('admin.reports.index', ['status' => $tabKey]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-gp text-xs font-bold transition {{ $currentStatus === $tabKey ? 'bg-gp-orange text-white shadow-sm' : 'bg-white border border-gp-border text-gp-warm hover:bg-gp-sand' }}">
                    <span>{{ $tabLabel }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === $tabKey ? 'bg-white text-gp-orange-dark' : 'bg-gp-sand text-gp-ink' }}">
                        {{ $tabCount }}
                    </span>
                </a>
            @endforeach
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-green-50 border border-green-200 text-green-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tableau des signalements -->
        <div class="bg-white rounded-gp border border-gp-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gp-sand/40 border-b border-gp-border text-gp-warm uppercase tracking-wider font-bold">
                        <tr>
                            <th class="p-3.5">Date</th>
                            <th class="p-3.5">Déclarant</th>
                            <th class="p-3.5">Élément ciblé</th>
                            <th class="p-3.5">Motif</th>
                            <th class="p-3.5">Statut</th>
                            <th class="p-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gp-border">
                        @forelse($reports as $report)
                            @php
                                $statusStyles = [
                                    'ouvert' => 'bg-red-100 text-red-800',
                                    'en_cours' => 'bg-amber-100 text-amber-800',
                                    'traite' => 'bg-emerald-100 text-emerald-800',
                                    'rejete' => 'bg-gray-100 text-gray-800',
                                ];
                                $reasons = [
                                    'colis_non_conforme' => 'Colis non conforme',
                                    'retard_excessif' => 'Retard excessif',
                                    'comportement_inapproprie' => 'Comportement',
                                    'suspicion_fraude' => 'Suspicion fraude',
                                    'autre' => 'Autre motif',
                                ];
                            @endphp
                            <tr class="hover:bg-gp-cream/40 transition">
                                <td class="p-3.5 whitespace-nowrap text-gp-warm">
                                    {{ $report->created_at->translatedFormat('d/m/Y H:i') }}
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-gp-ink">{{ $report->reporter->name }}</div>
                                    <div class="text-[11px] text-gp-warm">{{ $report->reporter->phone }}</div>
                                </td>
                                <td class="p-3.5">
                                    @if($report->targetUser)
                                        <span class="font-bold text-gp-ink">Utilisateur :</span> {{ $report->targetUser->name }}
                                    @elseif($report->trip)
                                        <span class="font-bold text-gp-ink">Trajet :</span> {{ $report->trip->origin_city }} → {{ $report->trip->destination_city }}
                                    @elseif($report->shipmentRequest)
                                        <span class="font-bold text-gp-ink">Colis #{{ $report->shipmentRequest->id }}</span>
                                    @else
                                        <span class="text-gp-warm italic">Non spécifié</span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-gp-sand text-gp-ink">
                                        {{ $reasons[$report->reason] ?? $report->reason }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusStyles[$report->status] ?? 'bg-gp-sand text-gp-ink' }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center px-3 py-1.5 rounded-gp bg-white border border-gp-border font-bold text-gp-ink hover:bg-gp-sand transition">
                                        Examiner →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gp-warm">
                                    Aucun signalement dans cette catégorie.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="p-4 border-t border-gp-border">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
