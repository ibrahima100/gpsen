<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GroupeurProfile;
use App\Models\Review;
use App\Models\User;

class RatingService
{
    /**
     * Recalculate average rating and review counts for a user.
     *
     * Queries all reviews targeted at this user, computes the arithmetic mean
     * and total count, and updates the user's groupeur profile if one exists.
     *
     * @param User $targetUser
     * @return void
     */
    public function recalculate(User $targetUser): void
    {
        $query = Review::query()->where('target_id', $targetUser->id);

        $count = (int) $query->count();
        $average = $count > 0 ? (float) $query->avg('rating') : 0.0;
        $average = round($average, 2);

        $profile = $targetUser->groupeurProfile ?? GroupeurProfile::where('user_id', $targetUser->id)->first();

        if ($profile !== null) {
            $profile->average_rating = $average;
            $profile->ratings_count = $count;
            $profile->save();
        }
    }
}
