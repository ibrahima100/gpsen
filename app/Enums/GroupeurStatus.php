<?php

namespace App\Enums;

enum GroupeurStatus: string
{
    case en_attente = 'en_attente';
    case valide     = 'valide';
    case refuse     = 'refuse';

    public function label(): string
    {
        return match($this) {
            GroupeurStatus::en_attente => 'En attente de validation',
            GroupeurStatus::valide     => 'Validé',
            GroupeurStatus::refuse     => 'Refusé',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            GroupeurStatus::en_attente => 'badge-pending',
            GroupeurStatus::valide     => 'badge-accepted',
            GroupeurStatus::refuse     => 'badge-refused',
        };
    }
}
