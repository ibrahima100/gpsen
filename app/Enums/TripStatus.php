<?php

namespace App\Enums;

enum TripStatus: string
{
    case ouvert = 'ouvert';
    case complet = 'complet';
    case parti = 'parti';
    case termine = 'termine';
    case annule = 'annule';

    public function label(): string
    {
        return match ($this) {
            TripStatus::ouvert => 'Ouvert',
            TripStatus::complet => 'Complet',
            TripStatus::parti => 'En route',
            TripStatus::termine => 'Terminé',
            TripStatus::annule => 'Annulé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            TripStatus::ouvert => 'badge-accepted',
            TripStatus::complet => 'badge-pending',
            TripStatus::parti => 'badge-transit',
            TripStatus::termine => 'badge-delivered',
            TripStatus::annule => 'badge-refused',
        };
    }

    public function isAcceptingRequests(): bool
    {
        return $this === TripStatus::ouvert || $this === TripStatus::complet;
    }
}
