<?php

namespace App\Notifications;

use App\Models\Warning;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WarningReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public Warning $warning) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Avertissement officiel de l\'administration',
            'message' => "Motif: {$this->warning->reason}. Vous disposez d'un délai de {$this->warning->delay_days} jours pour vous conformer aux règles.",
            'action_url' => route('dashboard'),
            'type' => 'warning_received',
            'warning_id' => $this->warning->id,
        ];
    }
}
