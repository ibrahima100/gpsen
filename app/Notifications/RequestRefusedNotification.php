<?php

namespace App\Notifications;

use App\Models\ShipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestRefusedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ShipmentRequest $shipmentRequest,
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Votre demande d\'envoi a été refusée',
            'message' => "Votre demande d'envoi pour le trajet {$this->shipmentRequest->trip->origin_city} → {$this->shipmentRequest->trip->destination_city} n'a pas pu être acceptée." . ($this->reason ? " Motif : {$this->reason}" : ''),
            'action_url' => route('expediteur.demandes.show', $this->shipmentRequest),
            'type' => 'request_refused',
            'shipment_request_id' => $this->shipmentRequest->id,
        ];
    }
}
