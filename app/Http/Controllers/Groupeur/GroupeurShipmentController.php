<?php

namespace App\Http\Controllers\Groupeur;

use App\Http\Controllers\Controller;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Services\ShipmentWorkflowService;
use App\Services\TripCapacityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupeurShipmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ShipmentWorkflowService $workflowService,
        protected TripCapacityService $capacityService
    ) {}

    /**
     * Liste des demandes reçues par le groupeur filtrées par statut (§5.8).
     */
    public function index(Request $request): View
    {
        $status = $request->query('statut');

        $query = ShipmentRequest::with(['trip', 'sender'])
            ->whereHas('trip', function ($q) {
                $q->where('groupeur_id', auth()->id());
            });

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(12)->withQueryString();

        $counts = [
            'en_attente' => ShipmentRequest::whereHas('trip', fn($q) => $q->where('groupeur_id', auth()->id()))->where('status', 'en_attente')->count(),
            'acceptee' => ShipmentRequest::whereHas('trip', fn($q) => $q->where('groupeur_id', auth()->id()))->where('status', 'acceptee')->count(),
            'collectee' => ShipmentRequest::whereHas('trip', fn($q) => $q->where('groupeur_id', auth()->id()))->where('status', 'collectee')->count(),
            'en_transit' => ShipmentRequest::whereHas('trip', fn($q) => $q->where('groupeur_id', auth()->id()))->where('status', 'en_transit')->count(),
            'livree' => ShipmentRequest::whereHas('trip', fn($q) => $q->where('groupeur_id', auth()->id()))->where('status', 'livree')->count(),
        ];

        return view('groupeur.demandes.index', compact('requests', 'counts', 'status'));
    }

    /**
     * Fiche détaillée de la demande côté groupeur (§5.8).
     */
    public function show(ShipmentRequest $shipmentRequest): View
    {
        $this->authorize('manage', $shipmentRequest);
        $shipmentRequest->load(['trip', 'sender', 'statusHistories.author']);

        $remainingCapacity = $this->capacityService->getRemainingCapacity($shipmentRequest->trip);

        return view('groupeur.demandes.show', [
            'request' => $shipmentRequest,
            'trip' => $shipmentRequest->trip,
            'remainingCapacity' => $remainingCapacity,
        ]);
    }

    /**
     * Accepter une demande avec lockForUpdate transactionnel anti-surbooking.
     */
    public function accept(ShipmentRequest $shipmentRequest): RedirectResponse
    {
        $this->authorize('manage', $shipmentRequest);

        try {
            DB::transaction(function () use ($shipmentRequest) {
                // Verrouillage transactionnel du trajet
                $trip = Trip::where('id', $shipmentRequest->trip_id)->lockForUpdate()->firstOrFail();

                // Vérifier la capacité restante sous verrou
                $remaining = $this->capacityService->getRemainingCapacity($trip);

                if ($remaining < (float) $shipmentRequest->weight_kg) {
                    throw new \RuntimeException("Capacité restante insuffisante ({$remaining} kg disponibles pour {$shipmentRequest->weight_kg} kg demandés).");
                }

                // Effectuer la transition
                $this->workflowService->transition($shipmentRequest, 'acceptee', auth()->user());
            });

            return redirect()->route('groupeur.demandes.show', $shipmentRequest)
                ->with('success', 'La demande a été acceptée avec succès. Le contact WhatsApp direct est activé.');
        } catch (\Throwable $e) {
            return back()->withErrors(['weight' => $e->getMessage()]);
        }
    }

    /**
     * Refuser une demande avec motif obligatoire.
     */
    public function reject(Request $request, ShipmentRequest $shipmentRequest): RedirectResponse
    {
        $this->authorize('manage', $shipmentRequest);

        $request->validate([
            'decision_note' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'decision_note.required' => 'Le motif du refus est obligatoire pour informer l\'expéditeur.',
        ]);

        $this->workflowService->transition($shipmentRequest, 'refusee', auth()->user(), $request->decision_note);

        return redirect()->route('groupeur.demandes.show', $shipmentRequest)
            ->with('warning', 'La demande a été refusée et le motif a été notifié à l\'expéditeur.');
    }

    /**
     * Faire avancer le statut (collectee, en_transit, livree).
     */
    public function updateStatus(Request $request, ShipmentRequest $shipmentRequest): RedirectResponse
    {
        $this->authorize('manage', $shipmentRequest);

        $request->validate([
            'status' => ['required', 'string', 'in:collectee,en_transit,livree'],
            'delivery_code' => ['nullable', 'string'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        // Si passage à livrée : validation du code de remise à 4 chiffres (OTP)
        if ($request->status === 'livree' && ! empty($shipmentRequest->delivery_code)) {
            $inputCode = trim((string) $request->input('delivery_code'));
            if ($inputCode !== $shipmentRequest->delivery_code) {
                return back()->withErrors([
                    'delivery_code' => 'Code de remise incorrect. Veuillez demander au destinataire le code à 4 chiffres fourni par GPsen.',
                ]);
            }
        }

        $this->workflowService->transition($shipmentRequest, $request->status, auth()->user(), $request->note);

        return redirect()->route('groupeur.demandes.show', $shipmentRequest)
            ->with('success', 'Le statut du colis a été mis à jour.');
    }
}
