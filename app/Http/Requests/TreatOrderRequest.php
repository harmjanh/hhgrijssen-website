<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TreatOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'snoeprollen_quantity' => ['required', 'integer', 'min:0', 'max:99'],
            'stroopwafels_quantity' => ['required', 'integer', 'min:0', 'max:99'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $snoeprollen = (int) $this->input('snoeprollen_quantity', 0);
            $stroopwafels = (int) $this->input('stroopwafels_quantity', 0);

            if ($snoeprollen < 1 && $stroopwafels < 1) {
                $validator->errors()->add(
                    'snoeprollen_quantity',
                    'Kies minimaal één product om te bestellen.'
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'E-mailadres moet een geldig e-mailadres zijn.',
            'phone.required' => 'Telefoonnummer is verplicht.',
            'snoeprollen_quantity.required' => 'Vul het aantal snoeprollen in.',
            'snoeprollen_quantity.integer' => 'Het aantal snoeprollen moet een heel getal zijn.',
            'snoeprollen_quantity.min' => 'Het aantal snoeprollen kan niet negatief zijn.',
            'stroopwafels_quantity.required' => 'Vul het aantal stroopwafels in.',
            'stroopwafels_quantity.integer' => 'Het aantal stroopwafels moet een heel getal zijn.',
            'stroopwafels_quantity.min' => 'Het aantal stroopwafels kan niet negatief zijn.',
            'website.max' => 'Er is een fout opgetreden. Probeer het later opnieuw.',
        ];
    }
}
