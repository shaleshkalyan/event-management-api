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

    public function registered(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'registered',
            'cancelled_at' => null,
        ]);
    }

    public function waiting(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'waiting',
            'cancelled_at' => null,
        ]);
    }

    public function cancelled(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
