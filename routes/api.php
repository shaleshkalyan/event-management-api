<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User Authentication & Registration (Public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Event Listing (Public) - Users can view events without needing to be authenticated.
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

// --- Authenticated & Rate Limited Routes ---
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // User Authentication (Authenticated Actions)
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('auth.user');
    Route::post('/logout', [AuthController::class, 'logout']);


    // Event Management (CRUD Operations)
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // Restore soft-deleted event
    Route::post('/events/{id}/restore', [EventController::class, 'restore']);

    // Registration System
    Route::post('/events/{event}/register', [RegistrationController::class, 'register']);

    // Cancel a specific registration
    Route::delete('/registrations/{userEventRegistration}', [RegistrationController::class, 'cancel']);

    // Get all registrations for the authenticated user
    Route::get('/users/registrations', [RegistrationController::class, 'userRegistrations']);
});