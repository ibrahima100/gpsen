<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Review $review
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $authorName = $this->review->author->name;
        $rating = $this->review->rating;
        $actionUrl = $notifiable->isGroupeur()
            ? route('groupeur.demandes.show', $this->review->shipment_request_id)
            : route('expediteur.demandes.show', $this->review->shipment_request_id);

        return [
            'title' => 'Nouvel avis reçu',
            'message' => "{$authorName} vous a attribué une note de {$rating}/5 étoiles.",
            'action_url' => $actionUrl,
            'type' => 'review_received',
            'review_id' => $this->review->id,
            'rating' => $rating,
        ];
    }
}
