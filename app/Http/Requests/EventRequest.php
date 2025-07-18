<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
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
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:255'],
            'date'                => ['required', 'date', 'after_or_equal:today'],
            'venue'               => ['required', 'string', 'max:255'],
            'capacity'            => ['required', 'integer', 'min:1'],
            'is_recurring'        => ['required', 'boolean'],
            'recurrence_type'     => [
                Rule::requiredIf(fn () => $this->boolean('is_recurring') === true),
                Rule::in(['weekly', 'monthly']),
            ],
            'regularTicketPrice'  => ['required', 'numeric', 'min:0'],
            'regularTicketCount'  => ['required', 'integer', 'min:1'],
            'vipTicketPrice'      => ['required', 'numeric', 'min:0'],
            'vipTicketCount'      => ['required', 'integer', 'min:1'],
        ];
    }
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required'                  => 'The event title is required.',
            'title.string'                    => 'The event title must be a string.',
            'description.string'              => 'The event description must be a string.',
            'date.required'                   => 'The event date is required.',
            'date.date'                       => 'The event date must be a valid date.',
            'date.after_or_equal'            => 'The event date must be today or in the future.',
            'venue.required'                  => 'The event venue is required.',
            'venue.string'                    => 'The event venue must be a string.',
            'capacity.required'               => 'The event capacity is required.',
            'capacity.integer'                => 'The event capacity must be an integer.',
            'capacity.min'                    => 'The event capacity must be at least 1.',
            'is_recurring.required'           => 'The event recurrence status is required.',
            'is_recurring.boolean'            => 'The event recurrence status must be true or false.',
            'recurrence_type.required_if'    => 'The recurrence type is required when the event is recurring.',
            'recurrence_type.in'             => 'The recurrence type must be either weekly or monthly.',
            'regularTicketPrice.required'    => 'The regular ticket price is required.',
            'regularTicketPrice.numeric'     => 'The regular ticket price must be a numeric value.',
            'regularTicketPrice.min'         => 'The regular ticket price must be at least 0.',
            'regularTicketCount.required'    => 'The regular ticket count is required.',
            'regularTicketCount.integer'     => 'The regular ticket count must be an integer.',
            'regularTicketCount.min'         => 'The regular ticket count must be at least 1.',
            'vipTicketPrice.required'        => 'The VIP ticket price is required.',
            'vipTicketPrice.numeric'         => 'The VIP ticket price must be a numeric value.',
            'vipTicketPrice.min'             => 'The VIP ticket price must be at least 0.',
            'vipTicketCount.required'        => 'The VIP ticket count is required.',
            'vipTicketCount.integer'         => 'The VIP ticket count must be an integer.',
            'vipTicketCount.min'             => 'The VIP ticket count must be at least 1.',
        ];
    }
}
