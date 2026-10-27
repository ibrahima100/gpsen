<?php

namespace App\Services;

class WhatsAppService
{
    /**
     * Construire un lien wa.me sécurisé et pré-rempli.
     */
    public static function buildLink(string $phone, string $message): string
    {
        // Nettoyer tous les caractères non numériques
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Si le numéro commence par 00, le remplacer par indicatif pur
        if (str_starts_with($clean, '00')) {
            $clean = substr($clean, 2);
        }

        $encodedText = urlencode($message);

        return "https://wa.me/{$clean}?text={$encodedText}";
    }

    /**
     * Générer le message pré-rempli pour un colis GPsen.
     */
    public static function generatePackageMessage(
        string $destinataireNom,
        string $expediteurOuGroupeurNom,
        float $poidsKg,
        string $trajet,
        ?string $codeLivraison = null
    ): string {
        $msg = "Bonjour {$destinataireNom}, je vous contacte depuis GPsen concernant le colis de {$poidsKg} kg sur le trajet {$trajet}.";

        if ($codeLivraison) {
            $msg .= " Code de remise sécurisé : [{$codeLivraison}].";
        }

        return $msg;
    }
}
