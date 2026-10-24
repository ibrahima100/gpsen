<x-admin-layout>
    <x-slot name="header">Supervision des utilisateurs</x-slot>

    <div class="space-y-6">
        <!-- Chiffres clés utilisateurs -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Total inscrits</span>
                <p class="text-2xl font-extrabold text-gp-ink mt-1">{{ $counts['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Expéditeurs</span>
                <p class="text-2xl font-extrabold text-gp-ink mt-1">{{ $counts['expediteurs'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gp-warm">Groupeurs</span>
                <p class="text-2xl font-extrabold text-gp-ink mt-1">{{ $counts['groupeurs'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-gp border border-gp-border shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-red-600">Suspendus</span>
                <p class="text-2xl font-extrabold text-red-700 mt-1">{{ $counts['suspendus'] }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-gp bg-green-50 border border-green-200 text-green-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-gp bg-red-50 border border-red-200 text-red-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Barre de recherche et filtres -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white p-4 rounded-gp border border-gp-border shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Rechercher par nom, email ou numéro de téléphone..." 
                       class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
            </div>

            <div>
                <select name="role" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                    <option value="tous" {{ $selectedRole === 'tous' ? 'selected' : '' }}>Tous les rôles</option>
                    <option value="expediteur" {{ $selectedRole === 'expediteur' ? 'selected' : '' }}>Expéditeurs</option>
                    <option value="groupeur" {{ $selectedRole === 'groupeur' ? 'selected' : '' }}>Groupeurs</option>
                    <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Administrateurs</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="status" class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
                    <option value="tous" {{ $selectedStatus === 'tous' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="actif" {{ $selectedStatus === 'actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="suspendu" {{ $selectedStatus === 'suspendu' ? 'selected' : '' }}>Suspendus</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider shrink-0 transition">
                    Filtrer
                </button>
            </div>
        </form>

        <!-- Liste des utilisateurs -->
        <div class="bg-white rounded-gp border border-gp-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gp-sand/40 border-b border-gp-border text-gp-warm uppercase tracking-wider font-bold">
                        <tr>
                            <th class="p-3.5">Utilisateur</th>
                            <th class="p-3.5">Téléphone</th>
                            <th class="p-3.5">Rôle</th>
                            <th class="p-3.5">Détail rôle</th>
                            <th class="p-3.5">Statut</th>
                            <th class="p-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gp-border">
                        @forelse($users as $user)
                            @php
                                $roleVal = $user->role?->value ?? (string)$user->role;
                            @endphp
                            <tr class="hover:bg-gp-cream/40 transition">
                                <td class="p-3.5">
                                    <div class="font-bold text-gp-ink">{{ $user->name }}</div>
                                    <div class="text-[11px] text-gp-warm">{{ $user->email }}</div>
                                    <div class="text-[10px] text-gp-warm/70">Inscrit le {{ $user->created_at->translatedFormat('d/m/Y') }}</div>
                                </td>
                                <td class="p-3.5 font-semibold text-gp-ink">
                                    {{ $user->phone }}
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $roleVal === 'admin' ? 'bg-purple-100 text-purple-800' : ($roleVal === 'groupeur' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $roleVal }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-gp-warm">
                                    @if($roleVal === 'groupeur')
                                        @php
                                            $vStatus = $user->groupeurProfile?->validation_status?->value ?? (string)$user->groupeurProfile?->validation_status;
                                        @endphp
                                        <span class="text-[11px]">
                                            Dossier CNI : <strong class="uppercase text-gp-ink">{{ $vStatus }}</strong>
                                        </span>
                                        <div class="text-[10px]">
                                            Note : {{ number_format($user->groupeurProfile?->average_rating ?? 5.0, 1) }} ★ ({{ $user->groupeurProfile?->ratings_count ?? 0 }} avis)
                                        </div>
                                    @elseif($roleVal === 'expediteur')
                                        <span class="text-[11px] text-gp-warm">Expéditeur certifié</span>
                                    @else
                                        <span class="text-[11px] text-gp-warm">Administrateur système</span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->status === 'actif' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}" onsubmit="return confirm('Voulez-vous vraiment changer le statut de cet utilisateur ?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-gp text-xs font-bold transition {{ $user->status === 'actif' ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                                                {{ $user->status === 'actif' ? 'Suspendre' : 'Réactiver' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-gp-warm italic">Votre compte</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gp-warm">
                                    Aucun utilisateur ne correspond à ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-gp-border">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
