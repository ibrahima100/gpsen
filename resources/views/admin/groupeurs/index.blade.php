<x-app-layout :title="__('Validation des groupeurs')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gp-ink">Dossiers Groupeurs à valider</h1>
                <p class="text-xs text-gp-warm mt-1">Vérifiez les pièces d'identité déposées pour sécuriser les envois de colis.</p>
            </div>
            @if($pendingCount > 0)
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-gp-orange-dark border border-orange-200">
                    {{ $pendingCount }} dossier(s) en attente
                </span>
            @endif
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

        <div class="bg-white rounded-gp border border-gp-border shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gp-sand/40 border-b border-gp-border text-xs uppercase tracking-wider text-gp-ink font-bold">
                    <tr>
                        <th class="p-4">Groupeur</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Trajets habituels</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gp-border">
                    @forelse($profiles as $profile)
                        <tr class="hover:bg-gp-sand/20 transition">
                            <td class="p-4">
                                <p class="font-bold text-gp-ink">{{ $profile->user->name }}</p>
                                <p class="text-xs text-gp-warm">{{ $profile->user->email }}</p>
                            </td>
                            <td class="p-4 text-xs font-medium text-gp-ink">
                                {{ $profile->user->phone }}
                            </td>
                            <td class="p-4 text-xs text-gp-warm">
                                {{ $profile->usual_routes ?: 'Non précisé' }}
                            </td>
                            <td class="p-4">
                                <x-status-badge :status="$profile->validation_status" />
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.groupeurs.show', $profile) }}" class="inline-flex items-center px-3 py-1.5 rounded-gp bg-gp-ink text-white font-bold text-xs hover:bg-gp-orange transition">
                                    Examiner le dossier &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-xs text-gp-warm">
                                Aucun dossier de groupeur enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $profiles->links() }}
        </div>
    </div>
</x-app-layout>
