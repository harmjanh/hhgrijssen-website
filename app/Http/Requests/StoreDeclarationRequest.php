<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeclarationRequest extends FormRequest
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
            'number' => ['required', 'string', 'max:50'],
            'zipcode' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'bankaccountnumber' => ['required', 'string', 'max:50', 'iban'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'explanation' => ['required', 'string'],
            'attachments' => ['required', 'array', 'max:10'], // Maximum 10 files
            'attachments.*' => ['required', 'file', 'max:10240', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx'], // 10MB max per file, specific mime types
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
            'name.required' => 'Naam is verplicht',
            'street.required' => 'Straat is verplicht',
            'number.required' => 'Huisnummer is verplicht',
            'zipcode.required' => 'Postcode is verplicht',
            'city.required' => 'Plaats is verplicht',
            'bankaccountnumber.required' => 'IBAN is verplicht',
            'amount.required' => 'Bedrag is verplicht',
            'amount.numeric' => 'Bedrag moet een nummer zijn',
            'amount.min' => 'Bedrag moet groter zijn dan 0',
            'explanation.required' => 'Toelichting is verplicht',
            'attachments.max' => 'U kunt maximaal 10 bestanden uploaden',
            'attachments.*.max' => 'Bestanden mogen niet groter zijn dan 10MB',
            'attachments.*.mimes' => 'Alleen afbeeldingen (JPEG, PNG, JPG, GIF), PDF en Word documenten zijn toegestaan',
        ];
    }
}
