<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SignUpRequest;
use App\Http\Requests\AuthRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     * @param SignUpRequest $request
     * @return JsonResponse
     */
    public function register(SignUpRequest $request): JsonResponse|ValidationException
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(
                'User registered successfully',
                JsonResponse::HTTP_CREATED,
                [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ]
            );
        } catch (Throwable $e) {
            $this->logActivity('User registration failed unexpectedly.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return $this->errorResponse(
                'Failed to register user due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Authenticate a user and issue an API token.
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse|ValidationException
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(
                'Logged in successfully',
                JsonResponse::HTTP_OK,
                [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ]
            );
        } catch (Throwable $e) {
            $this->logActivity('User Login failed.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return $this->errorResponse(
                'Failed to login user due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Log the authenticated user out (revoke their current token).
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse('Logged out successfully', JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            $this->logActivity('User Logout failed.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return $this->errorResponse(
                'Failed to logout user due to an internal server error. Please try again later.',
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
