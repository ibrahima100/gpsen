<?php

namespace App\Http\Controllers\Expediteur;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expediteur\StoreShipmentRequest;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Notifications\RequestReceivedNotification;
use App\Services\TripCapacityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShipmentRequestController extends Controller
{
    public function __construct(
        protected TripCapacityService $capacityService
    ) {}

    /**
     * Formulaire de demande d'envoi pour un trajet donné (§5.6).
     */
    public function create(Trip $trip): View|RedirectResponse
    {
        $remaining = $this->capacityService->getRemainingCapacity($trip);

        if ($remaining <= 0 || $trip->status?->value !== 'ouvert') {
            return redirect()->route('trajets.show', $trip)
                ->with('warning', 'Ce trajet est complet et ne peut plus recevoir de demande d\'envoi.');
        }

        return view('expediteur.demandes.create', [
            'trip' => $trip,
            'remainingCapacity' => $remaining,
        ]);
    }

    /**
     * Enregistrer une nouvelle demande d'envoi.
     */
    public function store(StoreShipmentRequest $request, Trip $trip): RedirectResponse
    {
        $shipmentRequest = new ShipmentRequest($request->validated());
        $shipmentRequest->trip_id = $trip->id;
        $shipmentRequest->sender_id = auth()->id();
        $shipmentRequest->status = RequestStatus::en_attente;
        $shipmentRequest->save();

        // Notifier le groupeur par le canal base de données
        $trip->groupeur->notify(new RequestReceivedNotification($shipmentRequest));

        return redirect()->route('expediteur.dashboard')
            ->with('success', "Votre demande d'envoi de {$shipmentRequest->weight_kg} kg a été transmise à {$trip->groupeur->name}. Vous serez notifié dès sa réponse.");
    }
}
