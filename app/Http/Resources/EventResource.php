<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'startDate' => $this->start_date->format('Y-m-d H:i:s'),
            'endDate' => $this->end_date->format('Y-m-d H:i:s'),
            'location' => $this->location,
            'maxAttendees' => $this->max_attendees,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            'deletedAt' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i:s')),
            'tickets' => EventTicketsResource::collection($this->whenLoaded('eventTickets')),
        ];
    }
}
