<?php

namespace App\Http\Controllers\Expediteur;

use App\Http\Controllers\Controller;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Notifications\RequestReceivedNotification;
use App\Services\ShipmentWorkflowService;
use App\Services\TripCapacityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TripCapacityService $capacityService,
        protected ShipmentWorkflowService $workflowService
    ) {}

    /**
     * Liste des demandes de l'expéditeur.
     */
    public function index(): View
    {
        $requests = auth()->user()->shipmentRequests()
            ->with(['trip.groupeur'])
            ->latest()
            ->paginate(10);

        return view('expediteur.demandes.index', compact('requests'));
    }

    /**
     * Fiche de suivi d'une demande avec chronologie datée et bouton WhatsApp (§5.7).
     */
    public function show(ShipmentRequest $shipmentRequest): View
    {
        $this->authorize('view', $shipmentRequest);

        $shipmentRequest->load(['trip.groupeur.groupeurProfile', 'statusHistories.author']);

        return view('expediteur.demandes.show', [
            'request' => $shipmentRequest,
            'trip' => $shipmentRequest->trip,
            'groupeur' => $shipmentRequest->trip->groupeur,
            'histories' => $shipmentRequest->statusHistories()->oldest()->get(),
        ]);
    }

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
    public function store(\App\Http\Requests\Expediteur\StoreShipmentRequest $request, Trip $trip): RedirectResponse
    {
        $shipmentRequest = new ShipmentRequest($request->validated());
        $shipmentRequest->trip_id = $trip->id;
        $shipmentRequest->sender_id = auth()->id();
        $shipmentRequest->status = \App\Enums\RequestStatus::en_attente;
        $shipmentRequest->save();

        // Créer le premier événement dans l'historique
        \App\Models\ShipmentStatusHistory::create([
            'shipment_request_id' => $shipmentRequest->id,
            'status' => 'en_attente',
            'changed_by' => auth()->id(),
            'note' => 'Demande d\'envoi transmise au voyageur.',
            'created_at' => now(),
        ]);

        // Notifier le groupeur
        $trip->groupeur->notify(new RequestReceivedNotification($shipmentRequest));

        return redirect()->route('expediteur.demandes.index')
            ->with('success', "Votre demande d'envoi de {$shipmentRequest->weight_kg} kg a été transmise à {$trip->groupeur->name}. Vous serez notifié dès sa réponse.");
    }

    /**
     * Annuler une demande avec motif.
     */
    public function cancel(Request $request, ShipmentRequest $shipmentRequest): RedirectResponse
    {
        $this->authorize('cancel', $shipmentRequest);

        $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'cancel_reason.required' => 'Le motif d\'annulation est obligatoire.',
        ]);

        $this->workflowService->transition($shipmentRequest, 'annulee', auth()->user(), $request->cancel_reason);

        return redirect()->route('expediteur.demandes.show', $shipmentRequest)
            ->with('warning', 'Votre demande d\'envoi a été annulée.');
    }
}
