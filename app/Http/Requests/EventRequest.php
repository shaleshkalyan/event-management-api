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
        $rules = [
            'name' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['date', 'after_or_equal:today'],
            'end_date' => ['date', 'after_or_equal:start_date'],
            'location' => ['string', 'max:255'],
            'max_attendees' => ['integer', 'min:1'],
            'is_active' => ['boolean'],
            'event_tickets' => ['sometimes', 'array'],
            'event_tickets.*.type' => ['required_with:event_tickets', 'string', 'max:255'],
            'event_tickets.*.price' => ['required_with:event_tickets', 'numeric', 'min:0'],
            'event_tickets.*.quantity_available' => ['required_with:event_tickets', 'integer', 'min:0'],
        ];

        // rule for 'store' method
        if ($this->isMethod('post')) {
            $rules['name'][] = 'required';
            $rules['start_date'][] = 'required';
            $rules['end_date'][] = 'required';
            $rules['location'][] = 'required';
            $rules['max_attendees'][] = 'required';
        }

        // For update operations
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['event_tickets.*.id'] = [
                'sometimes',
                'integer',
                Rule::exists('event_tickets', 'id')->where(function ($query) {
                    $query->where('event_id', $this->route('event')->id);
                }),
            ];
        }

        return $rules;
    }
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'The event start date must be today or a future date.',
            'end_date.after_or_equal' => 'The event end date must be on or after the start date.',
            'max_attendees.min' => 'The maximum number of attendees must be at least 1.',
            'event_tickets.*.price.min' => 'Ticket price must be a non-negative number.',
            'event_tickets.*.quantity_available.min' => 'Ticket quantity must be a non-negative integer.',
            'event_tickets.*.type.required_with' => 'Ticket type is required when providing event tickets.',
            'event_tickets.*.price.required_with' => 'Ticket price is required when providing event tickets.',
            'event_tickets.*.quantity_available.required_with' => 'Ticket quantity is required when providing event tickets.',
        ];
    }
}
