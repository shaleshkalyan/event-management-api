<?php

namespace App\Contracts;

use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;

interface EventsRepositoryInterface
{
    public function getAllActiveEvents(): Collection;
    public function find(int $id): ?Events;
    public function createEventWithTickets(array $data): Events;
    public function updateEventWithTickets(Events $event, array $data): Events;
    public function deleteEventWithTickets(Events $event): bool;
    public function restoreEvent(int $id): bool;
    public function getConfirmedRegistrationsCount(Events $event): int;
}