<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'in:colis_non_conforme,retard_excessif,comportement_inapproprie,suspicion_fraude,autre'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'target_user_id' => ['nullable', 'exists:users,id'],
            'trip_id' => ['nullable', 'exists:trips,id'],
            'shipment_request_id' => ['nullable', 'exists:shipment_requests,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Veuillez sélectionner un motif de signalement.',
            'reason.in' => 'Le motif sélectionné est invalide.',
            'description.required' => 'Veuillez préciser la description du problème rencontré.',
            'description.min' => 'La description doit comporter au moins 10 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ];
    }
}
