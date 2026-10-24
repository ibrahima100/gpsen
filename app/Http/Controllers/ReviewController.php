<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Models\ShipmentRequest;
use App\Notifications\ReviewReceivedNotification;
use App\Services\RatingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Store a new review for a delivered shipment request.
     */
    public function store(StoreReviewRequest $request, ShipmentRequest $shipmentRequest, RatingService $ratingService): RedirectResponse
    {
        $user = auth()->user();

        // 1. Demande doit impérativement être livrée
        if ($shipmentRequest->status !== RequestStatus::livree && $shipmentRequest->status?->value !== 'livree') {
            abort(422, 'Un avis ne peut être déposé que sur une demande livrée.');
        }

        // 2. Seules les deux parties concernées peuvent déposer un avis
        $isSender = $shipmentRequest->sender_id === $user->id;
        $isGroupeur = $shipmentRequest->trip->groupeur_id === $user->id;

        if (! $isSender && ! $isGroupeur) {
            abort(403, 'Vous n\'êtes pas autorisé à laisser un avis sur cette livraison.');
        }

        // 3. Déterminer la cible de l'avis
        $targetUser = $isSender ? $shipmentRequest->trip->groupeur : $shipmentRequest->sender;

        // 4. Vérifier l'unicité (un seul avis par auteur pour cette demande)
        $alreadyReviewed = Review::where('shipment_request_id', $shipmentRequest->id)
            ->where('author_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            abort(422, 'Vous avez déjà déposé un avis pour cette livraison.');
        }

        // 5. Créer l'avis dans une transaction
        $review = DB::transaction(function () use ($request, $shipmentRequest, $user, $targetUser, $ratingService) {
            $createdReview = Review::create([
                'shipment_request_id' => $shipmentRequest->id,
                'author_id' => $user->id,
                'target_id' => $targetUser->id,
                'rating' => (int) $request->validated('rating'),
                'comment' => $request->validated('comment'),
            ]);

            // Recalculer les moyennes
            $ratingService->recalculate($targetUser);

            return $createdReview;
        });

        // 6. Notifier le destinataire
        $targetUser->notify(new ReviewReceivedNotification($review));

        return back()->with('success', 'Votre avis a bien été enregistré. Merci pour votre retour !');
    }
}
