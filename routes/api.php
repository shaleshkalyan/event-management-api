<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;

// User Authentication & Registration (Public)
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Event Listing (Public) - Users can view events without needing to be authenticated.
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// --- Authenticated & Rate Limited Routes ---
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // User Authentication (Authenticated Actions)
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('auth.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');


    // Event Management (CRUD Operations)
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Restore soft-deleted event
    Route::post('/events/{id}/restore', [EventController::class, 'restore'])->name('events.restore');


    // Registration System
    Route::post('/events/{event}/register', [RegistrationController::class, 'register'])->name('registrations.register');

    // Cancel a specific registration
    Route::delete('/registrations/{userEventRegistration}', [RegistrationController::class, 'cancel'])->name('registrations.cancel');

    // Get all registrations for the authenticated user
    Route::get('/users/registrations', [RegistrationController::class, 'userRegistrations'])->name('registrations.user');
});