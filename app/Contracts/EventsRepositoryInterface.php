<?php

namespace App\Contracts;

use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;

interface EventsRepositoryInterface
{
    public function getAllActiveEvents(): Collection;
    public function find(int $id): ?Events;
    public function create(array $data): Events;
    public function update(Events $event, array $data): bool;
    public function delete(Events $event): bool;
    public function restore(int $id): bool;
    public function getConfirmedRegistrationsCount(Events $event): int;
}