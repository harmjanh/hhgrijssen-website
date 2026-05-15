<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'remarks' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Naam is verplicht.',
            'email.required'   => 'E-mailadres is verplicht.',
            'email.email'      => 'Vul een geldig e-mailadres in.',
            'remarks.required' => 'Opmerkingen is verplicht.',
            'website.max'      => 'Er is een fout opgetreden. Probeer het later opnieuw.',
        ];
    }
}
