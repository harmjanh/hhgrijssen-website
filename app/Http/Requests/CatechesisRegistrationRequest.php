<?php

namespace App\Http\Requests;

use App\Enums\CatechesisGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatechesisRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'group' => ['required', Rule::enum(CatechesisGroup::class)],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Voornaam is verplicht.',
            'last_name.required' => 'Achternaam is verplicht.',
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'E-mailadres moet een geldig e-mailadres zijn.',
            'phone.required' => 'Telefoonnummer is verplicht.',
            'group.required' => 'Kies een groep.',
            'group.enum' => 'Kies een geldige groep.',
            'website.max' => 'Er is een fout opgetreden. Probeer het later opnieuw.',
        ];
    }
}
