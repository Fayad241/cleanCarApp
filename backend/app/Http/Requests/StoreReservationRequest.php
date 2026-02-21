<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReservationRequest extends FormRequest
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
            'vehicule_id'          => ['required', 'uuid', 'exists:vehicules,id'],
            'service_id'           => ['required', 'uuid', 'exists:services,id'],
            'scheduled_date'       => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time'       => ['required', 'date_format:H:i'],
            'payment_method'       => ['nullable', Rule::in(['card', 'mobile_money', 'cash'])],
            'special_instructions' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_date.after_or_equal' => "La date doit être aujourd'hui ou dans le futur.",
            'scheduled_time.date_format'     => "L'heure doit être au format HH:MM.",
            'vehicule_id.exists'             => "Ce véhicule n'existe pas.",
            'service_id.exists'              => "Ce service n'existe pas.",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Données invalides.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
