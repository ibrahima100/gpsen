<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Seul l'administrateur peut lister tous les signalements.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Voir un signalement : l'administrateur ou l'auteur du signalement.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || $user->id === $report->reporter_id;
    }

    /**
     * Tout utilisateur actif connecté peut soumettre un signalement.
     */
    public function create(User $user): bool
    {
        return $user->status === 'actif';
    }

    /**
     * Seul l'administrateur peut traiter ou mettre à jour un signalement.
     */
    public function update(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }
}
