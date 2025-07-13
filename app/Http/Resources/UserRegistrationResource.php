<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'eventId' => $this->event_id,
            'eventTicketId' => $this->event_ticket_id,
            'registrationDate' => $this->registration_date->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            'event' => new EventResource($this->whenLoaded('event')),
            'ticketType' => new EventTicketsResource($this->whenLoaded('eventTicket')),
        ];
    }
}
