<?php

namespace App\Notifications;

use App\Models\ShipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestAcceptedNotification extends Notification
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
            'title' => 'Votre demande d\'envoi a été acceptée !',
            'message' => "Le groupeur {$this->shipmentRequest->trip->groupeur->name} a accepté votre colis de {$this->shipmentRequest->weight_kg} kg. Le lien WhatsApp direct est désormais disponible pour échanger.",
            'action_url' => route('expediteur.demandes.show', $this->shipmentRequest),
            'type' => 'request_accepted',
            'shipment_request_id' => $this->shipmentRequest->id,
        ];
    }
}
