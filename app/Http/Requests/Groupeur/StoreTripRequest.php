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
            'origin_country_id' => ['nullable', 'exists:countries,id'],
            'origin_city_id' => ['required', 'exists:cities,id'],
            'origin_country' => ['nullable', 'string', 'max:80'],
            'origin_city' => ['nullable', 'string', 'max:80'],
            'destination_country_id' => ['nullable', 'exists:countries,id'],
            'destination_city_id' => ['required', 'exists:cities,id'],
            'destination_country' => ['nullable', 'string', 'max:80'],
            'destination_city' => ['nullable', 'string', 'max:80'],
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
     * Prepare data for validation & ensure city belongs to country if country provided.
     */
    protected function passedValidation(): void
    {
        // Remplir automatiquement origin_city et origin_country depuis la table cities/countries
        $originCity = \App\Models\City::with('country')->find($this->origin_city_id);
        if ($originCity) {
            $this->merge([
                'origin_city' => $originCity->name,
                'origin_country' => $originCity->country->name,
            ]);
        }

        $destCity = \App\Models\City::with('country')->find($this->destination_city_id);
        if ($destCity) {
            $this->merge([
                'destination_city' => $destCity->name,
                'destination_country' => $destCity->country->name,
            ]);
        }
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'origin_city_id.required' => 'La ville de départ choisie dans la liste est requise.',
            'origin_city_id.exists' => 'La ville de départ sélectionnée est invalide.',
            'destination_city_id.required' => 'La ville de destination choisie dans la liste est requise.',
            'destination_city_id.exists' => 'La ville de destination sélectionnée est invalide.',
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
