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
            'title' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['date', 'after_or_equal:today'],
            'venue' => ['string', 'max:255'],
            'capacity' => ['integer', 'min:1'],
            'is_recurring' => ['boolean'],
            'recurrence_type' => ['nullable', 'string', Rule::in(['weekly', 'monthly'])],
            'event_tickets' => ['sometimes', 'array'],
            'event_tickets.*.type' => ['required_with:event_tickets', 'string', 'max:255'],
            'event_tickets.*.price' => ['required_with:event_tickets', 'numeric', 'min:0'],
            'event_tickets.*.quantity_available' => ['required_with:event_tickets', 'integer', 'min:0'],
        ];

        // rule for 'store' method
        if ($this->isMethod('post')) {
            $rules['title'][] = 'required';
            $rules['date'][] = 'required';
            $rules['venue'][] = 'required';
            $rules['capacity'][] = 'required';
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
            $rules['recurrence_type'][] = Rule::requiredIf($this->input('is_recurring') === true);
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
            'date.after_or_equal' => 'The event date must be today or a future date.',
            'capacity.min' => 'The event capacity must be at least 1.',
            'event_tickets.*.price.min' => 'Ticket price must be a non-negative number.',
            'event_tickets.*.quantity_available.min' => 'Ticket quantity must be a non-negative integer.',
            'recurrence_type.required_if' => 'The recurrence type is required when the event is recurring.',
            'recurrence_type.in' => 'The recurrence type must be either weekly or monthly.',
        ];
    }
}
