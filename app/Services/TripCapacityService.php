<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ShipmentRequest;
use App\Models\Trip;

class TripCapacityService
{
    /**
     * Calculate the remaining available capacity of a trip in kilograms.
     *
     * Queries shipment requests associated with the trip that occupy capacity
     * (statuses: acceptee, collectee, en_transit, livree) and subtracts their
     * total weight from the trip's initial capacity.
     *
     * @return float Remaining capacity in kg (never negative, min 0.0)
     */
    public function getRemainingCapacity(Trip $trip): float
    {
        $usedKg = (float) ShipmentRequest::query()
            ->where('trip_id', $trip->id)
            ->whereIn('status', ['acceptee', 'collectee', 'en_transit', 'livree'])
            ->sum('weight_kg');

        $initialCapacity = (float) $trip->capacity_kg;
        $remaining = $initialCapacity - $usedKg;

        return max(0.0, round($remaining, 2));
    }

    /**
     * Determine whether the trip has enough remaining capacity to accommodate
     * the requested weight.
     *
     * @return bool True if remaining capacity is greater than or equal to requested kg
     */
    public function canAccommodate(Trip $trip, float $requestedKg): bool
    {
        return $this->getRemainingCapacity($trip) >= $requestedKg;
    }

    /**
     * Update the trip status based on remaining capacity:
     * - If remaining capacity <= 0, set status to 'complet' and save.
     * - If remaining capacity > 0 and status is currently 'complet', set back to 'ouvert' and save.
     *
     * Note: Trips that are already in final or operational stages ('parti', 'termine', 'annule')
     * are preserved and not altered.
     */
    public function updateTripStatus(Trip $trip): void
    {
        $remaining = $this->getRemainingCapacity($trip);

        $currentStatus = $trip->status instanceof \BackedEnum
            ? $trip->status->value
            : (string) $trip->status;

        if ($remaining <= 0) {
            // Only transition to 'complet' if not already complet and not past departure/cancelled
            if ($currentStatus !== 'complet' && ! in_array($currentStatus, ['parti', 'termine', 'annule'], true)) {
                $trip->status = 'complet';
                $trip->save();
            }
        } else {
            // Restore back to 'ouvert' only if trip was previously marked 'complet'
            if ($currentStatus === 'complet') {
                $trip->status = 'ouvert';
                $trip->save();
            }
        }
    }
}
