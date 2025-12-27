<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrivacyConsentRequest extends FormRequest
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
            'street' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'voorbede_eredienst' => ['nullable', 'boolean'],
            'voorbede_zaaier' => ['nullable', 'boolean'],
            'verjaardag_zaaier' => ['nullable', 'boolean'],
            'rsv_gegevens' => ['nullable', 'boolean'],
            'place' => ['required', 'string', 'max:255'],
            'submission_date' => ['required', 'date'],
            'agreed' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'street.required' => 'Adres is verplicht.',
            'zipcode.required' => 'Postcode is verplicht.',
            'city.required' => 'Woonplaats is verplicht.',
            'birth_date.required' => 'Geboortedatum is verplicht.',
            'place.required' => 'Plaats is verplicht.',
            'submission_date.required' => 'Datum is verplicht.',
            'agreed.required' => 'U moet akkoord gaan met de voorwaarden.',
            'agreed.accepted' => 'U moet akkoord gaan met de voorwaarden.',
        ];
    }
}
