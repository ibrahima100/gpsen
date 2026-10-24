<x-admin-layout>
    <x-slot name="header">Tableau de bord administrateur</x-slot>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([['Groupeurs en attente', '0'], ['Trajets ouverts', '0'], ['Envois en cours', '0'], ['Signalements', '0']] as [$label, $value])
            <div class="gp-card p-5">
                <p class="text-sm text-gp-warm">{{ $label }}</p>
                <p class="mt-2 text-3xl font-extrabold text-gp-orange">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="gp-card p-6 mt-6">
        <h2 class="font-bold text-lg">Bienvenue, {{ auth()->user()->name }}</h2>
        <p class="text-gp-warm mt-1">Les modules de validation des groupeurs, trajets et signalements seront ajoutés aux étapes suivantes.</p>
    </div>
</x-admin-layout>
