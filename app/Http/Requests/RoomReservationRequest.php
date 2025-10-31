<?php

namespace App\Http\Requests;

use App\Models\RoomReservation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoomReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_id' => [
                'required',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    $startTime = Carbon::parse($this->input('start_time'));
                    $endTime = Carbon::parse($this->input('end_time'));
                    $excludeId = $this->route('roomReservation')?->id;

                    if (RoomReservation::hasTimeConflict($value, $startTime, $endTime, $excludeId)) {
                        $fail('Deze zaal is niet beschikbaar in de geselecteerde tijdsperiode. Er moet minimaal 30 minuten tussen reserveringen zitten.');
                    }
                },
            ],
            'subject' => 'required|string|max:255',
            'number_of_people' => 'required|integer|min:1|max:100',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_time.after' => 'De starttijd moet in de toekomst liggen.',
            'end_time.after' => 'De eindtijd moet na de starttijd liggen.',
            'number_of_people.min' => 'Het aantal personen moet minimaal 1 zijn.',
            'number_of_people.max' => 'Het aantal personen mag maximaal 100 zijn.',
        ];
    }
}
