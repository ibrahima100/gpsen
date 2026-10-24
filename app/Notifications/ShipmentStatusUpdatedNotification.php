<?php

namespace App\Notifications;

use App\Models\ShipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShipmentStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ShipmentRequest $shipmentRequest,
        public string $statusLabel
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Mise à jour du suivi de votre colis',
            'message' => "Votre colis pour le trajet {$this->shipmentRequest->trip->origin_city} → {$this->shipmentRequest->trip->destination_city} est désormais : {$this->statusLabel}.",
            'action_url' => route('expediteur.demandes.show', $this->shipmentRequest),
            'type' => 'status_updated',
            'shipment_request_id' => $this->shipmentRequest->id,
        ];
    }
}
