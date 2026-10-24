<?php

namespace App\Enums;

enum RequestStatus: string
{
    case en_attente = 'en_attente';
    case acceptee   = 'acceptee';
    case refusee    = 'refusee';
    case collectee  = 'collectee';
    case en_transit = 'en_transit';
    case livree     = 'livree';
    case annulee    = 'annulee';

    public function label(): string
    {
        return match($this) {
            RequestStatus::en_attente => 'En attente',
            RequestStatus::acceptee   => 'Acceptée',
            RequestStatus::refusee    => 'Refusée',
            RequestStatus::collectee  => 'Collectée',
            RequestStatus::en_transit => 'En transit',
            RequestStatus::livree     => 'Livrée',
            RequestStatus::annulee    => 'Annulée',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            RequestStatus::en_attente => 'badge-pending',
            RequestStatus::acceptee   => 'badge-accepted',
            RequestStatus::refusee    => 'badge-refused',
            RequestStatus::collectee  => 'badge-transit',
            RequestStatus::en_transit => 'badge-transit',
            RequestStatus::livree     => 'badge-delivered',
            RequestStatus::annulee    => 'badge-refused',
        };
    }

    /** Statuts qui comptent dans le calcul de capacité restante */
    public static function capacityStatuses(): array
    {
        return ['acceptee', 'collectee', 'en_transit', 'livree'];
    }

    /** Statuts autorisant l'affichage du lien WhatsApp */
    public static function whatsappStatuses(): array
    {
        return ['acceptee', 'collectee', 'en_transit', 'livree'];
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            RequestStatus::refusee,
            RequestStatus::livree,
            RequestStatus::annulee,
        ]);
    }
}
