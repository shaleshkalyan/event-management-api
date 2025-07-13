<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Events;
use App\Models\EventTickets;
use App\Models\UserEventRegistration;
use Illuminate\Database\Eloquent\Collection;

interface UserEventRegistrationRepositoryInterface
{
    public function create(array $data): UserEventRegistration;
    public function find(int $id): ?UserEventRegistration;
    public function update(UserEventRegistration $registration, array $data): bool;
    public function findExistingActiveRegistration(User $user, Events $event, EventTickets $eventTicket): ?UserEventRegistration;
    public function getUserRegistrations(User $user): Collection;
    public function findNextWaitingListRegistration(Events $event, EventTickets $eventTicket): ?UserEventRegistration;
}