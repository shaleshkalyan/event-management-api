<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     * @param array|string $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse(string $message = 'Success', int $statusCode = Response::HTTP_OK, array $data = []): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response.
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = Response::HTTP_BAD_REQUEST, array $errors = []): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'data' => $errors
        ];
        $this->logActivity("API Error: {$message}", ['errors' => $errors, 'status' => $statusCode]);

        return response()->json($response, $statusCode);
    }

    /**
     * Helper to log API activity.
     * @param string $message The log message.
     * @param array $context Additional contextual data for the log.
     */
    protected function logActivity(string $message, array $context = []): void
    {
        Log::error($message, array_merge([
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ], $context));
    }

    /**
     * Handles ValidationException specifically for API responses.
     * @param ValidationException $exception
     * @return JsonResponse
     */
    protected function validationErrorResponse(ValidationException $exception): JsonResponse
    {
        return $this->errorResponse(
            'The given data was invalid.',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $exception->errors()
        );
    }
}