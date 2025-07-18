<?php

namespace App\Contracts;

use App\Models\EventTickets;
use Illuminate\Database\Eloquent\Collection;

interface EventTicketsRepositoryInterface
{
    public function find(int $id): ?EventTickets;
    public function getEventTickets(int $eventId): Collection;
    public function getTicketsForEvent(int $eventId, string $ticketType): EventTickets;
    public function getConfirmedBookingsCount(EventTickets $ticket): int;
}