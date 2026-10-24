<?php

namespace App\Notifications;

use App\Models\ShipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ShipmentRequest $shipmentRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle demande d\'envoi reçue',
            'message' => "Nouvelle demande de {$this->shipmentRequest->sender->name} ({$this->shipmentRequest->weight_kg} kg) pour votre trajet {$this->shipmentRequest->trip->origin_city} → {$this->shipmentRequest->trip->destination_city}.",
            'action_url' => route('groupeur.trips.index'),
            'type' => 'request_received',
            'shipment_request_id' => $this->shipmentRequest->id,
        ];
    }
}
