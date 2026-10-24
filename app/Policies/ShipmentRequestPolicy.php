<?php

namespace App\Policies;

use App\Models\ShipmentRequest;
use App\Models\User;

class ShipmentRequestPolicy
{
    /**
     * Voir la demande (l'expéditeur propriétaire, le groupeur du trajet ou l'administrateur).
     */
    public function view(User $user, ShipmentRequest $shipmentRequest): bool
    {
        return $user->id === $shipmentRequest->sender_id
            || $user->id === $shipmentRequest->trip->groupeur_id
            || $user->isAdmin();
    }

    /**
     * Traiter la demande (seul le groupeur assigné au trajet).
     */
    public function manage(User $user, ShipmentRequest $shipmentRequest): bool
    {
        return $user->id === $shipmentRequest->trip->groupeur_id;
    }

    /**
     * Annuler la demande (l'expéditeur ou le groupeur).
     */
    public function cancel(User $user, ShipmentRequest $shipmentRequest): bool
    {
        return $user->id === $shipmentRequest->sender_id
            || $user->id === $shipmentRequest->trip->groupeur_id;
    }
}
