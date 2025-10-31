<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicDeclarationRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:50'],
            'zipcode' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'date_of_service' => ['required', 'date'],
            'time_of_service_1' => ['required', 'date_format:H:i'],
            'time_of_service_2' => ['nullable', 'date_format:H:i'],
            'kilometers' => ['required', 'integer', 'min:0'],
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
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'E-mailadres moet een geldig e-mailadres zijn.',
            'street.required' => 'Straat is verplicht.',
            'number.required' => 'Huisnummer is verplicht.',
            'zipcode.required' => 'Postcode is verplicht.',
            'city.required' => 'Plaats is verplicht.',
            'date_of_service.required' => 'Datum van dienst is verplicht.',
            'date_of_service.date' => 'Datum van dienst moet een geldige datum zijn.',
            'time_of_service_1.required' => 'Tijd van dienst 1 is verplicht.',
            'time_of_service_1.date_format' => 'Tijd van dienst 1 moet een geldige tijd zijn (HH:MM).',
            'time_of_service_2.date_format' => 'Tijd van dienst 2 moet een geldige tijd zijn (HH:MM).',
            'kilometers.required' => 'Aantal kilometers is verplicht.',
            'kilometers.integer' => 'Aantal kilometers moet een geheel getal zijn.',
            'kilometers.min' => 'Aantal kilometers moet minimaal 0 zijn.',
        ];
    }
}
