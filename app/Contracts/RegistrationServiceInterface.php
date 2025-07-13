<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Events;
use App\Models\UserEventRegistration;
use Illuminate\Database\Eloquent\Collection;

interface RegistrationServiceInterface
{
    public function registerUser(User $user, Events $event, int $eventTicketId): array;
    public function cancelUserRegistration(UserEventRegistration $registration): array;
    public function getUserRegistrations(User $user): Collection;
}