<?php

namespace App\Enums;

enum Role: string
{
    case expediteur = 'expediteur';
    case groupeur   = 'groupeur';
    case admin      = 'admin';

    public function label(): string
    {
        return match($this) {
            Role::expediteur => 'Expéditeur',
            Role::groupeur   => 'Groupeur',
            Role::admin      => 'Administrateur',
        };
    }
}
