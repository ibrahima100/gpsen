<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenegalPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     * Accepte les formats valides : +22177..., +22178..., +22176..., +22170..., +22175..., 77..., 78..., 76..., 70..., 75...
     * ou tout format international E.164 valide (+xxx...).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Le format du numéro de téléphone est invalide.');

            return;
        }

        // Nettoyer les espaces, tirets et points
        $clean = preg_replace('/[\s\.\-]/', '', $value);

        // Doit comporter au minimum 9 chiffres (format sénégalais direct ou international)
        if (! preg_match('/^(\+221|221)?[7][05678][0-9]{7}$/', $clean) && ! preg_match('/^\+[1-9][0-9]{7,14}$/', $clean)) {
            $fail('Le numéro de téléphone doit être un numéro valide (ex. +221 77 123 45 67 ou format international).');
        }
    }

    /**
     * Normaliser un numéro de téléphone en format E.164 standard (+221...).
     */
    public static function normalize(string $phone): string
    {
        $clean = preg_replace('/[\s\.\-]/', '', $phone);

        // Si le numéro commence par 7[05678] sans indicatif (9 chiffres locaux)
        if (preg_match('/^[7][05678][0-9]{7}$/', $clean)) {
            return '+221'.$clean;
        }

        // Si le numéro commence par 221 sans le +
        if (str_starts_with($clean, '221') && strlen($clean) === 12) {
            return '+'.$clean;
        }

        // S'il n'a pas de +, s'assurer qu'il en a un s'il est international
        if (! str_starts_with($clean, '+') && strlen($clean) > 8) {
            return '+'.$clean;
        }

        return $clean;
    }
}
