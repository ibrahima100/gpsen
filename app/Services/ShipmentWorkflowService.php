<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ShipmentRequest;
use App\Models\ShipmentStatusHistory;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class ShipmentWorkflowService
{
    /**
     * Allowed state transitions for shipment requests.
     *
     * @var array<string, list<string>>
     */
    public const ALLOWED_TRANSITIONS = [
        'en_attente' => ['acceptee', 'refusee', 'annulee'],
        'acceptee' => ['collectee', 'annulee'],
        'collectee' => ['en_transit'],
        'en_transit' => ['livree'],
        'refusee' => [],
        'livree' => [],
        'annulee' => [],
    ];

    /**
     * Create a new ShipmentWorkflowService instance.
     */
    public function __construct(
        protected ?TripCapacityService $tripCapacityService = null
    ) {
        $this->tripCapacityService ??= app(TripCapacityService::class);
    }

    /**
     * Check if a transition from current status to newStatus is allowed.
     */
    public function canTransition(ShipmentRequest $request, string $newStatus): bool
    {
        $currentStatus = $request->status instanceof \BackedEnum
            ? $request->status->value
            : (string) $request->status;

        $allowed = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    /**
     * Perform the state transition on a shipment request inside a database transaction.
     *
     * @return bool True on success
     *
     * @throws InvalidArgumentException When the requested transition is not permitted
     */
    public function transition(
        ShipmentRequest $request,
        string $newStatus,
        User $changedBy,
        ?string $note = null
    ): bool {
        if (! $this->canTransition($request, $newStatus)) {
            $currentStatus = $request->status instanceof \BackedEnum
                ? $request->status->value
                : (string) $request->status;

            throw new InvalidArgumentException(
                "Transition de statut invalide : impossible de passer de '{$currentStatus}' à '{$newStatus}'."
            );
        }

        return DB::transaction(function () use ($request, $newStatus, $changedBy, $note): bool {
            $currentStatus = $request->status instanceof \BackedEnum
                ? $request->status->value
                : (string) $request->status;

            $wasAccepted = ($currentStatus === 'acceptee');

            // 1. Updates $request->status to $newStatus
            $request->status = $newStatus;

            if ($note !== null && in_array($newStatus, ['refusee', 'annulee'], true)) {
                $request->decision_note = $note;
            }

            // 2. If $newStatus === 'acceptee': set accepted_at = now() and generate 4-digit delivery_code
            if ($newStatus === 'acceptee') {
                $request->accepted_at = now();
                if (empty($request->delivery_code)) {
                    $request->delivery_code = (string) random_int(1000, 9999);
                }
            }

            // 3. If $newStatus === 'livree': set delivered_at = now(), increment groupeur's delivered_count
            if ($newStatus === 'livree') {
                $request->delivered_at = now();

                $trip = $request->trip ?? Trip::find($request->trip_id);
                if ($trip) {
                    $groupeur = $trip->groupeur ?? User::find($trip->groupeur_id);
                    if ($groupeur) {
                        $profile = $groupeur->groupeurProfile;
                        if ($profile) {
                            $profile->increment('delivered_count');
                        }
                    }
                }
            }

            // Save the request before capacity check to ensure DB queries reflect the new status
            $request->save();

            // 2 (cont): Call updateTripStatus if accepted
            if ($newStatus === 'acceptee') {
                $trip = $request->trip ?? Trip::find($request->trip_id);
                if ($trip) {
                    $this->tripCapacityService->updateTripStatus($trip);
                }
            }

            // 4. If $newStatus === 'annulee' and was 'acceptee': restore capacity by calling updateTripStatus()
            if ($newStatus === 'annulee' && $wasAccepted) {
                $trip = $request->trip ?? Trip::find($request->trip_id);
                if ($trip) {
                    $this->tripCapacityService->updateTripStatus($trip);
                }
            }

            // 5. Creates ShipmentStatusHistory record with status, changed_by, note
            ShipmentStatusHistory::create([
                'shipment_request_id' => $request->id,
                'status' => $newStatus,
                'changed_by' => $changedBy->id,
                'note' => $note,
                'created_at' => now(),
            ]);

            // 6. Saves the request (persists any remaining model changes)
            $request->save();

            // 7. Sends appropriate notification
            try {
                if ($newStatus === 'acceptee') {
                    $request->sender->notify(new \App\Notifications\RequestAcceptedNotification($request));
                } elseif ($newStatus === 'refusee') {
                    $request->sender->notify(new \App\Notifications\RequestRefusedNotification($request, $note));
                } elseif (in_array($newStatus, ['collectee', 'en_transit', 'livree'], true)) {
                    $labels = [
                        'collectee' => 'Collecté',
                        'en_transit' => 'En transit (En cours d\'acheminement)',
                        'livree' => 'Livré à destination',
                    ];
                    $request->sender->notify(new \App\Notifications\ShipmentStatusUpdatedNotification($request, $labels[$newStatus] ?? $newStatus));
                }
            } catch (Throwable) {
                // Notifications will be gracefully handled without breaking transaction
            }

            // Return true on success
            return true;
        });
    }
}
