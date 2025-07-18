<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Contracts\EventManagementServiceInterface;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Throwable;

class EventController extends Controller
{
    use ApiResponse;

    protected $eventService;

    public function __construct(EventManagementServiceInterface $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of all active events.
     */
    public function index(): JsonResponse
    {
        try {
            $events = $this->eventService->getAllEvents();
            $eventData = EventResource::collection($events)->resolve();

            return $this->successResponse(
                'Events retrieved successfully',
                JsonResponse::HTTP_OK,
                $eventData
            );
        } catch (Throwable $e) {
            $this->logActivity('Failed to retrieve events.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse(
                'Could not retrieve events due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(EventRequest $request): JsonResponse
    {
        try {
            $eventData = $request->validated();
            $event = $this->eventService->createEvent($eventData);

            return $this->successResponse(
                'Event created successfully',
                JsonResponse::HTTP_CREATED,
                (new EventResource($event))->resolve()
            );
        } catch (Throwable $e) {
            $this->logActivity('Failed to create event.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return $this->errorResponse(
                'Could not create event due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Events $event): JsonResponse
    {
        try {
            return $this->successResponse(
                'Event details retrieved successfully',
                JsonResponse::HTTP_OK,
                (new EventResource($event->load('eventTickets')))->resolve()
            );
        } catch (Throwable $e) {
            $this->logActivity('Failed to retrieve event details.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $event->id,
            ]);
            return $this->errorResponse(
                'Could not retrieve event details due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified event in storage.
     */
    public function update(EventRequest $request, Events $event): JsonResponse
    {
        try {
            $eventData = $request->validated();
            $updated = $this->eventService->updateEvent($event, $eventData);

            if ($updated) {
                return $this->successResponse(
                    'Event updated successfully',
                    JsonResponse::HTTP_OK,
                    (new EventResource($event->refresh()))->resolve()
                );
            }
            return $this->errorResponse('Event update failed. Please check the provided data.', JsonResponse::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            $this->logActivity('Failed to update event.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $event->id,
                'request_data' => $request->all(),
            ]);
            return $this->errorResponse(
                'Could not update event due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified event from storage (soft delete).
     */
    public function destroy(Events $event): JsonResponse
    {
        try {
            $deleted = $this->eventService->deleteEvent($event);
            if ($deleted) {
                return $this->successResponse('Event deleted successfully', JsonResponse::HTTP_NO_CONTENT);
            }
            return $this->errorResponse('Event deletion failed.', JsonResponse::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            $this->logActivity('Failed to delete event.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $event->id,
            ]);
            return $this->errorResponse(
                'Could not delete event due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Restore a soft-deleted event.
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $restored = $this->eventService->restoreEvent((int)$id);
            if ($restored) {
                return $this->successResponse('Event restored successfully.', JsonResponse::HTTP_OK);
            }
            return $this->errorResponse('Event not found or could not be restored.', JsonResponse::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
                $this->logActivity('Failed to restore event.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $id,
            ]);
            return $this->errorResponse(
                'Could not restore event due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
