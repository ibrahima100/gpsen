<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\Review;
use App\Models\ShipmentRequest;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Déposer un avis :
     * 1. La demande doit être livrée.
     * 2. L'utilisateur doit être soit l'expéditeur, soit le groupeur du trajet.
     * 3. L'utilisateur ne doit pas avoir déjà déposé un avis sur cette demande.
     */
    public function create(User $user, ShipmentRequest $shipmentRequest): bool
    {
        if ($shipmentRequest->status !== RequestStatus::livree && $shipmentRequest->status?->value !== 'livree') {
            return false;
        }

        $isParty = $user->id === $shipmentRequest->sender_id || $user->id === $shipmentRequest->trip->groupeur_id;
        if (! $isParty) {
            return false;
        }

        $alreadyReviewed = Review::where('shipment_request_id', $shipmentRequest->id)
            ->where('author_id', $user->id)
            ->exists();

        return ! $alreadyReviewed;
    }
}
