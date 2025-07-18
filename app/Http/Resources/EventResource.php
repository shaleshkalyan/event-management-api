<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date?->format('Y-m-d H:i:s'),
            'venue' => $this->venue,
            'capacity' => $this->capacity,
            'isRecurring' => (bool) $this->is_recurring,
            'recurrenceType' => $this->recurrence_type,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deletedAt' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i:s')),
            'tickets' => EventTicketsResource::collection($this->whenLoaded('eventTickets')),
        ];
    }
}
