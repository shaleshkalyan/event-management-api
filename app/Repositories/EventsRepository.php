<?php

namespace App\Repositories;

use App\Contracts\EventsRepositoryInterface;
use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

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
    public function create(array $data): Events
    {
        $event = Events::create($data);
        Cache::forget('all_events');
        return $event;
    }

    /**
     * This function is used to update the event details.
     * @param Events $event
     * @param array $data
     * @return bool
     */
    public function update(Events $event, array $data): bool
    {
        $updated = $event->update($data);
        if ($updated) {
            Cache::forget('all_events');
        }
        return $updated;
    }

    /**
     * This function is used to delete the event.
     * @param Events $event
     * @return bool
     */
    public function delete(Events $event): bool
    {
        $deleted = $event->delete();
        if ($deleted) {
            Cache::forget('all_events');
        }
        return $deleted;
    }

    /**
     * This function is used to restore the Event after soft delete.
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool
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