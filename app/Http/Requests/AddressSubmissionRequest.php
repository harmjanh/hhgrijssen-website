<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'phone_number' => ['required', 'string', 'max:20'],

            // Old address fields
            'old_street' => ['required', 'string', 'max:255'],
            'old_number' => ['required', 'string', 'max:20'],
            'old_zipcode' => ['required', 'string', 'max:10'],
            'old_city' => ['required', 'string', 'max:255'],

            // New address fields
            'new_street' => ['required', 'string', 'max:255'],
            'new_number' => ['required', 'string', 'max:20'],
            'new_zipcode' => ['required', 'string', 'max:10'],
            'new_city' => ['required', 'string', 'max:255'],

            // Optional fields
            'other_people' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
