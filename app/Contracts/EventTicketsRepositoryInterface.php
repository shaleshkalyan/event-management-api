<?php

namespace App\Contracts;

use App\Models\EventTickets;
use Illuminate\Database\Eloquent\Collection;

interface EventTicketsRepositoryInterface
{
    public function find(int $id): ?EventTickets;
    public function getTicketsForEvent(int $eventId): Collection;
    public function getConfirmedBookingsCount(EventTickets $ticket): int;
}