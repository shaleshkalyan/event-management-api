<?php

namespace App\Repositories;

use App\Contracts\EventsRepositoryInterface;
use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventsRepository implements EventsRepositoryInterface
{
    /**
     * This function is used to get all events.
     * @return Collection
     */
    public function getAllActiveEvents(): Collection
    {
        return Cache::remember('all_events', now()->addMinutes(10), function () {
            return Events::orderBy('date', 'asc')->get();
        });
    }

    /**
     * This function is used to get event details.
     * @param int $id
     * @return Events|null
     */
    public function find(int $id): ?Events
    {
        return Events::find($id);
    }

    /**
     * This function is used to create a new event
     * @param array $data
     * @return Events
     */
    public function createEventWithTickets(array $data): Events
    {
        return DB::transaction(function () use ($data) {
            $event = Events::create([
                'title'           => $data['title'],
                'description'     => $data['description'],
                'date'            => $data['date'],
                'venue'           => $data['venue'],
                'capacity'        => $data['capacity'],
                'is_recurring'    => $data['is_recurring'],
                'recurrence_type' => $data['recurrence_type'] ?? null,
            ]);

            $event->eventTickets()->createMany([
                [
                    'type'     => 'Regular',
                    'price'    => $data['regularTicketPrice'],
                    'quantity' => $data['regularTicketCount'],
                ],
                [
                    'type'     => 'VIP',
                    'price'    => $data['vipTicketPrice'],
                    'quantity' => $data['vipTicketCount'],
                ],
            ]);

            Cache::forget('all_events');
            return $event->load('eventTickets');
        });
    }

    /**
     * This function is used to update the event details.
     * @param Events $event
     * @param array $data
     * @return Events
     */
    public function updateEventWithTickets(Events $event, array $data): Events
    {
        return DB::transaction(function () use ($event, $data) {
            $event->update([
                'title'           => $data['title'],
                'description'     => $data['description'],
                'date'            => $data['date'],
                'venue'           => $data['venue'],
                'capacity'        => $data['capacity'],
                'is_recurring'    => $data['is_recurring'],
                'recurrence_type' => $data['recurrence_type'] ?? null,
            ]);

            $tickets = [
                ['type'     => 'Regular', 'price' => $data['regularTicketPrice'], 'quantity' => $data['regularTicketCount']],
                ['type'     => 'VIP',     'price' => $data['vipTicketPrice'],     'quantity' => $data['vipTicketCount']],
            ];

            foreach ($tickets as $ticketData) {
                $event->eventTickets()->updateOrCreate(
                [
                    'event_id' => $event->id,
                    'type'     => $ticketData['type'],
                ],
                [
                    'price'    => $ticketData['price'],
                    'quantity' => $ticketData['quantity'],
                ]
            );
            }
            Cache::forget('all_events');
            return $event->load('eventTickets');
        });
    }

    /**
     * This function is used to delete the event.
     * @param Events $event
     * @return bool
     */
    public function deleteEventWithTickets(Events $event): bool
    {
        $event->delete();
        $isDeleted = $event->trashed();
        if ($isDeleted) {
            Cache::forget('all_events');
        }
        return $isDeleted;
    }

    /**
     * This function is used to restore the Event after soft delete.
     * @param int $id
     * @return bool
     */
    public function restoreEvent(int $id): bool
    {
        $event = Events::withTrashed()->find($id);
        if ($event && $event->trashed()) {
            $restored = $event->restore();
            if ($restored) {
                Cache::forget('all_events');
            }
            return $restored;
        }
        return false;
    }

    /**
     * This function is used to get the count of registered users for an event.
     * @param Events $event
     * @return int
     */
    public function getConfirmedRegistrationsCount(Events $event): int
    {
        return $event->userEventRegistrations()->where('status', 'registered')->count();
    }
}
