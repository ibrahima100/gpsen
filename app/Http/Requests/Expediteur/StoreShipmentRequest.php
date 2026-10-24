<?php

namespace App\Http\Requests\Expediteur;

use App\Models\Trip;
use App\Services\TripCapacityService;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isExpediteur() ?? false;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'min:5', 'max:500'],
            'category' => ['required', 'string', 'in:vetements,documents,electronique,cosmetiques,denrees_seches,autre'],
            'weight_kg' => ['required', 'numeric', 'min:0.5', 'max:100'],
            'declared_value' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'receiver_name' => ['required', 'string', 'max:120'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'receiver_city' => ['required', 'string', 'max:80'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Trip $trip */
            $trip = $this->route('trip');

            if (! $trip || $trip->status?->value !== 'ouvert') {
                $validator->errors()->add('weight_kg', 'Ce trajet n\'accepte plus de nouvelles demandes d\'envoi.');
                return;
            }

            $capacityService = app(TripCapacityService::class);
            $remaining = $capacityService->getRemainingCapacity($trip);
            $requestedWeight = (float) $this->input('weight_kg');

            if ($requestedWeight > $remaining) {
                $validator->errors()->add('weight_kg', "Le poids demandé ({$requestedWeight} kg) dépasse la capacité restante disponible sur ce trajet ({$remaining} kg).");
            }
        });
    }

    public function messages(): array
    {
        return [
            'description.required' => 'La description du colis est obligatoire.',
            'category.required' => 'La catégorie d\'objet est obligatoire.',
            'category.in' => 'Catégorie non valide.',
            'weight_kg.required' => 'Le poids estimé en kg est obligatoire.',
            'weight_kg.min' => 'Le poids minimum est de 0.5 kg.',
            'receiver_name.required' => 'Le nom complet du destinataire est requis.',
            'receiver_phone.required' => 'Le numéro WhatsApp/téléphone du destinataire est requis.',
            'receiver_city.required' => 'La ville de remise est requise.',
        ];
    }
}
