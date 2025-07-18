<?php

namespace App\Repositories;

use App\Contracts\EventTicketsRepositoryInterface;
use App\Models\EventTickets;
use Illuminate\Database\Eloquent\Collection;

class EventTicketsRepository implements EventTicketsRepositoryInterface
{
    /**
     * This function is used to get event ticket details.
     * @param int $id
     * @return EventTickets|null
     */
    public function find(int $id): ?EventTickets
    {
        return EventTickets::find($id);
    }

    /**
     * This function is used to get the tickets related to particular event.
     * @param int $eventId
     * @return Collection
     */
    public function getEventTickets(int $eventId): Collection
    {
        return EventTickets::where('event_id', $eventId)->get();
    }
    /**
     * This function is used to get the tickets related to particular event.
     * @param int $eventId
     * @param string $ticketType
     * @return EventTickets
     */
    public function getTicketsForEvent(int $eventId, string $ticketType): EventTickets
    {
        return EventTickets::where('event_id', $eventId)->where('type', $ticketType)->first();
    }

    /**
     * This function is used to get the total members with confirmed status for and event.
     * @param EventTickets $ticket
     * @return int
     */
    public function getConfirmedBookingsCount(EventTickets $ticket): int
    {
        return $ticket->userEventRegistrations()->where('status', 'registered')->count();
    }
}