<?php

namespace Database\Factories;

use App\Models\EventTickets;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Events;

class EventTicketsFactory extends Factory
{
    protected $model = EventTickets::class;

    public function definition(): array
    {
        return [
            'event_id' => Events::factory(),
            'type' => fake()->randomElement(['Regular', 'VIP']),
            'price' => fake()->numberBetween(0, 500),
            'quantity' => fake()->numberBetween(10, 50),
        ];
    }

    public function regular(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Regular',
            'price' => fake()->numberBetween(100, 500),
        ]);
    }

    public function vip(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'VIP',
            'price' => fake()->numberBetween(500, 5000),
        ]);
    }
}
