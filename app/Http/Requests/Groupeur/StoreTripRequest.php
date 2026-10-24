<?php

namespace App\Http\Requests\Groupeur;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isGroupeurValidated() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'origin_country' => ['required', 'string', 'max:80'],
            'origin_city' => ['required', 'string', 'max:80'],
            'destination_country' => ['required', 'string', 'max:80'],
            'destination_city' => ['required', 'string', 'max:80'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'capacity_kg' => ['required', 'numeric', 'min:1', 'max:1000'],
            'price_per_kg' => ['required', 'integer', 'min:500', 'max:200000'],
            'accepted_items' => ['nullable', 'string', 'max:1000'],
            'prohibited_items' => ['nullable', 'string', 'max:1000'],
            'pickup_info' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'origin_country.required' => 'Le pays de départ est requis.',
            'origin_city.required' => 'La ville de départ est requise.',
            'destination_country.required' => 'Le pays de destination est requis.',
            'destination_city.required' => 'La ville de destination est requise.',
            'departure_date.required' => 'La date de départ est requise.',
            'departure_date.after_or_equal' => 'La date de départ ne peut pas être dans le passé.',
            'arrival_date.after_or_equal' => 'La date d\'arrivée doit être postérieure ou égale au départ.',
            'capacity_kg.required' => 'La capacité disponible en kg est obligatoire.',
            'capacity_kg.min' => 'La capacité minimum est de 1 kg.',
            'price_per_kg.required' => 'Le prix indicatif par kg est obligatoire.',
            'price_per_kg.min' => 'Le prix par kg doit être d\'au moins 500 FCFA.',
        ];
    }
}
