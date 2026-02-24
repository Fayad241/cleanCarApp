<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreWalkInRequest extends FormRequest
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
            'service_id'           => ['required', 'uuid', 'exists:services,id'],
            'scheduled_date'       => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time'       => ['required', 'date_format:H:i'],
            'vehicule_brand'       => ['required', 'string'],
            'vehicule_model'       => ['required', 'string'],
            'vehicule_size'        => ['required', Rule::in(['small', 'medium', 'large', 'xlarge', 'motorcycle'])],
            'vehicule_color'       => ['nullable', 'string'],
            'special_instructions' => ['nullable', 'string'],
            'payment_method'       => ['required', Rule::in(['cash', 'fedapay', 'stripe', 'paystack'])],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_date.after_or_equal' => 'La date ne peut pas être dans le passé.',
            'scheduled_time.date_format'     => "L'heure doit être au format HH:MM.",
            'service_id.exists'              => "Ce service n'existe pas.",
            'vehicule_size.in'               => 'Taille de véhicule invalide.',
            'payment_method.in'              => 'Méthode de paiement invalide.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Données invalides.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
