<?php

namespace App\Contracts;

use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;

interface EventManagementServiceInterface
{
    public function getAllEvents(): Collection;
    public function getEventById(int $id): ?Events;
    public function createEvent(array $data): Events;
    public function updateEvent(Events $event, array $data): Events;
    public function deleteEvent(Events $event): bool;
    public function restoreEvent(int $id): bool;
}