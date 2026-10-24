<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupeurRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Votre dossier groupeur a été refusé',
            'message' => "Motif du refus : {$this->reason}. Vous pouvez mettre à jour votre pièce d'identité.",
            'action_url' => route('groupeur.waiting'),
            'type' => 'groupeur_rejected',
        ];
    }
}
