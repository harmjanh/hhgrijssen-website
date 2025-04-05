<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoinOrderRequest extends FormRequest
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
            'blue_coins' => ['required', 'integer', 'min:0'],
            'red_coins' => ['required', 'integer', 'min:0'],
        ];
    }
}
