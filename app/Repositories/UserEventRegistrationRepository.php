<?php

namespace App\Repositories;

use App\Contracts\UserEventRegistrationRepositoryInterface;
use App\Models\User;
use App\Models\Events;
use App\Models\EventTickets;
use App\Models\UserEventRegistration;
use Illuminate\Database\Eloquent\Collection;

class UserEventRegistrationRepository implements UserEventRegistrationRepositoryInterface
{
    /**
     * This function is used to register user for an event.
     * @param array $data
     * @return UserEventRegistration
     */
    public function create(array $data): UserEventRegistration
    {
        return UserEventRegistration::create($data);
    }

    /**
     * This function is used to get the registration detail based on id.
     * @param int $id
     * @return UserEventRegistration|null
     */
    public function find(int $id): ?UserEventRegistration
    {
        return UserEventRegistration::find($id);
    }

    /**
     * This function is used to update the registration data.
     * @param UserEventRegistration $registration
     * @param array $data
     * @return bool
     */
    public function update(UserEventRegistration $registration, array $data): bool
    {
        return $registration->update($data);
    }

    /**
     * This function is used to get the existing registration details.
     * @param User $user
     * @param Events $event
     * @param EventTickets $eventTicket
     * @return UserEventRegistration|null
     */
    public function findExistingActiveRegistration(User $user, Events $event, EventTickets $eventTicket): ?UserEventRegistration
    {
        return UserEventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->where('event_ticket_id', $eventTicket->id)
            ->whereIn('status', ['confirmed', 'waiting'])
            ->first();
    }

    /**
     * This function is used to get the user registration details.
     * @param User $user
     * @return Collection
     */
    public function getUserRegistrations(User $user): Collection
    {
        return $user->userEventRegistrations()->with(['event', 'eventTicket'])->get();
    }

    /**
     * This function is used to get the event details with Waiting status
     * @param Events $event
     * @param EventTickets $eventTicket
     * @return UserEventRegistration|null
     */
    public function findNextWaitingListRegistration(Events $event, EventTickets $eventTicket): ?UserEventRegistration
    {
        return $event->userEventRegistrations()
            ->where('event_ticket_id', $eventTicket->id)
            ->where('status', 'waiting')
            ->orderBy('registered_at', 'asc')
            ->first();
    }
}