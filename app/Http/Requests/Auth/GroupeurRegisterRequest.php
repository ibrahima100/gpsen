<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class GroupeurRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bio' => ['required', 'string', 'min:20', 'max:1000'],
            'usual_routes' => ['nullable', 'string', 'max:255'],
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5 Mo max
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.required' => 'Le numéro de téléphone (WhatsApp) est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'bio.required' => 'Une brève présentation de votre activité de groupeur est requise.',
            'bio.min' => 'Votre présentation doit comporter au moins 20 caractères.',
            'id_document.required' => 'Une pièce d\'identité (passeport ou CNI) est indispensable pour valider votre compte.',
            'id_document.mimes' => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'id_document.max' => 'La taille du document ne peut pas dépasser 5 Mo.',
        ];
    }
}
