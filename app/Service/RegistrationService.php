<?php

namespace App\Service;

use App\Contracts\RegistrationServiceInterface;
use App\Contracts\EventsRepositoryInterface;
use App\Contracts\EventTicketsRepositoryInterface;
use App\Contracts\UserEventRegistrationRepositoryInterface;
use App\Models\User;
use App\Models\Events;
use App\Models\EventTickets;
use App\Models\UserEventRegistration;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RegistrationService implements RegistrationServiceInterface
{
    protected $eventRepository;
    protected $eventTicketRepository;
    protected $userRegistrationRepository;

    /**
     * Initialize the registration service with required repositories
     *
     * @param EventsRepositoryInterface $eventRepository Repository for event operations
     * @param EventTicketsRepositoryInterface $eventTicketRepository Repository for event ticket operations
     * @param UserEventRegistrationRepositoryInterface $userRegistrationRepository Repository for user registration operations
     */
    public function __construct(
        EventsRepositoryInterface $eventRepository,
        EventTicketsRepositoryInterface $eventTicketRepository,
        UserEventRegistrationRepositoryInterface $userRegistrationRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->eventTicketRepository = $eventTicketRepository;
        $this->userRegistrationRepository = $userRegistrationRepository;
    }

    /**
     * This function is used to register a user for an event with a specific ticket type
     * @param User $user The user to register
     * @param Events $event The event to register for
     * @param int $eventTicketId The ID of the ticket type
     * @return array Registration result with status, message and registration details
     */
    public function registerUser(User $user, Events $event, int $eventTicketId): array
    {
        $eventTicket = $this->eventTicketRepository->find($eventTicketId);

        if (!$eventTicket || $eventTicket->event_id !== $event->id) {
            return ['status' => 'error', 'message' => 'Invalid ticket for this event.'];
        }

        $existingRegistration = $this->userRegistrationRepository->findExistingActiveRegistration($user, $event, $eventTicket);

        if ($existingRegistration) {
            $statusMessage = ucfirst($existingRegistration->status);
            return ['status' => 'conflict', 'message' => "You are already $statusMessage for this event with this ticket type.", 'registration' => $existingRegistration];
        }

        return DB::transaction(function () use ($user, $event, $eventTicket) {
            $registrationStatus = 'registered';
            $message = 'Successfully registered for the event!';

            $currentTicketConfirmed = $this->eventTicketRepository->getConfirmedBookingsCount($eventTicket);

            if ($currentTicketConfirmed >= $eventTicket->quantity) {
                $registrationStatus = 'waiting';
                $message = 'Selected ticket type is full. You have been added to the waiting list.';
            } else {
                $currentEventConfirmed = $this->eventRepository->getConfirmedRegistrationsCount($event);
                if ($currentEventConfirmed >= $event->capacity) {
                    $registrationStatus = 'waiting';
                    $message = 'Event overall capacity reached. You have been added to the waiting list.';
                }
            }

            $registration = $this->userRegistrationRepository->create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'event_ticket_id' => $eventTicket->id,
                'status' => $registrationStatus,
                'registered_at' => now(),
            ]);

            return ['status' => 'success', 'message' => $message, 'registration' => $registration];
        });
    }

    /**
     * This function is used to cancel a user's event registration
     * @param UserEventRegistration $registration The registration to cancel
     * @return array Cancellation result with status and message
     */
    public function cancelUserRegistration(UserEventRegistration $registration): array
    {
        if (!in_array($registration->status, ['registered', 'waiting'])) {
            return ['status' => 'error', 'message' => 'This registration cannot be cancelled.'];
        }

        return DB::transaction(function () use ($registration) {
            $this->userRegistrationRepository->update($registration, [
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $event = $registration->event;
            $eventTicket = $registration->eventTicket;

            $eventTicket->refresh();

            $promotedRegistration = $this->promoteNextWaitingListUser($event, $eventTicket);

            if ($promotedRegistration) {
                return ['status' => 'success', 'message' => 'Registration cancelled. A waiting list spot has been filled and user notified.'];
            }

            return ['status' => 'success', 'message' => 'Registration cancelled successfully.'];
        });
    }

    /**
     * This function is used to get all registrations for a specific user.
     * @param User $user The user whose registrations to retrieve
     * @return Collection Collection of user's event registrations
     */
    public function getUserRegistrations(User $user): Collection
    {
        return $this->userRegistrationRepository->getUserRegistrations($user);
    }

    /**
     * This function is used to promote the next user from the waiting list when a spot becomes available
     * @param Events $event The event to promote a waiting user for
     * @param EventTickets $eventTicket The ticket type to promote a waiting user for
     * @return UserEventRegistration|null The promoted registration or null if no promotion occurred
     */
    protected function promoteNextWaitingListUser(Events $event, EventTickets $eventTicket): ?UserEventRegistration
    {
        $currentTicketConfirmed = $this->eventTicketRepository->getConfirmedBookingsCount($eventTicket);
        $currentEventConfirmed = $this->eventRepository->getConfirmedRegistrationsCount($event);

        if ($currentTicketConfirmed < $eventTicket->quantity && $currentEventConfirmed < $event->capacity) {
            $nextWaitingUserRegistration = $this->userRegistrationRepository->findNextWaitingListRegistration($event, $eventTicket);

            if ($nextWaitingUserRegistration) {
                $this->userRegistrationRepository->update($nextWaitingUserRegistration, ['status' => 'registered']);
                return $nextWaitingUserRegistration;
            }
        }
        return null;
    }
}