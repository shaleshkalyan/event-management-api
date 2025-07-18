<?php

namespace Database\Factories;

use App\Models\UserEventRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Events;
use App\Models\EventTickets;

class UserEventRegistrationFactory extends Factory
{
    protected $model = UserEventRegistration::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Events::factory(),
            'event_ticket_id' => EventTickets::factory(),
            'status' => fake()->randomElement(['registered', 'waiting', 'cancelled']),
            'registered_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'cancelled_at' => null,
        ];
    }

    public function registered(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'registered',
        ]);
    }

    public function waiting(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'waiting',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
