<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\UserEventRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Contracts\RegistrationServiceInterface;
use App\Http\Requests\UserEventRegistrationRequest;
use App\Http\Resources\UserRegistrationResource;
use App\Traits\ApiResponse;
use Throwable;

class RegistrationController extends Controller
{
    use ApiResponse;

    protected $registrationService;

    public function __construct(RegistrationServiceInterface $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * Register the authenticated user for a specific event with a chosen ticket type.
     */
    public function register(UserEventRegistrationRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $eventId = $validated['eventId'];
            $ticketType = $validated['eventTicketType'];

            $result = $this->registrationService->registerUser($user, $eventId, $ticketType);

            if ($result['status'] === 'error') {
                return $this->errorResponse($result['message'], JsonResponse::HTTP_BAD_REQUEST);
            } elseif ($result['status'] === 'conflict') {
                return $this->errorResponse($result['message'], JsonResponse::HTTP_CONFLICT);
            }

            return $this->successResponse(
                $result['message'],
                JsonResponse::HTTP_CREATED,
                ['registration' => (new UserRegistrationResource($result['registration']))->resolve()]
            );
        } catch (Throwable $e) {
            $this->logActivity('Failed to register user for event.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $eventId,
                'request_data' => $request->all(),
            ]);
            return $this->errorResponse(
                'Could not complete registration due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Cancel a user's event registration.
     */
    public function cancel(Request $request, Events $event): JsonResponse
    {
        try {
            $user = $request->user();
            $result = $this->registrationService->cancelUserRegistration($user,$event);

            if ($result['status'] === 'error') {
                return $this->errorResponse($result['message'], JsonResponse::HTTP_BAD_REQUEST);
            }

            return $this->successResponse('Registration cancelled successfully.', JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            $this->logActivity('Failed to cancel user registration.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $event->id,
            ]);
            return $this->errorResponse(
                'Could not cancel registration due to an internal server error. Please try again.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get all event registrations for the authenticated user.
     */
    public function getRegistrations(Request $request): JsonResponse
    {
        try {
            $registrations = $this->registrationService->getUserRegistrations($request->user());
            $registrationData = UserRegistrationResource::collection($registrations)->resolve();

            return $this->successResponse(
                'User registrations retrieved successfully',
                JsonResponse::HTTP_OK,
                $registrationData
            );
        } catch (Throwable $e) {
            $this->logActivity('Failed to retrieve user registrations.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse(
                'Could not retrieve your registrations due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
