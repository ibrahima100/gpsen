<?php

namespace App\Http\Controllers\Groupeur;

use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Groupeur\StoreTripRequest;
use App\Models\Trip;
use App\Services\TripCapacityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected TripCapacityService $capacityService
    ) {}

    /**
     * Mes trajets (Groupeur).
     */
    public function index(): View
    {
        $trips = auth()->user()->trips()
            ->latest('departure_date')
            ->paginate(10);

        return view('groupeur.trips.index', [
            'trips' => $trips,
            'capacityService' => $this->capacityService,
        ]);
    }

    /**
     * Formulaire nouveau trajet.
     */
    public function create(): View
    {
        return view('groupeur.trips.create');
    }

    /**
     * Enregistrer un nouveau trajet.
     */
    public function store(StoreTripRequest $request): RedirectResponse
    {
        $trip = new Trip($request->validated());
        $trip->groupeur_id = auth()->id();
        $trip->status = TripStatus::ouvert;
        $trip->save();

        return redirect()->route('groupeur.trips.index')
            ->with('success', 'Votre trajet a été publié avec succès.');
    }

    /**
     * Formulaire de modification d'un trajet.
     */
    public function edit(Trip $trip): View
    {
        $this->authorize('update', $trip);

        return view('groupeur.trips.edit', [
            'trip' => $trip,
            'remainingCapacity' => $this->capacityService->getRemainingCapacity($trip),
        ]);
    }

    /**
     * Mettre à jour un trajet.
     */
    public function update(StoreTripRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorize('update', $trip);

        $newCapacity = (float) $request->validated('capacity_kg');
        $bookedWeight = (float) $trip->shipmentRequests()
            ->whereIn('status', ['acceptee', 'collectee', 'en_transit'])
            ->sum('weight_kg');

        if ($newCapacity < $bookedWeight) {
            return back()->withErrors([
                'capacity_kg' => "La capacité totale ({$newCapacity} kg) ne peut pas être inférieure au poids déjà engagé sur ce trajet ({$bookedWeight} kg).",
            ])->withInput();
        }

        $trip->update($request->validated());
        $this->capacityService->updateTripStatus($trip);

        return redirect()->route('groupeur.trips.index')
            ->with('success', 'Le trajet a été mis à jour.');
    }

    /**
     * Annuler un trajet avec motif.
     */
    public function cancel(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorize('update', $trip);

        $request->validate([
            'cancelled_reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'cancelled_reason.required' => 'Le motif d\'annulation est obligatoire.',
        ]);

        $trip->update([
            'status' => TripStatus::annule,
            'cancelled_reason' => $request->cancelled_reason,
        ]);

        return redirect()->route('groupeur.trips.index')
            ->with('warning', 'Le trajet a été annulé.');
    }
}
