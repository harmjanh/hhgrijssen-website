<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolidarityFundAuthorizationRequest extends FormRequest
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
            'initials' => ['required', 'string', 'max:20'],
            'street' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[0-9]{7}([A-Z0-9]?){0,16}$/i'],
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
            'initials.required' => 'Voorletters zijn verplicht.',
            'street.required' => 'Adres is verplicht.',
            'zipcode.required' => 'Postcode is verplicht.',
            'city.required' => 'Woonplaats is verplicht.',
            'iban.required' => 'IBAN is verplicht.',
            'iban.regex' => 'Het IBAN formaat is ongeldig.',
            'submission_date.required' => 'Datum is verplicht.',
            'agreed.required' => 'U moet akkoord gaan met de voorwaarden.',
            'agreed.accepted' => 'U moet akkoord gaan met de voorwaarden.',
        ];
    }
}
