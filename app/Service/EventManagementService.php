<?php

namespace App\Service;

use App\Contracts\EventManagementServiceInterface;
use App\Contracts\EventsRepositoryInterface;
use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;

class EventManagementService implements EventManagementServiceInterface
{
    /**
     * @var EventsRepositoryInterface
     */
    protected $eventRepository;

    /**
     * EventManagementService constructor.
     * @param EventsRepositoryInterface $eventRepository The event repository instance
     */
    public function __construct(EventsRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * This function is used to get all active events from the repository
     * @return Collection Collection of active Events
     */
    public function getAllEvents(): Collection
    {
        return $this->eventRepository->getAllActiveEvents();
    }

    /**
     * This function is used to find an event by its ID
     * @param int $id The ID of the event to find
     * @return Events|null The found event or null if not found
     */
    public function getEventById(int $id): ?Events
    {
        return $this->eventRepository->find($id);
    }

    /**
     * This function is used to create a new event with the provided data.
     * @param array $data The event data for creation
     * @return Events The newly created event
     */
    public function createEvent(array $data): Events
    {
        return $this->eventRepository->createEventWithTickets($data);
    }

    /**
     * This function is used to update an existing event with new data.
     * @param Events $event The event to update
     * @param array $data The new data to update the event with
     * @return Events The updated event
     */
    public function updateEvent(Events $event, array $data): Events
    {
        return $this->eventRepository->updateEventWithTickets($event, $data);

    }

    /**
     * This function is used to delete an existing event.
     * @param Events $event The event to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteEvent(Events $event): bool
    {
        return $this->eventRepository->deleteEventWithTickets($event);

    }

    /**
     * This function is used to restore a previously deleted event.
     * @param int $id The ID of the event to restore
     * @return bool Whether the restoration was successful
     */
    public function restoreEvent(int $id): bool
    {
        return $this->eventRepository->restoreEvent($id);
    }
}