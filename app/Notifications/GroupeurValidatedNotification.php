<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupeurValidatedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Félicitations ! Votre profil groupeur est validé',
            'message' => 'Votre pièce d\'identité a été vérifiée par l\'administration GPsen. Vous pouvez dès maintenant publier vos trajets et recevoir des demandes.',
            'action_url' => route('groupeur.trips.create'),
            'type' => 'groupeur_validated',
        ];
    }
}
