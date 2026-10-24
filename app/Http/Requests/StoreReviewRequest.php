<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Veuillez sélectionner une note de 1 à 5 étoiles.',
            'rating.min' => 'La note minimum est de 1 étoile.',
            'rating.max' => 'La note maximum est de 5 étoiles.',
            'comment.max' => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ];
    }
}
