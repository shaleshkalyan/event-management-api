<?php

namespace App\Http\Requests;

use App\Models\EventTickets;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserEventRegistrationRequest extends FormRequest
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
        $eventId = $this->route('event')->id;
        return [
            'event_ticket_id' => [
                'required',
                'integer',
                Rule::exists(EventTickets::class, 'id')->where(function ($query) use ($eventId) {
                    $query->where('event_id', $eventId);
                }),
            ],
        ];
    }
}
