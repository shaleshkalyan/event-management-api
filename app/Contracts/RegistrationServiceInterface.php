<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Events;
use App\Models\UserEventRegistration;
use Illuminate\Database\Eloquent\Collection;

interface RegistrationServiceInterface
{
    public function registerUser(User $user, int $eventId, string $eventTicketType): array;
    public function cancelUserRegistration(User $user, Events $event): array;
    public function getUserRegistrations(User $user): Collection;
}